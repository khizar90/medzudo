<?php

namespace App\Http\Controllers\Api;

use App\Actions\BlockedUser;
use App\Actions\FileUploadAction;
use App\Actions\FirebaseNotification;
use App\Actions\NewNotification;
use App\Actions\User\UserProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\CreatePostRequest;
use App\Http\Requests\Api\User\PostCommentRequest;
use App\Models\Follow;
use App\Models\Notification;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\PostSave;
use App\Models\User;
use App\Models\UserDevice;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use stdClass;

class PostController extends Controller
{

    public function home(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockedUser::handle($user->uuid);

        $posts = Post::whereNotIn('user_id', $blocked)->latest()->paginate(12);
        $followIds = Follow::where('user_id', $user->uuid)->pluck('follow_id');
        $suggestions = User::whereNotIn('uuid', $blocked)->where('account_type', 'individual')->whereNotIn('uuid', $followIds)->where('uuid', '!=', $user->uuid)->limit(12)->pluck('uuid')->toArray();
        $suggestions = UserProfileAction::userList($suggestions,$user->uuid);

        foreach ($suggestions as $follow) {
            $follow->is_follow = false;
        }
        foreach ($posts as $post) {
            $postby = UserProfileAction::userCommon($post->user_id,$user->uuid);
            $repostBy = new stdClass();
            if ($post->parent_id != 0) {
                $parentPost = Post::find($post->parent_id);
                $repostBy = UserProfileAction::userCommon($post->user_id,$user->uuid);
                $postby = UserProfileAction::userCommon($parentPost->user_id,$user->uuid);
                // $repostBy = User::where('uuid', $post->user_id)->select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->first();
                // $postby = User::where('uuid', $parentPost->user_id)->select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->first();
            }

            $comment_count = PostComment::where('post_id', $post->id)->count();
            $like_count = PostLike::where('post_id', $post->id)->count();
            $likestatus = PostLike::where('post_id', $post->id)->where('user_id', $user->uuid)->first();
            $saved = PostSave::where('post_id', $post->id)->where('user_id', $user->uuid)->first();
            $post->media = empty($post->media) ? [] : explode(',', $post->media);
            $likes = PostLike::where('post_id', $post->id)->latest()->limit(3)->pluck('user_id');
            $like_users = User::select('uuid', 'first_name', 'last_name', 'image')->whereIn('uuid', $likes)->get();
            if ($likestatus) {
                $post->is_liked = true;
            } else {
                $post->is_liked = false;
            }

            if ($saved) {
                $post->is_saved = true;
            } else {
                $post->is_saved = false;
            }

            $post->comment_count = $comment_count;
            $post->like_count = $like_count;
            $post->like_users = $like_users;
            $post->user = $postby;
            $post->repostBy = $repostBy;
        }

        return response()->json([
            'status' => true,
            // 'user' => $user,
            'action' =>  'Feed',
            'data' => array(
                'post' => $posts,
                'suggestions' => $suggestions
            )
        ]);
    }

