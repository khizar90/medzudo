<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Community\Meetup\CreateCommunityMeetupRequest;
use App\Models\Community;
use App\Models\CommunityMeetup;
use App\Models\CommunityMeetupJoinRequest;
use App\Models\CommunityMeetupSave;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommunityMeetupController extends Controller
{
    public function create(CreateCommunityMeetupRequest $request)
    {
        $user = User::find($request->user()->uuid);
        $create = new CommunityMeetup();
        $create->user_id = $user->uuid;
        $create->community_id = $request->community_id;
        $create->title = $request->title;
        $create->organizer = $request->organizer;
        $create->category = $request->category;
        $create->start_date = $request->start_date;
        $create->end_date = $request->end_date;
        $create->mode = $request->mode;
        $create->description = $request->description;
        $file = $request->file('cover');
        $path = Storage::disk('local')->put('user/' . $user->uuid . '/community/meetup', $file);
        $create->cover = '/uploads/' . $path;

        $create->start_time = $request->start_time ?: '';
        $create->start_timestamp = $request->start_timestamp ?: '';
        $create->end_timestamp = $request->end_timestamp ?: '';
        $create->location = $request->location ?: '';
        $create->lat = $request->lat ?: '';
        $create->lng = $request->lng ?: '';
        $create->address = $request->address ?: '';
        $create->website = $request->website ?: '';
        $create->register_link = $request->register_link ?: '';
        $create->audience_limit = $request->audience_limit ?: '';
        $create->password = $request->password ?: '';
        $create->price = $request->price ?: 0;
        $create->cme_point = $request->cme_point ?: '';
        if ($request->has('file')) {
            $file1 = $request->file('file');
            $path = Storage::disk('local')->put('user/' . $user->uuid . '/community/meetup', $file1);
            $create->cover = '/uploads/' . $path;
        }
        $create->save();


        return response()->json([
            'status' => true,
            'action' => 'Meetup Created',
            'data' => $create
        ]);
    }

    public function edit(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $create = CommunityMeetup::find($request->meetup_id);
        if ($create) {
            $create->title = $request->title;
            $create->organizer = $request->organizer;
            $create->category = $request->category;
            $create->start_date = $request->start_date;

            $create->end_date = $request->end_date;

            $create->mode = $request->mode;
            $create->description = $request->description;
            if ($request->has('cover')) {
                $file = $request->file('cover');
                $path = Storage::disk('local')->put('user/' . $user->uuid . '/community/meetup', $file);
                $create->cover = '/uploads/' . $path;
            }

            $create->start_time = $request->start_time ?: '';
            $create->start_timestamp = $request->start_timestamp ?: '';
            $create->end_timestamp = $request->end_timestamp ?: '';
            $create->location = $request->location ?: '';
            $create->lat = $request->lat ?: '';
            $create->lng = $request->lng ?: '';
            $create->address = $request->address ?: '';
            $create->website = $request->website ?: '';
            $create->register_link = $request->register_link ?: '';
            $create->audience_limit = $request->audience_limit ?: '';
            $create->password = $request->password ?: '';
            $create->price = $request->price ?: 0;
            $create->cme_point = $request->cme_point ?: '';

            if ($request->has('file')) {
                $file1 = $request->file('file');
                $path = Storage::disk('local')->put('user/' . $user->uuid . '/community/meetup', $file1);
                $create->cover = '/uploads/' . $path;
            }
            $create->save();


            return response()->json([
                'status' => true,
                'action' => 'Meetup Edit',
                'data' => $create
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Meetup Edit',
        ]);
    }

    public function delete($meetup_id)
    {
        $find = CommunityMeetup::find($meetup_id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => 'Meetup Deleted!',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Meetup not found',
        ]);
    }

    public function save(Request $request, $meetup_id)
    {
        $meetup = CommunityMeetup::find($meetup_id);
        $user = User::where('uuid', $request->user()->uuid)->first();
        if ($meetup) {
            $check = CommunityMeetupSave::where('meetup_id', $meetup_id)->where('user_id', $user->uuid)->first();
            if ($check) {
                $check->delete();
                return response()->json([
                    'status' => true,
                    'action' =>  'Meetup unsaved',
                ]);
            }
            $like  = new CommunityMeetupSave();
            $like->meetup_id = $meetup_id;
            $like->user_id = $user->uuid;
            $like->save();
            return response()->json([
                'status' => true,
                'action' =>  'Meetup saved',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => 'Meetup not found',
            ]);
        }
    }

    public function joinRequest(Request $request, $type, $meetup_id)
    {
        $user = User::find($request->user()->uuid);
        $find = CommunityMeetupJoinRequest::where('user_id', $user->uuid)->where('meetup_id', $meetup_id)->first();
        if ($type == 'send') {

            if ($find) {
                $find->delete();
                return response()->json([
                    'status' => true,
                    'action' => 'Request Cancel',
                ]);
            }
            $create = new CommunityMeetupJoinRequest();
            $create->user_id = $user->uuid;
            $create->meetup_id = $meetup_id;
            $create->save();
            return response()->json([
                'status' => true,
                'action' => 'Request Send',
            ]);
        }

        if ($type == 'accept') {
            if ($find) {
                $find->status = 'accept';
                $find->save();
                return response()->json([
                    'status' => true,
                    'action' => 'Request accept',
                ]);
            }
            return response()->json([
                'status' => false,
                'action' => 'Request not found',
            ]);
        }
        if ($type == 'leave') {
            if ($find) {
                $find->delete();

                return response()->json([
                    'status' => true,
                    'action' => 'Meetup Leave',
                ]);
            }
            return response()->json([
                'status' => false,
                'action' => 'Request not found',
            ]);
        }
    }

    public function detail(Request $request, $meetup_id)
    {
        $user = User::find($request->user()->uuid);
        $meetup = CommunityMeetup::find($meetup_id);
        if ($meetup) {
            $owner = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->where('uuid', $meetup->user_id)->first();
            $meetup->participant_count = CommunityMeetupJoinRequest::where('meetup_id', $meetup_id)->where('status', 'accept')->count();
            $meetup->join_request_count = CommunityMeetupJoinRequest::where('meetup_id', $meetup_id)->where('status', 'pending')->count();
            $participantIds = CommunityMeetupJoinRequest::where('meetup_id', $meetup_id)->where('status', 'accept')->pluck('user_id');
            $participants = User::whereIn('uuid', $participantIds)->limit(4)->pluck('image');
            $meetup->participants = $participants;
            $meetup->reason = '';
            $request_check = CommunityMeetupJoinRequest::where('meetup_id', $meetup_id)->where('user_id', $user->uuid)->first();
            if ($request_check) {
                if ($request_check->status == 'pending') {
                    $meetup->is_join = 'cancel';
                }
                if ($request_check->status == 'accept') {
                    $meetup->is_join = 'leave';
                }
                if ($request_check->status == 'leave') {
                    $meetup->is_join = 'join';
                }
                if ($request_check->status == 'invite') {
                    $meetup->is_join = 'accept_invite';
                }
                if ($request_check->status == 'reject') {
                    $meetup->is_join = 'rejected';
                    $meetup->reason = $request_check->reason;
                }
            } else {
                $meetup->is_join = 'join';
            }

            $is_save = CommunityMeetupSave::where('meetup_id', $meetup_id)->where('user_id', $user->uuid)->first();
            if ($is_save) {
                $meetup->is_save = true;
            } else {
                $meetup->is_save = false;
            }

            $meetup->owner = $owner;


            return response()->json([
                'status' => true,
                'action' => 'Meetup Detail',
                'data' => $meetup
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Meetup not found',
        ]);
    }
}
