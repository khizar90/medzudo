<?php

namespace App\Http\Controllers\Api;

use App\Actions\FileUploadAction;
use App\Actions\User\UserProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Requests\Api\DeleteAccountRequest;
use App\Http\Requests\Api\EditProfileRequest;
use App\Http\Requests\Api\GetVerifyRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\LogoutRequest;
use App\Http\Requests\Api\NewPasswordRequest;
use App\Http\Requests\Api\OtpVerifyRequest;
use App\Http\Requests\Api\RecoverVerifyRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\UserInterestRequest;
use App\Http\Requests\Api\VerifyRequest;
use App\Jobs\AssociationJob;
use App\Jobs\CompanyJob;
use App\Jobs\DoctorOfficeJob;
use App\Jobs\ElderlyCareJob;
use App\Jobs\HospitalJob;
use App\Jobs\IndividualJob;
use App\Jobs\RehabilitationJob;
use App\Jobs\SocietyJob;
use App\Jobs\StartUpJob;
use App\Mail\ForgotOtp;
use App\Mail\OtpSend;
use App\Mail\Register\Association;
use App\Mail\Register\Company;
use App\Mail\Register\DoctorOffice;
use App\Mail\Register\ElderlyCare;
use App\Mail\Register\Hospital;
use App\Mail\Register\Individual;
use App\Mail\Register\Rehabilitation;
use App\Mail\Register\Society;
use App\Mail\Register\StartUp;
use App\Models\BlockList;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Follow;
use App\Models\ImageVerify;
use App\Models\Management;
use App\Models\OtpVerify;
use App\Models\Report;
use App\Models\User;
use App\Models\UserBusinessDetail;
use App\Models\UserCategory;
use App\Models\UserDetail;
use App\Models\UserDevice;
use App\Models\UserGallery;
use App\Models\UserInterest;
use App\Models\UserLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use stdClass;

class AuthController extends Controller
{
    public function verify(VerifyRequest $request)
    {

        $otp = random_int(100000, 999999);

        $mail_details = [
            'body' => $otp,
        ];
        Mail::to($request->email)->send(new OtpSend($mail_details));


        $user = new OtpVerify();
        $user->email = $request->email;
        $user->otp = $otp;
        $user->save();
        return response()->json([
            'status' => true,
            'action' => 'User verify and Otp send',
        ]);
    }
    public function otpVerify(OtpVerifyRequest $request)
    {
        $user = OtpVerify::where('email', $request->email)->latest()->first();
        if ($user) {
            if ($request->otp == $user->otp) {
                $user = OtpVerify::where('email', $request->email)->delete();
                return response()->json([
                    'status' => true,
                    'action' => 'OTP verify',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'action' => 'OTP is invalid, Please enter a valid OTP',
                ]);
            }
        }
    }