    public function suggestion(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockedUser::handle($user->uuid);
        $followIds = Follow::where('user_id', $user->uuid)->pluck('follow_id');
        $suggestions = User::where('account_type', 'individual')->whereNotIn('uuid', $blocked)->whereNotIn('uuid', $followIds)->where('uuid', '!=', $user->uuid)->pluck('uuid')->toArray();
        $suggestions = UserProfileAction::userListWithPaging($suggestions,12,$user->uuid);
        foreach ($suggestions as $follow) {
            $follow->is_follow = false;
        }
        return response()->json([
            'status' => true,
            'action' =>  'Suggestions',
            'data' => $suggestions
        ]);
    }
    public function create(CreatePostRequest $request)
    {
        $user = User::find($request->user()->uuid);
        $create = new Post();

        $imagePaths = [];
        if ($request->has('media')) {
            if ($request->type == 'image') {
                foreach ($request->media as $file) {
                    $path = FileUploadAction::handle('user/' . $user->uuid  . '/post', $file);
                    $imagePaths[] = $path;
                }

                $path = Storage::disk('local')->put('user/' . $user->uuid . '/post', $file);
                $imagePath = public_path('uploads/' . $path);
                list($width, $height) = getimagesize($imagePath);
                $size = $width / $height;
                $size = number_format($size, 2);
                $create->thumbnail = $imagePaths[0];
                $mediaString  = implode(',', $imagePaths);
                $create->media = $mediaString;
                $create->size = $size;
                Storage::disk('local')->delete($path);
            }
            if ($request->type == 'video') {
                foreach ($request->media as $index => $file) {
                    $path = FileUploadAction::handle('user/' . $user->uuid  . '/post', $file);
                    if ($index == 0) {
                        $path1 = $path;
                    }
                    $imagePaths[] = $path;
                }

                $filename = uniqid() . 'thumb.png';

                FFMpeg::fromDisk('s3')->open($path1)->getFrameFromSeconds(02)->export()->inFormat(new X264)->save($filename);
                $thumbnail = Storage::disk('s3')->path($filename);
                $create->thumbnail = $thumbnail;
                $storedPublic = Storage::disk('local')->putFile($path1, $file);
                FFMpeg::fromDisk('local')->open($storedPublic)->getFrameFromSeconds(02)->export()->inFormat(new X264)->save($filename);
                $sizeDetail = getimagesize(Storage::disk('local')->path($filename));
                if ($sizeDetail)
                    $size = number_format($sizeDetail[0] / $sizeDetail[1], 2, '.', '');
                if (File::exists(public_path('/uploads/' . $filename)))
                    File::delete(public_path('/uploads/' . $filename));
                Storage::disk('local')->delete($storedPublic);
                $mediaString  = implode(',', $imagePaths);
                $create->media = $mediaString;
                $create->size = $size;
            }
        }
        $create->caption = $request->caption ?: '';
        $create->user_id = $user->uuid;
        $create->type = $request->type;
        $create->time = time();
        $create->save();


        $post = Post::find($create->id);
        $postby = UserProfileAction::userCommon($post->user_id,$user->uuid);
        $repostBy = new stdClass();
        if ($post->parent_id != 0) {
            $parentPost = Post::find($post->parent_id);
            $repostBy = UserProfileAction::userCommon($post->user_id,$user->uuid);
            $postby = UserProfileAction::userCommon($parentPost->user_id,$user->uuid);
        }

        $comment_count = PostComment::where('post_id', $post->id)->count();
        $like_count = PostLike::where('post_id', $post->id)->count();
        $likestatus = PostLike::where('post_id', $post->id)->where('user_id', $user->uuid)->first();
        $saved = PostSave::where('post_id', $post->id)->where('user_id', $user->uuid)->first();
        $post->media = empty($post->media) ? [] : explode(',', $post->media);
        $likes = PostLike::where('post_id', $post->id)->latest()->limit(3)->pluck('user_id');
        $like_users = User::select('uuid', 'first_name', 'last_name', 'image')->whereIn('uuid', $likes)->get();
        if ($likestatus) {
            $post->is_liked = true;
        } else {
            $post->is_liked = false;
        }

        if ($saved) {
            $post->is_saved = true;
        } else {
            $post->is_saved = false;
        }

        $post->comment_count = $comment_count;
        $post->like_count = $like_count;
        $post->like_users = $like_users;
        $post->user = $postby;
        $post->repostBy = $repostBy;
        return response()->json([
            'status' => true,
            'action' =>  'Post Added',
            'data' => $post
        ]);
    }

