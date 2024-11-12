<?php

namespace App\Http\Controllers\Api;

use App\Actions\BlockedUser;
use App\Actions\FileUploadAction;
use App\Actions\FirebaseNotification;
use App\Actions\User\UserProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Group\CreateGroupRequest;
use App\Models\BlockList;
use App\Models\Follow;
use App\Models\Group;
use App\Models\GroupParticipant;
use App\Models\Message;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use stdClass;

class GroupController extends Controller
{
    public function create(CreateGroupRequest $request)
    {
        $loginUser = User::find($request->user()->uuid);
        $create = new Group();
        $create->user_id = $loginUser->uuid;
        $create->name = $request->name;
        $create->description = $request->description ?: '';
        $create->cover = '';
        if ($request->has('cover')) {
            $file = $request->file('cover');
            $path = FileUploadAction::handle('user/' . $loginUser->uuid . '/group', $file);
            $create->cover = $path;
        }
        $create->time = time();
        $create->save();
        $group = Group::find($create->id);
        if ($group) {
            if ($request->has('user_ids')) {
                $userIDs = explode(',', $request->user_ids);
                foreach ($userIDs as $user_id) {
                    $user = User::find($user_id);
                    if ($user) {
                        $find = GroupParticipant::where('user_id', $user->uuid)->where('group_id', $group->id)->first();
                        if (!$find) {
                            $create = new GroupParticipant();
                            $create->group_id = $group->id;
                            $create->user_id = $user->uuid;
                            $create->save();
                            $tokens = UserDevice::where('user_id', $user->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                            $last_name = $loginUser->last_name;
                            if ($last_name) {
                                FirebaseNotification::handle($tokens, $loginUser->first_name . ' ' . $loginUser->last_name . ' created a new group and added you. Start chatting with everyone!', $group->name, ['data_id' => $group->id, 'type' => 'social', 'sub_type' => 'group_added'], 1);
                            } else {
                                FirebaseNotification::handle($tokens, $loginUser->first_name . ' created a new group and added you. Start chatting with everyone!', $group->name, ['data_id' => $group->id, 'type' => 'social', 'sub_type' => 'group_added'], 1);
                            }
                        }
                    } else {
                        return response()->json([
                            'status' => false,
                            'action' => $user_id . " User Not Found!",
                        ]);
                    }
                }
            }
        }
        return response()->json([
            'status' => true,
            'action' => "Group Created",
            'data' => $group
        ]);
    }
    public function edit(Request $request, $group_id)
    {
        $user = User::find($request->user()->uuid);
        $group = Group::find($group_id);

        if ($group) {
            if ($request->has('name')) {
                $group->name = $request->name;
            }
            if ($request->has('cover')) {
                $file = $request->file('cover');
                $path = FileUploadAction::handle('user/' . $user->uuid . '/group', $file);
                $group->cover = $path;
            }
            if ($request->has('description')) {
                $group->description = $request->description;
            }
            $group->save();
            return response()->json([
                'status' => true,
                'action' => "Group Edit",
                'data' => $group
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "Group Not Found!",
        ]);
    }
    public function delete(Request $request, $group_id)
    {
        $user = User::find($request->user()->uuid);
        $group = Group::find($group_id);
        if ($group) {
            Message::where('group_id', $group_id)->delete();
            $group->delete();
            return response()->json([
                'status' => true,
                'action' => "Group Deleted!",
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "Group Not Found!",
        ]);
    }

    public function participantList(Request $request, $group_id)
    {
        $user = User::find($request->user()->uuid);
        $group = Group::find($group_id);
        if ($group) {
            $blocked = BlockList::where('user_id', $user->uuid)->pluck('block_id');
            $blocked1 = BlockList::where('block_id', $user->uuid)->pluck('user_id');
            $blocked = $blocked->merge($blocked1);
            $user_ids = User::whereNotIn('uuid', $blocked)
                ->where('uuid', '!=', $user->uuid)
                ->pluck('uuid')
                ->toArray();

            $users = UserProfileAction::userListWithPaging($user_ids, 12, $user->uuid);

            foreach ($users as $user) {
                $check = GroupParticipant::where('user_id', $user->uuid)->where('group_id', $group_id)->where('status', 0)->first();
                $user->is_added = false;
                if ($check) {
                    $user->is_added = true;
                }
            }
            return response()->json([
                'status' => true,
                'data' => $users,
                'action' => "Participant List",
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "Group Not Found!",
        ]);
    }

    public function searchParticipant(Request $request, $group_id)
    {
        $user = User::find($request->user()->uuid);
        $group = Group::find($group_id);
        if ($group) {
            $blocked = BlockList::where('user_id', $user->uuid)->pluck('block_id');
            $blocked1 = BlockList::where('block_id', $user->uuid)->pluck('user_id');
            $blocked = $blocked->merge($blocked1);
            if ($request->keyword) {
                $user_ids = User::whereNotIn('uuid', $blocked)
                    ->where('uuid', '!=', $user->uuid)
                    ->where(function ($query) use ($request) {
                        $query->where("first_name", "LIKE", "%" . $request->keyword . "%")
                            ->orWhere("last_name", "LIKE", "%" . $request->keyword . "%");
                    })
                    ->pluck('uuid')
                    ->toArray();

                $users = UserProfileAction::userListWithPaging($user_ids, 12, $user->uuid);

                foreach ($users as $user) {
                    $check = GroupParticipant::where('user_id', $user->uuid)->where('group_id', $group_id)->where('status', 0)->first();
                    $user->is_added = false;
                    if ($check) {
                        $user->is_added = true;
                    }
                }
                return response()->json([
                    'status' => true,
                    'data' => $users,
                    'action' => "Participant List",
                ]);
            }
            $users = new stdClass();
            return response()->json([
                'status' => true,
                'data' => $users,
                'action' => "Participant List",
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "Group Not Found!",
        ]);
    }


    public function addParticipant(Request $request, $group_id)
    {
        $loginUser = User::find($request->user()->uuid);
        $group = Group::find($group_id);
        $userIDs = explode(',', $request->user_ids);
        if ($group) {
            foreach ($userIDs as $user_id) {
                $user = User::find($user_id);
                if ($user) {
                    $find = GroupParticipant::where('user_id', $user->uuid)->where('group_id', $group->id)->first();
                    if (!$find) {
                        $create = new GroupParticipant();
                        $create->group_id = $group->id;
                        $create->user_id = $user->uuid;
                        $create->save();
                        $tokens = UserDevice::where('user_id', $user->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                        $last_name = $loginUser->last_name;
                        if ($last_name) {
                            FirebaseNotification::handle($tokens, $loginUser->first_name . ' ' . $loginUser->last_name . '.' . " You're now a member of " . $group->name . ". " . "Tap to see what’s happening!", $group->name, ['data_id' => $group->id, 'type' => 'social', 'sub_type' => 'group_added'], 1);
                        } else {
                            FirebaseNotification::handle($tokens, $loginUser->first_name . '.' . " You're now a member of " . $group->name . ". " . "Tap to see what’s happening!", $group->name, ['data_id' => $group->id, 'type' => 'social', 'sub_type' => 'group_added'], 1);
                        }
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'action' => $user_id . " User Not Found!",
                    ]);
                }
            }
            return response()->json([
                'status' => true,
                'action' => "Participants Added",
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "Group Not Found!",
        ]);
    }

    public function detail(Request $request, $group_id)
    {
        $user = User::find($request->user()->uuid);
        $group = Group::find($group_id);
        if ($group) {
            $participants = GroupParticipant::where('group_id', $group->id)->pluck('user_id')->toArray();
            $participants = UserProfileAction::userList($participants, $user->uuid);
            $participant_count = GroupParticipant::where('group_id', $group->id)->count();
            $group->participants = $participants;
            $group->participant_count = $participant_count;
            return response()->json([
                'status' => true,
                'data' => $group,
                'action' => "Group Detail ",
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "Group Not Found!",
        ]);
    }

    public function leave(Request $request, $group_id)
    {
        $user = User::find($request->user()->uuid);
        $find = Group::find($group_id);
        if ($find) {
            GroupParticipant::where('group_id', $group_id)->where('user_id', $user->uuid)->delete();
            return response()->json([
                'status' => true,
                'action' => "Group Leave!",
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "Group Not Found!",
        ]);
    }

    public function removeParticipant(Request $request, $group_id, $participant_id)
    {
        $find = Group::find($group_id);
        if ($find) {
            GroupParticipant::where('group_id', $group_id)->where('user_id', $participant_id)->delete();
            return response()->json([
                'status' => true,
                'action' => "Participant Remove!",
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "Group Not Found!",
        ]);
    }

    public function listUsers(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockedUser::handle($user->uuid);
        $following = Follow::where('user_id', $user->uuid)->whereNotIn('user_id', $blocked)->whereNotIn('follow_id', $blocked)->pluck('follow_id')->toArray();
        $followers = Follow::where('follow_id', $user->uuid)->whereNotIn('user_id', $blocked)->whereNotIn('follow_id', $blocked)->pluck('user_id')->toArray();
        $userIds = User::whereNotIn('uuid', $blocked)->pluck('uuid')->toArray();
        if (count($following) > 0) {
            $users = UserProfileAction::userListWithPaging($following, 12, $user->uuid);
            return response()->json([
                'status' => true,
                'action' =>  'Followings',
                'data' => $users
            ]);
        } elseif (count($followers) > 0) {
            $users = UserProfileAction::userListWithPaging($followers, 12, $user->uuid);
            return response()->json([
                'status' => true,
                'action' =>  'Followers',
                'data' => $users
            ]);
        } else {
            $users = UserProfileAction::userListWithPaging($userIds, 12, $user->uuid);
            return response()->json([
                'status' => true,
                'action' =>  'Users',
                'data' => $users
            ]);
        }
    }
}
