<?php

namespace App\Http\Controllers\Api;

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
use App\Mail\ForgotOtp;
use App\Mail\OtpSend;
use App\Models\BlockList;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Follow;
use App\Models\ImageVerify;
use App\Models\Management;
use App\Models\OtpVerify;
use App\Models\User;
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
        $create->position = $request->position ?: '';
        $create->account_type = $request->account_type;
        $create->password = Hash::make($request->password);


        if ($request->hasFile('request_image')) {
            $file = $request->file('request_image');
            // $path = Storage::disk('s3')->putFile('user/' . $request->user_id . '/profile', $file);
            // $path = Storage::disk('s3')->url($path);
            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/user/' . $request->user_id . '/request_image/', $filename))
                $path = '/uploads/user/' . $request->user_id . '/request_image/' . $filename;
            $create->request_image = $path;
        }


        $check = User::where('username', $username)->first();
        if ($check) {
            return response()->json([
                'status' => false,
                'action' => 'An error occurred. Please fill the form again.',
            ]);
        }
        $create->save();

        $userdevice = new UserDevice();
        $userdevice->user_id = $create->uuid;
        $userdevice->device_name = $request->device_name ?? 'No name';
        $userdevice->device_id = $request->device_id ?? 'No ID';
        $userdevice->timezone = $request->timezone ?? 'No Time';
        $userdevice->token = $request->fcm_token ?? 'No tocken';
        $userdevice->save();


        $newuser  = User::select('uuid', 'first_name', 'last_name', 'type', 'username', 'email', 'image', 'account_type', 'position', 'request_verify', 'verify')->where('uuid', $create->uuid)->first();

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
        $user = User::select('uuid', 'first_name', 'last_name', 'type', 'username', 'email', 'image', 'account_type', 'position', 'request_verify', 'verify', 'password')->where('email', $request->email)->first();
        $interest = UserInterest::where('user_id', $user->uuid)->first();
        if ($interest) {
            $user->interest_add = true;
        } else {
            $user->interest_add = false;
        }
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
                'first_name' => $user->username
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
        $user = User::find($request->user_id);
        if ($user) {
            UserInterest::where('user_id', $request->user_id)->delete();
            $categoriesIds = explode(',', $request->categories);

            foreach ($categoriesIds as $category) {
                $find = Category::find($category);
                if ($find) {
                    $create = new UserInterest();
                    $create->user_id = $request->user_id;
                    $create->category_id = $category;
                    $create->save();
                } else {
                    return response()->json([
                        'status' => false,
                        'action' => $category . " Catgeory id is inValid"
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

    public function userInterest($user_id)
    {
        $interest = UserInterest::where('user_id', $user_id)->pluck('category_id');
        $categories  = Category::select('id', 'name', 'image')->whereIn('id', $interest)->get();
        return response()->json([
            'status' => true,
            'action' =>  'User Interest',
            'data' => $categories
        ]);
    }

    public function logout(LogoutRequest $request)
    {
        $user = User::find($request->user_id);
        UserDevice::where('user_id', $request->user_id)->where('device_id', $request->device_id)->delete();
        // $user->tokens()->delete();
        return response()->json([
            'status' => true,
            'action' => 'User logged out'
        ]);
    }

    public function deleteAccount(DeleteAccountRequest $request)
    {
        $user = User::find($request->user_id);
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                Follow::where('user_id', $request->user_id)->orWhere('follow_id', $request->user_id)->delete();
                // $user->tokens()->delete();

                $user->delete();

                return response()->json([
                    'status' => true,
                    'action' => "Account deleted",
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'action' => 'Please enter correct password',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'action' => "User not found"
            ]);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = User::find($request->user_id);
        if ($user) {
            if (Hash::check($request->old_password, $user->password)) {
                if (Hash::check($request->new_password, $user->password)) {

                    return response()->json([
                        'status' => false,
                        'action' => "New password is same as old password",
                    ]);
                } else {
                    $user->update([
                        'password' => Hash::make($request->new_password)
                    ]);
                    return response()->json([
                        'status' => true,
                        'action' => "Password  change",
                    ]);
                }
            }
            return response()->json([
                'status' => false,
                'action' => "Old password is wrong",
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => 'User not found'
            ]);
        }
    }

    public function editImage(Request $request)
    {

        $user = User::find($request->user_id);
        if ($user) {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                // $path = Storage::disk('s3')->putFile('user/' . $request->user_id . '/profile', $file);
                // $path = Storage::disk('s3')->url($path);
                $extension = $file->getClientOriginalExtension();
                $mime = explode('/', $file->getClientMimeType());
                $filename = time() . '-' . uniqid() . '.' . $extension;
                if ($file->move('uploads/user/' . $request->user_id . '/profile/', $filename))
                    $path = '/uploads/user/' . $request->user_id . '/profile/' . $filename;
                $user->image = $path;
            }
            $user->save();
            $interest = UserInterest::where('user_id', $user->uuid)->first();
            if ($interest) {
                $user->interest_add = true;
            } else {
                $user->interest_add = false;
            }
            $token = $request->bearerToken();
            $user->token = $token;

            return response()->json([
                'status' => true,
                'action' => "Image edit",
                'data' => $user
            ]);
        }

        return response()->json([
            'status' => false,
            'action' => "User not found"
        ]);
    }

    public function removeImage(Request $request, $user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            $user->image = '';

            $user->save();
            $interest = UserInterest::where('user_id', $user->uuid)->first();
            if ($interest) {
                $user->interest_add = true;
            } else {
                $user->interest_add = false;
            }
            $token = $request->bearerToken();
            $user->token = $token;
            return response()->json([
                'status' => true,
                'action' => "Image remove",
                'data' => $user
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => "User not found"
            ]);
        }
    }
    public function blockList($id)
    {
        $block_ids = BlockList::where('user_id', $id)->pluck('block_id');
        $blockUsers = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->whereIn('uuid', $block_ids)->paginate(12);
        foreach ($blockUsers as $block) {
            $block->block = true;
        }
        return response()->json([
            'status' => true,
            'action' =>  'Block list',
            'data' => $blockUsers
        ]);
    }

    public function getVerify(GetVerifyRequest $request)
    {

        $cehck = ImageVerify::where('user_id', $request->user_id)->first();
        if ($cehck) {
            return response()->json([
                'status' => true,
                'action' => "Request Already submited"
            ]);
        } else {
            $user = User::find($request->user_id);
            if ($user) {
                $userImage =  new ImageVerify();
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $mime = explode('/', $file->getClientMimeType());
                $filename = time() . '-' . uniqid() . '.' . $extension;
                if ($file->move('uploads/user/' . $request->user_id . '/verify/', $filename))
                    $image = '/uploads/user/' . $request->user_id . '/verify/' . $filename;

                $userImage->user_id = $request->user_id;
                $userImage->image = $image;
                $user->verify = 2;
                $userImage->save();
                $user->save();

                return response()->json([
                    'status' => true,
                    'action' => "Request submited"
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'action' => "User not found"
                ]);
            }
        }
    }

    public function editProfile(Request $request)
    {

        $user = User::find($request->user()->uuid);
        if ($user) {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $path = Storage::disk('local')->put('user/' . $user->uuid . '/profile', $file);
                $user->image = '/uploads/' . $path;
            }

            if ($request->has('username')) {
                if (User::where('username', $request->username)->where('uuid', '!=', $user->uuid)->exists()) {
                    return response()->json([
                        'status' => false,
                        'action' => 'Username already taken'
                    ]);
                } else {
                    $user->username = $request->username;
                }
            }

            if ($request->has('email')) {
                if (User::where('email', $request->email)->where('uuid', '!=', $user->uuid)->exists()) {
                    return response()->json([
                        'status' => false,
                        'action' => 'Email Address is already registered'
                    ]);
                } else {
                    $user->email = $request->email;
                }
            }

            if ($request->has('first_name')) {
                $user->first_name = $request->first_name;
            }

            if ($request->has('last_name')) {
                $user->last_name = $request->last_name;
            }




            if ($request->has('location')) {
                if ($request->location == '@empty_data_') {
                    $user->location = '';
                    $user->lat = '';
                    $user->lng = '';
                } else {
                    $user->location = $request->location;
                    $user->lat = $request->lat;
                    $user->lng = $request->lng;
                }
            }

            if ($request->has('position')) {
                if ($request->position != null) {
                    $user->position = $request->position;
                }
            }

            if ($request->has('about')) {
                if ($request->about == '@empty_data_') {
                    $user->about = '';
                } else {
                    $user->about = $request->about;
                }
            }

            if ($request->has('carrier')) {
                if ($request->carrier == '@empty_data_') {
                    $user->carrier = '';
                } else {
                    $user->carrier = $request->carrier;
                }
            }


            if ($request->has('phone_number')) {
                if ($request->phone_number == '@empty_data_') {
                    $user->phone_number = '';
                } else {
                    $user->phone_number = $request->phone_number;
                }
            }


            if ($request->has('for_training')) {
                if ($request->for_training == '@empty_data_') {
                    $user->for_training = '';
                } else {
                    $user->for_training = $request->for_training;
                }
            }
            if ($request->has('no_of_bed')) {
                if ($request->no_of_bed == '@empty_data_') {
                    $user->no_of_bed = 0;
                } else {
                    $user->no_of_bed = $request->no_of_bed;
                }
            }
            if ($request->has('special_feature')) {
                if ($request->special_feature == '@empty_data_') {
                    $user->special_feature = '';
                } else {
                    $user->special_feature = $request->special_feature;
                }
            }
            if ($request->has('No_of_employees')) {
                if ($request->No_of_employees == '@empty_data_') {
                    $user->No_of_employees = 0;
                } else {
                    $user->No_of_employees = $request->No_of_employees;
                }
            }

            if ($request->has('multi_images')) {

                $images = $request->file('multi_images');

                foreach ($images as $file) {
                    $extension = $file->getClientOriginalExtension();
                    $mime = explode('/', $file->getClientMimeType());
                    $filename = time() . '-' . uniqid() . '.' . $extension;
                    if ($file->move('uploads/user/' . $user->uuid . '/gallery/', $filename)) {
                        $imagePaths = '/uploads/user/' . $user->uuid . '/gallery/' . $filename;
                    }
                    $gallery = new UserGallery();
                    $gallery->user_id = $user->uuid;
                    $gallery->image = $imagePaths;
                    $gallery->save();
                }
            }

            if ($request->has('website_link')) {
                if ($request->website_link == '@empty_data_') {
                    $user->website_link = '';
                } else {
                    $user->website_link = $request->website_link;
                }
            }

            if ($request->has('linkedin_link')) {
                if ($request->linkedin_link == '@empty_data_') {
                    $user->linkedin_link = '';
                } else {
                    $user->linkedin_link = $request->linkedin_link;
                }
            }

            if ($request->has('instagram_link')) {
                if ($request->instagram_link == '@empty_data_') {
                    $user->instagram_link = '';
                } else {
                    $user->instagram_link = $request->instagram_link;
                }
            }

            if ($request->has('facebook_link')) {
                if ($request->facebook_link == '@empty_data_') {
                    $user->facebook_link = '';
                } else {
                    $user->facebook_link = $request->facebook_link;
                }
            }
            if ($request->has('youtube_link')) {
                if ($request->youtube_link == '@empty_data_') {
                    $user->youtube_link = '';
                } else {
                    $user->youtube_link = $request->youtube_link;
                }
            }
            $user->save();

            $user->multi_images = UserGallery::where('user_id', $user->uuid)->get();
            $interest = UserInterest::where('user_id', $user->uuid)->first();
            if ($interest) {
                $user->interest_add = true;
            } else {
                $user->interest_add = false;
            }
            $token = $request->bearerToken();
            $user->token = $token;
            return response()->json([
                'status' => true,
                'action' => "Profile edit",
                'data' => $user
            ]);
        }

        return response()->json([
            'status' => false,
            'action' => "User not found"
        ]);
    }

    public function deleteGallery($id)
    {
        $find = UserGallery::find($id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => "Gallery Image Deleted"
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "Gallery Image not found"
        ]);
    }
    public function addDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,uuid',
            'type' => 'required',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }

        $create =  new UserDetail();
        if ($request->type == 'education') {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'title' => 'required',
                'start_year' => 'required',
                'end_year' => 'required',
            ]);

            $errorMessage = implode(', ', $validator->errors()->all());

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'action' => $errorMessage
                ]);
            }
            $create->user_id = $request->user_id;

            $create->name = $request->name;
            $create->title = $request->title;
            $create->start_year = $request->start_year;
            $create->end_year = $request->end_year;
            $create->type = $request->type;
            $create->save();
        }
        if ($request->type == 'experience') {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'title' => 'required',
                'start_year' => 'required',
                'end_year' => 'required',
                'location' => 'required',
            ]);

            $errorMessage = implode(', ', $validator->errors()->all());

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'action' => $errorMessage
                ]);
            }
            $create->user_id = $request->user_id;
            $create->name = $request->name;
            $create->title = $request->title;
            $create->start_year = $request->start_year;
            $create->end_year = $request->end_year;
            $create->type = $request->type;
            $create->location = $request->location;
            $create->save();
        }
        if ($request->type == 'certification') {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'title' => 'required',
                'start_year' => 'required',
            ]);

            $errorMessage = implode(', ', $validator->errors()->all());

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'action' => $errorMessage
                ]);
            }
            $create->user_id = $request->user_id;

            $create->name = $request->name;
            $create->title = $request->title;
            $create->start_year = $request->start_year;
            $create->end_year = '';
            $create->type = $request->type;

            $create->save();
        }
        if ($request->type == 'link') {

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'url' => 'required',
            ]);

            $errorMessage = implode(', ', $validator->errors()->all());

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'action' => $errorMessage
                ]);
            }
            $create->user_id = $request->user_id;
            $create->type = $request->type;

            $create->title = $request->title;
            $create->url = $request->url;
            $create->save();
        }
        $newData = UserDetail::find($create->id);
        return response()->json([
            'status' => true,
            'action' => 'Detail Added',
            'data' => $newData
        ]);
    }

    public function getDetail($type, $user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            $userdetail = UserDetail::where('type', $type)->where('user_id', $user_id)->latest()->get();
            return response()->json([
                'status' => true,
                'action' => 'Detail',
                'data' => $userdetail
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'User not found'
        ]);
    }
    public function deleteDetail($id)
    {
        $find = UserDetail::find($id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => 'Detail Deleted'
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Detail not found'
        ]);
    }

    public function addDepartment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,uuid',
            'name' => 'required',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }
        $create = new Department();
        $create->user_id = $request->user_id;
        $create->name = $request->name;
        $create->save();
        return response()->json([
            'status' => true,
            'action' => 'Depart Added',
        ]);
    }

    public function editDepartment(Request $request)
    {
        $depart = Department::find($request->depart_id);
        if ($depart) {
            $depart->name = $request->name;
            $depart->save();
            return response()->json([
                'status' => true,
                'action' => 'Deaprt Edit',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Depart not found',
        ]);
    }


    public function listDepartment($id)
    {
        $user = User::find($id);
        if ($user) {
            $depart = Department::where('user_id', $id)->get();
            return response()->json([
                'status' => true,
                'action' => 'Depart List',
                'data' => $depart
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'User not found',
        ]);
    }

    public function listDepartmentUser($id)
    {
        $depart = Department::find($id);
        if ($depart) {
            $users = DepartmentUser::where('depart_id', $id)->get();
            return response()->json([
                'status' => true,
                'action' => 'Depart Users',
                'data' => $users
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Depart not found',
        ]);
    }
    public function addDepartmentUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'depart_id' => 'required|exists:departments,id',
            'image' => 'required',
            'name' => 'required',
            'designation' => 'required',
            'role' => 'required',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }
        $depart = Department::find($request->depart_id);
        $create = new DepartmentUser();

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/user/' . $depart->user_id . '/department/'  . $request->depart_id . '/user/', $filename))
                $path = '/uploads/user/' . $depart->user_id . '/department/' . $request->depart_id  . '/user/' . $filename;
            $create->image = $path;
        }
        $create->name = $request->name;

        $create->depart_id = $request->depart_id;
        $create->email = $request->email ?: '';
        $create->designation = $request->designation;
        $create->role = $request->role;
        $create->save();
        $newData = DepartmentUser::find($create->id);

        return response()->json([
            'status' => true,
            'action' => 'User Added',
            'data' => $newData

        ]);
    }

    public function deleteDepartmentUser($id)
    {
        $user = DepartmentUser::find($id);
        if ($user) {
            $user->delete();
            return response()->json([
                'status' => true,
                'action' => 'Depart User Deleted',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Depart User not found',
        ]);
    }

    public function management($user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            $management = Management::where('user_id', $user_id)->latest()->first();
            return response()->json([
                'status' => true,
                'action' => 'Management',
                'data' => $management
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'User not found',
        ]);
    }


    public function addManagement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,uuid',
            'image' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'designation' => 'required',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }

        $create =  new Management();
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/user/' . $request->user_id . '/management/', $filename))
                $path = '/uploads/user/' . $request->user_id . '/management/'  . $filename;
            $create->image = $path;
        }
        $create->user_id = $request->user_id;
        $create->name = $request->name;
        $create->email = $request->email;
        $create->designation = $request->designation;
        $create->save();
        $newData = Management::find($create->id);
        return response()->json([
            'status' => true,
            'action' => 'Management Edit',
            'data' => $newData
        ]);
    }

    public function editManagement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'management_id' => 'required|exists:management,id',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }

        $create =  Management::find($request->management_id);
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/user/' . $request->user_id . '/management/', $filename))
                $path = '/uploads/user/' . $request->user_id . '/management/'  . $filename;
            $create->image = $path;
        }

        if ($request->has('name')) {
            $create->name = $request->name;
        }

        if ($request->has('email')) {
            $create->email = $request->email;
        }
        if ($request->has('designation')) {
            $create->designation = $request->designation;
        }
        $create->save();

        $newData = Management::find($create->id);
        return response()->json([
            'status' => true,
            'action' => 'Management Edit',
            'data' => $newData
        ]);
    }

    public function contact($user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            $contact = Contact::where('user_id', $user_id)->latest()->first();
            return response()->json([
                'status' => true,
                'action' => 'Contact',
                'data' => $contact
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'User not found',
        ]);
    }

    public function addContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,uuid',
            'name' => 'required',
            'image' => 'required',
            'country_code' => 'required',
            'phone_number' => 'required',
            'email' => 'required|email',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }

        $create =  new Contact();
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/user/' . $request->user_id . '/contact/', $filename))
                $path = '/uploads/user/' . $request->user_id . '/contact/'  . $filename;
            $create->image = $path;
        }

        $create->name = $request->name;
        $create->user_id = $request->user_id;
        $create->email = $request->email;
        $create->country_code = $request->country_code;
        $create->phone_number = $request->phone_number;
        $create->save();
        $newData = Management::find($create->id);
        return response()->json([
            'status' => true,
            'action' => 'Contact Edit',
            'data' => $newData
        ]);
    }

    public function editContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|exists:contacts,id',

        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }

        $create =  Contact::find($request->contact_id);
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/user/' . $request->user_id . '/contact/', $filename))
                $path = '/uploads/user/' . $request->user_id . '/contact/'  . $filename;
            $create->image = $path;
        }


        if ($request->has('name')) {
            $create->name = $request->name;
        }
        if ($request->has('email')) {
            $create->email = $request->email;
        }
        if ($request->has('country_code')) {
            $create->country_code = $request->country_code;
        }
        if ($request->has('phone_number')) {
            $create->phone_number = $request->phone_number;
        }
        $create->save();
        $newData = Management::find($create->id);
        return response()->json([
            'status' => true,
            'action' => 'Contact Edit',
            'data' => $newData
        ]);
    }
}
