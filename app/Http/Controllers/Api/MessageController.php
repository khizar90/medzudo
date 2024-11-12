<?php

namespace App\Http\Controllers\Api;

use App\Actions\BlockedUser;
use App\Actions\FileUploadAction;
use App\Actions\FirebaseNotification;
use App\Actions\User\UserProfileAction;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupParticipant;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Pusher\Pusher;
use stdClass;

class MessageController extends Controller
{


    public function send(Request $request)
    {
        $user = User::find($request->user()->uuid);
        if ($request->group_id) {
            $validator = Validator::make($request->all(), [
                'group_id' => "required|exists:groups,id",
                'type' => 'required',
                'message' => 'required_without:attachment'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'to' => "required|exists:users,uuid",
                'type' => 'required',
                'message' => 'required_without:attachment',
            ]);
        }

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        if ($request->group_id) {
            $chat_message = new Message();
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = FileUploadAction::handle('user/' . $user->uuid . '/chat', $file);
                $chat_message->attachment = $path;
            }
            $chat_message->from = $user->uuid;
            $chat_message->to = '';
            $chat_message->group_id = $request->group_id;
            $chat_message->type = $request->type;
            $chat_message->message = $request->message ?: '';
            $chat_message->time = time();
            $chat_message->from_to = '';
            $chat_message->save();
            $chat_message = Message::find($chat_message->id);
            $user = UserProfileAction::userCommon($chat_message->from, $user->uuid);
            $chat_message->user = $user;

            $group = Group::find($chat_message->group_id);
            $ownerTokens = [];
            if ($user->uuid != $group->user_id) {
                $ownerTokens = UserDevice::where('user_id', $group->user_id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            }
            $participantIds = GroupParticipant::where('group_id', $group->id)->pluck('user_id');
            $tokens = UserDevice::whereIn('user_id', $participantIds)->where('user_id', '!=', $user->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            $tokens = array_merge($ownerTokens, $tokens);
            $last_name = $user->last_name;
            $message = $chat_message->message;
            if (!$message) {
                if ($chat_message->type == 'image') {
                    $message = 'Tap to view the image';
                } elseif ($chat_message->type == 'video') {
                    $message = 'Tap to view the video';
                } else {
                    $message = 'Tap to view the attachment';
                }
            }
            if ($last_name) {
                FirebaseNotification::handle($tokens, $user->first_name . ' ' . $user->last_name . ': ' . $message, $group->name, ['data_id' => $group->id, 'type' => 'social', 'sub_type' => 'group_message'], 1);
            } else {
                FirebaseNotification::handle($tokens, $user->first_name . ': ' .  $message, $group->name, ['data_id' => $group->id, 'type' => 'social', 'sub_type' => 'group_message'], 1);
            }
            $pusher = new Pusher('3276c63f44d6ef0bd450', '9872cc35110fea8f2966', '1887732', [
                'cluster' => 'us3',
                'useTLS' => true,
            ]);
            $pusher->trigger($request->group_id, 'new-message', $chat_message);
        } else {
            $chat_message = new Message();

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = FileUploadAction::handle('user/' . $user->uuid . '/chat', $file);
                $chat_message->attachment = $path;
            }

            $chat_message->from = $user->uuid;
            $chat_message->to = $request->to;
            $chat_message->type = $request->type;
            $chat_message->message = $request->message ?: '';
            $chat_message->time = time();
            $find = Message::where('from_to', $user->uuid . '-' . $request->to)->orWhere('from_to', $request->to . '-' . $user->uuid)->first();
            $channel = '';
            if ($find) {
                $channel = $find->from_to;
                $chat_message->from_to = $find->from_to;
            } else {
                $channel = '';
                $chat_message->from_to = $user->uuid . '-' . $request->to;
            }
            $chat_message->save();
            $chat_message = Message::find($chat_message->id);
            $user = UserProfileAction::userCommon($chat_message->from, $user->uuid);
            $chat_message->user = $user;
            $tokens = UserDevice::where('user_id', $chat_message->to)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            $last_name = $user->last_name;
            $message = $chat_message->message;
            if (!$message) {
                if ($chat_message->type == 'image') {
                    $message = 'Tap to view the image';
                } elseif ($chat_message->type == 'video') {
                    $message = 'Tap to view the video';
                } else {
                    $message = 'Tap to view the attachment';
                }
            }
            if ($last_name) {
                FirebaseNotification::handle($tokens, $message, $user->first_name . ' ' . $user->last_name, ['data_id' => $user->uuid, 'type' => 'social', 'sub_type' => 'simple_message'], 1);
            } else {
                FirebaseNotification::handle($tokens, $message, $user->first_name, ['data_id' => $user->uuid, 'type' => 'social', 'sub_type' => 'simple_message'], 1);
            }
            $pusher = new Pusher('app_key', 'app_secret', 'app_id', [
                'cluster' => 'us3',
                'useTLS' => true,
            ]);
            $pusher->trigger($chat_message->from_to, 'new-message', $chat_message);
        }
        return response()->json([
            'status' => true,
            'action' => "Message send",
            'data' => $chat_message
        ]);
    }

