<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\ImageVerify;
use App\Models\User;
use App\Models\UserDevice;
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
        // if ($request->ajax()) {
        //     $query = $request->input('query');
        //     $users = User::where('account_type', $type);
        //     if ($query) {
        //         $users = $users->where('email', 'like', '%' . $query . '%');
        //     }
        //     $users = $users->latest()->Paginate(20);

        //     return view('user.user-ajax', compact('users', 'type'));
        // }

        return view('user.index', compact('users', 'type'));
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



    public function createSendNotification()
    {
        return view('notification');
    }

    public function sendNotification(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);


        $tokens = UserDevice::where('token', '!=', '')->pluck('token')->toArray();
        // FirebaseNotification::handle($tokens, 0, $request->body, $request->title);
        // FirebaseNotification::handle($tokens, $request->body, $request->title, ['data_id' => 0, 'type' => 'admin_panel']);

        return redirect()->back()->with('success', 'Notification sent');
    }
}
