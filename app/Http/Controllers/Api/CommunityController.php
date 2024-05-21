<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Community\CreateCommunityRequest;
use App\Models\Category;
use App\Models\Community;
use App\Models\CommunityCategories;
use App\Models\CommunityPicture;
use App\Models\CommunitySponsor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use stdClass;

class CommunityController extends Controller
{
    public function create(CreateCommunityRequest $request)
    {
        $user = User::find($request->user()->uuid);
        $categoriesIds = explode(',', $request->categories);


        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/user/' . $user->uuid . '/community/cover', $filename))
                $path = '/uploads/user/' . $user->uuid . '/community/cover/' . $filename;
        }
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/user/' . $user->uuid . '/community/logo', $filename))
                $path1 = '/uploads/user/' . $user->uuid . '/community/logo/' . $filename;
        }
        $create = new Community();
        $create->user_id = $user->uuid;
        $create->cover =  $path;
        $create->logo = $path1;
        $create->name = $request->name;
        $create->tagline = $request->tagline;
        $create->location = $request->location;
        $create->lat = $request->lat;
        $create->lng = $request->lng;
        $create->categories = $request->categories;
        $create->type = $request->type;
        $create->mode = $request->mode;
        $create->price = $request->price;
        $create->description = $request->description;
        $create->save();

        foreach ($categoriesIds as $category) {
            $post_category = new CommunityCategories();
            $post_category->community_id = $create->id;
            $post_category->category_id = $category;
            $post_category->save();
        }

        $new = Community::find($create->id);

        return response()->json([
            'status' => true,
            'action' =>  'Community Added',
            'data' => $new
        ]);
    }

    public function edit(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $categoriesIds = explode(',', $request->categories);
        $create = Community::find($request->community_id);


        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/user/' . $user->uuid . '/community/cover', $filename))
                $path = '/uploads/user/' . $user->uuid . '/community/cover/' . $filename;
            $create->cover =  $path;
        }
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/user/' . $user->uuid . '/community/logo', $filename))
                $path1 = '/uploads/user/' . $user->uuid . '/community/logo/' . $filename;
            $create->logo = $path1;
        }

        if ($request->has('name')) {
            $create->name = $request->name;
        }

        if ($request->has('tagline')) {
            $create->tagline = $request->tagline;
        }

        if ($request->has('location')) {
            $create->location = $request->location;
            $create->lat = $request->lat;
            $create->lng = $request->lng;
        }
        if ($request->has('categories')) {
            $create->categories = $request->categories;
        }

        if ($request->has('type')) {
            $create->type = $request->type;
        }
        if ($request->has('mode')) {
            $create->mode = $request->mode;
        }
        if ($request->has('description')) {
            $create->description = $request->description;
        }
        if ($request->has('price')) {
            $create->price = $request->price;
        }

        if ($request->has('picture')) {
            $files = $request->file('picture');
            foreach ($files as $file) {
                $picture = new CommunityPicture();
                $picture->community_id = $request->community_id;
                $extension = $file->getClientOriginalExtension();
                $mime = explode('/', $file->getClientMimeType());
                $filename = time() . '-' . uniqid() . '.' . $extension;
                if ($file->move('uploads/user/' . $user->uuid . '/community/' . $request->community_id . '/picture', $filename)) {
                    $path = '/uploads/user/' . $user->uuid . '/community/' . $request->community_id . '/picture/' . $filename;
                    $picture->picture = $path;
                }
                $picture->save();

            }
        }

        if ($request->has('website_link')) {
            $create->website_link = $request->website_link;
        }

        if ($request->has('linkedin_link')) {
            $create->linkedin_link = $request->linkedin_link;
        }
        if ($request->has('instagram_link')) {
            $create->instagram_link = $request->instagram_link;
        }
        if ($request->has('facebook_link')) {
            $create->facebook_link = $request->facebook_link;
        }
        if ($request->has('youtube_link')) {
            $create->youtube_link = $request->youtube_link;
        }
        if ($request->has('tiktok_link')) {
            $create->tiktok_link = $request->tiktok_link;
        }

        if ($request->has('sponsor_image') && $request->has('sponsor_link')) {
            $file = $request->file('sponsor_image');
            $sponser = new CommunitySponsor();
            $sponser->community_id = $request->community_id;
            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/user/' . $user->uuid . '/community/' . $request->community_id . '/sponser', $filename))
                $path1 = '/uploads/user/' . $user->uuid . '/community/' . $request->community_id . '/sponser/' . $filename;
            $sponser->image = $path1;

            $sponser->link = $request->sponsor_link;
            $sponser->save();
        }

        $create->save();

        return response()->json([
            'status' => true,
            'action' =>  'Community Edit',
        ]);
    }
    public function listSponsor(Request $request,$commmunity_id){

        $commmunity = Community::find($commmunity_id);
        if($commmunity){
            $list = CommunitySponsor::latest()->get();
            return response()->json([
                'status' => true,
                'action' =>  'Community Sponsors',
                'data' => $list
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Community not Found',
        ]);

    }

    public function deleteSponsor(Request $request,$sponsor_id){
        $sponsor = CommunitySponsor::find($sponsor_id);
        if($sponsor){
            $sponsor->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Sponsor Deleted',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Sponsor not Found',
        ]);
    }

    public function deletePicture(Request $request,$picture_id){
        $sponsor = CommunityPicture::find($picture_id);
        if($sponsor){
            $sponsor->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Picture Deleted',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Picture not Found',
        ]);
    }

    public function home(Request $request){
        $user = User::find($request->user()->uuid);
        $categories = Category::select('id','name','image')->where('type','interest')->get();
        $myCommunities = Community::where('user_id',$user->uuid)->latest()->limit(12)->get();

        foreach($myCommunities as $my){
            $my->participant_count = 0;
            $my->participants = [];
        }

        $allCommunities = Community::where('type','Public')->where('user_id', '!=',$user->uuid)->latest()->paginate(12);

        foreach($allCommunities as $all){
            $all->participant_count = 0;
            $all->participants = [];
        }
        return response()->json([
            'status' => true,
            'action' =>  'Home',
            'data' =>  array(
                'request_count' => 0,
                'categories' => $categories,
                'my_community' => $myCommunities,
                'all_community' => $allCommunities,
            ),
        ]);
    }

    public function search(Request $request){
        $user = User::find($request->user()->uuid);
        if ($request->keyword != null || $request->keyword != '') {
            $communities  = Community::where('user_id','!=',$user->uuid)->where("name", "LIKE", "%" . $request->keyword . "%")->latest()->paginate(12);

            foreach($communities as $all){
                $all->participant_count = 0;
                $all->participants = [];
            }
            return response()->json([
                'status' => true,
                'action' =>  "Communities",
                'data' => $communities
            ]);
        }
        $communities = new stdClass();
        return response()->json([
            'status' => true,
            'action' =>  "Communities",
            'data' => $communities
        ]);
    }
    public function categorySearch(Request $request,$category_id){
        $communityIds = CommunityCategories::where('category_id',$category_id)->pluck('community_id');
        $communities = Community::whereIn('id',$communityIds)->orderBy('id','desc')->paginate(12);
        foreach($communities as $all){
            $all->participant_count = 0;
            $all->participants = [];
        }
        return response()->json([
            'status' => true,
            'action' =>  "Communities",
            'data' => $communities
        ]);
    }
}
