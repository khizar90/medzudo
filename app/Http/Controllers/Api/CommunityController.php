<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Community\CreateCommunityRequest;
use App\Models\Category;
use App\Models\Community;
use App\Models\CommunityCategories;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommunityController extends Controller
{
    public function create(CreateCommunityRequest $request)
    {
        $user = User::find($request->user()->uuid);
        $categoriesIds = explode(',', $request->categories);


        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $path = Storage::disk('local')->put('user/' . $user->uuid. '/community/cover', $file);
        }
        if ($request->hasFile('logo')) {
            $file1 = $request->file('logo');
            $path1 = Storage::disk('local')->put('user/' . $user->uuid . '/community/logo', $file1);
        }
        $create = new Community();
        $create->user_id = $user->uuid;
        $create->cover = $path;
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
}
