<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Community\Post\CommunityCreatePostRequest;
use App\Http\Requests\Api\Community\Post\CommunityPostCommentRequest;
use App\Http\Requests\Api\Community\Post\CommunityPostVoteRequest;
use App\Models\Community;
use App\Models\CommunityPost;
use App\Models\CommunityPostComment;
use App\Models\CommunityPostCommentLike;
use App\Models\CommunityPostLike;
use App\Models\CommunityPostSave;
use App\Models\CommunityPostVote;
use App\Models\User;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommunityPostController extends Controller
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

    public function create(CommunityCreatePostRequest $request)
    {
        $user = User::find($request->user()->uuid);
        $create = new CommunityPost();

        $imagePaths = [];
        if ($request->type == 'image') {
            foreach ($request->media as $file) {
                $path = Storage::disk('local')->put('user/' . $user->uuid . '/community/post', $file);
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
        $user = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->where('uuid', $request->user()->uuid)->first();
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

    public function DeleteComment($comment_id)
    {
        $post = CommunityPostComment::find($comment_id);
        if ($post) {
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

    public function vote(CommunityPostVoteRequest $request){
        $user = User::find($request->user()->uuid);
        $find = CommunityPostVote::where('user_id',$user->uuid)->where('post_id',$request->post_id)->where('option',$request->option)->first();
        if($find){
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
}
