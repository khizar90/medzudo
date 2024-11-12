<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class ShareableController extends Controller
{
    public function user($type, $loginId, $id)
    {
        $title = 'medzudo App';
        $description = 'medzudo App';
        $image = 'https://api.medzudo.app/image_placeholder.png';
        $user = User::where('uuid', $id)->first();
        if ($user) {
            if ($user->last_name) {
                $title = $user->first_name . ' ' . $user->last_name;
            } else {
                $title = $user->first_name;
            }
            $description = $user->about;
            if ($user->image != '')
                $image = 'https://d38vqtrl6p25ob.cloudfront.net/' . $user->image;
        }
        return view('medzudo.show', compact('title', 'description', 'image'));
    }

    public function other($type, $loginId, $id)
    {
        $title = 'medzudo App';
        $description = 'medzudo App';
        $image = 'https://api.medzudo.app/image_placeholder.png';
        if ($type == 'social_post') {
            $title = 'Post';
            $post = Post::find($id);
            if ($post) {
                $user = User::find($post->user_id);
                if ($post->caption != '')
                    $description = $post->caption;
                if ($post->thumbnail != '')
                    $image = 'https://d38vqtrl6p25ob.cloudfront.net/' . $post->thumbnail;
            }
        }
        if ($type == 'community_post') {
            $title = 'Post';
            $post = CommunityPost::find($id);
            if ($post) {
                $user = User::find($post->user_id);
                if ($post->caption != '')
                    $description = $post->caption;
                if ($post->thumbnail != '')
                    $image = 'https://d38vqtrl6p25ob.cloudfront.net/' . $post->thumbnail;
            }
        }
        return view('medzudo.show', compact('title', 'description', 'image'));
    }
}
