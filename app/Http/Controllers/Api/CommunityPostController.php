<?php

namespace App\Http\Controllers\Api;

use App\Actions\BlockedUser;
use App\Actions\FileUploadAction;
use App\Actions\User\UserProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Community\Post\CommunityCreatePostRequest;
use App\Http\Requests\Api\Community\Post\CommunityPostCommentRequest;
use App\Http\Requests\Api\Community\Post\CommunityPostVoteRequest;

use App\Models\CommunityMedia;
use App\Models\CommunityPost;
use App\Models\CommunityPostComment;
use App\Models\CommunityPostCommentLike;
use App\Models\CommunityPostLike;
use App\Models\CommunityPostSave;
use App\Models\CommunityPostVote;
use App\Models\User;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CommunityPostController extends Controller
{

    public function create(CommunityCreatePostRequest $request)
    {
        $user = User::find($request->user()->uuid);
        $create = new CommunityPost();

        $imagePaths = [];
        if ($request->type == 'image') {
            foreach ($request->media as $file) {
                $path = FileUploadAction::handle('user/' . $user->uuid . '/community/post', $file);
                $imagePaths[] = $path;
            }
            $path = Storage::disk('local')->put('user/' . $user->uuid . '/community/post', $file);
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
                $path = FileUploadAction::handle('user/' . $user->uuid . '/community/post', $file);
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
        $create->user_id = $user->uuid;
        $create->community_id = $request->community_id;
        $create->type = $request->type;
        $create->caption = $request->caption ?: '';
        $create->option_1 = $request->option_1 ?: '';
        $create->option_2 = $request->option_2 ?: '';
        $create->option_3 = $request->option_3 ?: '';
        $create->option_4 = $request->option_4 ?: '';
        $create->time = time();
        $create->save();


        if ($request->add_into_media == 1) {
            if ($request->has('media')) {
                $media = explode(',', $create->media);
                foreach ($media as $file) {
                    $createMedia = new CommunityMedia();
                    $createMedia->media = $file;
                    $createMedia->thumbnail = $create->thumbnail;
                    $createMedia->tagline = '';
                    $createMedia->community_id = $create->community_id;
                    $createMedia->type = $create->type;
                    $createMedia->save();
                }
            }
        }
        $new = CommunityPost::find($create->id);
        return response()->json([
            'status' => true,
            'action' =>  'Post Added',
            'data' => $new
        ]);
    }

    public function delete($post_id)
    {
        $post = CommunityPost::find($post_id);
        if ($post) {
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
        $post = CommunityPost::find($post_id);
        $other = User::where('uuid', $request->user()->uuid)->first();
        $user = User::where('uuid', $post->user_id)->first();
        if ($post) {
            $check = CommunityPostLike::where('post_id', $post_id)->where('user_id', $other->uuid)->first();
            if ($check) {
                $check->delete();
                //  Notification::where('data_id', $post_id)->where('type', 'like_post')->where('person_id', $user_id)->delete();
                return response()->json([
                    'status' => true,
                    'action' =>  'Post like remove',
                ]);
            } else {
                $like = new CommunityPostLike();
                $like->post_id = $post_id;
                $like->user_id = $other->uuid;
                $like->save();
                // NewNotification::handle($user, $other->uuid, $post->id, 'has liked your post', 'social', 'like_post');
                // if ($post->user_id != $user_id) {
                //     $tokens = UserDevice::where('user_id', $user->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                //     FirebaseNotification::handle($tokens, $other->first_name . ' ' . $other->last_name . ' liked your post.', 'Thrubal', ['data_id' => $post_id, 'type' => 'like_post', 'sub_type' => 'like_post']);
                // }
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
        $post = CommunityPost::find($post_id);
        $user = User::where('uuid', $request->user()->uuid)->first();
        if ($post) {
            $check = CommunityPostSave::where('post_id', $post_id)->where('user_id', $user->uuid)->first();
            if ($check) {
                $check->delete();
                return response()->json([
                    'status' => true,
                    'action' =>  'Post unsaved',
                ]);
            }
            $like  = new CommunityPostSave();
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

    public function comment(CommunityPostCommentRequest $request)
    {
        $post = CommunityPost::find($request->post_id);
        $user = UserProfileAction::userCommon($request->user()->uuid,$request->user()->uuid);
        $comment  = new CommunityPostComment();
        $comment->post_id = $request->post_id;
        $comment->user_id = $user->uuid;
        $comment->comment = $request->comment;
        $comment->time = strtotime(date('Y-m-d H:i:s'));
        $comment->parent_id = $request->parent_id ?: 0;
        $comment->save();

        $total = CommunityPostComment::where('post_id', $request->post_id)->count();
        $new_comment = CommunityPostComment::find($comment->id);
        $new_comment->user = $user;
        return response()->json([
            'status' => true,
            'action' =>  'Comment added',
            'total' => $total,
            'data' => $new_comment
        ]);
    }

    public function deleteComment($comment_id)
    {
        $post = CommunityPostComment::find($comment_id);
        if ($post) {
            CommunityPostComment::where('parent_id', $post->id)->delete();
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
    public function likeComment(Request $request, $comment_id)
    {
        $comment = CommunityPostComment::find($comment_id);
        $other = User::where('uuid', $request->user()->uuid)->first();

        if ($comment) {
            $check = CommunityPostCommentLike::where('comment_id', $comment_id)->where('user_id', $other->uuid)->first();
            if ($check) {
                $check->delete();
                //  Notification::where('data_id', $post_id)->where('type', 'like_post')->where('person_id', $user_id)->delete();
                return response()->json([
                    'status' => true,
                    'action' =>  'Comment like remove',
                ]);
            } else {
                $like = new CommunityPostCommentLike();
                $like->comment_id = $comment_id;
                $like->user_id = $other->uuid;
                $like->save();
                // NewNotification::handle($user, $other->uuid, $post->id, 'has liked your post', 'social', 'like_post');
                // if ($post->user_id != $user_id) {
                //     $tokens = UserDevice::where('user_id', $user->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                //     FirebaseNotification::handle($tokens, $other->first_name . ' ' . $other->last_name . ' liked your post.', 'Thrubal', ['data_id' => $post_id, 'type' => 'like_post', 'sub_type' => 'like_post']);
                // }
                return response()->json([
                    'status' => true,
                    'action' => 'Comment like',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'action' => 'Comment not found',
            ]);
        }
    }

    public function vote(CommunityPostVoteRequest $request)
    {
        $user = User::find($request->user()->uuid);
        $find = CommunityPostVote::where('user_id', $user->uuid)->where('post_id', $request->post_id)->first();
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => 'Vote Remove',
            ]);
        }
        $create = new CommunityPostVote();
        $create->post_id = $request->post_id;
        $create->user_id = $user->uuid;
        $create->option = $request->option;
        $create->save();
        return response()->json([
            'status' => true,
            'action' => 'Vote Added',
        ]);
    }
    public function commentList(Request $request, $post_id)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockedUser::handle($user->uuid);

        $post = CommunityPost::find($post_id);
        if ($post) {

            $comments = CommunityPostComment::whereNotIn('user_id', $blocked)->where('post_id', $post->id)->where('parent_id', 0)->paginate(12);

            foreach ($comments as $comment) {
                $user = UserProfileAction::userCommon($comment->user_id,$user->uuid);
                $likes = CommunityPostCommentLike::where('comment_id', $comment->id)->count();
                $replies = CommunityPostComment::where('parent_id', $comment->id)->count();
                $comment->likes = $likes;
                $comment->replies = $replies;
                $comment->user = $user;

                $likestatus = CommunityPostCommentLike::where('comment_id', $comment->id)->where('user_id', $user->uuid)->first();

                if ($likestatus) {
                    $comment->is_liked = true;
                } else {
                    $comment->is_liked = false;
                }
            }
            $total = CommunityPostComment::where('post_id', $post_id)->count();
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
    public function commentReplies(Request $request, $comment_id)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockedUser::handle($user->uuid);

        $comments = CommunityPostComment::whereNotIn('user_id', $blocked)->where('parent_id', $comment_id)->get();
        foreach ($comments as $comment) {
            $user = UserProfileAction::userCommon($comment->user_id,$user->uuid);
            $likes = CommunityPostCommentLike::where('comment_id', $comment->id)->count();
            // $replies = Comment::where('parent_id', $comment->id)->count();
            $comment->likes = $likes;
            $comment->replies = 0;
            $comment->user = $user;
            $likestatus = CommunityPostCommentLike::where('comment_id', $comment->id)->where('user_id', $user->uuid)->first();

            if ($likestatus) {
                $comment->is_liked = true;
            } else {
                $comment->is_liked = false;
            }
        }

        $comment = CommunityPostComment::find($comment_id);

        $total = CommunityPostComment::where('post_id', $comment->post_id)->count();

        return response()->json([
            'status' => true,
            'action' =>  "Comments",
            'total' => $total,
            'data' => $comments
        ]);
    }

    public function likeList(Request $request, $post_id)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockedUser::handle($user->uuid);

        $post = CommunityPost::find($post_id);
        if ($post) {
            $likes = CommunityPostLike::whereNotIn('user_id', $blocked)->where('post_id', $post_id)->pluck('user_id');
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
        $post = CommunityPost::find($post_id);

        if ($post) {
            $postby = UserProfileAction::userCommon($post->user_id,$user->uuid);
            $comment_count = CommunityPostComment::where('post_id', $post->id)->count();
            $like_count = CommunityPostLike::where('post_id', $post->id)->count();
            $likestatus = CommunityPostLike::where('post_id', $post->id)->where('user_id', $user->uuid)->first();
            $saved = CommunityPostSave::where('post_id', $post->id)->where('user_id', $user->uuid)->first();
            $post->media = empty($post->media) ? [] : explode(',', $post->media);
            $likes = CommunityPostLike::where('post_id', $post->id)->latest()->limit(3)->pluck('user_id');
            $like_users = User::select('uuid', 'first_name', 'last_name', 'image')->whereIn('uuid', $likes)->where('uuid', '!=', $user->uuid)->get();
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
            $total_vote_count = 0;
            $my_voted_option = '';
            $option_1_count = 0;
            $option_2_count = 0;
            $option_3_count = 0;
            $option_4_count = 0;
            if ($post->type == 'poll') {
                $total_vote_count = CommunityPostVote::where('post_id', $post->id)->count();
                $checkVote = CommunityPostVote::where('user_id', $user->uuid)->where('post_id', $post->id)->first();
                if ($checkVote) {
                    $my_voted_option = $checkVote->option;
                }
                $option_1_count = CommunityPostVote::where('post_id', $post->id)->where('option', 'option_1')->count();
                $option_1_count = $option_1_count / $total_vote_count * 100;
                $option_2_count = CommunityPostVote::where('post_id', $post->id)->where('option', 'option_2')->count();
                $option_2_count = $option_2_count / $total_vote_count * 100;
                $option_3_count = CommunityPostVote::where('post_id', $post->id)->where('option', 'option_3')->count();
                $option_3_count = $option_3_count / $total_vote_count * 100;
                $option_4_count = CommunityPostVote::where('post_id', $post->id)->where('option', 'option_4')->count();
                $option_4_count = $option_4_count / $total_vote_count * 100;
            }
            $post->my_voted_option = $my_voted_option;

            $post->total_vote_count = $total_vote_count;
            $post->option_1_count = $option_1_count;
            $post->option_2_count = $option_2_count;
            $post->option_3_count = $option_3_count;
            $post->option_4_count = $option_4_count;


            $post->comment_count = $comment_count;
            $post->like_count = $like_count;
            $post->like_users = $like_users;
            $post->user = $postby;


            return response()->json([
                'status' => true,
                'action' =>  'Feed',
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
