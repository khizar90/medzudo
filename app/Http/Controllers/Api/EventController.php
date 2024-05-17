<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddEventQuestionRequest;
use App\Http\Requests\Api\CreateEventRequest;
use App\Http\Requests\Api\EditEventQuestionRequest;
use App\Http\Requests\Api\EditEventRequest;
use App\Http\Requests\Api\SaveEventRequest;
use App\Models\Category;
use App\Models\Event;
use App\Models\EventQuestion;
use App\Models\JoinEvent;
use App\Models\SaveEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class EventController extends Controller
{
    public function create(CreateEventRequest $request)
    {
        $create = new Event();

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            // $path = Storage::disk('s3')->putFile('user/' . $request->user_id . '/profile', $file);
            // $path = Storage::disk('s3')->url($path);
            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/user/' . $request->user_id . '/event/', $filename))
                $path =  '/uploads/user/' . $request->user_id . '/event/' . $filename;
            $create->media = $path;
        }

        $create->user_id = $request->user_id;
        $create->category_id = $request->category_id;
        $create->media = $path;
        $create->title = $request->title;
        $create->organizer_name = $request->organizer_name;
        $create->start_date = $request->start_date;
        $create->start_time = $request->start_time ?: '';
        $create->start_timestamp = $request->start_timestamp ?: '';
        $create->end_date = $request->end_date;
        $create->end_time = $request->end_time ?: '';
        $create->end_timestamp = $request->end_timestamp ?: '';
        $create->location = $request->location ?: '';
        $create->lat = $request->lat ?: '';
        $create->lng = $request->lng ?: '';
        $create->address = $request->address ?: '';
        $create->website = $request->website ?: '';
        $create->registration_link = $request->registration_link ?: '';
        $create->availability = $request->availability;
        $create->audience_limit = $request->audience_limit ?: '';
        $create->password = $request->password ?: '';
        $create->description = $request->description;
        $create->save();
        $newEvent = Event::find($create->id);

        return response()->json([
            'status' => true,
            'action' => 'Event Added',
            'event_id' => $newEvent->id
        ]);
    }

    public function edit(EditEventRequest $request)
    {
        $create = Event::find($request->event_id);


        if ($request->hasFile('media')) {
            $file = $request->file('media');
            // $path = Storage::disk('s3')->putFile('user/' . $request->user_id . '/profile', $file);
            // $path = Storage::disk('s3')->url($path);
            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/user/' . $request->user_id . '/event/', $filename))
                $path =  '/uploads/user/' . $request->user_id . '/event/' . $filename;
            $create->media = $path;
            $create->media = $path;
        }


        $create->category_id = $request->category_id;
        $create->title = $request->title;
        $create->organizer_name = $request->organizer_name;
        $create->start_date = $request->start_date;
        $create->end_date = $request->end_date;

        if ($request->start_time == '@empty_data_') {
            $create->start_time = '';
        } else {
            $create->start_time = $request->start_time;
        }

        if ($request->start_timestamp == '@empty_data_') {
            $create->start_timestamp =  '';
        } else {
            $create->start_timestamp = $request->start_timestamp;
        }

        if ($request->end_time == '@empty_data_') {
            $create->end_time =  '';
        } else {
            $create->end_time = $request->end_time;
        }

        if ($request->end_timestamp == '@empty_data_') {
            $create->end_timestamp = '';
        } else {
            $create->end_timestamp = $request->end_timestamp;
        }

        if ($request->location == '@empty_data_') {
            $create->location = '';
            $create->lat = $request->lat;
            $create->lng = $request->lng;
        } else {
            $create->location = $request->location;
            $create->lat = $request->lat;
            $create->lng = $request->lng;
        }

        if ($request->address == '@empty_data_') {
            $create->address = '';
        } else {
            $create->address = $request->address;
        }


        if ($request->website == '@empty_data_') {
            $create->website = '';
        } else {
            $create->website = $request->website;
        }
        if ($request->registration_link == '@empty_data_') {
            $create->registration_link = '';
        } else {
            $create->registration_link = $request->registration_link;
        }
        if ($request->audience_limit == '@empty_data_') {
            $create->audience_limit = '';
        } else {
            $create->audience_limit = $request->audience_limit;
        }

        if ($request->password == '@empty_data_') {
            $create->password = '';
        } else {
            $create->password = $request->password;
        }

        $create->availability = $request->availability;
        $create->description = $request->description;
        $create->save();
        $newEvent = Event::find($create->id);

        return response()->json([
            'status' => true,
            'action' => 'Event Edit',
            'data' => $newEvent
        ]);
    }

    public function addQuestion(AddEventQuestionRequest $request)
    {
        $questions = $request->json('questions');
        foreach ($questions as $questionData) {
            $question = new EventQuestion();
            $question->event_id = $request->json('event_id');
            $question->question = $questionData['question'];


            $question->options =  $questionData['options'] ?: "";

            $question->save();
        }

        return response()->json([
            'status' => true,
            'action' => "Questions Added",
        ]);
    }
    public function editQuestion(EditEventQuestionRequest $request)
    {
        $question = EventQuestion::find($request->question_id);
        if ($question) {
            $question->question = $request->question;
            if ($request->empty_array == 1) {
                $question->options =  '';
            }
            if ($request->empty_array == 0) {
                $question->options =  $request->options;
            }
            $question->save();
            $question->options = explode(',', $question->options);

            return response()->json([
                'status' => true,
                'action' => "Questions Edit",
                'data' => $question
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "Questions not found",
        ]);
    }
    public function deleteQuestion($que_id)
    {
        $find = EventQuestion::find($que_id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => "Question Deleted",
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "Question  not found",
        ]);
    }

    public function delete($event_id)
    {
        $find = Event::find($event_id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => 'Event Deleted'
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Event not found'
        ]);
    }



    public function detail($user_id, $event_id)
    {
        $event = Event::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->where('id', $event_id)->first();
        if ($event) {
            $category = Category::find($event->category_id);
            $event->category = $category;

            $is_saved = SaveEvent::where('event_id', $event_id)->where('user_id', $user_id)->first();
            if ($is_saved) {
                $event->is_saved = true;
            } else {
                $event->is_saved = false;
            }

            $is_join = JoinEvent::where('user_id',$user_id)->where('event_id',$event_id)->first();
            if($is_join){
                $event->is_join = true;
            }
            else{
                $event->is_join = false;
            }


            $questions = EventQuestion::where('event_id', $event_id)->get();
            foreach ($questions as $question) {
                $options = explode(',', $question->options);
                if (!empty($options) && !($options == [""])) {
                    $question->options = $options;
                } else {
                    $question->options = [];
                }
            }
            $event->questions = $questions;
            return response()->json([
                'status' => true,
                'action' => "Event detail",
                'data' => $event
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "Event not Found",
        ]);
    }

    public function save(SaveEventRequest $request)
    {
        $check = SaveEvent::where('event_id', $request->event_id)->where('user_id', $request->user_id)->first();
        if ($check) {
            $check->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Event Unsaved',
            ]);
        }

        $create  = new SaveEvent();
        $create->event_id = $request->event_id;
        $create->user_id = $request->user_id;
        $create->save();

        return response()->json([
            'status' => true,
            'action' =>  'Event saved',
        ]);
    }

    public function saveList($type, $user_id)
    {
        if ($type == 'save') {
            $save_forum_ids = SaveEvent::where('user_id', $user_id)->pluck('event_id');
            $events  = Event::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->whereIn('id', $save_forum_ids)->latest()->paginate(12);
            foreach ($events as $event) {
                $event->is_saved = true;
                $category = Category::find($event->category_id);
                $event->category = $category;
            }
        }
        if($type == 'join'){
            $join_event_ids = JoinEvent::where('user_id', $user_id)->pluck('event_id');
            $events  = Event::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->whereIn('id', $join_event_ids)->latest()->paginate(12);
            foreach ($events as $event) {
                $event->is_saved = true;
                $category = Category::find($event->category_id);
                $event->category = $category;
            }
        }


        return response()->json([
            'status' => true,
            'action' => 'Events List',
            'data' => $events
        ]);
    }
    public function userEvents($user_id, $status)
    {
        $user = User::find($user_id);
        if ($user) {
            $events  = Event::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->where('user_id', $user_id)->where('status', $status)->latest()->paginate(12);
            foreach ($events as $event) {
                // $is_saved = SaveEvent::where('event_id', $event->id)->where('user_id', $user_id)->first();
                // if ($is_saved) {
                //     $event->is_saved = true;
                // } else {
                //     $event->is_saved = false;
                // }
                $category = Category::find($event->category_id);
                $event->category = $category;
            }
            return response()->json([
                'status' => true,
                'action' =>  'My Events',
                'data' => $events
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'User not Found',
        ]);
    }

    public function search(Request $request)
    {

        if ($request->keyword != null || $request->keyword != '') {
            $events  = Event::with(['user:uuid,first_name,last_name,image,email,verify,account_type,username,position'])->where("title", "LIKE", "%" . $request->keyword . "%")->latest()->paginate(12);

            foreach ($events as $event) {
                $category = Category::find($event->category_id);
                $event->category = $category;
            }
            return response()->json([
                'status' => true,
                'action' =>  "Events",
                'data' => $events
            ]);
        }
        $events = new stdClass();
        return response()->json([
            'status' => true,
            'action' =>  "Events",
            'data' => $events
        ]);
    }

    public function home(Request $request)
    {
        $find = User::find($request->user_id);
        $current_timestamp = Carbon::now()->timestamp;
        Event::where('end_timestamp', '<', $current_timestamp)->update(['status' => 1]);
        if ($find) {
            $dates = Event::selectRaw('DATE(start_date) as date, COUNT(*) as count')
                ->whereDate('start_date', '>=', $request->date)
                ->where('status', 0)
                ->groupBy('date')
                ->get();

            if ($request->has('lat') && $request->has('lng')) {
                $userLat = $request->lat;
                $userLng = $request->lng;


                $events = DB::table('events')
                    ->select(
                        '*',
                        DB::raw("(6371 * acos(cos(radians($userLat)) * cos(radians(lat)) * cos(radians(lng) - radians($userLng)) + sin(radians($userLat)) * sin(radians(lat)))) AS distance")
                    )
                    ->whereDate('start_date', '>=', $request->date)
                    ->where('status', 0)
                    ->having('distance', '<=', $request->radius)
                    ->orderBy('distance')
                    ->latest()->get();

                foreach ($events as $event) {
                    $category = Category::find($event->category_id);
                    $event->category = $category;
                }
                return response()->json([
                    'status' => true,
                    'action' =>  "Event home",
                    'data' => array(
                        'dates' => $dates,
                        'events' => $events,
                    )
                ]);
            }
            $events = Event::where('status', 0)->whereDate('start_date', '>=', $request->date)->latest()->get();
            foreach ($events as $event) {
                $category = Category::find($event->category_id);
                $event->category = $category;
            }
            return response()->json([
                'status' => true,
                'action' =>  "Event home",
                'data' => array(
                    'dates' => $dates,
                    'events' => $events,
                )
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  "User not found",
        ]);
    }

    public function joinEvent($event_id, $user_id)
    {
        $find = User::find($user_id);
        if ($find) {
            $event = Event::find($event_id);
            if ($event) {

                $find = JoinEvent::where('user_id', $user_id)->where('event_id', $event_id)->first();
                if ($find) {
                    $find->delete();
                    return response()->json([
                        'status' => true,
                        'action' =>  "Event Un join",
                    ]);
                }

                $create = new JoinEvent();
                $create->user_id = $user_id;
                $create->event_id = $event_id;
                $create->save();
                return response()->json([
                    'status' => true,
                    'action' =>  "Event join",
                ]);
            }
            return response()->json([
                'status' => false,
                'action' =>  "Event not found",
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  "User not found",
        ]);
    }

    public function members($event_id){
        $event = Event::find($event_id);
        if($event){
            $user_ids = JoinEvent::where('event_id',$event_id)->pluck('user_id');
            $users = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username','position')->whereIn('uuid',$user_ids)->paginate(12);
            return response()->json([
                'status' => true,
                'action' =>  "Join Members",
                'data' => $users
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  "Event not found",
        ]);
    }
}
