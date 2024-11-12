<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCommunityController extends Controller
{
    public function analytics(){
        $total = Community::count();
        return view('community.analytics',compact('total'));
    }

    public function list(){
        $list = Community::latest()->paginate(32);
        return view('community.list',compact('list'));
    }

    public function delete($community_id){
        $find = Community::find($community_id);
        if($find){
            // $find->delete();
        }
        return response()->json(true);
    }

    public function detail($community_id){
        $community = Community::find($community_id);
        if($community){
            
        }
        return redirect()->back();
    }
}