    public function conversation(Request $request, $to_id)
    {
        $user = User::find($request->user()->uuid);
        $messages = Message::where('ticket_id', 0)->where('group_id', 0)->where('from_to', $user->uuid . '-' . $to_id)->orWhere('from_to', $to_id . '-' . $user->uuid)->latest()->Paginate(50);

        foreach ($messages as $message) {
            $user = UserProfileAction::userCommon($message->from, $user->uuid);
            $message->user = $user;
        }
        $otherUser = UserProfileAction::userCommon($to_id, $user->uuid);
        return response()->json([
            'status' => true,
            'action' =>  'Conversation',
            'user' => $otherUser,
            'data' => $messages,
        ]);
    }
    public function messageRead(Request $request, $to_id)
    {
        $user = User::find($request->user()->uuid);
        Message::where('from', $to_id)->where('to', $user->uuid)->where('is_read', 0)->update(['is_read' => 1]);
        return response(['status' => true, 'action' => 'Messages read']);
    }
    public function inbox(Request $request)
    {

        $user = User::find($request->user()->uuid);
        $get = Message::select('from_to')->where('ticket_id', 0)->where('group_id', 0)->where('from', $user->uuid)->orWhere('to', $user->uuid)->where('ticket_id', 0)->where('group_id', 0)->groupBy('from_to')->pluck('from_to');
        $arr = [];
        foreach ($get as $item) {
            $message = Message::where('from_to', $item)->latest()->first();
            if ($message) {
                if ($message->from == $user->uuid) {
                    $user1 = UserProfileAction::userCommon($message->to, $user->uuid);
                }
                if ($message->to == $user->uuid) {
                    $user1 = UserProfileAction::userCommon($message->from, $user->uuid);
                }
            }
            $unread_count = Message::where('from_to', $item)->where('to', $user->uuid)->where('is_read', 0)->count();
            $obj = new stdClass();
            $obj->message = $message->message;
            $obj->time = $message->time;
            $obj->type = $message->type;
            $obj->is_read = $message->is_read;
            $obj->from = $message->from;
            $obj->from_to = $message->from_to;
            $obj->user = $user1;
            $obj->unread_count = $unread_count;
            $arr[] = $obj;
        }

        $sorted = collect($arr)->sortByDesc('time');

        // ---COMMENTED FOR FUTURE USE IF NEEDED FOR PAGINATION---
        // $sorted = $sorted->forPage($request->page, 20);

        $arr1 = [];
        $count = 0;
        foreach ($sorted as $item) {
            $arr1[] = $item;
        }
        return response()->json([
            'status' => true,
            'action' =>  'Inbox',
            'data' => $arr1
        ]);
    }

