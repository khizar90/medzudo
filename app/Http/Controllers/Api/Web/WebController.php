<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Models\Category;
use App\Models\Community;
use App\Models\CommunityCourse;
use App\Models\CommunityCoursePurchase;
use App\Models\CommunityCourseSection;
use App\Models\CommunityCourseSectionVideo;
use App\Models\CommunityJoinRequest;
use App\Models\CommunityPicture;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WebController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::select('uuid', 'first_name', 'last_name', 'username', 'email', 'image', 'password')->where('email', $request->email)->first();
        // $interest = UserInterest::where('user_id', $user->uuid)->first();
        // if ($interest) {
        //     $user->interest_add = true;
        // } else {
        //     $user->interest_add = false;
        // }
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $userdevice = new UserDevice();
                $userdevice->user_id = $user->uuid;
                $userdevice->device_name = $request->device_name ?? 'No name';
                $userdevice->device_id = $request->device_id ?? 'No ID';
                $userdevice->timezone = $request->timezone ?? 'No Time';
                $userdevice->token = $request->fcm_token ?? 'No tocken';
                $userdevice->save();
                $user->token = $user->createToken('Login')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'action' => "Login successfully",
                    'data' => $user,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'action' => 'Password is invalid, please enter a valid Password',
                ]);
            }
        }
        return response()->json([
            'status' => false,
            'action' => "Account not Found",
        ]);
    }

    public function myCommunity(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $myCommunities = Community::select('id', 'cover', 'logo', 'name')->where('user_id', $user->uuid)->latest()->limit(12)->get();
        return response()->json([
            'status' => true,
            'action' => "My Communities",
            'data' => $myCommunities,
        ]);
    }

    public function listCourses(Request $request,$community_id)
    {
        $user = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->where('uuid', $request->user()->uuid)->first();

        $courses = CommunityCourse::where('community_id', $community_id)->get();
        foreach ($courses as $course) {
            $course->section_count = CommunityCourseSection::where('course_id', $course->id)->count();
            $duration  = CommunityCourseSectionVideo::where('course_id', $course->id)->sum('duration');
            $course->duration_count = $duration;
            $is_purchase = CommunityCoursePurchase::where('user_id', $user->uuid)->where('course_id', $course->id)->first();
            if ($is_purchase) {
                $course->is_purchase = true;
            } else {
                $course->is_purchase = false;
            }
        }
        return response()->json([
            'status' => true,
            'user' => $user,
            'action' =>  'Course list',
            'data' => $courses
        ]);
    }
}
