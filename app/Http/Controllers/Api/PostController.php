<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\CreatePostRequest;
use App\Http\Requests\Api\User\PostCommentRequest;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\PostSave;
use App\Models\User;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    function getVideoThumb($path)
    {
        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open(public_path($path));
        $thumbnailFileName = time() . '-' . uniqid() . '.jpg';
        $thumbnailPath = '/uploads/thumbnails/' . $thumbnailFileName;
        $video->frame(TimeCode::fromSeconds(1))->save(public_path($thumbnailPath));
        return $thumbnailPath;
    }

    public function create(CreatePostRequest $request)
    {
        $user = User::find($request->user()->uuid);
        $create = new Post();

        $imagePaths = [];
        if ($request->type == 'image') {
            foreach ($request->media as $file) {
                $path = Storage::disk('local')->put('user/' . $user->uuid . '/post', $file);
                $imagePaths[] = '/uploads/' . $path;
            }

            $imagePath = public_path($imagePaths[0]);
            list($width, $height) = getimagesize($imagePath);
            $size = $width / $height;
            $size = number_format($size, 2);

            $create->thumbnail = $imagePaths[0];
            $mediaString  = implode(',', $imagePaths);
            $create->media = $mediaString;
            $create->size = $size;
        }
        if ($request->type == 'video') {
            foreach ($request->media as $file) {
                $path = Storage::disk('local')->put('user/' . $user->uuid . '/community/post', $file);
                $imagePaths[] = '/uploads/' . $path;
            }

            $thumbnailPath = $this->getVideoThumb($imagePaths[0]);
            $create->thumbnail = $thumbnailPath;

            $imagePath = public_path($thumbnailPath);
            list($width, $height) = getimagesize($imagePath);
            $size = $width / $height;
            $size = number_format($size, 2);

            $create->thumbnail = $thumbnailPath;
            $mediaString  = implode(',', $imagePaths);
            $create->media = $mediaString;
            $create->size = $size;
        }
        $create->caption = $request->caption ? : '';
        $create->user_id = $user->uuid;
        $create->type = $request->type;
        $create->time = time();
        $create->save();


        $new = Post::find($create->id);
        return response()->json([
            'status' => true,
            'action' =>  'Post Added',
            'data' => $new
        ]);
    }

    public function repost(Request $request,$post_id){
        $user = User::find($request->user()->uuid);
        $post = Post::find($post_id);
        if($post){
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
            Post::where('parent_id',$post->id)->delete();
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
                //  Notification::where('data_id', $post_id)->where('type', 'like_post')->where('person_id', $user_id)->delete();
                return response()->json([
                    'status' => true,
                    'action' =>  'Post like remove',
                ]);
            } else {
                $like = new PostLike();
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
        $user = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->where('uuid', $request->user()->uuid)->first();
        $comment  = new PostComment();
        $comment->post_id = $request->post_id;
        $comment->user_id = $user->uuid;
        $comment->comment = $request->comment;
        $comment->time = strtotime(date('Y-m-d H:i:s'));
        $comment->save();

        $total = PostComment::where('post_id', $request->post_id)->count();
        $new_comment = PostComment::find($comment->id);
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
        $post = PostComment::find($comment_id);
        if ($post) {
            PostComment::where('parent_id',$post->id)->delete();
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
                $user = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->where('uuid', $comment->user_id)->first();
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
            $users =  User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->whereIn('uuid', $likes)->Paginate(10);
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
            $postby = User::where('uuid', $post->user_id)->select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->first();
            $comment_count = PostComment::where('post_id', $post->id)->count();
            $like_count = PostLike::where('post_id', $post->id)->count();
            $likestatus = PostLike::where('post_id', $post->id)->where('user_id', $user->uuid)->first();
            $saved = PostSave::where('post_id', $post->id)->where('user_id', $user->uuid)->first();
            $post->media = empty($post->media) ? [] : explode(',', $post->media);
            $likes = PostLike::where('post_id', $post->id)->latest()->limit(3)->pluck('user_id');
            $like_users = User::select('uuid', 'first_name', 'last_name')->whereIn('uuid', $likes)->where('uuid', '!=', $user->uuid)->get();
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
