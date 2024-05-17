<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BlockRequest;
use App\Http\Requests\Api\FollowRequest;
use App\Http\Requests\Api\UserProfileRequest;
use App\Models\BlockList;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Follow;
use App\Models\Management;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserGallery;
use App\Models\UserInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use stdClass;

class UserController extends Controller
{
    public function profile(UserProfileRequest $request)
    {
        $obj = new stdClass();
        if ($request->user_id == $request->to_id) {
            $blocked = BlockList::where('user_id', $request->user_id)->pluck('block_id');
            $blocked1 = BlockList::where('block_id', $request->user_id)->pluck('user_id');
            $blocked = $blocked->merge($blocked1);

            $user = User::where('uuid', $request->user_id)->first();
            $user->multi_images = UserGallery::where('user_id', $request->user_id)->get();
            $user->follower = Follow::whereNotIn('user_id', $blocked)->where('follow_id', $request->user_id)->count();
            $user->following = Follow::whereNotIn('follow_id', $blocked)->where('user_id', $request->user_id)->count();


            $user->post = 0;
            $user->follow = false;
            $user->empolyes = 0;
            $user->jobs_count = 0;
            $user->group = 0;
            $user->is_block = false;
            $user->education = UserDetail::where('user_id', $user->uuid)->where('type', 'education')->get();
            $user->link = UserDetail::where('user_id', $user->uuid)->where('type', 'link')->get();
            $user->experience = UserDetail::where('user_id', $user->uuid)->where('type', 'experience')->get();
            $user->certification = UserDetail::where('user_id', $user->uuid)->where('type', 'certification')->get();
            $user->specialization = UserDetail::where('user_id', $user->uuid)->where('type', 'specialization')->get();
            $user->posts = [];
            $user->jobs = [];
            $departments = Department::where('user_id', $request->user_id)->get();
            foreach ($departments as $department) {
                $departUsers = DepartmentUser::where('depart_id', $department->id)->limit(3)->get();
                $department->depart_users = $departUsers;
            }
            $user->departments = $departments;
            $management = Management::where('user_id', $request->user_id)->latest()->first();
            if ($management) {
                $user->management = $management;
            } else {
                $user->management = $obj;
            }
            $contact = Contact::where('user_id', $request->user_id)->latest()->first();
            if ($contact) {
                $user->contact = $contact;
            } else {
                $user->contact = $obj;
            }

            $interest = UserInterest::where('user_id', $request->user_id)->pluck('category_id');
            $categories  = Category::select('id', 'name', 'image')->whereIn('id', $interest)->get();
            $user->interest = $categories;
        } else {

            $user = User::where('uuid', $request->to_id)->first();
            $user->multi_images = UserGallery::where('user_id', $request->to_id)->get();

            $blocked = BlockList::where('user_id', $request->to_id)->pluck('block_id');
            $blocked1 = BlockList::where('block_id', $request->to_id)->pluck('user_id');
            $blocked = $blocked->merge($blocked1);
            $follow = Follow::where('user_id', $request->user_id)->where('follow_id', $request->to_id)->first();


            $user->follower = Follow::whereNotIn('user_id', $blocked)->where('follow_id', $request->to_id)->count();
            $user->following = Follow::whereNotIn('follow_id', $blocked)->where('user_id', $request->to_id)->count();

            $block = Blocklist::where('user_id', $request->user_id)->orWhere('block_id', $request->to_id)->first();
            if ($block) {
                $user->is_block = true;
            } else {
                $user->is_block = false;
            }
            if ($follow) {
                $user->follow = true;
            } else {
                $user->follow = false;
            }
            $user->post = 0;
            $user->empolyes = 0;
            $user->jobs_count = 0;
            $user->group = 0;
            $user->education = UserDetail::where('user_id', $user->uuid)->where('type', 'education')->get();
            $user->link = UserDetail::where('user_id', $user->uuid)->where('type', 'link')->get();
            $user->experience = UserDetail::where('user_id', $user->uuid)->where('type', 'experience')->get();
            $user->certification = UserDetail::where('user_id', $user->uuid)->where('type', 'certification')->get();
            $user->specialization = UserDetail::where('user_id', $user->uuid)->where('type', 'specialization')->get();
            $user->posts = [];
            $user->jobs = [];
            $departments = Department::where('user_id', $request->to_id)->get();
            foreach ($departments as $department) {
                $departUsers = DepartmentUser::where('depart_id', $department->id)->limit(3)->get();
                $department->depart_users = $departUsers;
            }
            $user->departments = $departments;
            $management = Management::where('user_id', $request->to_id)->latest()->first();
            if ($management) {
                $user->management = $management;
            } else {
                $user->management = $obj;
            }
            $contact = Contact::where('user_id', $request->to_id)->latest()->first();
            if ($contact) {
                $user->contact = $contact;
            } else {
                $user->contact = $obj;
            }
            $interest = UserInterest::where('user_id', $request->to_id)->pluck('category_id');
            $categories  = Category::select('id', 'name', 'image')->whereIn('id', $interest)->get();
            $user->interest = $categories;
        }
        return response()->json([
            'status' => true,
            'action' =>  'User profle',
            'data' => $user
        ]);
    }

