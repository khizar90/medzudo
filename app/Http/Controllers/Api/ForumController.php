<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddForumRequest;
use App\Http\Requests\Api\CommentForumRequest;
use App\Http\Requests\Api\EditForumRequest;
use App\Http\Requests\Api\SaveForumRequest;
use App\Http\Requests\Api\VoteForumRequest;
use App\Models\Category;
use App\Models\Forum;
use App\Models\ForumComment;
use App\Models\ForumView;
use App\Models\ForumVote;
use App\Models\SaveForum;
use App\Models\User;
use Illuminate\Foundation\Providers\FormRequestServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use stdClass;

class ForumController extends Controller
{
    public function create(AddForumRequest $request)
    {
        $create = new Forum();
        if ($request->hasFile('media')) {
            $file = $request->file('media');
             $path = Storage::disk('s3')->putFile('user/' . $request->user_id . '/forum', $file);
             $path = Storage::disk('s3')->url($path);
            $create->media = $path;
        }
        $create->user_id = $request->user_id;
        $create->category_id = $request->category_id;
        $create->question = $request->question;
        $create->description = $request->description;
        $create->time = strtotime(date('Y-m-d H:i:s'));
        $create->save();

        return response()->json([
            'status' => true,
            'action' => 'Forum Added'
        ]);
    }

    public function edit(EditForumRequest $request)
    {
        $create = Forum::find($request->forum_id);
        if ($request->hasFile('media')) {
            if($create->media != '')
                Storage::disk('s3')->delete($create->media);
            $file = $request->file('media');
             $path = Storage::disk('s3')->putFile('user/' . $request->user_id . '/forum', $file);
             $path = Storage::disk('s3')->url($path);
            $create->media = $path;
        }
        $create->category_id = $request->category_id;
        $create->question = $request->question;
        $create->description = $request->description;
        $create->save();

        return response()->json([
            'status' => true,
            'action' => 'Forum Edit'
        ]);
    }

    public function removeImage($forum_id){
        $forum = Forum::find($forum_id);
        if($forum){
            $forum->media ='';
            $forum->save();
            return response()->json([
                'status' => true,
                'action' => 'Image Remove'
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'No forum found'
        ]);
    }
    public function delete($id)
    {
        $find = Forum::find($id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => 'Forum Deleted'
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Forum not found'
        ]);
    }