    public function register(RegisterRequest $request)
    {
        $last_name = $request->last_name ?: '';
        $create = new User();
        $create->first_name = $request->first_name;
        $create->last_name = $request->last_name ?: '';
        $create->type = $request->type ?: '';
        $username = strtolower($request->first_name . $last_name . time());
        $create->username = $username;
        $create->email = $request->email;
        $create->sector = $request->sector ?: '';
        $create->account_type = $request->account_type;
        $create->title = $request->title ?: '';
        $create->password = Hash::make($request->password);

        $check = User::where('username', $username)->first();
        if ($check) {
            return response()->json([
                'status' => false,
                'action' => 'An error occurred. Please fill the form again.',
            ]);
        }
        $create->save();

        $business_detail =  new UserBusinessDetail();
        $business_detail->user_id = $create->uuid;
        $business_detail->save();
        $userDevice = new UserDevice();
        $userDevice->user_id = $create->uuid;
        $userDevice->device_name = $request->device_name ?? 'No name';
        $userDevice->device_id = $request->device_id ?? 'No ID';
        $userDevice->timezone = $request->timezone ?? 'No Time';
        $userDevice->token = $request->fcm_token ?? 'No token';
        $userDevice->save();


        $newuser  = User::where('uuid', $create->uuid)->first();
        $mailDetails = [
            'first_name' => $newuser->first_name . ' ' . $newuser->last_name
        ];
        if ($request->account_type == 'facility' && $request->type == 'Hospital') {
            // HospitalJob::dispatch($mailDetails, $newuser->email)->delay(now()->addMinutes(2));
            Mail::to($newuser->email)->send(new Hospital($mailDetails));
        }
        if ($request->account_type == 'facility' && $request->type == "Doctor's Office") {
            // DoctorOfficeJob::dispatch($mailDetails, $newuser->email)->delay(now()->addMinutes(2));
            Mail::to($newuser->email)->send(new DoctorOffice($mailDetails));
        }
        if ($request->account_type == 'facility' && $request->type == 'Elderlycare') {
            // ElderlyCareJob::dispatch($mailDetails, $newuser->email)->delay(now()->addMinutes(2));
            Mail::to($newuser->email)->send(new ElderlyCare($mailDetails));
        }
        if ($request->account_type == 'facility' && $request->type == 'Rehabilitation') {
            // RehabilitationJob::dispatch($mailDetails, $newuser->email)->delay(now()->addMinutes(2));
            Mail::to($newuser->email)->send(new Rehabilitation($mailDetails));
        }
        if ($request->account_type == 'organization' && $request->type == 'Start-Up') {
            // StartUpJob::dispatch($mailDetails, $newuser->email)->delay(now()->addMinutes(2));
            Mail::to($newuser->email)->send(new StartUp($mailDetails));
        }
        if ($request->account_type == 'organization' && $request->type == 'Company') {
            // CompanyJob::dispatch($mailDetails, $newuser->email)->delay(now()->addMinutes(2));
            Mail::to($newuser->email)->send(new Company($mailDetails));
        }
        if ($request->account_type == 'organization' && $request->type == 'Association') {
            // AssociationJob::dispatch($mailDetails, $newuser->email)->delay(now()->addMinutes(2));
            Mail::to($newuser->email)->send(new Association($mailDetails));
        }
        if ($request->account_type == 'organization' && $request->type == 'Society') {
            // SocietyJob::dispatch($mailDetails, $newuser->email)->delay(now()->addMinutes(2));
            Mail::to($newuser->email)->send(new Society($mailDetails));
        }
        if ($request->account_type == 'individual') {
            // IndividualJob::dispatch($mailDetails, $newuser->email)->delay(now()->addMinutes(2));
            Mail::to($newuser->email)->send(new Individual($mailDetails));
        }

        $userDetail = UserBusinessDetail::where('user_id', $newuser->uuid)->latest()->first();
        if (!$userDetail) {
            $userDetail = new stdClass();
        }
        $newuser->business_detail = $userDetail;

        $interest = UserInterest::where('user_id', $newuser->uuid)->first();
        if ($interest) {
            $newuser->interest_add = true;
        } else {
            $newuser->interest_add = false;
        }
        $newuser->token = $newuser->createToken('Register')->plainTextToken;
        return response()->json([
            'status' => true,
            'action' => 'User register successfully',
            'data' => $newuser
        ]);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        $userDetail = UserBusinessDetail::where('user_id', $user->uuid)->latest()->first();
        if (!$userDetail) {
            $userDetail = new stdClass();
        }
        $user->business_detail = $userDetail;
        $interest = UserInterest::where('user_id', $user->uuid)->first();
        if ($interest) {
            $user->interest_add = true;
        } else {
            $user->interest_add = false;
        }
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $userDevice = new UserDevice();
                $userDevice->user_id = $user->uuid;
                $userDevice->device_name = $request->device_name ?? 'No name';
                $userDevice->device_id = $request->device_id ?? 'No ID';
                $userDevice->timezone = $request->timezone ?? 'No Time';
                $userDevice->token = $request->fcm_token ?? 'No token';
                $userDevice->save();
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

    public function recover(RecoverVerifyRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $otp = random_int(100000, 999999);

            $userOtp = new OtpVerify();
            $userOtp->email = $request->email;
            $userOtp->otp = $otp;
            $userOtp->save();

            $mailDetails = [
                'body' => $otp,
                'first_name' => $user->first_name . ' ' . $user->last_name
            ];

            Mail::to($request->email)->send(new ForgotOtp($mailDetails));

            return response()->json([
                'status' => true,
                'action' => 'Otp send successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => 'Account not found'
            ]);
        }
    }
    public function newPassword(NewPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'action' => "New password is same as Old password",
                ]);
            } else {
                $user->update([
                    'password' => Hash::make($request->password)
                ]);
                return response()->json([
                    'status' => true,
                    'action' => "New password set",
                ]);
            }
            // $user->update([
            //     'password' => Hash::make($request->password)
            // ]);
            return response()->json([
                'status' => true,
                'action' => "New Password set"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => 'This Email Address is not registered'
            ]);
        }
    }

    public function addInterest(UserInterestRequest $request)
    {
        $user = User::find($request->user()->uuid);
        if ($user) {
            UserInterest::where('user_id', $user->uuid)->delete();
            $categoriesIds = explode(',', $request->categories);

            foreach ($categoriesIds as $category) {
                $find = Category::find($category);
                if ($find) {
                    $create = new UserInterest();
                    $create->user_id = $user->uuid;
                    $create->category_id = $category;
                    $create->save();
                } else {
                    return response()->json([
                        'status' => false,
                        'action' => $category . " Category id is inValid"
                    ]);
                }
            }
            return response()->json([
                'status' => true,
                'action' => 'User Interest Added'
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'User not Found'
        ]);
    }

    public function updateFcm(Request $request){
        $userDevice = UserDevice::where('device_id',$request->device_id)->latest()->first();
        if($userDevice){
            $userDevice->token = $request->fcm_token;
            $userDevice->save();
            return response()->json([
                'status' => true,
                'action' => 'Fcm Update!'
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Device not Found'
        ]);
    }
}