    public function follow(FollowRequest $request)
    {
        $check = Follow::where('user_id', $request->from_id)->where('follow_id', $request->to_id)->first();
        if ($check) {
            $check->delete();

            // Notification::where('person_id', $request->from_id)->where('type', 'follow')->delete();

            return response()->json([
                'status' => true,
                'action' =>  'User UnFollow',
            ]);
        }
        $follow = new Follow();
        $follow->user_id = $request->from_id;
        $follow->follow_id = $request->to_id;
        $follow->save();

        $from = User::find($request->from_id);
        $to = User::find($request->to_id);
        $post = 0;

        // NewNotification::handle($to, $from->uuid, $post, 'started following you.', 'follow');
        // $user = User::find($request->from_id);
        // $tokens = UserDevice::where('user_id', $request->to_id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
        // FirebaseNotification::handle($tokens, $user->username . ' started following you.', 'MemeBox', ['data_id' => $request->from_id, 'type' => 'follow']);

        return response()->json([
            'status' => true,
            'action' =>  'User Follow',
        ]);
    }


    public function following($id)
    {
        $user = User::find($id);
        if ($user) {

            $blocked = BlockList::where('user_id', $id)->pluck('block_id');
            $blocked1 = BlockList::where('block_id', $id)->pluck('user_id');
            $blocked = $blocked->merge($blocked1);

            $followingIds = Follow::where('user_id', $id)->pluck('follow_id');

            $followingIds = Follow::where('user_id', $id)->whereNotIn('follow_id', $blocked)->pluck('follow_id')->toArray();

            $followings = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->whereIn('uuid', $followingIds)->paginate(12);

            foreach ($followings as $item) {
                $item->is_follow = true;
            }
            return response()->json([
                'status' => true,
                'action' =>  'Following',
                'data' => $followings
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'User not found',
        ]);
    }

    public function followers($id)
    {
        $user = User::find($id);
        if ($user) {

            $blocked = BlockList::where('user_id', $id)->pluck('block_id');
            $blocked1 = BlockList::where('block_id', $id)->pluck('user_id');
            $blocked = $blocked->merge($blocked1);

            $followerIds = Follow::where('follow_id', $id)->pluck('user_id');



            $followers = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->whereIn('uuid', $followerIds)
                ->whereNotIn('uuid', $blocked)
                ->paginate(12);


            foreach ($followers as $item) {
                $follow  = Follow::where('user_id', $id)->where('follow_id', $item->uuid)->first();
                if ($follow) {
                    $item->is_follow = true;
                } else {
                    $item->is_follow = false;
                }
            }


            return response()->json([
                'status' => true,
                'action' =>  'Followers',
                'data' => $followers
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'User not found',
        ]);
    }

    // public function followRequest($user_id,$follow_id){
    //     $check = Follow::where('user_id', $user_id)->first();
    //     if ($check) {
    //         $check->delete();


    //         return response()->json([
    //             'status' => true,
    //             'action' =>  'User UnFollow',
    //         ]);
    //     }
    // }

    public function block(BlockRequest $request)
    {
        $check = Blocklist::where('block_id', $request->block_id)->where('user_id',  $request->user_id)->first();
        if ($check) {
            $check->delete();
            return response()->json([
                'status' => true,
                'action' => 'User unblocked'
            ]);
        } else {
            $block = new Blocklist;
            $block->block_id = $request->block_id;
            $block->user_id = $request->user_id;
            $block->save();
            return response()->json([
                'status' => true,
                'action' => 'User blocked'
            ]);
        }
    }
}