    public function detail($user_id, $forum_id)
    {

        $forum = Forum::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->where('id', $forum_id)->first();
        if ($forum) {
            $category = Category::find($forum->category_id);
            // $forum->user = $user;
            $forum->category = $category;

            $is_vote = ForumVote::where('forum_id', $forum_id)->first();
            if ($is_vote) {
                $forum->is_vote = $is_vote->vote;
            } else {
                $forum->is_vote = '';
            }


            $is_upvote = ForumVote::where('forum_id', $forum->id)->where('user_id', $user_id)->where('vote', 'up')->first();
            if ($is_upvote) {
                $forum->is_upvote = true;
            } else {
                $forum->is_upvote = false;
            }

            $is_downvote = ForumVote::where('forum_id', $forum->id)->where('user_id', $user_id)->where('vote', 'down')->first();
            if ($is_downvote) {
                $forum->is_downvote = true;
            } else {
                $forum->is_downvote = false;
            }


            $is_saved = SaveForum::where('forum_id', $forum_id)->where('user_id', $user_id)->first();
            if ($is_saved) {
                $forum->is_saved = true;
            } else {
                $forum->is_saved = false;
            }

            $comment_count = ForumComment::where('forum_id', $forum_id)->count();
            $upvote_count = ForumVote::where('forum_id', $forum_id)->where('vote', 'up')->count();
            $downvote_count = ForumVote::where('forum_id', $forum_id)->where('vote', 'down')->count();
            $view_count = ForumView::where('forum_id', $forum_id)->count();
            $forum->upvote_count = $upvote_count;
            $forum->downvote_count = $downvote_count;
            $forum->comment_count = $comment_count;
            $forum->view_count = $view_count;
            $comment = ForumComment::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->where('forum_id', $forum->id)->get();
            $forum->commnent = $comment;

            $view = new ForumView();
            $view->user_id = $user_id;
            $view->forum_id = $forum_id;
            $view->save();



            return response()->json([
                'status' => true,
                'action' => 'Forum Detail',
                'data' => $forum
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Forum not found',
        ]);
    }

    public function vote(VoteForumRequest $request)
    {
        $vote = ForumVote::where('forum_id', $request->forum_id)->where('user_id', $request->user_id)->first();
        if ($vote) {
            if ($vote->vote == $request->vote) {
                $vote->delete();
                return response()->json([
                    'status' => true,
                    'action' =>  'Forum Vote',
                ]);
            } else {
                $vote->vote = $request->vote;
                $vote->save();
                return response()->json([
                    'status' => true,
                    'action' =>  'Forum Vote',
                ]);
            }
        }
        $create = new ForumVote();
        $create->user_id = $request->user_id;
        $create->forum_id = $request->forum_id;
        $create->vote = $request->vote;
        $create->save();


        return response()->json([
            'status' => true,
            'action' =>  'Forum vote',
        ]);
    }

    public function comment(CommentForumRequest $request)
    {
        $user = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username','position')->where('uuid', $request->user_id)->first();
        $comment  = new ForumComment();
        $comment->forum_id = $request->forum_id;
        $comment->user_id = $request->user_id;
        $comment->comment = $request->comment;
        $comment->time = strtotime(date('Y-m-d H:i:s'));
        $comment->save();
        $new_comment = ForumComment::find($comment->id);
        $new_comment->comment_by = $user;
        return response()->json([
            'status' => true,
            'action' =>  'Comment added',
            'data' => $new_comment
        ]);
    }
    public function commentDelete($comment_id)
    {
        $find = ForumComment::find($comment_id);
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
        $categories  = Category::where('type', 'forum')->get();

        $forums = Forum::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->latest()->paginate(12);
        foreach ($forums as $forum) {
            $is_vote = ForumVote::where('forum_id', $forum->id)->where('user_id', $user_id)->first();
            if ($is_vote) {
                $forum->is_vote = $is_vote->vote;
            } else {
                $forum->is_vote = '';
            }

            $is_upvote = ForumVote::where('forum_id', $forum->id)->where('user_id', $user_id)->where('vote', 'up')->first();
            if ($is_upvote) {
                $forum->is_upvote = true;
            } else {
                $forum->is_upvote = false;
            }

            $is_downvote = ForumVote::where('forum_id', $forum->id)->where('user_id', $user_id)->where('vote', 'down')->first();
            if ($is_downvote) {
                $forum->is_downvote = true;
            } else {
                $forum->is_downvote = false;
            }
            $is_saved = SaveForum::where('forum_id', $forum->id)->where('user_id', $user_id)->first();
            if ($is_saved) {
                $forum->is_saved = true;
            } else {
                $forum->is_saved = false;
            }
            $category = Category::find($forum->category_id);
            $forum->category = $category;
            $comment_count = ForumComment::where('forum_id', $forum->id)->count();
            $upvote_count = ForumVote::where('forum_id', $forum->id)->where('vote', 'up')->count();
            $downvote_count = ForumVote::where('forum_id', $forum->id)->where('vote', 'down')->count();
            $view_count = ForumView::where('forum_id', $forum->id)->count();
            $forum->upvote_count = $upvote_count;
            $forum->downvote_count = $downvote_count;
            $forum->comment_count = $comment_count;
            $forum->view_count = $view_count;
        }




        return response()->json([
            'status' => true,
            'action' => 'Home',
            'data' => array(
                'categories' => $categories,
                'forums' => $forums
            )
        ]);
    }

    public function save(SaveForumRequest $request)
    {


        $check = SaveForum::where('forum_id', $request->forum_id)->where('user_id', $request->user_id)->first();
        if ($check) {
            $check->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Forum Unsaved',
            ]);
        }

        $create  = new SaveForum();
        $create->forum_id = $request->forum_id;
        $create->user_id = $request->user_id;
        $create->save();

