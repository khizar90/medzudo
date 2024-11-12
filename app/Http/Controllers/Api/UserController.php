<?php

namespace App\Http\Controllers\Api;

use App\Actions\BlockedUser;
use App\Actions\FileUploadAction;
use App\Actions\FirebaseNotification;
use App\Actions\NewNotification;
use App\Actions\User\UserProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BlockRequest;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Requests\Api\DeleteAccountRequest;
use App\Http\Requests\Api\FollowRequest;
use App\Http\Requests\Api\GetVerifyRequest;
use App\Http\Requests\Api\LogoutRequest;
use App\Http\Requests\Api\User\EditImageRequest;
use App\Http\Requests\Api\UserProfileRequest;
use App\Models\BlockList;
use App\Models\Category;
use App\Models\Community;
use App\Models\CommunityJoinRequest;
use App\Models\CommunityPicture;
use App\Models\CommunityPurchase;
use App\Models\Contact;
use App\Models\Department;
use App\Models\DepartmentMember;
use App\Models\DepartmentUser;
use App\Models\Follow;
use App\Models\Group;
use App\Models\GroupParticipant;
use App\Models\ImageVerify;
use App\Models\Management;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\PostSave;
use App\Models\RemindMe;
use App\Models\Report;
use App\Models\User;
use App\Models\UserBusinessDetail;
use App\Models\UserCategory;
use App\Models\UserDetail;
use App\Models\UserDevice;
use App\Models\UserGallery;
use App\Models\UserInterest;
use App\Models\UserMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use stdClass;

class UserController extends Controller
{
    public function setProfile(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $user->professionId = $request->professionId ?: '';
        $user->professionName = $request->professionName ?: '';
        $user->specializationId = $request->specializationId ?: '';
        $user->specializationName = $request->specializationName ?: '';
        $user->subSpecializationId = $request->subSpecializationId ?: '';
        $user->subSpecializationName = $request->subSpecializationName ?: '';
        $user->position = $request->position ?: '';
        $user->experience = $request->experience ?: '';
        $user->age = $request->age ?: '';
        $user->gender = $request->gender ?: '';
        $user->location = $request->location ?: '';
        $user->lat = $request->lat ?: '';
        $user->lng = $request->lng ?: '';
        $user->no_of_employe = $request->no_of_employe ?: 0;
        $user->no_of_bed = $request->no_of_bed ?: 0;
        $user->departmentId = $request->departmentId ?: '';
        $user->departmentName = $request->departmentName ?: '';
        $user->trainingId = $request->trainingId ?: '';
        $user->trainingName = $request->trainingName ?: '';

        if ($request->has('image')) {
            $file = $request->file('image');
            $path = FileUploadAction::handle('user/' . $user->uuid . '/profile', $file);
            $user->image  = $path;
        }
        $user->website_link = $request->website_link ?: '';
        $user->about = $request->about ?: '';
        $user->save();

        if ($request->has('professionId') && $request->professionId != '') {
            $professionIds = explode(',', $request->professionId);
            foreach ($professionIds as $id) {
                $profession = new UserCategory();
                $profession->user_id = $user->uuid;
                $profession->category_id = $id;
                $profession->type = 'profession';
                $profession->save();
            }
        }
        if ($request->has('specializationId') && $request->specializationId != '') {
            $specializationIds = explode(',', $request->specializationId);
            foreach ($specializationIds as $id) {
                $specialization = new UserCategory();
                $specialization->user_id = $user->uuid;
                $specialization->category_id = $id;
                $specialization->type = 'specialization';
                $specialization->save();
            }
        }
        if ($request->has('subSpecializationId') && $request->subSpecializationId != '') {
            $subSpecializationIds = explode(',', $request->subSpecializationId);
            foreach ($subSpecializationIds as $id) {
                $subSpecialization = new UserCategory();
                $subSpecialization->user_id = $user->uuid;
                $subSpecialization->category_id = $id;
                $subSpecialization->type = 'sub-specialization';
                $subSpecialization->save();
            }
        }

        if ($request->has('departmentId' && $request->departmentId != '')) {
            $departmentIds = explode(',', $request->departmentId);
            foreach ($departmentIds as $id) {
                $department = new UserCategory();
                $department->user_id = $user->uuid;
                $department->category_id = $id;
                $department->type = 'department';
                $department->save();
            }
        }
        if ($request->has('trainingId') && $request->trainingId != '') {
            $trainingIds = explode(',', $request->trainingId);
            foreach ($trainingIds as $id) {
                $training = new UserCategory();
                $training->user_id = $user->uuid;
                $training->category_id = $id;
                $training->type = 'training';
                $training->save();
            }
        }
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
        $token = $request->bearerToken();
        $user->token = $token;
        return response()->json([
            'status' => true,
            'action' => 'Profile Set',
            'data' => $user
        ]);
    }

    public function completeProfile(Request $request)
    {
        $user = User::find($request->user()->uuid);
        if ($request->has('image')) {
            $file = $request->file('image');
            $path = FileUploadAction::handle('user/' . $user->uuid . '/profile', $file);
            $user->image  = $path;
        }
        $user->website_link = $request->website_link ?: '';
        $user->about = $request->about ?: '';
        $user->save();

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
        $token = $request->bearerToken();
        $user->token = $token;
        return response()->json([
            'status' => true,
            'action' => 'Profile Set',
            'data' => $user
        ]);
    }

