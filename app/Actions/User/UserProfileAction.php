<?php

namespace App\Actions\User;

use App\Models\BlockList;
use App\Models\Community;
use App\Models\Follow;
use App\Models\User;

class UserProfileAction
{
    public static function userCommon($user_id, $login_id)
    {
        $user = User::select('uuid', 'first_name', 'last_name', 'type',  'username', 'email', 'sector', 'image', 'cover', 'account_type', 'position', 'verify', 'location', 'lat', 'lng', 'gender', 'professionName', 'specializationName','created_at')
            ->firstWhere('uuid', $user_id);

        $user->follower = Follow::where('follow_id', $user->uuid)->count();
        $user->following = Follow::where('user_id', $user->uuid)->count();
        $user->communities = Community::where('user_id', $user->uuid)->count();
        $user->follow = false;
        $user->block = false;
        $follow = Follow::where('user_id', $login_id)->where('follow_id', $user->uuid)->first();
        if ($follow) {
            $user->follow = true;
        }
        $block = BlockList::where('user_id', $login_id)->where('block_id', $user->uuid)->first();
        if ($block) {
            $user->block = true;
        }
        if (!$user) {
            return null;
        }

        return $user;
    }
    public static function userListWithPaging(array $user_ids, $perPage = 12, $login_id)
    {
        // Select attributes for all users in the provided array of user_ids
        $users = User::select('uuid', 'first_name', 'last_name', 'type',  'username', 'email', 'sector', 'image', 'cover', 'account_type', 'position', 'verify', 'location', 'lat', 'lng', 'gender', 'professionName', 'specializationName','created_at')
            ->whereIn('uuid', $user_ids)
            ->paginate($perPage);  // Paginate the users
        foreach ($users as $user) {
            $user->follower = Follow::where('follow_id', $user->uuid)->count();
            $user->following = Follow::where('user_id', $user->uuid)->count();
            $user->communities = Community::where('user_id', $user->uuid)->count();
            $user->follow = false;
            $user->block = false;
            $follow = Follow::where('user_id', $login_id)->where('follow_id', $user->uuid)->first();
            if ($follow) {
                $user->follow = true;
            }
            $block = BlockList::where('user_id', $login_id)->where('block_id', $user->uuid)->first();
            if ($block) {
                $user->block = true;
            }
        }
        return $users;  // Return the paginated collection
    }
    public static function userList(array $user_ids, $login_id)
    {
        // Select attributes for all users in the provided array of user_ids
        $users = User::select('uuid', 'first_name', 'last_name', 'type',  'username', 'email', 'sector', 'image', 'cover', 'account_type', 'position', 'verify', 'location', 'lat', 'lng', 'gender', 'professionName', 'specializationName','created_at')
            ->whereIn('uuid', $user_ids)
            ->get();  // Paginate the users

        foreach ($users as $user) {
            $user->follower = Follow::where('follow_id', $user->uuid)->count();
            $user->following = Follow::where('user_id', $user->uuid)->count();
            $user->communities = Community::where('user_id', $user->uuid)->count();
            $user->follow = false;
            $user->block = false;
            $follow = Follow::where('user_id', $login_id)->where('follow_id', $user->uuid)->first();
            if ($follow) {
                $user->follow = true;
            }
            $block = BlockList::where('user_id', $login_id)->where('block_id', $user->uuid)->first();
            if ($block) {
                $user->block = true;
            }
        }
        return $users;  // Return the paginated collection
    }
}