        return response()->json([
            'status' => true,
            'action' =>  'Forum saved',
        ]);
    }
    public function saveList($user_id)
    {
        $save_forum_ids = SaveForum::where('user_id', $user_id)->pluck('forum_id');
        $forums  = Forum::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username'])->whereIn('id', $save_forum_ids)->latest()->paginate(12);
        foreach ($forums as $forum) {
            $is_vote = ForumVote::where('forum_id', $forum->id)->where('user_id', $user_id)->first();
            if ($is_vote) {
                $forum->is_vote = $is_vote->vote;
            } else {
                $forum->is_vote = '';
            }

            $is_upvote = ForumVote::where('forum_id', $forum->id)->where('user_id', $user_id)->where('vote', 'up')->first();
            if ($is_upvote) {
                $forum->is_upvote = true;
            } else {
                $forum->is_upvote = false;
            }

            $is_downvote = ForumVote::where('forum_id', $forum->id)->where('user_id', $user_id)->where('vote', 'down')->first();
            if ($is_downvote) {
                $forum->is_downvote = true;
            } else {
                $forum->is_downvote = false;
            }

            $forum->is_saved = true;

            $category = Category::find($forum->category_id);
            $forum->category = $category;
            $comment_count = ForumComment::where('forum_id', $forum->id)->count();
            $upvote_count = ForumVote::where('forum_id', $forum->id)->where('vote', 'up')->count();
            $downvote_count = ForumVote::where('forum_id', $forum->id)->where('vote', 'down')->count();
            $view_count = ForumView::where('forum_id', $forum->id)->count();
            $forum->upvote_count = $upvote_count;
            $forum->downvote_count = $downvote_count;
            $forum->comment_count = $comment_count;
            $forum->view_count = $view_count;
        }




        return response()->json([
            'status' => true,
            'action' => 'Save Forums List',
            'data' => $forums
        ]);
    }

    public function userForum($type, $user_id)
    {
        if ($type == 'my') {
            $forums  = Forum::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->where('user_id', $user_id)->latest()->paginate(12);
            foreach ($forums as $forum) {
                $is_vote = ForumVote::where('forum_id', $forum->id)->where('user_id', $user_id)->first();

                if ($is_vote) {
                    $forum->is_vote = $is_vote->vote;
                } else {
                    $forum->is_vote = '';
                }

                $is_upvote = ForumVote::where('forum_id', $forum->id)->where('user_id', $user_id)->where('vote', 'up')->first();
                if ($is_upvote) {
                    $forum->is_upvote = true;
                } else {
                    $forum->is_upvote = false;
                }

                $is_downvote = ForumVote::where('forum_id', $forum->id)->where('user_id', $user_id)->where('vote', 'down')->first();
                if ($is_downvote) {
                    $forum->is_downvote = true;
                } else {
                    $forum->is_downvote = false;
                }

                $forum->is_saved = true;

                $category = Category::find($forum->category_id);
                $forum->category = $category;
                $comment_count = ForumComment::where('forum_id', $forum->id)->count();
                $upvote_count = ForumVote::where('forum_id', $forum->id)->where('vote', 'up')->count();
                $downvote_count = ForumVote::where('forum_id', $forum->id)->where('vote', 'down')->count();
                $view_count = ForumView::where('forum_id', $forum->id)->count();
                $forum->upvote_count = $upvote_count;
                $forum->downvote_count = $downvote_count;
                $forum->comment_count = $comment_count;
                $forum->view_count = $view_count;
            }
        }
        if ($type == 'saved') {
            $save_forum_ids = SaveForum::where('user_id', $user_id)->pluck('forum_id');
            $forums  = Forum::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username'])->whereIn('id', $save_forum_ids)->latest()->paginate(12);
            foreach ($forums as $forum) {
                $is_vote = ForumVote::where('forum_id', $forum->id)->where('user_id', $user_id)->first();
                if ($is_vote) {
                    $forum->is_vote = $is_vote->vote;
                } else {
                    $forum->is_vote = '';
                }

                $is_upvote = ForumVote::where('forum_id', $forum->id)->where('user_id', $user_id)->where('vote', 'up')->first();
                if ($is_upvote) {
                    $forum->is_upvote = true;
                } else {
                    $forum->is_upvote = false;
                }

                $is_downvote = ForumVote::where('forum_id', $forum->id)->where('user_id', $user_id)->where('vote', 'down')->first();
                if ($is_downvote) {
                    $forum->is_downvote = true;
                } else {
                    $forum->is_downvote = false;
                }
                $forum->is_saved = true;

                $category = Category::find($forum->category_id);
                $forum->category = $category;
                $comment_count = ForumComment::where('forum_id', $forum->id)->count();
                $upvote_count = ForumVote::where('forum_id', $forum->id)->where('vote', 'up')->count();
                $downvote_count = ForumVote::where('forum_id', $forum->id)->where('vote', 'down')->count();
                $view_count = ForumView::where('forum_id', $forum->id)->count();
                $forum->upvote_count = $upvote_count;
                $forum->downvote_count = $downvote_count;
                $forum->comment_count = $comment_count;
                $forum->view_count = $view_count;
            }
        }

        return response()->json([
            'status' => true,
            'action' => 'Forums List',
            'data' => $forums
        ]);
    }

    public function categorySearch(Request $request)
    {
        $find = Category::find($request->category_id);
        if ($find) {
            $forums  = Forum::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->where('category_id', $request->category_id)->latest()->paginate(12);
            foreach ($forums as $forum) {
                $is_vote = ForumVote::where('forum_id', $forum->id)->where('user_id', $request->user_id)->first();
                if ($is_vote) {
                    $forum->is_vote = $is_vote->vote;
                } else {
                    $forum->is_vote = '';
                }
                $is_upvote = ForumVote::where('forum_id', $forum->id)->where('user_id', $request->user_id)->where('vote', 'up')->first();
                if ($is_upvote) {
                    $forum->is_upvote = true;
                } else {
                    $forum->is_upvote = false;
                }

                $is_downvote = ForumVote::where('forum_id', $forum->id)->where('user_id', $request->user_id)->where('vote', 'down')->first();
                if ($is_downvote) {
                    $forum->is_downvote = true;
                } else {
                    $forum->is_downvote = false;
                }

                $forum->is_saved = true;

                $category = Category::find($forum->category_id);
                $forum->category = $category;
                $comment_count = ForumComment::where('forum_id', $forum->id)->count();
                $upvote_count = ForumVote::where('forum_id', $forum->id)->where('vote', 'up')->count();
                $downvote_count = ForumVote::where('forum_id', $forum->id)->where('vote', 'down')->count();
                $view_count = ForumView::where('forum_id', $forum->id)->count();
                $forum->upvote_count = $upvote_count;
                $forum->downvote_count = $downvote_count;
                $forum->comment_count = $comment_count;
                $forum->view_count = $view_count;
            }




            return response()->json([
                'status' => true,
                'action' => 'Category Forums List',
                'data' => $forums
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
            $forums  = Forum::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->where("question", "LIKE", "%" . $request->keyword . "%")->latest()->paginate(12);

            foreach ($forums as $forum) {
                $is_vote = ForumVote::where('forum_id', $forum->id)->where('user_id', $request->user_id)->first();
                if ($is_vote) {
                    $forum->is_vote = $is_vote->vote;
                } else {
                    $forum->is_vote = '';
                }
                $is_upvote = ForumVote::where('forum_id', $forum->id)->where('user_id', $request->user_id)->where('vote', 'up')->first();
                if ($is_upvote) {
                    $forum->is_upvote = true;
                } else {
                    $forum->is_upvote = false;
                }

                $is_downvote = ForumVote::where('forum_id', $forum->id)->where('user_id', $request->user_id)->where('vote', 'down')->first();
                if ($is_downvote) {
                    $forum->is_downvote = true;
                } else {
                    $forum->is_downvote = false;
                }

                $forum->is_saved = true;

                $category = Category::find($forum->category_id);
                $forum->category = $category;
                $comment_count = ForumComment::where('forum_id', $forum->id)->count();
                $upvote_count = ForumVote::where('forum_id', $forum->id)->where('vote', 'up')->count();
                $downvote_count = ForumVote::where('forum_id', $forum->id)->where('vote', 'down')->count();
                $view_count = ForumView::where('forum_id', $forum->id)->count();
                $forum->upvote_count = $upvote_count;
                $forum->downvote_count = $downvote_count;
                $forum->comment_count = $comment_count;
                $forum->view_count = $view_count;
            }
            return response()->json([
                'status' => true,
                'action' =>  "Forum",
                'data' => $forums
            ]);
        }
        $forums = new stdClass();
        return response()->json([
            'status' => true,
            'action' =>  "Forum",
            'data' => $forums
        ]);
    }
}
