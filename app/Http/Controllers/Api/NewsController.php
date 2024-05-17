<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddNewsRequest;
use App\Http\Requests\Api\CommentNewsRequest;
use App\Http\Requests\Api\EditNewsRequest;
use App\Http\Requests\Api\LikeNewsRequest;
use App\Http\Requests\Api\SaveNewsRequest;
use App\Models\Category;
use App\Models\LikeNews;
use App\Models\News;
use App\Models\NewsComment;
use App\Models\SaveNews;
use App\Models\User;
use Illuminate\Http\Request;
use stdClass;

class NewsController extends Controller
{
    public function create(AddNewsRequest $request)
    {
        $create = new News();
        if ($request->hasFile('media')) {
            $file = $request->file('media');
            // $path = Storage::disk('s3')->putFile('user/' . $request->user_id . '/profile', $file);
            // $path = Storage::disk('s3')->url($path);
            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/user/' . $request->user_id . '/news/', $filename))
                $path =  '/uploads/user/' . $request->user_id . '/news/' . $filename;
            $create->media = $path;
        }
        $create->user_id = $request->user_id;
        $create->category_id = $request->category_id;
        $create->title = $request->title;
        $create->description = $request->description;
        $create->time = strtotime(date('Y-m-d H:i:s'));

        $create->save();

        return response()->json([
            'status' => true,
            'action' => 'News Added'
        ]);
    }

    public function edit(EditNewsRequest $request)
    {
        $create = News::find($request->news_id);

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            // $path = Storage::disk('s3')->putFile('user/' . $request->user_id . '/profile', $file);
            // $path = Storage::disk('s3')->url($path);
            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/user/' . $request->user_id . '/news/', $filename))
                $path =  '/uploads/user/' . $request->user_id . '/news/' . $filename;
            $create->media = $path;
        }
        $create->category_id = $request->category_id;
        $create->title = $request->title;
        $create->description = $request->description;
        $create->save();

