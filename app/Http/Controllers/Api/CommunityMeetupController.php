<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Community\Meetup\CreateCommunityMeetupRequest;
use App\Models\Community;
use App\Models\CommunityMeetup;
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
}