    public function repost(Request $request, $post_id)
    {
        $user = User::find($request->user()->uuid);
        $post = Post::find($post_id);
        if ($post) {
            $create = new Post();
            $create->user_id = $user->uuid;
            $create->parent_id = $post->id;
            $create->media = $post->media;
            $create->thumbnail = $post->thumbnail;
            $create->caption = $post->caption;
            $create->type = $post->type;
            $create->size = $post->size;
            $create->time = time();
            $create->save();
            return response()->json([
                'status' => true,
                'action' =>  'Post Reposted!',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Post not found!',
        ]);
    }

    public function delete($post_id)
    {
        $post = Post::find($post_id);
        if ($post) {
            Post::where('parent_id', $post->id)->delete();
            $post->delete();
            return response()->json([
                'status' => true,
                'action' => 'Post Deleted!',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => 'Post not found',
            ]);
        }
    }

    public function like(Request $request, $post_id)
    {
        $post = Post::find($post_id);
        $other = User::where('uuid', $request->user()->uuid)->first();
        $user = User::where('uuid', $post->user_id)->first();
        if ($post) {
            $check = PostLike::where('post_id', $post_id)->where('user_id', $other->uuid)->first();
            if ($check) {
                $check->delete();
                Notification::where('person_id', $other->uuid)->where('user_id', $user->uuid)->where('type', 'like_post')->where('notification_type', 'social')->delete();
                return response()->json([
                    'status' => true,
                    'action' =>  'Post like remove',
                ]);
            } else {
                $like = new PostLike();
                $like->post_id = $post_id;
                $like->user_id = $other->uuid;
                $like->save();
                NewNotification::handle($user->uuid, $other->uuid, $post->id, 'has liked your post', 'like_post', 'social');
                if ($post->user_id != $other->uuid) {
                    $tokens = UserDevice::where('user_id', $user->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                    $last_name = $other->last_name;
                    if ($last_name) {
                        FirebaseNotification::handle($tokens, $other->first_name . ' ' . $other->last_name . ' liked your post.', 'medzudo', ['data_id' => $post_id, 'type' => 'social', 'sub_type' => 'like_post'], 1);
                    } else {
                        FirebaseNotification::handle($tokens, $other->first_name . ' liked your post.', 'medzudo', ['data_id' => $post_id, 'type' => 'social', 'sub_type' => 'like_post'], 1);
                    }
                }
                return response()->json([
                    'status' => true,
                    'action' => 'Post like',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'action' => 'Post not found',
            ]);
        }
    }



    public function save(Request $request, $post_id)
    {
        $post = Post::find($post_id);
        $user = User::where('uuid', $request->user()->uuid)->first();
        if ($post) {
            $check = PostSave::where('post_id', $post_id)->where('user_id', $user->uuid)->first();
            if ($check) {
                $check->delete();
                return response()->json([
                    'status' => true,
                    'action' =>  'Post unsaved',
                ]);
            }
            $like  = new PostSave();
            $like->post_id = $post_id;
            $like->user_id = $user->uuid;
            $like->save();
            return response()->json([
                'status' => true,
                'action' =>  'Post saved',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => 'Post not found',
            ]);
        }
    }

    public function comment(PostCommentRequest $request)
    {
        $post = Post::find($request->post_id);
        $user = UserProfileAction::userCommon($request->user()->uuid,$request->user()->uuid);
        $comment  = new PostComment();
        $comment->post_id = $request->post_id;
        $comment->user_id = $user->uuid;
        $comment->comment = $request->comment;
        $comment->time = strtotime(date('Y-m-d H:i:s'));
        $comment->save();

        $total = PostComment::where('post_id', $request->post_id)->count();
        $new_comment = PostComment::find($comment->id);
        $new_comment->user = $user;

        NewNotification::handle($post->user_id, $user->uuid, $post->id, 'has comment on your post', 'comment_post', 'social');
        if ($post->user_id != $user->uuid) {
            $tokens = UserDevice::where('user_id', $post->user_id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            $last_name = $user->last_name;
            if ($last_name) {
                FirebaseNotification::handle($tokens, $user->first_name . ' ' . $user->last_name . ' commented: ' . $comment->comment, 'medzudo', ['data_id' => $post->id, 'type' => 'social', 'sub_type' => 'comment_post'], 1);
            } else {
                FirebaseNotification::handle($tokens, $user->first_name . ' commented: ' . $comment->comment, 'medzudo', ['data_id' => $post->id, 'type' => 'social', 'sub_type' => 'comment_post'], 1);
            }
        }

        return response()->json([
            'status' => true,
            'action' =>  'Comment added',
            'total' => $total,
            'data' => $new_comment
        ]);
    }

    public function deleteComment(Request $request, $comment_id)
    {
        $user = User::find($request->user()->uuid);
        $post = PostComment::find($comment_id);
        if ($post) {
            $mainPost = Post::find($post->post_id);
            Notification::where('person_id', $user->uuid)->where('user_id', $mainPost->user_id)->where('type', 'comment_post')->where('notification_type', 'social')->delete();
            $post->delete();
            return response()->json([
                'status' => true,
                'action' => 'Comment Deleted!',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => 'Comment not found',
            ]);
        }
    }



    public function commentList(Request $request, $post_id)
    {
        $user = User::find($request->user()->uuid);

        $post = Post::find($post_id);
        if ($post) {

            $comments = PostComment::where('post_id', $post->id)->paginate(12);

            foreach ($comments as $comment) {
                $user = UserProfileAction::userCommon($comment->user_id,$user->uuid);
                $comment->user = $user;
            }
            $total = PostComment::where('post_id', $post_id)->count();
            return response()->json([
                'status' => true,
                'action' =>  "Comments",
                'total' => $total,
                'data' => $comments
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  "No post found",
        ]);
    }


    public function likeList(Request $request, $post_id)
    {
        $user = User::find($request->user()->uuid);
        $post = Post::find($post_id);
        if ($post) {
            $likes = PostLike::where('post_id', $post_id)->pluck('user_id');
            $users =  User::whereIn('uuid', $likes)->pluck('uuid')->toArray();
            $users = UserProfileAction::userListWithPaging($users, 10,$user->uuid);
            return response()->json([
                'status' => true,
                'action' =>  "Users",
                'data' => $users
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  "No post found",
        ]);
    }

    public function detail(Request $request, $post_id)
    {
        $user = User::find($request->user()->uuid);
        $post = Post::find($post_id);

        if ($post) {
            $postby = UserProfileAction::userCommon($post->user_id,$user->uuid);
            $comment_count = PostComment::where('post_id', $post->id)->count();
            $like_count = PostLike::where('post_id', $post->id)->count();
            $likestatus = PostLike::where('post_id', $post->id)->where('user_id', $user->uuid)->first();
            $saved = PostSave::where('post_id', $post->id)->where('user_id', $user->uuid)->first();
            $post->media = empty($post->media) ? [] : explode(',', $post->media);
            $likes = PostLike::where('post_id', $post->id)->latest()->limit(3)->pluck('user_id');
            $like_users = User::select('uuid', 'first_name', 'last_name', 'image')->whereIn('uuid', $likes)->get();
            if ($likestatus) {
                $post->is_liked = true;
            } else {
                $post->is_liked = false;
            }

            if ($saved) {
                $post->is_saved = true;
            } else {
                $post->is_saved = false;
            }

            $post->comment_count = $comment_count;
            $post->like_count = $like_count;
            $post->like_users = $like_users;
            $post->user = $postby;


            return response()->json([
                'status' => true,
                'action' =>  'Detail Post',
                'data' => $post
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' =>  'Post not Found',
            ]);
        }
    }
}