    public function updateUser(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $userDetail = UserBusinessDetail::where('user_id', $user->uuid)->latest()->first();
        if (!$userDetail) {
            $userDetail = new UserBusinessDetail();
            $userDetail->user_id = $user->uuid;
        }
        if ($request->has('cover')) {
            $file = $request->file('cover');
            $path = FileUploadAction::handle('user/' . $user->uuid . '/profile', $file);
            $user->cover = $path;
        }
        if ($request->has('pitch_deck')) {
            $file = $request->file('pitch_deck');
            $path = FileUploadAction::handle('user/' . $user->uuid . '/pitch_deck', $file);
            $userDetail->pitch_deck = $path;
        }
        if ($request->has('image')) {
            $file = $request->file('image');
            $path = FileUploadAction::handle('user/' . $user->uuid . '/profile', $file);
            $user->image = $path;
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
        if ($request->has('phone_number')) {
            if (User::where('phone_number', $request->phone_number)->where('uuid', '!=', $user->uuid)->exists()) {
                return response()->json([
                    'status' => false,
                    'action' => 'Phone number Address is already registered'
                ]);
            } else {
                $user->phone_number = $request->phone_number;
            }
        }
        if ($request->has('first_name')) {
            $user->first_name = $request->first_name;
        }
        if ($request->has('last_name')) {
            $user->last_name = $request->last_name;
        }
        if ($request->has('title')) {
            $user->title = $request->title;
        }
        if ($request->has('age')) {
            $user->age = $request->age;
        }
        if ($request->has('gender')) {
            $user->gender = $request->gender;
        }
        if ($request->has('employer')) {
            $user->employer = $request->employer;
        }
        if ($request->has('sector')) {
            $user->sector = $request->sector;
        }
        if ($request->has('position')) {
            $user->position = $request->position;
        }
        if ($request->has('experience')) {
            $user->experience = $request->experience;
        }
        if ($request->has('location') && $request->has('lat') && $request->has('lng')) {
            $user->location = $request->location;
            $user->lat = $request->lat;
            $user->lng = $request->lng;
        }
        if ($request->has('association_memberships')) {
            $user->association_memberships = $request->association_memberships;
        }
        if ($request->has('no_of_employe')) {
            $user->no_of_employe = $request->no_of_employe;
        }
        if ($request->has('no_of_bed')) {
            $user->no_of_bed = $request->no_of_bed;
        }
        if ($request->has('carrier')) {
            $user->carrier = $request->carrier;
        }
        if ($request->has('about')) {
            $user->about = $request->about;
        }
        if ($request->has('institution_number')) {
            $userDetail->institution_number = $request->institution_number;
        }
        if ($request->has('website_link')) {
            $user->website_link = $request->website_link;
        }
        if ($request->has('linkedin_link')) {
            $user->linkedin_link = $request->linkedin_link;
        }
        if ($request->has('instagram_link')) {
            $user->instagram_link = $request->instagram_link;
        }
        if ($request->has('facebook_link')) {
            $user->facebook_link = $request->facebook_link;
        }
        if ($request->has('youtube_link')) {
            $user->youtube_link = $request->youtube_link;
        }
        if ($request->has('x_link')) {
            $user->x_link = $request->x_link;
        }
        if ($request->has('trainingFocusId') && $request->has('trainingFocusName')) {
            $userDetail->trainingFocusId = $request->trainingFocusId;
            $userDetail->trainingFocusName = $request->trainingFocusName;
        }
        if ($request->has('trainingQualificationId') && $request->has('trainingQualificationName')) {
            $userDetail->trainingQualificationId = $request->trainingQualificationId;
            $userDetail->trainingQualificationName = $request->trainingQualificationName;
        }
        if ($request->has('staffBenefitId') && $request->has('staffBenefitName')) {
            $userDetail->staffBenefitId = $request->staffBenefitId;
            $userDetail->staffBenefitName = $request->staffBenefitName;
        }
        if ($request->has('specialFeatureId') && $request->has('specialFeatureName')) {
            $userDetail->specialFeatureId = $request->specialFeatureId;
            $userDetail->specialFeatureName = $request->specialFeatureName;
        }
        if ($request->has('treatmentServiceId') && $request->has('treatmentServiceName')) {
            $userDetail->treatmentServiceId = $request->treatmentServiceId;
            $userDetail->treatmentServiceName = $request->treatmentServiceName;
        }
        if ($request->has('legalTypeId') && $request->has('legalTypeName')) {
            $userDetail->legalTypeId = $request->legalTypeId;
            $userDetail->legalTypeName = $request->legalTypeName;
        }
        if ($request->has('yearlyRevenueId') && $request->has('yearlyRevenueName')) {
            $userDetail->yearlyRevenueId = $request->yearlyRevenueId;
            $userDetail->yearlyRevenueName = $request->yearlyRevenueName;
        }
        if ($request->has('financingStageId') && $request->has('financingStageName')) {
            $userDetail->financingStageId = $request->financingStageId;
            $userDetail->financingStageName = $request->financingStageName;
        }
        if ($request->has('customer_problem')) {
            $userDetail->customer_problem = $request->customer_problem;
        }
        if ($request->has('business_model')) {
            $userDetail->business_model = $request->business_model;
        }
        if ($request->has('market_description')) {
            $userDetail->market_description = $request->market_description;
        }
        if ($request->has('customer_focus')) {
            $userDetail->customer_focus = $request->customer_focus;
        }
        if ($request->has('technology_description')) {
            $userDetail->technology_description = $request->technology_description;
        }
        if ($request->has('usp')) {
            $userDetail->usp = $request->usp;
        }
        if ($request->has('medicalFocusId') && $request->has('medicalFocusName')) {
            $userDetail->medicalFocusId = $request->medicalFocusId;
            $userDetail->medicalFocusName = $request->medicalFocusName;
        }
        if ($request->has('targetGroupId') && $request->has('targetGroupName')) {
            $userDetail->targetGroupId = $request->targetGroupId;
            $userDetail->targetGroupName = $request->targetGroupName;
        }
        if ($request->has('member_benefits')) {
            $userDetail->member_benefits = $request->member_benefits;
        }
        if ($request->has('working_groups')) {
            $userDetail->working_groups = $request->working_groups;
        }
        if ($request->has('association_engagement')) {
            $userDetail->association_engagement = $request->association_engagement;
        }
        if ($request->has('member_fee')) {
            $userDetail->member_fee = $request->member_fee;
        }
        if ($request->has('become_member')) {
            $userDetail->become_member = $request->become_member;
        }

        if ($request->has('companyFeatureId') && $request->has('companyFeatureName')) {
            $userDetail->companyFeatureId = $request->companyFeatureId;
            $userDetail->companyFeatureName = $request->companyFeatureName;
        }

        if ($request->has('professionId') && $request->professionId != '' && $request->has('professionName') && $request->professionName != '') {
            UserCategory::where('user_id', $user->uuid)->where('type', 'profession')->delete();
            $user->professionId = $request->professionId ?: '';
            $user->professionName = $request->professionName ?: '';
            $professionIds = explode(',', $request->professionId);
            foreach ($professionIds as $id) {
                $profession = new UserCategory();
                $profession->user_id = $user->uuid;
                $profession->category_id = $id;
                $profession->type = 'profession';
                $profession->save();
            }
        }
        if ($request->has('specializationId') && $request->specializationId != '' && $request->has('specializationName') && $request->specializationName != '') {
            UserCategory::where('user_id', $user->uuid)->where('type', 'specialization')->delete();
            $user->specializationId = $request->specializationId ?: '';
            $user->specializationName = $request->specializationName ?: '';
            $specializationIds = explode(',', $request->specializationId);
            foreach ($specializationIds as $id) {
                $specialization = new UserCategory();
                $specialization->user_id = $user->uuid;
                $specialization->category_id = $id;
                $specialization->type = 'specialization';
                $specialization->save();
            }
        }
        if ($request->has('subSpecializationId') && $request->subSpecializationId != '' && $request->has('subSpecializationName') && $request->subSpecializationName != '') {
            UserCategory::where('user_id', $user->uuid)->where('type', 'sub-specialization')->delete();
            $user->subSpecializationId = $request->subSpecializationId ?: '';
            $user->subSpecializationName = $request->subSpecializationName ?: '';
            $subSpecializationIds = explode(',', $request->subSpecializationId);
            foreach ($subSpecializationIds as $id) {
                $subSpecialization = new UserCategory();
                $subSpecialization->user_id = $user->uuid;
                $subSpecialization->category_id = $id;
                $subSpecialization->type = 'sub-specialization';
                $subSpecialization->save();
            }
        }
        if ($request->has('trainingId') && $request->trainingId != '' && $request->has('trainingName') && $request->trainingName != '') {
            UserCategory::where('user_id', $user->uuid)->where('type', 'training')->delete();
            $user->trainingId = $request->trainingId ?: '';
            $user->trainingName = $request->trainingName ?: '';
            $trainingIds = explode(',', $request->trainingId);
            foreach ($trainingIds as $id) {
                $training = new UserCategory();
                $training->user_id = $user->uuid;
                $training->category_id = $id;
                $training->type = 'training';
                $training->save();
            }
        }
        $userDetail->save();
        $user->save();
        $user = User::find($user->uuid);
        $userDetail = UserBusinessDetail::where('user_id', $user->uuid)->latest()->first();
        $user->business_detail = $userDetail;
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
            'data' => $user,
            'action' =>  'User Update',
        ]);
    }

    public function editImage(EditImageRequest $request)
    {
        $user = User::find($request->user()->uuid);
        $file = $request->file('media');
        if ($request->type == 'cover') {
            $path = FileUploadAction::handle('user/' . $user->uuid . '/profile', $file);
            $user->cover = $path;
        }
        if ($request->type == 'image') {
            $path = FileUploadAction::handle('user/' . $user->uuid . '/profile', $file);
            $user->image = $path;
        }
        $user->save();
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
        $token = $request->bearerToken();
        $user->token = $token;
        return response()->json([
            'status' => true,
            'action' => "Image edit",
            'data' => $user
        ]);
    }

    public function removeImage(Request $request, $type)
    {
        $user = User::find($request->user()->uuid);
        if ($request->type == 'cover') {
            Storage::disk('s3')->delete($user->cover);
            $user->cover = '';
        }
        if ($request->type == 'image') {
            Storage::disk('s3')->delete($user->image);
            $user->image = '';
        }
        $user->save();
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
        $token = $request->bearerToken();
        $user->token = $token;
        return response()->json([
            'status' => true,
            'action' => "Image remove",
            'data' => $user
        ]);
    }

    public function getVerify(GetVerifyRequest $request)
    {
        $user = User::find($request->user()->uuid);
        $check = ImageVerify::where('user_id', $user->uuid)->first();
        if ($check) {
            return response()->json([
                'status' => true,
                'action' => "Request Already submitted"
            ]);
        } else {
            if ($user) {
                $userImage =  new ImageVerify();
                $file = $request->file('image');
                $path = FileUploadAction::handle('user/' . $user->uuid . '/verify', $file);
                $userImage->user_id = $user->uuid;
                $userImage->image = $path;
                $user->verify = 2;
                $userImage->save();
                $user->save();
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
                $token = $request->bearerToken();
                $user->token = $token;
                return response()->json([
                    'status' => true,
                    'data' => $user,
                    'action' => "Request submitted"
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'action' => "User not found"
                ]);
            }
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = User::find($request->user()->uuid);
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

    public function deleteAccount(DeleteAccountRequest $request)
    {
        $user = User::find($request->user()->uuid);
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                Message::where('from', $user->uuid)->delete();
                Message::where('to', $user->uuid)->delete();
                Follow::where('user_id', $user->uuid)->orWhere('follow_id', $user->uuid)->delete();
                Report::where('reported_id', $user->uuid)->where('type', 'user')->delete();
                Report::where('user_id', $user->uuid)->where('type', 'user')->delete();
                $user->tokens()->delete();
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

    public function userInterest(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $interest = UserInterest::where('user_id', $user->uuid)->pluck('category_id');
        $categories  = Category::select('id', 'name', 'image')->whereIn('id', $interest)->get();
        return response()->json([
            'status' => true,
            'action' =>  'User Interest',
            'data' => $categories
        ]);
    }

    public function logout(LogoutRequest $request)
    {
        $user = User::find($request->user()->uuid);
        UserDevice::where('user_id', $user->uuid)->where('device_id', $request->device_id)->delete();
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'action' => 'User logged out'
        ]);
    }

    public function blockList(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $block_ids = BlockList::where('user_id', $user->uuid)->pluck('block_id')->toArray();
        $blockUsers = UserProfileAction::userListWithPaging($block_ids, 12, $user->uuid);
        foreach ($blockUsers as $block) {
            $block->block = true;
        }
        return response()->json([
            'status' => true,
            'action' =>  'Block list',
            'data' => $blockUsers
        ]);
    }


    public function profile(Request $request, $to_id, $type)
    {
        $user = User::find($request->user()->uuid);
        if ($user->uuid == $to_id) {
            $user = User::where('uuid', $user->uuid)->first();
            $user->business_detail = UserBusinessDetail::where('user_id', $user->uuid)->first();
            $user->follower = Follow::where('follow_id', $user->uuid)->count();
            $user->following = Follow::where('user_id', $user->uuid)->count();
            $user->communities = Community::where('user_id', $user->uuid)->count();
            $user->follow = false;
            $user->block = false;
            if ($type == 'about') {
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
            }
            if ($type == 'posts') {
                $posts = Post::where('user_id', $user->uuid)->latest()->paginate(12);
                foreach ($posts as $post) {
                    $postBy = UserProfileAction::userCommon($post->user_id, $user->uuid);
                    $rePostBy = new stdClass();
                    if ($post->parent_id != 0) {
                        $parentPost = Post::find($post->parent_id);
                        $rePostBy = UserProfileAction::userCommon($post->user_id, $user->uuid);
                        $postBy = UserProfileAction::userCommon($parentPost->user_id, $user->uuid);
                    }

                    $comment_count = PostComment::where('post_id', $post->id)->count();
                    $like_count = PostLike::where('post_id', $post->id)->count();
                    $likeStatus = PostLike::where('post_id', $post->id)->where('user_id', $user->uuid)->first();
                    $saved = PostSave::where('post_id', $post->id)->where('user_id', $user->uuid)->first();
                    $post->media = empty($post->media) ? [] : explode(',', $post->media);
                    $likes = PostLike::where('post_id', $post->id)->latest()->limit(3)->pluck('user_id');
                    $like_users = User::select('uuid', 'first_name', 'last_name', 'image')->whereIn('uuid', $likes)->get();
                    if ($likeStatus) {
                        $post->is_liked = true;
                    } else {
                        $post->is_liked = false;
                    }

                    if ($saved) {
                        $post->is_saved = true;
                    } else {
                        $post->is_saved = false;
                    }

                    $post->comment_count = $comment_count;
                    $post->like_count = $like_count;
                    $post->like_users = $like_users;
                    $post->user = $postBy;
                    $post->rePostBy = $rePostBy;
                }
                $user->post = $posts;
            }
            if ($type == 'communities') {
                $communities = Community::where('user_id', $user->uuid)->latest()->paginate(12);
                foreach ($communities as $item) {
                    $categoriesIds  = explode(',', $item->categories);
                    $categories = Category::whereIn('id', $categoriesIds)->get();
                    $item->categories = $categories;
                    $pictures = CommunityPicture::where('community_id', $item->id)->get();
                    $item->pictures = $pictures;
                    $item->participant_count = CommunityJoinRequest::where('community_id', $item->id)->where('status', '!=', 'pending')->count();
                    $participantIds = CommunityJoinRequest::where('community_id', $item->id)->where('status', '!=', 'pending')->pluck('user_id');
                    $participants = User::whereIn('uuid', $participantIds)->limit(3)->pluck('image');
                    $item->participants = $participants;
                    $is_purchase = CommunityPurchase::where('user_id', $user->uuid)->where('community_id', $item->id)->first();
                    $item->is_purchase = false;
                    if ($is_purchase) {
                        $item->is_purchase = true;
                    }
                    $item->user = UserProfileAction::userCommon($item->user_id, $user->uuid);
                }
                $user->communities = $communities;
            }
        } else {
            $loginUser = User::where('uuid', $user->uuid)->first();
            $user = User::where('uuid', $to_id)->first();
            $user->business_detail = UserBusinessDetail::where('user_id', $user->uuid)->first();
            $user->follower = Follow::where('follow_id', $user->uuid)->count();
            $user->following = Follow::where('user_id', $user->uuid)->count();
            $user->communities = Community::where('user_id', $user->uuid)->count();
            $user->follow = false;
            $user->block = false;
            $follow = Follow::where('user_id', $loginUser->uuid)->where('follow_id', $user->uuid)->first();
            if ($follow) {
                $user->follow = true;
            }
            $block = BlockList::where('user_id', $loginUser->uuid)->where('block_id', $user->uuid)->first();
            if ($block) {
                $user->block = true;
            }
            if ($type == 'about') {
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
                        $find_department_user = UserProfileAction::userCommon($department_user->member_id, $loginUser->uuid);
                        $department_user->user = $find_department_user;
                    }
                }
                $user->departments = $departments;
                $user->contact = Contact::where('user_id', $user->uuid)->get();
                $management = Management::where('user_id', $user->uuid)->get();
                foreach ($management as $management_user) {
                    $find_management_user = UserProfileAction::userCommon($management_user->management_id, $loginUser->uuid);
                    $management_user->user = $find_management_user;
                }
                $user->management = $management;
                $user->media = UserMedia::where('user_id', $user->uuid)->get();
                $teams = DepartmentMember::where('user_id', $user->uuid)->where('department_id', null)->get();
                foreach ($teams as $teams_user) {
                    $find_teams_user = UserProfileAction::userCommon($teams_user->member_id, $loginUser->uuid);
                    $teams_user->user = $find_teams_user;
                }
                $user->teams = $teams;
            }
            if ($type == 'posts') {
                $posts = Post::where('user_id', $user->uuid)->latest()->paginate(12);
                foreach ($posts as $post) {
                    $postBy = UserProfileAction::userCommon($post->user_id, $loginUser->uuid);
                    $rePostBy = new stdClass();
                    if ($post->parent_id != 0) {
                        $parentPost = Post::find($post->parent_id);
                        $rePostBy = UserProfileAction::userCommon($post->user_id, $loginUser->uuid);
                        $postBy = UserProfileAction::userCommon($parentPost->user_id, $loginUser->uuid);
                    }
                    $comment_count = PostComment::where('post_id', $post->id)->count();
                    $like_count = PostLike::where('post_id', $post->id)->count();
                    $likeStatus = PostLike::where('post_id', $post->id)->where('user_id', $user->uuid)->first();
                    $saved = PostSave::where('post_id', $post->id)->where('user_id', $user->uuid)->first();
                    $post->media = empty($post->media) ? [] : explode(',', $post->media);
                    $likes = PostLike::where('post_id', $post->id)->latest()->limit(3)->pluck('user_id');
                    $like_users = User::select('uuid', 'first_name', 'last_name', 'image')->whereIn('uuid', $likes)->get();
                    if ($likeStatus) {
                        $post->is_liked = true;
                    } else {
                        $post->is_liked = false;
                    }
                    if ($saved) {
                        $post->is_saved = true;
                    } else {
                        $post->is_saved = false;
                    }
                    $post->comment_count = $comment_count;
                    $post->like_count = $like_count;
                    $post->like_users = $like_users;
                    $post->user = $postBy;
                    $post->rePostBy = $rePostBy;
                }
                $user->post = $posts;
            }
            if ($type == 'communities') {
                $communities = Community::where('user_id', $user->uuid)->latest()->paginate(12);
                foreach ($communities as $item) {
                    $categoriesIds  = explode(',', $item->categories);
                    $categories = Category::whereIn('id', $categoriesIds)->get();
                    $item->categories = $categories;
                    $pictures = CommunityPicture::where('community_id', $item->id)->get();
                    $item->pictures = $pictures;
                    $item->participant_count = CommunityJoinRequest::where('community_id', $item->id)->where('status', '!=', 'pending')->count();
                    $participantIds = CommunityJoinRequest::where('community_id', $item->id)->where('status', '!=', 'pending')->pluck('user_id');
                    $participants = User::whereIn('uuid', $participantIds)->limit(3)->pluck('image');
                    $item->participants = $participants;
                    $is_purchase = CommunityPurchase::where('user_id', $user->uuid)->where('community_id', $item->id)->first();
                    $item->is_purchase = false;
                    if ($is_purchase) {
                        $item->is_purchase = true;
                    }
                    $item->user = UserProfileAction::userCommon($item->user_id, $loginUser->uuid);
                }
                $user->communities = $communities;
            }
        }
        return response()->json([
            'status' => true,
            'action' =>  'User Profile',
            'data' => $user
        ]);
    }

    public function follow(Request $request, $to_id)
    {
        $user = User::find($request->user()->uuid);
        $check = Follow::where('user_id', $user->uuid)->where('follow_id', $to_id)->first();
        if ($check) {
            $check->delete();
            Notification::where('person_id', $user->uuid)->where('user_id', $to_id)->where('type', 'follow')->where('notification_type', 'social')->delete();
            return response()->json([
                'status' => true,
                'action' =>  'User UnFollow',
            ]);
        }
        $follow = new Follow();
        $follow->user_id = $user->uuid;
        $follow->follow_id = $to_id;
        $follow->save();
        $from = User::find($user->uuid);
        $to = User::find($to_id);
        $post = 0;
        NewNotification::handle($to_id, $from->uuid, $post, 'started following you.', 'follow', 'social');
        $tokens = UserDevice::where('user_id', $to_id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
        $last_name = $user->last_name;
        if ($last_name) {
            FirebaseNotification::handle($tokens, $user->first_name . ' ' . $user->last_name . ' started following you.', 'medzudo', ['data_id' => $from->uuid, 'type' => 'social', 'sub_type' => 'follow', 'profile_type' => $from->account_type], 1);
        } else {
            FirebaseNotification::handle($tokens, $user->first_name . ' started following you.', 'medzudo', ['data_id' => $from->uuid, 'type' => 'social', 'sub_type' => 'follow', 'profile_type' => $from->account_type], 1);
        }
        return response()->json([
            'status' => true,
            'action' =>  'User Follow',
        ]);
    }


    public function following(Request $request, $user_id)
    {
        $user = User::find($request->user()->uuid);
        if ($user) {
            $blocked = BlockedUser::handle($user->uuid);
            $followingIds = Follow::where('user_id', $user_id)->whereNotIn('user_id', $blocked)->whereNotIn('follow_id', $blocked)->pluck('follow_id')->toArray();
            $followings = UserProfileAction::userListWithPaging($followingIds, 12, $user->uuid);
            foreach ($followings as $item) {
                $item->is_follow = true;
            }
            return response()->json([
                'status' => true,
                'action' =>  'Following',
                'data' => $followings
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'User not found',
        ]);
    }

    public function followers(Request $request, $user_id)
    {
        $user = User::find($request->user()->uuid);
        if ($user) {
            $blocked = BlockedUser::handle($user->uuid);
            $followerIds = Follow::where('follow_id', $user_id)->whereNotIn('user_id', $blocked)->whereNotIn('follow_id', $blocked)->pluck('user_id')->toArray();
            $followers = UserProfileAction::userListWithPaging($followerIds, 12, $user->uuid);
            return response()->json([
                'status' => true,
                'action' =>  'Followers',
                'data' => $followers
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'User not found',
        ]);
    }

    public function block(Request $request, $block_id)
    {
        $user = User::find($request->user()->uuid);
        $check = BlockList::where('block_id', $block_id)->where('user_id',  $user->uuid)->first();
        if ($check) {
            $check->delete();
            return response()->json([
                'status' => true,
                'action' => 'User unblocked'
            ]);
        } else {
            $block = new BlockList;
            $block->block_id = $block_id;
            $block->user_id = $user->uuid;
            $block->save();
            Follow::where('user_id', $user->uuid)->where('follow_id', $block_id)->delete();
            Follow::where('follow_id', $user->uuid)->where('user_id', $block_id)->delete();
            return response()->json([
                'status' => true,
                'action' => 'User blocked'
            ]);
        }
    }

    public function discover(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockedUser::handle($user->uuid);
        $followIds = Follow::where('user_id', $user->uuid)->pluck('follow_id');
        $communitiesId = CommunityPurchase::where('user_id', $user->uuid)->pluck('community_id');
        $individual = User::where('account_type', 'individual')->whereNotIn('uuid', $blocked)->whereNotIn('uuid', $followIds)->where('uuid', '!=', $user->uuid)->inRandomOrder()->limit(6)->pluck('uuid')->toArray();
        $individual = UserProfileAction::userList($individual, $user->uuid);
        foreach ($individual as $item) {
            $item->is_follow = false;
        }
        $facility = User::where('account_type', 'facility')->whereNotIn('uuid', $blocked)->where('uuid', '!=', $user->uuid)->inRandomOrder()->limit(12)->pluck('uuid')->toArray();
        $facility = UserProfileAction::userList($facility, $user->uuid);
        $organization = User::where('account_type', 'organization')->whereNotIn('uuid', $blocked)->where('uuid', '!=', $user->uuid)->inRandomOrder()->limit(12)->pluck('uuid')->toArray();
        $organization = UserProfileAction::userList($organization, $user->uuid);
        $communities = Community::whereNotIn('id', $communitiesId)->whereNotIn('user_id', $blocked)->where('user_id', '!=', $user->uuid)->inRandomOrder()->limit(12)->get();
        foreach ($communities as $item) {
            $categoriesIds  = explode(',', $item->categories);
            $categories = Category::whereIn('id', $categoriesIds)->get();
            $item->categories = $categories;
            $pictures = CommunityPicture::where('community_id', $item->id)->get();
            $item->pictures = $pictures;
            $item->participant_count = CommunityJoinRequest::where('community_id', $item->id)->where('status', '!=', 'pending')->count();
            $participantIds = CommunityJoinRequest::where('community_id', $item->id)->where('status', '!=', 'pending')->pluck('user_id');
            $participants = User::whereIn('uuid', $participantIds)->limit(3)->pluck('image');
            $item->participants = $participants;
            $item->is_purchase = false;
            $item->user = UserProfileAction::userCommon($item->user_id, $user->uuid);
        }
        return response()->json([
            'status' => true,
            'data' => array(
                'individual' => $individual,
                'facility' => $facility,
                'organization' => $organization,
                'communities' => $communities,
            ),
            'action' =>  'Discover',
        ]);
    }

    public function discoverList(Request $request, $type)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockedUser::handle($user->uuid);
        if ($type == 'individual') {
            $followIds = Follow::where('user_id', $user->uuid)->pluck('follow_id');
            $list = User::where('account_type', 'individual')->whereNotIn('uuid', $blocked)->whereNotIn('uuid', $followIds)->where('uuid', '!=', $user->uuid);
            if ($request->has('sector') && $request->sector != '') {
                $list->where('sector', $request->sector);
            }
            if ($request->has('professionIds') && $request->professionIds != '') {
                $categories = explode(',', $request->professionIds);
                $userIds = UserCategory::whereIn('category_id', $categories)->where('type', 'profession')->pluck('user_id');
                $list->whereIn('uuid', $userIds);
            }
            if ($request->has('position') && $request->position != '') {
                $list = $list->where('position', $request->position);
            }
            if ($request->has('interestIds') && $request->interestIds != '') {
                $categories = explode(',', $request->interestIds);
                $userIds = UserInterest::whereIn('category_id', $categories)->pluck('user_id');
                $list->whereIn('uuid', $userIds);
            }
            $list = $list->inRandomOrder()->pluck('uuid')->toArray();
            $list = UserProfileAction::userListWithPaging($list, 12, $user->uuid);
            foreach ($list as $item) {
                $item->is_follow = false;
            }
        }
        if ($type == 'facility') {
            $list = User::where('account_type', 'facility')->whereNotIn('uuid', $blocked)->where('uuid', '!=', $user->uuid);
            if ($request->has('type') && $request->type != '') {
                $list->where('type', $request->type);
            }
            if ($request->has('specializationIds') && $request->specializationIds != '') {
                $categories = explode(',', $request->specializationIds);
                $userIds = UserCategory::whereIn('category_id', $categories)->where('type', 'specialization')->pluck('user_id');
                $list->whereIn('uuid', $userIds);
            }
            if ($request->has('trainingIds' && $request->trainingIds != '')) {
                $categories = explode(',', $request->trainingIds);
                $userIds = UserCategory::whereIn('category_id', $categories)->where('type', 'training')->pluck('user_id');
                $list->whereIn('uuid', $userIds);
            }
            $list = $list->inRandomOrder()->pluck('uuid')->toArray();
            $list = UserProfileAction::userListWithPaging($list, 12, $user->uuid);
        }
        if ($type == 'organization') {
            $list = User::where('account_type', 'organization')->whereNotIn('uuid', $blocked)->where('uuid', '!=', $user->uuid);
            if ($request->has('type') && $request->type != '') {
                $list->where('type', $request->type);
            }
            if ($request->has('sectorIds') && $request->sectorIds != '') {
                $sectorIds = explode(',', $request->sectorIds);
                $userIds = UserCategory::whereIn('category_id', $sectorIds)->where('type', 'profession')->pluck('user_id');
                $list->whereIn('uuid', $userIds);
            }
            $list = $list->inRandomOrder()->pluck('uuid')->toArray();
            $list = UserProfileAction::userListWithPaging($list, 12, $user->uuid);
        }

        return response()->json([
            'status' => true,
            'data' => $list,
            'action' =>  'Discover',
        ]);
    }

    public function discoverSearch(Request $request, $type)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockedUser::handle($user->uuid);
        if ($type == 'individual') {
            $list = User::where('account_type', 'individual')->whereNotIn('uuid', $blocked)->where(function ($query) use ($request) {
                $query->where("first_name", "LIKE", "%" . $request->keyword . "%")
                    ->orWhere("last_name", "LIKE", "%" . $request->keyword . "%");
            })->inRandomOrder()->pluck('uuid')->toArray();
            $list = UserProfileAction::userListWithPaging($list, 12, $user->uuid);
            foreach ($list as $item) {
                $check  = Follow::where('user_id', $user->uuid)->where('follow_id', $item->uuid)->first();
                if ($check) {
                    $item->is_follow = true;
                } else {
                    $item->is_follow = false;
                }
            }
        }

        if ($type == 'facility') {
            $list = User::where('account_type', 'facility')->whereNotIn('uuid', $blocked)->where("first_name", "LIKE", "%" . $request->keyword . "%")->inRandomOrder()->pluck('uuid')->toArray();
            $list = UserProfileAction::userListWithPaging($list, 12, $user->uuid);
        }
        if ($type == 'organization') {
            $list = User::where('account_type', 'organization')->whereNotIn('uuid', $blocked)->where("first_name", "LIKE", "%" . $request->keyword . "%")->inRandomOrder()->pluck('uuid')->toArray();
            $list = UserProfileAction::userListWithPaging($list, 12, $user->uuid);
        }
        return response()->json([
            'status' => true,
            'data' => $list,
            'action' =>  'Discover',
        ]);
    }

    public function addDetail(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $validator = Validator::make($request->all(), [
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
            ]);
            $errorMessage = implode(', ', $validator->errors()->all());
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'action' => $errorMessage
                ]);
            }
            $create->user_id = $user->uuid;
            $create->name = $request->name;
            $create->title = $request->title;
            $create->start_year = $request->start_year;
            $create->end_year = $request->end_year ?: '';
            $create->type = $request->type;
            $create->save();
        }
        if ($request->type == 'experience') {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'title' => 'required',
                'start_year' => 'required',
                'location' => 'required',
            ]);
            $errorMessage = implode(', ', $validator->errors()->all());
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'action' => $errorMessage
                ]);
            }
            $create->user_id = $user->uuid;
            $create->name = $request->name;
            $create->title = $request->title;
            $create->start_year = $request->start_year;
            $create->end_year = $request->end_year ?: '';
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
            $create->user_id = $user->uuid;
            $create->name = $request->name;
            $create->title = $request->title;
            $create->start_year = $request->start_year;
            $create->end_year = $request->end_year ?: '';
            $create->type = $request->type;
            $create->save();
        }
        if ($request->type == 'publication') {
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
            $create->user_id = $user->uuid;
            $create->name = $request->name;
            $create->title = $request->title;
            $create->url = $request->url ?: '';
            $create->number = $request->number ?: '';
            $create->abstract = $request->abstract ?: '';
            $create->start_year = $request->start_year;
            $create->type = $request->type;
            $create->save();
        }
        if ($request->type == 'finance-round') {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'start_year' => 'required',
                'number' => 'required',
            ]);
            $errorMessage = implode(', ', $validator->errors()->all());
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'action' => $errorMessage
                ]);
            }
            $create->user_id = $user->uuid;
            $create->name = $request->name;
            $create->title = $request->title ?: '';
            $create->number = $request->number;
            $create->start_year = $request->start_year;
            $create->type = $request->type;
            $create->save();
        }
        if ($request->type == 'educational-program') {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            $errorMessage = implode(', ', $validator->errors()->all());
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'action' => $errorMessage
                ]);
            }
            $create->user_id = $user->uuid;
            $create->name = $request->name;
            $create->title = $request->title ?: '';
            $create->type = $request->type;
            $create->save();
        }
        $newData = UserDetail::find($create->id);
        return response()->json([
            'status' => true,
            'action' => ucfirst($newData->type) . ' Added',
            'data' => $newData
        ]);
    }

    public function editDetail(Request $request, $id)
    {
        $find = UserDetail::find($id);
        if ($find) {
            if ($request->has('name')) {
                $find->name = $request->name;
            }
            if ($request->has('title')) {
                $find->title = $request->title;
            }
            if ($request->has('start_year')) {
                $find->start_year = $request->start_year;
            }
            if ($request->has('end_year')) {
                $find->end_year = $request->end_year ?: '';
            }
            if ($request->has('location')) {
                $find->location = $request->location;
            }
            if ($request->has('url')) {
                $find->url = $request->url;
            }
            if ($request->has('number')) {
                $find->number = $request->number;
            }
            if ($request->has('abstract')) {
                $find->abstract = $request->abstract;
            }
            $find->save();
            return response()->json([
                'status' => true,
                'data' => $find,
                'action' => 'Detail Edit'
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Detail not found'
        ]);
    }

    public function getDetail(Request $request, $type)
    {
        $user = User::find($request->user()->uuid);
        if ($user) {
            $detail = UserDetail::where('type', $type)->where('user_id', $user->uuid)->latest()->get();
            return response()->json([
                'status' => true,
                'action' => 'Detail',
                'data' => $detail
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

    public function contact(Request $request)
    {
        $user = User::find($request->user()->uuid);
        if ($user) {
            $contact = Contact::where('user_id', $user->uuid)->latest()->get();
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
        $user  = User::find($request->user()->uuid);
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            // 'image' => 'required',
            // 'country_code' => 'required',
            'phone_number' => 'required',
            'email' => 'required|email',
            'designation' => 'required'
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
            $path = FileUploadAction::handle('user/'  . $user->uuid . '/contact', $file);
            $create->image = $path;
        } else {
            $create->image = '';
        }
        $create->name = $request->name;
        $create->user_id = $user->uuid;
        $create->email = $request->email;
        $create->country_code = '';
        $create->phone_number = $request->phone_number;
        $create->designation = $request->designation;
        $create->save();
        $newData = Contact::find($create->id);
        return response()->json([
            'status' => true,
            'action' => 'Contact Edit',
            'data' => $newData
        ]);
    }

    public function editContact(Request $request, $contact_id)
    {
        $user  = User::find($request->user()->uuid);
        $create =  Contact::find($contact_id);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = FileUploadAction::handle('user/'  . $user->uuid . '/contact', $file);
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
        if ($request->has('designation')) {
            $create->designation = $request->designation;
        }
        $create->save();
        $newData = Contact::find($create->id);
        return response()->json([
            'status' => true,
            'action' => 'Contact Edit',
            'data' => $newData
        ]);
    }

    public function deleteContact($id)
    {
        $find = Contact::find($id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => 'Contact Deleted'
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Contact not found'
        ]);
    }

    public function management(Request $request)
    {
        $user  = User::find($request->user()->uuid);
        $list = Management::where('user_id', $user->uuid)->get();
        foreach ($list as $item) {
            $user = UserProfileAction::userCommon($item->management_id, $user->uuid);
            $item->user = $user;
        }
        return response()->json([
            'status' => true,
            'data' => $list,
            'action' => 'Management List'
        ]);
    }
    public function addManagement(Request $request)
    {
        $user  = User::find($request->user()->uuid);
        $validator = Validator::make($request->all(), [
            'management_id'  => 'required|exists:users,uuid',
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
        $create->image = '';
        $create->user_id = $user->uuid;
        $create->management_id = $request->management_id;
        $create->name = '';
        $create->email = '';
        $create->designation = $request->designation;
        $create->is_mention = $request->is_mention ?: 0;
        $create->save();
        $newData = Management::find($create->id);
        $user = UserProfileAction::userCommon($newData->management_id, $user->uuid);
        $newData->user = $user;
        return response()->json([
            'status' => true,
            'action' => 'Management Added',
            'data' => $newData
        ]);
    }

    public function editManagement(Request $request, $id)
    {
        $user = User::find($request->user()->uuid);

        $management = Management::find($id);
        if ($management) {
            if ($request->has('designation')) {
                $management->designation = $request->designation;
            }
            if ($request->has('is_mention')) {
                $management->is_mention = $request->is_mention;
            }
            $management->save();
            $user = UserProfileAction::userCommon($management->management_id, $user->uuid);
            $management->user = $user;
            return response()->json([
                'status' => true,
                'action' => 'Management Edit',
                'data' => $management
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Management not found'
        ]);
    }
    public function deleteManagement($id)
    {
        $find = Management::find($id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => 'Management Deleted'
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Management not found'
        ]);
    }

    public function department(Request $request)
    {
        $user  = User::find($request->user()->uuid);
        $list = Department::where('user_id', $user->uuid)->get();
        return response()->json([
            'status' => true,
            'data' => $list,
            'action' => 'Department List'
        ]);
    }
    public function addDepartment(Request $request)
    {
        $user  = User::find($request->user()->uuid);
        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'departmentId' => 'required',
            'departmentName' => 'required',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }
        $create = new Department();
        $create->user_id = $user->uuid;
        $create->name = $request->name;
        $create->departmentId = $request->departmentId;
        $create->departmentName = $request->departmentName;
        $create->save();
        $newData  = Department::find($create->id);
        return response()->json([
            'status' => true,
            'data' => $newData,
            'action' => 'Department Added'
        ]);
    }

    public function editDepartment(Request $request, $id)
    {
        $create = Department::find($id);
        if ($create) {
            if ($request->has('name')) {
                $create->name = $request->name;
            }
            if ($request->has('departmentId')) {
                $create->departmentId = $request->departmentId;
            }
            if ($request->has('departmentName')) {
                $create->departmentName = $request->departmentName;
            }
            $create->save();
            return response()->json([
                'status' => true,
                'data' => $create,
                'action' => 'Department Edit'
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Department not found'
        ]);
    }
    public function deleteDepartment($id)
    {
        $find = Department::find($id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => 'Department Deleted'
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Department not found'
        ]);
    }

    public function departmentMember(Request $request, $department_id = null)
    {
        $user = User::find($request->user()->uuid);
        $list = DepartmentMember::where('user_id', $user->uuid)->where('department_id', $department_id)->get();
        foreach ($list as $item) {
            $user = UserProfileAction::userCommon($item->member_id, $user->uuid);
            $item->user = $user;
        }
        return response()->json([
            'status' => true,
            'data' => $list,
            'action' => 'Department Member List'
        ]);
    }
    public function addDepartmentMember(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $validator = Validator::make($request->all(), [
            'member_id'  => 'required|exists:users,uuid',
            'designation' => 'required',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }
        $create = new DepartmentMember();
        $create->user_id = $user->uuid;
        $create->member_id = $request->member_id;
        $create->department_id = $request->department_id ?: null;
        $create->departmentName = $request->departmentName ?: '';
        $create->designation = $request->designation;
        $create->is_mention = $request->is_mention ?: 0;
        $create->save();
        $newData = DepartmentMember::find($create->id);
        $user = UserProfileAction::userCommon($newData->member_id, $user->uuid);
        $newData->user = $user;
        return response()->json([
            'status' => true,
            'data' => $newData,
            'action' => 'Department Member Added'
        ]);
    }
    public function editDepartmentMember(Request $request, $id)
    {
        $user = User::find($request->user()->uuid);
        $create = DepartmentMember::find($id);
        if ($create) {
            if ($request->has('departmentName')) {
                $create->departmentName = $request->departmentName;
            }
            if ($request->has('designation')) {
                $create->designation = $request->designation;
            }
            if ($request->has('is_mention')) {
                $create->is_mention = $request->is_mention;
            }
            $create->save();
            $newData = DepartmentMember::find($create->id);
            $user = UserProfileAction::userCommon($newData->member_id, $user->uuid);
            $newData->user = $user;
            return response()->json([
                'status' => true,
                'data' => $newData,
                'action' => 'Department Member Added'
            ]);
        }
        return response()->json([
            'status' => true,
            'action' => 'Department Member not Found'
        ]);
    }
    public function deleteDepartmentMember($id)
    {
        $find = DepartmentMember::find($id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => 'Department Member Deleted'
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Department Member Not found'
        ]);
    }


    public function listDMTUsers(Request $request, $type)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockedUser::handle($user->uuid);
        $user_ids = User::whereNotIn('uuid', $blocked)
            ->where('uuid', '!=', $user->uuid)
            ->pluck('uuid')
            ->toArray();
        if ($type == 'management') {
            $users = UserProfileAction::userListWithPaging($user_ids, 12, $user->uuid);
            foreach ($users as $item) {
                $check = Management::where('user_id', $user->uuid)->where('management_id', $item->uuid)->first();
                $item->is_added = false;
                if ($check) {
                    $item->is_added = true;
                }
            }
        }
        if ($type == 'department') {
            $users = UserProfileAction::userListWithPaging($user_ids, 12, $user->uuid);
            foreach ($users as $item) {
                $check = DepartmentMember::where('user_id', $user->uuid)->where('member_id', $item->uuid)->where('department_id', $request->department_id)->first();
                $item->is_added = false;
                if ($check) {
                    $item->is_added = true;
                }
            }
        }
        return response()->json([
            'status' => true,
            'data' => $users,
            'action' => "Users List",
        ]);
    }
    public function searchDMTUsers(Request $request, $type)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockedUser::handle($user->uuid);
        if ($request->keyword) {
            $user_ids = User::whereNotIn('uuid', $blocked)
                ->where('uuid', '!=', $user->uuid)
                ->where(function ($query) use ($request) {
                    $query->where("first_name", "LIKE", "%" . $request->keyword . "%")
                        ->orWhere("last_name", "LIKE", "%" . $request->keyword . "%");
                })
                ->pluck('uuid')
                ->toArray();
            if ($type == 'management') {
                $users = UserProfileAction::userListWithPaging($user_ids, 12, $user->uuid);
                foreach ($users as $item) {
                    $check = Management::where('user_id', $user->uuid)->where('management_id', $item->uuid)->first();
                    $item->is_added = false;
                    if ($check) {
                        $item->is_added = true;
                    }
                }
            }
            if ($type == 'department') {
                $users = UserProfileAction::userListWithPaging($user_ids, 12, $user->uuid);
                foreach ($users as $item) {
                    $check = DepartmentMember::where('user_id', $user->uuid)->where('member_id', $item->uuid)->where('department_id', $request->department_id)->first();
                    $item->is_added = false;
                    if ($check) {
                        $item->is_added = true;
                    }
                }
            }
            return response()->json([
                'status' => true,
                'data' => $users,
                'action' => "Users List",
            ]);
        }
        $users = new stdClass();
        return response()->json([
            'status' => true,
            'data' => $users,
            'action' => "Users List",
        ]);
    }
    public function addMedia(Request $request)
    {
        $user  = User::find($request->user()->uuid);
        $validator = Validator::make($request->all(), [
            'type'  => 'required',
            'media' => 'required',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }
        $create = new UserMedia();
        $create->type = $request->type;
        $create->user_id = $user->uuid;
        if ($request->type == 'image' || $request->type == 'video') {
            $file = $request->file('media');
            $path = FileUploadAction::handle('user/' . $user->uuid . '/media', $file);
            $create->media  = $path;
        } else {
            $create->media = $request->media;
        }
        $create->save();
        $media = UserMedia::where('user_id', $user->uuid)->get();
        return response()->json([
            'status' => true,
            'data' => $media,
            'action' => 'User Media'
        ]);
    }

    public function listMedia(Request $request)
    {
        $user  = User::find($request->user()->uuid);
        $media = UserMedia::where('user_id', $user->uuid)->get();
        return response()->json([
            'status' => true,
            'data' => $media,
            'action' => 'User Media'
        ]);
    }
    public function deleteMedia($id)
    {
        $find = UserMedia::find($id);
        if ($find) {
            Storage::disk('s3')->delete($find->media);
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => 'Media Deleted'
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Media Not found'
        ]);
    }

    public function notification(Request $request, $type)
    {
        $user = User::find($request->user()->uuid);
        if ($user) {
            $blocked = BlockedUser::handle($user->uuid);
            // date_default_timezone_set($user->timezone);
            //            $notis = Notification::select('common')->where('notification_type', $request->type)->where('user_id', $user->id)->whereNotIn('person_id', $blocked)->distinct('common')->latest('common')->Paginate(12);
            $commons = Notification::select('common')->where('notification_type', $type)->where('user_id', $user->uuid)->whereNotIn('person_id', $blocked)->groupBy('common')->get();
            $times = [];
            foreach ($commons as $item)
                $times[] = Notification::where('user_id', $user->uuid)->where('common', $item->common)->whereNotIn('person_id', $blocked)->latest()->first()->time;

            $notis = Notification::select('time', 'common')->whereIn('time', $times)->where('user_id', $user->uuid)->whereNotIn('person_id', $blocked)->latest('time')->Paginate(12);
            foreach ($notis as $index => $notif) {
                $checkDate = explode('+', $notif->common)[0];
                $current = $checkDate;
                if ($index == 0 && !$request->page || $request->page == 1 && $index == 0)
                    $notif->first = true;
                elseif ($index == 0 && $request->page && $request->page != 1) {
                    $notisOld = Notification::select('time', 'common')->whereIn('time', $times)->where('user_id', $user->uuid)->latest('time')->limit(12)->skip(($request->page - 1) * 12)->orderBy('common', 'DESC')->get();
                    $previousDate = explode('+', $notisOld[0]->common)[0];
                    $next = $previousDate;
                    if ($current == $next)
                        $notif->first = false;
                    else
                        $notif->first = true;
                } else {
                    if ($index - 1 >= 0) {
                        $previousDate = explode('+', $notis[$index - 1]->common)[0];
                        $next = $previousDate;
                        if ($current == $next)
                            $notif->first = false;
                        else
                            $notif->first = true;
                    }
                }
                $date = date_format(date_create($checkDate), 'D, d F');
                $tomorrow = date("Y-m-d", strtotime("-1 days"));
                $todayDate = date('Y-m-d');
                if ($checkDate == $tomorrow)
                    $notif->date = 'Yesterday';
                elseif ($checkDate == $todayDate)
                    $notif->date = 'Today';
                else
                    $notif->date = $date;
                $data = Notification::select('id', 'person_id', 'type', 'notification_type', 'body', 'time', 'is_read', 'data_id')->where('user_id', $user->uuid)->where('common', $notif->common)->whereNotIn('person_id', $blocked)->latest()->first();
                if ($data) {
                    $person = UserProfileAction::userCommon($data->person_id, $user->uuid);

                    // $data->user = UserObject::handle($person, $blocked, $user->uuid);
                    $data->user = $person;

                    $post = Post::find($data->data_id);
                    // if ($post) {
                    //     if ($data->type === 'comment')
                    //         $post->comment = Comment::select('id', 'comment')->find($data->sub_data_id);
                    // }
                    $data->post = $post ?: new stdClass();
                } else
                    $data = new stdClass();
                $notif->data = $data;
                $ids = Notification::where('common', $notif->common)->whereNotIn('person_id', $blocked)->distinct()->pluck('person_id');
                $notif->users_count = count($ids);
                $userIds = User::whereIn('uuid', $ids)->take(2)->pluck('uuid')->toArray();
                $notif->users = UserProfileAction::userList($userIds, $user->uuid);
                Notification::where('user_id', $user->uuid)->where('notification_type', $type)->where('is_read', 0)->update(['is_read' => 1]);
            }
            return response()->json(['status' => true, 'data' => $notis, 'action' => 'List of notifications']);
        } else
            return response()->json(['status' => false, 'data' => [], 'action' => 'User not found']);
    }

    public function unreadCounter(Request $request)
    {

        $user = User::find($request->user()->uuid);
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
        $notification_count = Notification::where('user_id', $user->uuid)->where('is_read', 0)->count();

        return response()->json([
            'status' => true,
            'action' =>  'Counter',
            'data' => array(
                'message_count' => $message_count + $unreadGroupCount,
                'group_message_count' => 0,
                'notification_count' => $notification_count,
            )
        ]);
    }

    public function globalSearch(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockedUser::handle($user->uuid);
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
            return response()->json([
                'status' => true,
                'data' => $users,
                'action' => "Users List",
            ]);
        }
        $users = new stdClass();
        return response()->json([
            'status' => true,
            'data' => $users,
            'action' => "Users List",
        ]);
    }

    public function remindMe(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $create = new RemindMe();
        $create->user_id = $user->uuid;
        $create->time = time();
        $create->save();
        return response()->json([
            'status' => true,
            'action' => "Remind Added",
        ]);
    }
}