    public function groupConversation(Request $request, $group_id)
    {
        $user = User::find($request->user()->uuid);
        $group = Group::find($group_id);
        if ($group) {
            $owner = UserProfileAction::userCommon($group->user_id, $user->uuid);
            $participant_count = GroupParticipant::where('group_id', $group->id)->count();
            $group->participant_count = $participant_count;
            $messages = Message::where('group_id', $group_id)->latest()->paginate(50);
            foreach ($messages as $message) {
                $user = UserProfileAction::userCommon($message->from, $user->uuid);
                $message->user = $user;
            }
            return response()->json([
                'status' => true,
                'action' => "Conversation",
                'owner' => $owner,
                'group' => $group,
                'data' => $messages,
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "Group Not Found!",
        ]);
    }
    public function groupInbox(Request $request)
    {
        $user = User::find($request->user()->uuid);

        $groups = Group::where('user_id', $user->uuid)->get();
        $groupIds = GroupParticipant::where('user_id', $user->uuid)->pluck('group_id');
        $groups1 = Group::whereIn('id', $groupIds)->get();
        $combinedGroups = $groups->merge($groups1);

        foreach ($combinedGroups as $item) {
            $message = Message::where('group_id', $item->id)->latest()->first();
            $obj = new stdClass();

            if ($message) {
                $total_message = Message::where('from', '!=', $user->uuid)->where('group_id', $item->id)->count();
                $messageIDs = Message::where('from', '!=', $user->uuid)->where('group_id', $item->id)->pluck('id');
                $total_read_message = MessageRead::whereIn('message_id', $messageIDs)->where('user_id', $user->uuid)->count();

                $total_unread_message = $total_message - $total_read_message;
                $user1 = UserProfileAction::userCommon($message->from, $user->uuid);
                $obj->message = $message->message;
                $obj->type = $message->type;
                $obj->unread_count = $total_unread_message;
                $obj->time = $message->time;
                $obj->user = $user1;
                $item->message  = $obj;
                $item->time = $message->time;
            } else {
                $item->message  = $obj;
                $item->time = $item->time;
            }
        }
        $sorted = collect($combinedGroups)->sortByDesc('time');

        // ---COMMENTED FOR FUTURE USE IF NEEDED FOR PAGINATION---
        // $sorted = $sorted->forPage($request->page, 20);

        $arr1 = [];
        $count = 0;
        foreach ($sorted as $item) {
            $arr1[] = $item;
        }
        return response()->json([
            'status' => true,
            'action' => 'Inbox',
            'data' => $arr1,
        ]);
    }

    public function groupMessageRead(Request $request, $group_id)
    {
        $user = User::find($request->user()->uuid);
        $readIds = MessageRead::where('user_id', $user->uuid)->where('group_id', $group_id)->pluck('message_id');
        $messageIds = Message::where('group_id', $group_id)->where('from', '!=', $user->uuid)->whereNotIn('id', $readIds)->pluck('id');
        foreach ($messageIds as $id) {
            $find = MessageRead::where('group_id', $group_id)->where('message_id', $id)->where('user_id', $user->uuid)->first();
            if (!$find) {
                $create = new MessageRead();
                $create->message_id = $id;
                $create->group_id = $group_id;
                $create->user_id = $user->uuid;
                $create->time = time();
                $create->save();
            }
        }
        return response(['status' => true, 'action' => 'Messages read']);
    }

    public function unifiedInbox(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockedUser::handle($user->uuid);

        // --- Fetch Direct Messages ---
        $directMessages = Message::select('from_to')
            ->where('ticket_id', 0)
            ->where('group_id', 0)
            ->where(function ($query) use ($user) {
                $query->where('from', $user->uuid)
                    ->orWhere('to', $user->uuid);
            })
            ->whereNotIn('from', $blocked)
            ->whereNotIn('to', $blocked)
            ->groupBy('from_to')
            ->get();

        $directArr = [];
        foreach ($directMessages as $item) {
            $message = Message::where('from_to', $item->from_to)->latest()->first();
            if ($message) {
                $user1 = $message->from == $user->uuid
                    ? UserProfileAction::userCommon($message->to, $user->uuid)
                    : UserProfileAction::userCommon($message->from, $user->uuid);
            }

            $unreadCount = Message::where('from_to', $item->from_to)
                ->where('to', $user->uuid)
                ->where('is_read', 0)
                ->count();

            $directObj = new stdClass();
            $directObj->from = $message->from;
            $directObj->to = $message->to;
            $directObj->message = $message->message;
            $directObj->attachment_type = $message->type;
            $directObj->time = $message->time;
            $directObj->type = 'direct';
            $directObj->is_read = $message->is_read;
            $directObj->from_to = $message->from_to;
            $directObj->user = $user1;
            $directObj->unread_count = $unreadCount;
            $directObj->group_id = 0;
            $directObj->group_name = '';
            $directObj->group_image = '';
            $directArr[] = $directObj;
        }

        // --- Fetch Group Messages ---
        $groups = Group::where('user_id', $user->uuid)->get();
        $groupIds = GroupParticipant::where('user_id', $user->uuid)->pluck('group_id');
        $groups1 = Group::whereIn('id', $groupIds)->get();
        $combinedGroups = $groups->merge($groups1);

        $groupArr = [];
        foreach ($combinedGroups as $item) {
            $message = Message::where('group_id', $item->id)->latest()->first();
            $groupObj = new stdClass();

            if ($message) {
                $totalMessages = Message::where('from', '!=', $user->uuid)->where('group_id', $item->id)->count();
                $messageIDs = Message::where('from', '!=', $user->uuid)->where('group_id', $item->id)->pluck('id');
                $totalReadMessages = MessageRead::whereIn('message_id', $messageIDs)->where('user_id', $user->uuid)->distinct('message_id')->count();

                $totalUnreadMessages = $totalMessages - $totalReadMessages;
                $user1 = UserProfileAction::userCommon($message->from, $user->uuid);

                $groupObj->from = $message->from;
                $groupObj->to = '';
                $groupObj->message = $message->message;
                $groupObj->attachment_type = $message->type;
                $groupObj->type = 'group';
                $groupObj->time = $message->time;
                $groupObj->user = $user1;
                $groupObj->unread_count = $totalUnreadMessages;
                $groupObj->group_id = $item->id;
                $groupObj->group_name = $item->name;
                $groupObj->group_image = $item->cover;
                $groupArr[] = $groupObj;
            } else {
                $user1 = UserProfileAction::userCommon($item->user_id, $user->uuid);
                $groupObj->from = '';
                $groupObj->to = '';
                $groupObj->message = '';
                $groupObj->attachment_type = 'created';
                $groupObj->type = 'group';
                $groupObj->time = $item->time;
                $groupObj->user = $user1;
                $groupObj->unread_count = 0;
                $groupObj->group_id = $item->id;
                $groupObj->group_name = $item->name;
                $groupObj->group_image = $item->cover;
                $groupArr[] = $groupObj;
            }
        }

        // --- Merge and Sort Both Arrays ---
        $mergedArr = collect($directArr)->merge($groupArr)->sortByDesc('time')->values()->all();

        $groups = Group::where('user_id', $user->uuid)->pluck('id');
        $groupIds = GroupParticipant::where('user_id', $user->uuid)->pluck('group_id');
        $combinedGroups = $groups->merge($groupIds);
        $unreadGroupCount = Message::where('from', '!=', $user->uuid)
            ->whereIn('group_id', $combinedGroups)
            ->whereDoesntHave('messageReads', function ($query) use ($user) {
                $query->where('user_id', $user->uuid);
            })
            ->distinct('group_id')
            ->count('group_id');

        $message_count = Message::where('group_id', 0)->where('to', $user->uuid)->where('is_read', 0)->distinct('from')->count();

        return response()->json([
            'status' => true,
            'action' => 'Unified Inbox',
            'total_unread' => $message_count + $unreadGroupCount,
            'data' => $mergedArr,
        ]);
    }
}
