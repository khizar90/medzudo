<?php

namespace App\Http\Controllers\Api;

use App\Actions\AppVersions;
use App\Actions\BetaCode;
use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Category;
use App\Models\Faq;
use App\Models\User;
use App\Models\UserBusinessDetail;
use App\Models\UserInterest;
use Illuminate\Http\Request;
use stdClass;

class SettingController extends Controller
{
    public function faqs()
    {
        $list = Faq::all();
        return response()->json([
            'status' => true,
            'action' =>  'Faqs',
            'data' => $list
        ]);
    }

    public function splash(Request $request, $user_id = null)
    {
        $media_url = 'https://d38vqtrl6p25ob.cloudfront.net/';
        $obj = new stdClass();
        $obj1 = new stdClass();
        $appVersions = AppVersions::handle();
        $beta_code = BetaCode::handle();
        if ($user_id != null) {
            $user = User::find($user_id);
            if ($user) {
                $obj->user = $user;
                $userDetail = UserBusinessDetail::where('user_id', $user->uuid)->latest()->first();
                if (!$userDetail) {
                    $userDetail = new stdClass();
                }
                $obj->user->business_detail = $userDetail;
                $interest = UserInterest::where('user_id', $user->uuid)->first();
                if ($interest) {
                    $obj->user->interest_add = true;
                } else {
                    $obj->user->interest_add = false;
                }
                $token = $request->bearerToken();
                if ($token) {
                    $user->token = $token;
                } else {
                    $user->token = '';
                }
                $is_delete = false;
            } else {
                $is_delete = true;
                $obj->user = $obj1;
            }
        } else {
            $obj->user = $obj1;
            $is_delete = false;
        }

        return response()->json([
            'status' => true,
            'action' => "Splash",
            'is_delete' => $is_delete,
            'data' => $obj,
            'media_url' => $media_url,
            'beta_code' => $beta_code,
            'app_versions' => $appVersions,

        ]);
    }

    public function categories($type)
    {
        $categories = Category::select('id', 'name', 'image')->where('type', $type)
            ->orderByRaw("CASE WHEN name = 'No Title' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->get();

        if ($type == 'healthcare-profession' || $type == 'stem-profession' || $type == 'management-profession' ||  $type == 'association-sector' ||  $type == 'society-sector' ||  $type == 'company-sector' ||  $type == 'start-sector' || $type == 'elderly-care' || $type == 'hospital-department') {

            $profession = Category::select('id', 'name', 'image')->where('type', $type)->get();
            foreach ($profession as $item) {
                $specialization  = Category::select('id', 'name', 'image')->where('parent_id', $item->id)->get();
                foreach ($specialization as $item1) {
                    $sub_specialization  = Category::select('id', 'name', 'image')->where('parent_id', $item1->id)->get();
                    $item1->sub_specialization = $sub_specialization;
                }
                $item->specialization = $specialization;
            }
            return response()->json([
                'status' => true,
                'action' => "Categories",
                'data' => $profession,
            ]);
        }

        if ($type == 'hospital-specialization' || $type == 'doctor-specialization'  || $type == 'rehabilitation-specialization') {
            $specialization  = Category::select('id', 'name', 'image')->where('type', $type)->get();
            foreach ($specialization as $item) {
                $sub_specialization  = Category::select('id', 'name', 'image')->where('parent_id', $item->id)->get();
                $item->sub_specialization = $sub_specialization;
            }

            $department = Category::select('id', 'name', 'image')->where('type', 'department')->get();
            $training = Category::select('id', 'name', 'image')->where('type', 'training')->get();

            return response()->json([
                'status' => true,
                'action' => "Categories",
                'data' => array(
                    'specialization' => $specialization,
                    'department' => $department,
                    'training' => $training,
                ),
            ]);
        }
        return response()->json([
            'status' => true,
            'action' => "Categories",
            'data' => $categories,
        ]);
    }
}
