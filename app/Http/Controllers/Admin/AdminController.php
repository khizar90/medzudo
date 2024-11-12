<?php

namespace App\Http\Controllers\Admin;

use App\Actions\User\UserProfileAction;
use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Community;
use App\Models\Contact;
use App\Models\Department;
use App\Models\DepartmentMember;
use App\Models\Faq;
use App\Models\Follow;
use App\Models\ImageVerify;
use App\Models\Management;
use App\Models\Message;
use App\Models\Report;
use App\Models\User;
use App\Models\UserBusinessDetail;
use App\Models\UserDetail;
use App\Models\UserDevice;
use App\Models\UserMedia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index()
    {

        // $verify = User::where('verify', 1)->count();

        $total = User::count();
        $todayActive = 0;

        $todayNew = User::whereDate('created_at', date('Y-m-d'))->count();
        $mainUsers = User::pluck('uuid');
        $loggedIn = UserDevice::whereIn('user_id', $mainUsers)->where('token', '!=', '')->distinct('user_id')->count();

        $iosTraffic = UserDevice::whereIn('user_id', $mainUsers)->where('device_name', 'ios')->count();
        $androidTraffic = UserDevice::whereIn('user_id', $mainUsers)->where('device_name', 'android')->count();


        return view('index', compact('todayActive', 'total', 'todayNew', 'mainUsers', 'loggedIn', 'iosTraffic', 'androidTraffic'));
    }


    public function users(Request $request, $type)
    {
        $users = User::where('account_type', $type)->latest()->paginate(20);
        if ($request->ajax()) {

            $query = $request->input('query');
            $users = User::where('account_type', $type);

            if ($query) {
                $users = $users->where('email', 'like', '%' . $query . '%');
            }
            $users = $users->latest()->paginate(20);
            if ($type == 'individual') {
                return view('user.individual.user-ajax', compact('users', 'type'));
            }
            if ($type == 'facility') {
                return view('user.facility.user-ajax', compact('users', 'type'));
            }
            if ($type == 'organization') {
                return view('user.organization.user-ajax', compact('users', 'type'));
            }
        }

        if ($type == 'individual') {
            return view('user.individual.index', compact('users', 'type'));
        }
        if ($type == 'facility') {
            return view('user.facility.index', compact('users', 'type'));
        }
        if ($type == 'organization') {
            return view('user.organization.index', compact('users', 'type'));
        }
    }

    public function userProfile($type,$user_id){
        $user = User::find($user_id);
        $user->business_detail = UserBusinessDetail::where('user_id', $user->uuid)->first();
        $user->follower = Follow::where('follow_id', $user->uuid)->count();
        $user->following = Follow::where('user_id', $user->uuid)->count();
        $user->communities = Community::where('user_id', $user->uuid)->count();
        $user->all_experience = UserDetail::where('user_id', $user->uuid)->where('type', 'experience')->get();
        $user->all_education = UserDetail::where('user_id', $user->uuid)->where('type', 'education')->get();
        $user->all_certification = UserDetail::where('user_id', $user->uuid)->where('type', 'certification')->get();
        $user->all_publication = UserDetail::where('user_id', $user->uuid)->where('type', 'publication')->get();
        $user->all_finance_round = UserDetail::where('user_id', $user->uuid)->where('type', 'finance-round')->get();
        $user->all_educational_program = UserDetail::where('user_id', $user->uuid)->where('type', 'educational-program')->get();
        $departments = Department::where('user_id', $user->uuid)->get();
        foreach ($departments as $department) {
            $department_users = DepartmentMember::where('user_id', $department->user_id)->where('department_id', $department->id)->get();
            $department->department_users = $department_users;
            foreach ($department_users as $department_user) {
                $find_department_user = UserProfileAction::userCommon($department_user->member_id, $user->uuid);
                $department_user->user = $find_department_user;
            }
        }
        $user->departments = $departments;
        $user->contact = Contact::where('user_id', $user->uuid)->get();
        $management = Management::where('user_id', $user->uuid)->get();
        foreach ($management as $management_user) {
            $find_management_user = UserProfileAction::userCommon($management_user->management_id, $user->uuid);
            $management_user->user = $find_management_user;
        }
        $user->management = $management;
        $user->media = UserMedia::where('user_id', $user->uuid)->get();
        $teams = DepartmentMember::where('user_id', $user->uuid)->where('department_id', null)->get();
        foreach ($teams as $teams_user) {
            $find_teams_user = UserProfileAction::userCommon($teams_user->member_id, $user->uuid);
            $teams_user->user = $find_teams_user;
        }
        $user->teams = $teams;

        if($user->account_type == 'individual'){
            return view('user.individual.show',compact('user','type'));
        }
        if($user->account_type == 'facility'){
            return view('user.facility.show',compact('user','type'));
        }
        if($user->account_type == 'organization'){
            return view('user.organization.show',compact('user','type'));
        }

    }
    public function exportCSV(Request $request)
    {

        $users = User::select('username', 'email', 'phone_number')->get();

        $columns = ['username', 'email', 'phone_number'];
        $handle = fopen(storage_path('users.csv'), 'w');

        fputcsv($handle, $columns);

        foreach ($users->chunk(2000) as $chunk) {
            foreach ($chunk as $user) {
                fputcsv($handle, $user->toArray());
            }
        }

        fclose($handle);

        return response()->download(storage_path('users.csv'))->deleteFileAfterSend(true);
    }

    public function deleteUser($user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            Message::where('from', $user->uuid)->delete();
            Message::where('to', $user->uuid)->delete();
            Follow::where('user_id', $user->uuid)->orWhere('follow_id', $user->uuid)->delete();
            Report::where('reported_id', $user->uuid)->where('type', 'user')->delete();
            Report::where('user_id', $user->uuid)->where('type', 'user')->delete();
            $user->delete();
        }
        return redirect()->back();
    }

    public function verifyUsers(Request $request)
    {


        $users = User::where('verify', 2)->where('account_type', 'individual')->latest()->paginate(20);

        if ($request->ajax()) {
            $query = $request->input('query');

            $users  = User::query();
            if ($query) {

                $users = $users->where('email', 'like', '%' . $query . '%')->orWhere('username', 'like', '%' . $query . '%');
            }
            $users = $users->latest()->Paginate(20);
            foreach ($users as $user) {
                $image = ImageVerify::where('user_id', $user->uuid)->latest()->first();
                $user->userimage = $image;
            }
            return view('user.verify_ajax', compact('users'));
        }

        foreach ($users as $user) {
            $image = ImageVerify::where('user_id', $user->uuid)->latest()->first();
            $user->userimage = $image;
        }


        return view('user.verify_request', compact('users'));
    }

    public function getVerify($type, $user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            if ($type == 'individual') {
                $user->verify = 1;
                $user->save();
                return redirect()->back();
            }
            if ($type == 'organization') {
                $user->request_verify = 1;
                $user->save();
                return redirect()->back();
            }
        }

        return redirect()->back();
    }

    public function organizationVerify()
    {
        $users = User::where('request_verify', 0)->where('account_type', 'organization')->latest()->paginate(20);
        return view('user.organization_verify_request', compact('users'));
    }

    public function faqs()
    {
        $faqs = Faq::all();

        return view('faq', compact('faqs'));
    }

    public function deleteFaq($id)
    {
        $faq  = Faq::find($id);
        $faq->delete();
        return redirect()->back();
    }

    public function addFaq(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'answer' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $faq = new Faq();
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();
        return redirect()->back();
    }

    public function editFaq(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'answer' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $faq = Faq::find($id);
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();
        return redirect()->back();
    }


    public function version($status)
    {
        if ($status == 'android') {
            return view('app-version.android', compact('status'));
        }
        if ($status == 'iOS') {
            return view('app-version.iOS', compact('status'));
        }
    }

    public function versionSave($type)
    {
        if ($type == 'android') {
            foreach ($_POST as $key => $value) {
                if ($key == "_token")
                    continue;

                $data = array();
                $data['value'] = $value;
                $data['updated_at'] = Carbon::now();

                if (AppSetting::where('name', $key)->exists()) {
                    AppSetting::where('name', $key)->update($data);
                } else {
                    $data['name'] = $key;
                    $data['created_at'] = Carbon::now();
                    AppSetting::insert($data);
                }
            }
            return redirect()->back()->with('message', 'Android version updated!');
        }
        if ($type == 'iOS') {
            foreach ($_POST as $key => $value) {
                if ($key == "_token")
                    continue;

                $data = array();
                $data['value'] = $value;
                $data['updated_at'] = Carbon::now();

                if (AppSetting::where('name', $key)->exists()) {
                    AppSetting::where('name', $key)->update($data);
                } else {
                    $data['name'] = $key;
                    $data['created_at'] = Carbon::now();
                    AppSetting::insert($data);
                }
            }
            return redirect()->back()->with('message', 'IOS version updated!');
        }
    }

    public function emergency()
    {
        $is_firebase_query = AppSetting::where('name', 'is_firebase_query')->first();
        if ($is_firebase_query) {
            $is_firebase_query = $is_firebase_query->value;
        } else {
            $is_firebase_query = 0;
        }

        $stop_login = AppSetting::where('name', 'stop-login')->first();

        if ($stop_login) {
            $stop_login = $stop_login->value;
        } else {
            $stop_login = 0;
        }
        $stop_signup = AppSetting::where('name', 'stop-signup')->first();

        if ($stop_signup) {
            $stop_signup = $stop_signup->value;
        } else {
            $stop_signup = 0;
        }

        $stop_subscription = AppSetting::where('name', 'stop-subscription')->first();

        if ($stop_subscription) {
            $stop_subscription = $stop_subscription->value;
        } else {
            $stop_subscription = 0;
        }

        $stop_post = AppSetting::where('name', 'stop-post')->first();

        if ($stop_post) {
            $stop_post = $stop_post->value;
        } else {
            $stop_post = 0;
        }

        $stop_app = AppSetting::where('name', 'stop-app')->first();

        if ($stop_app) {
            $stop_app = $stop_app->value;
        } else {
            $stop_app = 0;
        }

        $stripe = AppSetting::where('name', 'stripe')->first();

        if ($stripe) {
            $stripe = $stripe->value;
        } else {
            $stripe = 0;
        }
        $beta_code = AppSetting::where('name', 'beta-code')->first();

        if ($beta_code) {
            $beta_code = $beta_code->value;
        } else {
            $beta_code = 0;
        }

        return view('check.index', compact('is_firebase_query', 'stop_login', 'stop_signup', 'stop_subscription', 'stop_post', 'stop_app', 'stripe','beta_code'));
    }

    public function emergencyCheck($name, $value)
    {

        $find = AppSetting::where('name', $name)->first();
        if ($find) {
            $find->value = $value;
            $find->save();
            return redirect()->back();
        }
        $create = new AppSetting();
        $create->name = $name;
        $create->value = $value;
        $create->save();
        return redirect()->back();
    }

    public function emergencyMessage(Request $request)
    {
        $check = AppSetting::where('name', $request->name)->first();
        if ($check) {
            $check->value = $request->message;
            $check->save();
        } else {
            $create = new AppSetting();
            $create->name = $request->name;
            $create->value = $request->message;
            $create->save();
        }
        return redirect()->back();
    }
}