        return response()->json([
            'status' => true,
            'action' => 'News Edit'
        ]);
    }
    public function delete($news_id)
    {
        $find = News::find($news_id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => 'News Deleted'
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'News not found'
        ]);
    }
    public function Like(LikeNewsRequest $request)
    {
        $news = News::find($request->news_id);


        $vote = LikeNews::where('news_id', $request->news_id)->where('user_id', $request->user_id)->first();
        if ($vote) {
            $vote->delete();
            $news->total_likes = $news->total_likes - 1;

            return response()->json([
                'status' => true,
                'action' =>  'News Like Remove',
            ]);
        }
        $create = new LikeNews();
        $create->user_id = $request->user_id;
        $create->news_id = $request->news_id;
        $create->save();

        $news->total_likes = $news->total_likes + 1;
        $news->save();
        return response()->json([
            'status' => true,
            'action' =>  'News Liked',
        ]);
    }
    public function comment(CommentNewsRequest $request)
    {
        $user = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username')->where('uuid', $request->user_id)->first();
        $comment  = new NewsComment();
        $comment->news_id = $request->news_id;
        $comment->user_id = $request->user_id;
        $comment->comment = $request->comment;
        $comment->time = strtotime(date('Y-m-d H:i:s'));
        $comment->save();
        $new_comment = NewsComment::find($comment->id);
        $new_comment->comment_by = $user;
        return response()->json([
            'status' => true,
            'action' =>  'Comment added',
            'data' => $new_comment
        ]);
    }
    public function commentDelete($comment_id)
    {
        $find = NewsComment::find($comment_id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Comment Deleted',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Comment not found',
        ]);
    }

    public function list($user_id)
    {
        $categories  = Category::where('type','news')->get();
        $trending = News::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->orderByDesc('total_likes')->limit(10)->get();
        $recent = News::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->latest()->paginate(12);
        foreach ($recent as $item) {
            $is_like = LikeNews::where('news_id', $item->id)->first();
            if ($is_like) {
                $item->is_like = true;
            } else {
                $item->is_like = false;
            }
            $is_saved = SaveNews::where('news_id', $item->id)->where('user_id', $user_id)->first();
            if ($is_saved) {
                $item->is_saved = true;
            } else {
                $item->is_saved = false;
            }
            $category = Category::find($item->category_id);
            $item->category = $category;
            $comment_count = NewsComment::where('news_id', $item->id)->count();
            $like_count = LikeNews::where('news_id', $item->id)->count();
            $item->like_count = $like_count;
            $item->comment_count = $comment_count;
        }
        foreach ($trending as $item1) {
            $is_like = LikeNews::where('news_id', $item1->id)->first();
            if ($is_like) {
                $item1->is_like = true;
            } else {
                $item1->is_like = false;
            }
            $is_saved = SaveNews::where('news_id', $item1->id)->where('user_id', $user_id)->first();
            if ($is_saved) {
                $item1->is_saved = true;
            } else {
                $item1->is_saved = false;
            }
            $category = Category::find($item1->category_id);
            $item1->category = $category;
            $comment_count = NewsComment::where('news_id', $item1->id)->count();
            $like_count = LikeNews::where('news_id', $item1->id)->count();
            $item1->like_count = $like_count;
            $item1->comment_count = $comment_count;
        }
        return response()->json([
            'status' => true,
            'action' => 'Home',
            'data' => array(
                'categories' => $categories,
                'trending' => $trending,
                'recent' => $recent
            )
        ]);
    }

    public function trending($user_id)
    {
        $trending = News::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->orderByDesc('total_likes')->paginate(12);
        foreach ($trending as $item1) {
            $is_like = LikeNews::where('news_id', $item1->id)->first();
            if ($is_like) {
                $item1->is_like = true;
            } else {
                $item1->is_like = false;
            }
            $is_saved = SaveNews::where('news_id', $item1->id)->where('user_id', $user_id)->first();
            if ($is_saved) {
                $item1->is_saved = true;
            } else {
                $item1->is_saved = false;
            }
            $category = Category::find($item1->category_id);
            $item1->category = $category;
            $comment_count = NewsComment::where('news_id', $item1->id)->count();
            $like_count = LikeNews::where('news_id', $item1->id)->count();
            $item1->like_count = $like_count;
            $item1->comment_count = $comment_count;
        }
        return response()->json([
            'status' => true,
            'action' => 'Trendings',
            'data' => $trending
        ]);
    }

    public function detail($user_id, $news_id)
    {
        $news = News::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->where('id', $news_id)->first();
        // $user = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username')->where('uuid', $news->user_id)->first();
        $category = Category::find($news->category_id);
        // $news->user = $user;
        $news->category = $category;

        $is_like = LikeNews::where('news_id', $news_id)->first();
        if ($is_like) {
            $news->is_like = true;
        } else {
            $news->is_like = false;
        }
        $is_saved = SaveNews::where('news_id', $news_id)->where('user_id', $user_id)->first();
        if ($is_saved) {
            $news->is_saved = true;
        } else {
            $news->is_saved = false;
        }
        $category = Category::find($news->category_id);
        $news->category = $category;
        $comment_count = NewsComment::where('news_id', $news_id)->count();
        $like_count = LikeNews::where('news_id', $news_id)->count();
        $news->like_count = $like_count;
        $news->comment_count = $comment_count;
        $comment = NewsComment::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->where('news_id', $news_id)->get();
        $news->commnent = $comment;


        return response()->json([
            'status' => true,
            'action' => 'News Detail',
            'data' => $news
        ]);
    }

    public function save(SaveNewsRequest $request)
    {
        $check = SaveNews::where('news_id', $request->news_id)->where('user_id', $request->user_id)->first();
        if ($check) {
            $check->delete();
            return response()->json([
                'status' => true,
                'action' =>  'News Unsaved',
            ]);
        }

        $create  = new SaveNews();
        $create->news_id = $request->news_id;
        $create->user_id = $request->user_id;
        $create->save();

        return response()->json([
            'status' => true,
            'action' =>  'News saved',
        ]);
    }

    public function saveList($user_id)
    {
        $save_news_ids = SaveNews::where('user_id', $user_id)->pluck('news_id');
        $news  = News::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->whereIn('id', $save_news_ids)->latest()->paginate(12);
        foreach ($news as $item) {
            $is_like = LikeNews::where('news_id', $item->id)->first();
            if ($is_like) {
                $item->is_like = true;
            } else {
                $item->is_like = false;
            }
            $is_saved = SaveNews::where('news_id', $item->id)->where('user_id', $user_id)->first();
            if ($is_saved) {
                $item->is_saved = true;
            } else {
                $item->is_saved = false;
            }
            $category = Category::find($item->category_id);
            $item->category = $category;
            $comment_count = NewsComment::where('news_id', $item->id)->count();
            $like_count = LikeNews::where('news_id', $item->id)->count();
            $item->like_count = $like_count;
            $item->comment_count = $comment_count;
        }

        return response()->json([
            'status' => true,
            'action' => 'Save News List',
            'data' => $news
        ]);
    }
    public function userNews($type, $user_id)
    {
        if ($type == 'my') {
            $news  = News::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->where('user_id', $user_id)->latest()->paginate(12);
            foreach ($news as $item) {
                $is_like = LikeNews::where('news_id', $item->id)->first();
                if ($is_like) {
                    $item->is_like = true;
                } else {
                    $item->is_like = false;
                }
                $is_saved = SaveNews::where('news_id', $item->id)->where('user_id', $user_id)->first();
                if ($is_saved) {
                    $item->is_saved = true;
                } else {
                    $item->is_saved = false;
                }
                $category = Category::find($item->category_id);
                $item->category = $category;
                $comment_count = NewsComment::where('news_id', $item->id)->count();
                $like_count = LikeNews::where('news_id', $item->id)->count();
                $item->like_count = $like_count;
                $item->comment_count = $comment_count;
            }
        }
        if ($type == 'saved') {
            $save_news_ids = SaveNews::where('user_id', $user_id)->pluck('news_id');
            $news  = News::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->whereIn('id', $save_news_ids)->latest()->paginate(12);
            foreach ($news as $item) {
                $is_like = LikeNews::where('news_id', $item->id)->first();
                if ($is_like) {
                    $item->is_like = true;
                } else {
                    $item->is_like = false;
                }
                $is_saved = SaveNews::where('news_id', $item->id)->where('user_id', $user_id)->first();
                if ($is_saved) {
                    $item->is_saved = true;
                } else {
                    $item->is_saved = false;
                }
                $category = Category::find($item->category_id);
                $item->category = $category;
                $comment_count = NewsComment::where('news_id', $item->id)->count();
                $like_count = LikeNews::where('news_id', $item->id)->count();
                $item->like_count = $like_count;
                $item->comment_count = $comment_count;
            }
        }

        return response()->json([
            'status' => true,
            'action' => 'Save news List',
            'data' => $news
        ]);
    }

    public function categorySearch($category_id, $user_id)
    {
        $find = Category::find($category_id);
        if ($find) {
            $news  = News::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->where('category_id', $category_id)->latest()->paginate(12);
            foreach ($news as $item) {
                $is_like = LikeNews::where('news_id', $item->id)->first();
                if ($is_like) {
                    $item->is_like = true;
                } else {
                    $item->is_like = false;
                }
                $is_saved = SaveNews::where('news_id', $item->id)->where('user_id', $user_id)->first();
                if ($is_saved) {
                    $item->is_saved = true;
                } else {
                    $item->is_saved = false;
                }
                $category = Category::find($item->category_id);
                $item->category = $category;
                $comment_count = NewsComment::where('news_id', $item->id)->count();
                $like_count = LikeNews::where('news_id', $item->id)->count();
                $item->like_count = $like_count;
                $item->comment_count = $comment_count;
            }




            return response()->json([
                'status' => true,
                'action' => 'Category News List',
                'data' => $news
            ]);
        }

        return response()->json([
            'status' => false,
            'action' =>  "Category not found",
        ]);
    }

    public function search(Request $request)
    {

        if ($request->keyword != null || $request->keyword != '') {
            $news  = News::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->where("title", "LIKE", "%" . $request->keyword . "%")->latest()->paginate(12);

            foreach ($news as $item) {
                $is_like = LikeNews::where('news_id', $item->id)->first();
                if ($is_like) {
                    $item->is_like = true;
                } else {
                    $item->is_like = false;
                }
                $is_saved = SaveNews::where('news_id', $item->id)->where('user_id', $request->user_id)->first();
                if ($is_saved) {
                    $item->is_saved = true;
                } else {
                    $item->is_saved = false;
                }
                $category = Category::find($item->category_id);
                $item->category = $category;
                $comment_count = NewsComment::where('news_id', $item->id)->count();
                $like_count = LikeNews::where('news_id', $item->id)->count();
                $item->like_count = $like_count;
                $item->comment_count = $comment_count;
            }
            return response()->json([
                'status' => true,
                'action' =>  "News",
                'data' => $news
            ]);
        }
        $news = new stdClass();
        return response()->json([
            'status' => true,
            'action' =>  "NEws",
            'data' => $news
        ]);
    }
}
