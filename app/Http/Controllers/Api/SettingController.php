<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Faq;
use App\Models\User;
use App\Models\UserInterest;
use Illuminate\Http\Request;
use stdClass;

class SettingController extends Controller
{
    public function faqs()
    {
        $list = Faq::all();
        return response()->json([
            'status' => true,
            'action' =>  'Faqs',
            'data' => $list
        ]);
    }

    public function splash($user_id = null)
    {
        $obj = new stdClass();
        $obj1 = new stdClass();

        $position = Category::select('id', 'name', 'image')->where('type', 'position')->get();
        // $report_user = Category::select('id', 'name', 'image')->where('type', 'user')->get();
        // $report_post = Category::select('id', 'name', 'image')->where('type', 'post')->get();
        // // $interest = Category::select('id', 'name','image')->where('type', 'interest')->get();
        // $forum = Category::select('id', 'name', 'image')->where('type', 'forum')->get();
        // $news = Category::select('id', 'name', 'image')->where('type', 'news')->get();
        // $events = Category::select('id', 'name', 'image')->where('type', 'event')->get();
        $obj->position_category = $position;
        // $obj->report_user_category = $report_user;
        // $obj->report_post_category = $report_post;
        // // $obj->interest = $interest;
        // $obj->forum_category = $forum;
        // $obj->news_category = $news;
        // $obj->events_category = $events;

        if ($user_id != null) {
            $user = User::select('uuid','first_name','last_name','type','username','email','image','account_type','position','request_verify','verify')->where('uuid',$user_id)->first();
            if ($user) {
                $obj->user = $user;
                $interest = UserInterest::where('user_id', $user->uuid)->first();
                if ($interest) {
                    $obj->user->interest_add = true;
                } else {
                    $obj->user->interest_add = false;
                }
                $is_delete = false;
            } else {
                $is_delete = true;
                $obj->user = $obj1;
            }
        } else {
            $obj->user = $obj1;
            $is_delete = false;
        }

        return response()->json([
            'status' => true,
            'action' => "Splash",
            'is_delete' => $is_delete,
            'data' => $obj,
        ]);
    }

    public function categories($type)
    {
        $interest = Category::select('id', 'name', 'image')->where('type', $type)->get();
        return response()->json([
            'status' => true,
            'action' => "Categories",
            'data' => $interest,
        ]);
    }


}
