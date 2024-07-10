<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Community\CommunityAddFolderRequest;
use App\Http\Requests\Api\Community\CommunityAddMediaRequest;
use App\Http\Requests\Api\Community\CommunityEditFolderRequest;
use App\Http\Requests\Api\Community\CommunityPurchaseCourseRequest;
use App\Http\Requests\Api\Community\CreateCommunityCourseRequest;
use App\Http\Requests\Api\Community\CreateCommunityCourseSectionRequest;
use App\Http\Requests\Api\Community\CreateCommunityCourseSectionVideoRequest;
use App\Http\Requests\Api\Community\CreateCommunityRequest;
use App\Models\BlockList;
use App\Models\Category;
use App\Models\Community;
use App\Models\CommunityCategories;
use App\Models\CommunityCourse;
use App\Models\CommunityCourseCertificate;
use App\Models\CommunityCoursePurchase;
use App\Models\CommunityCourseSection;
use App\Models\CommunityCourseSectionVideo;
use App\Models\CommunityCourseSectionVideoSeen;
use App\Models\CommunityFolder;
use App\Models\CommunityJoinRequest;
use App\Models\CommunityMedia;
use App\Models\CommunityPicture;
use App\Models\CommunityPinnedMedia;
use App\Models\CommunityPost;
use App\Models\CommunityPostComment;
use App\Models\CommunityPostLike;
use App\Models\CommunityPostSave;
use App\Models\CommunityPostVote;
use App\Models\CommunitySectionSeen;
use App\Models\CommunitySectionVideoSeen;
use App\Models\CommunitySponsor;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use stdClass;
use PDF;

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

        $joinRequest = new CommunityJoinRequest();
        $joinRequest->user_id = $user->uuid;
        $joinRequest->community_id = $new->id;
        $joinRequest->status = 'accept';
        $joinRequest->save();

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
    public function listSponsor(Request $request, $community_id)
    {

        $community = Community::find($community_id);
        if ($community) {
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

    public function deleteSponsor(Request $request, $sponsor_id)
    {
        $sponsor = CommunitySponsor::find($sponsor_id);
        if ($sponsor) {
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

    public function deletePicture(Request $request, $picture_id)
    {
        $sponsor = CommunityPicture::find($picture_id);
        if ($sponsor) {
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

    public function home(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $categoriesall = Category::select('id', 'name', 'image')->where('type', 'interest')->get();
        $myCommunities = Community::where('user_id', $user->uuid)->latest()->limit(12)->get();
        foreach ($myCommunities as $my) {
            $categoriesIds  = explode(',', $my->categories);
            $categories = Category::whereIn('id', $categoriesIds)->get();
            $my->categories = $categories;
            $pictures = CommunityPicture::where('community_id', $my->id)->get();
            $my->pictures = $pictures;
            $my->participant_count = CommunityJoinRequest::where('community_id', $my->id)->where('status', 'accept')->count();
            $participantIds = CommunityJoinRequest::where('community_id', $my->id)->where('status', 'accept')->pluck('user_id');
            $participants = User::whereIn('uuid', $participantIds)->limit(3)->pluck('image');
            $my->participants = $participants;
        }


        $allCommunities = Community::where('mode', 'Public')->where('user_id', '!=', $user->uuid)->latest()->paginate(12);

        foreach ($allCommunities as $all) {
            $categoriesIds  = explode(',', $all->categories);
            $categories = Category::whereIn('id', $categoriesIds)->get();
            $all->categories = $categories;
            $pictures = CommunityPicture::where('community_id', $all->id)->get();
            $all->pictures = $pictures;
            $all->participant_count = CommunityJoinRequest::where('community_id', $all->id)->where('status', 'accept')->count();
            $participantIds = CommunityJoinRequest::where('community_id', $all->id)->where('status', 'accept')->pluck('user_id');
            $participants = User::whereIn('uuid', $participantIds)->limit(3)->pluck('image');
            $all->participants = $participants;
        }

        $request_count = CommunityJoinRequest::where('user_id', $user->uuid)->where('status', 'penidng')->count();
        return response()->json([
            'status' => true,
            'action' =>  'Home',
            'data' =>  array(
                'request_count' => $request_count,
                'categories' => $categoriesall,
                'my_community' => $myCommunities,
                'all_community' => $allCommunities,
            ),
        ]);
    }

    public function list(Request $request, $type)
    {
        $user = User::find($request->user()->uuid);
        if ($type == 'my-communities') {
            $communities = Community::where('user_id', $user->uuid)->latest()->paginate(12);
            foreach ($communities as $item) {
                $categoriesIds  = explode(',', $item->categories);
                $categories = Category::whereIn('id', $categoriesIds)->get();
                $item->categories = $categories;
                $pictures = CommunityPicture::where('community_id', $item->id)->get();
                $item->pictures = $pictures;
                $item->participant_count = CommunityJoinRequest::where('community_id', $item->id)->where('status', 'accept')->count();
                $participantIds = CommunityJoinRequest::where('community_id', $item->id)->where('status', 'accept')->pluck('user_id');
                $participants = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->whereIn('uuid', $participantIds)->limit(3)->pluck('image');
                $item->participants = $participants;
            }
        }
        return response()->json([
            'status' => true,
            'action' =>  "Communities",
            'data' => $communities
        ]);
    }

    public function search(Request $request)
    {
        $user = User::find($request->user()->uuid);
        if ($request->keyword != null || $request->keyword != '') {
            $communities  = Community::where("name", "LIKE", "%" . $request->keyword . "%")->latest()->paginate(12);

            foreach ($communities as $all) {
                $categoriesIds  = explode(',', $all->categories);
                $categories = Category::whereIn('id', $categoriesIds)->get();
                $all->categories = $categories;
                $pictures = CommunityPicture::where('community_id', $all->id)->get();
                $all->pictures = $pictures;
                $all->participant_count = CommunityJoinRequest::where('community_id', $all->id)->where('status', 'accept')->count();
                $participantIds = CommunityJoinRequest::where('community_id', $all->id)->where('status', 'accept')->pluck('user_id');
                $participants = User::whereIn('uuid', $participantIds)->limit(3)->pluck('image');
                $all->participants = $participants;
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
    public function categorySearch(Request $request, $category_id)
    {
        $communityIds = CommunityCategories::where('category_id', $category_id)->pluck('community_id');
        $communities = Community::whereIn('id', $communityIds)->orderBy('id', 'desc')->paginate(12);
        foreach ($communities as $all) {
            $categoriesIds  = explode(',', $all->categories);
            $categories = Category::whereIn('id', $categoriesIds)->get();
            $all->categories = $categories;
            $pictures = CommunityPicture::where('community_id', $all->id)->get();
            $all->pictures = $pictures;
            $all->participant_count = CommunityJoinRequest::where('community_id', $all->id)->where('status', 'accept')->count();
            $participantIds = CommunityJoinRequest::where('community_id', $all->id)->where('status', 'accept')->pluck('user_id');
            $participants = User::whereIn('uuid', $participantIds)->limit(3)->pluck('image');
            $all->participants = $participants;
        }
        return response()->json([
            'status' => true,
            'action' =>  "Communities",
            'data' => $communities
        ]);
    }

    public function detail(Request $request, $community_id, $type)
    {
        $user = User::find($request->user()->uuid);
        $community = Community::find($community_id);
        if ($community) {

            if ($type == 'courses') {
                $courses = CommunityCourse::where('community_id', $community_id)->paginate(12);
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
                    'action' =>  'Course  list',
                    'data' => $courses
                ]);
            }
            if ($type == 'about') {
                $categoriesIds  = explode(',', $community->categories);
                $categories = Category::whereIn('id', $categoriesIds)->get();
                $community->categories = $categories;
                $pictures = CommunityPicture::where('community_id', $community->id)->get();
                $community->pictures = $pictures;
                $community->participant_count = CommunityJoinRequest::where('community_id', $community->id)->where('status', 'accept')->count();
                $participantIds = CommunityJoinRequest::where('community_id', $community->id)->where('status', 'accept')->pluck('user_id');
                $participants = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->whereIn('uuid', $participantIds)->limit(3)->pluck('image');
                $community->participants = $participants;
                $community->sponsor = CommunitySponsor::where('community_id', $community->id)->get();
                $community->user =  User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->where('uuid', $community->user_id)->first();
                return response()->json([
                    'status' => true,
                    'action' =>  'About',
                    'data' => $community
                ]);
            }

            if ($type == 'feed') {
                $posts = CommunityPost::where('community_id', $community_id)->latest()->paginate(12);
                foreach ($posts as $post) {
                    $postby = User::where('uuid', $post->user_id)->select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->first();
                    $comment_count = CommunityPostComment::where('post_id', $post->id)->count();
                    $like_count = CommunityPostLike::where('post_id', $post->id)->count();
                    $likestatus = CommunityPostLike::where('post_id', $post->id)->where('user_id', $user->uuid)->first();
                    $saved = CommunityPostSave::where('post_id', $post->id)->where('user_id', $user->uuid)->first();
                    $post->media = empty($post->media) ? [] : explode(',', $post->media);
                    $likes = CommunityPostLike::where('post_id', $post->id)->latest()->limit(3)->pluck('user_id');
                    $like_users = User::select('uuid', 'first_name', 'last_name', 'image')->whereIn('uuid', $likes)->get();
                    if ($likestatus) {
                        $post->is_liked = true;
                    } else {
                        $post->is_liked = false;
                    }

                    if ($saved) {
                        $post->is_saved = true;
                    } else {
                        $post->is_saved = false;
                    }
                    $total_vote_count = 0;
                    $my_voted_option = '';
                    $option_1_count = 0;
                    $option_2_count = 0;
                    $option_3_count = 0;
                    $option_4_count = 0;
                    if ($post->type == 'poll') {
                        $total_vote_count = CommunityPostVote::where('post_id', $post->id)->count();
                        $checkVote = CommunityPostVote::where('user_id', $user->uuid)->where('post_id', $post->id)->first();
                        if ($checkVote) {
                            $my_voted_option = $checkVote->option;
                        }
                        $option_1_count = CommunityPostVote::where('post_id', $post->id)->where('option', 'option_1')->count();
                        $option_2_count = CommunityPostVote::where('post_id', $post->id)->where('option', 'option_2')->count();
                        $option_3_count = CommunityPostVote::where('post_id', $post->id)->where('option', 'option_3')->count();
                        $option_4_count = CommunityPostVote::where('post_id', $post->id)->where('option', 'option_4')->count();

                        if ($total_vote_count > 0) {
                            $option_1_count = $option_1_count / $total_vote_count * 100;
                            $option_2_count = $option_2_count / $total_vote_count * 100;
                            $option_3_count = $option_3_count / $total_vote_count * 100;
                            $option_4_count = $option_4_count / $total_vote_count * 100;
                        }
                    }
                    $post->my_voted_option = $my_voted_option;

                    $post->total_vote_count = $total_vote_count;
                    $post->option_1_count = $option_1_count;
                    $post->option_2_count = $option_2_count;
                    $post->option_3_count = $option_3_count;
                    $post->option_4_count = $option_4_count;


                    $post->comment_count = $comment_count;
                    $post->like_count = $like_count;
                    $post->like_users = $like_users;
                    $post->user = $postby;
                }

                return response()->json([
                    'status' => true,
                    'action' =>  'Feed',
                    'data' => $posts
                ]);
            }
        }
        return response()->json([
            'status' => false,
            'action' =>  'Community Not Found',
        ]);
    }


    public function listUser(Request $request, $community_id)
    {
        $user = User::find($request->user()->uuid);
        if ($user) {

            $blocked = BlockList::where('user_id', $user->uuid)->pluck('block_id');
            $blocked1 = BlockList::where('block_id', $user->uuid)->pluck('user_id');
            $blocked = $blocked->merge($blocked1);

            $followingIds = Follow::where('user_id', $user->uuid)->pluck('follow_id');

            $followingIds = Follow::where('user_id', $user->uuid)->whereNotIn('follow_id', $blocked)->pluck('follow_id')->toArray();

            $followings = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->whereIn('uuid', $followingIds)->paginate(12);
            if (count($followings) > 0) {

                foreach ($followings as $item) {
                    $find = CommunityJoinRequest::where('user_id', $item->uuid)->where('community_id', $community_id)->first();
                    if ($find) {
                        $item->is_invited = true;
                    } else {
                        $item->is_invited = false;
                    }
                }
                return response()->json([
                    'status' => true,
                    'action' =>  'Following',
                    'data' => $followings
                ]);
            } else {
                $users = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->where('uuid', '!=', $user->uuid)->whereNotIn('follow_id', $blocked)->paginate(12);
                foreach ($users as $item1) {
                    $find = CommunityJoinRequest::where('user_id', $item1->uuid)->where('community_id', $community_id)->first();
                    if ($find) {
                        $item1->is_invited = true;
                    } else {
                        $item1->is_invited = false;
                    }
                }
                return response()->json([
                    'status' => true,
                    'action' =>  'Users',
                    'data' => $users
                ]);
            }
        }
        return response()->json([
            'status' => false,
            'action' =>  'User not found',
        ]);
    }

    public function sendInvite(Request $request, $community_id, $to_id)
    {
        $user = User::find($request->user()->uuid);
        $find = CommunityJoinRequest::where('user_id', $to_id)->where('community_id', $community_id)->where('status', 'pending')->first();
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Invite Deleted',
            ]);
        }

        $create = new CommunityJoinRequest();
        $create->user_id = $to_id;
        $create->community_id = $community_id;
        $create->save();
        return response()->json([
            'status' => true,
            'action' =>  'Invite Send',
        ]);
    }

    public function searchUsers(Request $request, $community_id)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockList::where('user_id', $user->uuid)->pluck('block_id');
        $blocked1 = Blocklist::where('block_id', $user->uuid)->pluck('user_id');
        $blocked = $blocked->merge($blocked1);

        if ($request->keyword != null || $request->keyword != '') {

            $users  = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->whereNotIn('uuid', $blocked)->where('uuid', '!=', $user->uuid)->where("first_name", "LIKE", "%" . $request->keyword . "%")->orWhere("last_name", "LIKE", "%" . $request->keyword . "%")->latest()->paginate(12);

            foreach ($users as $item1) {
                $find = CommunityJoinRequest::where('user_id', $item1->uuid)->where('community_id', $community_id)->first();
                if ($find) {
                    $item1->is_invited = true;
                } else {
                    $item1->is_invited = false;
                }
            }
            return response()->json([
                'status' => true,
                'action' =>  'Search Result',
                'data' => $users
            ]);
        } else {
            $users = new stdClass();
            return response()->json([
                'status' => true,
                'action' =>  'Search Result',
                'data' => $users
            ]);
        }
    }

    public function InvitedCommunity(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $communityIds = CommunityJoinRequest::where('user_id', $user->uuid)->where('status', 'pending')->pluck('community_id');
        $communities = Community::whereIn('id', $communityIds)->orderBy('id', 'desc')->paginate(12);
        foreach ($communities as $item) {
            $categoriesIds  = explode(',', $item->categories);
            $categories = Category::whereIn('id', $categoriesIds)->get();
            $item->categories = $categories;
            $pictures = CommunityPicture::where('community_id', $item->id)->get();
            $item->pictures = $pictures;
            $item->participant_count = CommunityJoinRequest::where('community_id', $item->id)->where('status', 'accept')->count();
            $participantIds = CommunityJoinRequest::where('community_id', $item->id)->where('status', 'accept')->pluck('user_id');
            $participants = User::whereIn('uuid', $participantIds)->limit(3)->pluck('image');
            $item->participants = $participants;
        }
        return response()->json([
            'status' => true,
            'action' =>  'Communities',
            'data' => $communities
        ]);
    }

    public function delete(Request $request, $id)
    {
        $find = Community::find($id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Community Deleted!',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Community not found',
        ]);
    }

    public function addMedia(CommunityAddMediaRequest $request)
    {

        $create = new CommunityMedia();
        $file = $request->file('media');
        $community = Community::find($request->community_id);
        $path = Storage::disk('local')->put('user/' . $community->user_id . '/community/media', $file);
        $filename = basename($path);
        $create->media = '/uploads/' . $path;
        $create->tagline = $request->tagline;
        $create->community_id = $request->community_id;
        $create->type = $request->type;
        if ($request->type == 'video') {
            $thumbnailPath = $this->getVideoThumb($create->media);
            $create->thumbnail = $thumbnailPath;
        }
        $create->folder_id = $request->folder_id ?: 0;
        $create->save();
        return response()->json([
            'status' => true,
            'action' =>  'Community media Added',
        ]);
    }
    function getVideoThumb($path)
    {
        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open(public_path($path));
        $thumbnailFileName = time() . '-' . uniqid() . '.jpg';
        $thumbnailPath = '/uploads/thumbnails/' . $thumbnailFileName;
        $video->frame(TimeCode::fromSeconds(1))->save(public_path($thumbnailPath));
        return $thumbnailPath;
    }

    public function deleteMedia($media_id)
    {
        $find = CommunityMedia::find($media_id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Community media Deleted!',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Community media not found',
        ]);
    }

    public function addFolder(CommunityAddFolderRequest $request)
    {
        $create = new CommunityFolder();
        $create->community_id = $request->community_id;
        $create->name = $request->name;
        $create->price = $request->price ?: 0;
        $create->save();
        return response()->json([
            'status' => true,
            'action' =>  'Community Folder Added',
        ]);
    }

    public function folderMedia($folder_id)
    {
        $media = CommunityMedia::where('folder_id', $folder_id)->paginate(12);
        return response()->json([
            'status' => true,
            'action' =>  'Community Folder Media',
            'data' => $media
        ]);
    }
    public function editFolder(CommunityEditFolderRequest $request)
    {
        $folder = CommunityFolder::find($request->folder_id);
        if ($request->has('name')) {
            $folder->name = $request->name;
        }

        if ($request->has('price')) {
            $folder->price = $request->price;
        }
        $folder->save();
        return response()->json([
            'status' => true,
            'action' =>  'Community Folder Edit',
        ]);
    }
    public function deleteFolder($folder_id)
    {
        $find = CommunityFolder::find($folder_id);
        if ($find) {
            CommunityMedia::where('folder_id', $folder_id)->delete();
            $find->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Community Folder Deleted!',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Community Folder not found',
        ]);
    }

    public function pinMedia($media_id, $community_id)
    {

        $community = Community::find($community_id);
        if ($community) {
            $media = CommunityMedia::find($media_id);
            if ($media) {
                $find = CommunityPinnedMedia::where('community_id', $community_id)->where('media_id', $media_id)->first();
                if ($find) {
                    $find->delete();
                    return response()->json([
                        'status' => true,
                        'action' =>  'Un Pinned',
                    ]);
                }
                $create = new CommunityPinnedMedia();
                $create->community_id = $community_id;
                $create->media_id = $media_id;
                $create->save();
                return response()->json([
                    'status' => true,
                    'action' =>  'Pinned',
                ]);
            }
            return response()->json([
                'status' => false,
                'action' =>  'Community media not found',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Community not found',
        ]);
    }

    public function communitMediaHome($community_id)
    {
        $community = Community::find($community_id);
        if ($community) {
            $pinnedMedia = [];
            $pinnedMediaIds = CommunityPinnedMedia::where('community_id', $community_id)->orderBy('id', 'desc')->limit(12)->pluck('media_id');
            foreach ($pinnedMediaIds as $id) {
                $media = CommunityMedia::find($id);
                $pinnedMedia[] = $media;
            }
            $folders = CommunityFolder::where('community_id', $community_id)->limit(12)->get();
            $media = CommunityMedia::where('community_id', $community_id)->where('folder_id', 0)->latest()->paginate(12);
            return response()->json([
                'status' => true,
                'action' =>  'Community Media Home',
                'data' => array(
                    'pinned_media' => $pinnedMedia,
                    'folders' => $folders,
                    'media' => $media
                )
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Community not found',
        ]);
    }
    public function listFolder(Request $request, $type, $community_id)
    {
        $community = Community::find($community_id);

        if ($community) {
            if ($type == 'folders') {
                $folders = CommunityFolder::where('community_id', $community_id)->paginate(12);
                return response()->json([
                    'status' => true,
                    'action' =>  'Community Folders',
                    'data' => $folders
                ]);
            }
            if ($type == 'pinned') {

                $pinnedMedia = [];
                $pinnedMediaIds = CommunityPinnedMedia::where('community_id', $community_id)->orderBy('id', 'desc')->pluck('media_id');
                foreach ($pinnedMediaIds as $id) {
                    $media = CommunityMedia::find($id);
                    $pinnedMedia[] = $media;
                }

                $count  = count($pinnedMedia);
                $pinnedMedia = collect($pinnedMedia);
                $pinnedMedia = $pinnedMedia->forPage($request->page, 12)->values();

                return response()->json([
                    'status' => true,
                    'action' =>  'Community Pinned Media',
                    'data' => array(
                        'data' => $pinnedMedia,
                        'total' => $count
                    )
                ]);
            }
        }
        return response()->json([
            'status' => false,
            'action' =>  'Community not found',
        ]);
    }

    public function createCourse(CreateCommunityCourseRequest $request)
    {
        $user = User::find($request->user()->uuid);
        $community = Community::find($request->community_id);
        if ($community) {
            $file = $request->file('image');

            $path = Storage::disk('local')->put('user/' . $community->user_id . '/community/course', $file);

            $imagePath = public_path('/uploads/' . $path);
            list($width, $height) = getimagesize($imagePath);

            $size = $width / $height;
            $size = number_format($size, 2);

            $create = new CommunityCourse();
            $create->image = '/uploads/' . $path;
            $create->size = $size;
            $create->user_id = $user->uuid;
            $create->community_id = $request->community_id;
            $create->title = $request->title;
            $create->point = $request->point;
            $create->description = $request->description;
            $create->price = $request->price;
            $create->save();
            return response()->json([
                'status' => true,
                'action' =>  'Community Course Added',
                'data' => $create
            ]);
        }

        return response()->json([
            'status' => false,
            'action' =>  'Community not found',
        ]);
    }
    public function editCourse(Request $request)
    {
        $create = CommunityCourse::find($request->course_id);
        $community = Community::find($create->community_id);
        if ($create) {
            if ($request->has('image')) {
                $file = $request->file('image');
                $path = Storage::disk('local')->put('user/' . $community->user_id . '/community/course', $file);
                $create->image = '/uploads/' . $path;
            }
            if ($request->has('title')) {
                $create->title = $request->title;
            }
            if ($request->has('description')) {
                $create->description = $request->description;
            }
            if ($request->has('price')) {
                $create->price = $request->price;
            }
            if ($request->has('point')) {
                $create->point = $request->point;
            }
            $create->save();
            return response()->json([
                'status' => true,
                'action' =>  'Community Course Edit',
                'data' => $create
            ]);
        }

        return response()->json([
            'status' => false,
            'action' =>  'Course not found',
        ]);
    }

    public function deleteCourse($course_id)
    {
        $course = CommunityCourse::find($course_id);
        if ($course) {
            $course->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Community Deleted!',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Course not found',
        ]);
    }



    public function detailCourse(Request $request, $course_id)
    {
        $user = User::find($request->user()->uuid);
        $course = CommunityCourse::find($course_id);
        if ($course) {
            $course->section_count = CommunityCourseSection::where('course_id', $course->id)->count();
            $duration  = CommunityCourseSectionVideo::where('course_id', $course->id)->sum('duration');
            $course->duration_count = $duration;
            $videos_count = CommunityCourseSectionVideo::where('course_id', $course->id)->count();
            $seen_count = CommunityCourseSectionVideoSeen::where('user_id', $user->uuid)->where('course_id', $course->id)->count();
            if ($videos_count > 0) {
                $average_seen = $seen_count / $videos_count;
                $average_seen = $average_seen * 100;
            } else {
                $average_seen = 0; // or handle the case when there are no videos
            }
            $course->progress = $average_seen;
            $course->user = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->where('uuid', $course->user_id)->first();

            $is_purchase = CommunityCoursePurchase::where('user_id', $user->uuid)->where('course_id', $course->id)->first();
            if ($is_purchase) {
                $course->is_purchase = true;
            } else {
                $course->is_purchase = false;
            }
            return response()->json([
                'status' => true,
                'action' =>  'Course  Detail',
                'data' => $course
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Course not found',
        ]);
    }

    public function courseSectionList($course_id)
    {
        $course = CommunityCourse::find($course_id);
        if ($course) {
            $list = CommunityCourseSection::where('course_id', $course_id)->get();
            return response()->json([
                'status' => true,
                'action' =>  'Community Course Sections',
                'data' => $list
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Course not found',
        ]);
    }

    public function createCourseSection(CreateCommunityCourseSectionRequest $request)
    {
        $create = new CommunityCourseSection();
        $create->course_id = $request->course_id;
        $create->title = $request->title;
        $create->save();
        return response()->json([
            'status' => true,
            'action' =>  'Community Course Section Added',
            'data' => $create
        ]);
    }

    public function editCourseSection(Request $request)
    {
        $create = CommunityCourseSection::find($request->section_id);
        if ($create) {
            if ($request->has('title')) {
                $create->title = $request->title;
            }
            $create->save();
            return response()->json([
                'status' => true,
                'action' =>  'Section Edit',
                'data' => $create
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Section not found',
        ]);
    }
    public function deleteCourseSection($section_id)
    {
        $section = CommunityCourseSection::find($section_id);
        if ($section) {
            $section->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Section Deleted!',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Section not found',
        ]);
    }

    public function listCourseSectionVideos(Request $request, $section_id)
    {
        $user = User::find($request->user()->uuid);
        $section = CommunityCourseSection::find($section_id);
        if ($section) {
            $list = CommunityCourseSectionVideo::where('section_id', $section_id)->get();
            foreach ($list as $item) {
                $find = CommunityCourseSectionVideoSeen::where('user_id', $user->uuid)->where('video_id', $item->id)->first();
                if ($find) {
                    $item->is_seen = true;
                } else {
                    $item->is_seen = false;
                }
            }
            return response()->json([
                'status' => true,
                'action' =>  'Course Sections Videos',
                'data' => $list
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Section not found',
        ]);
    }
    public function createCourseSectionVideo(CreateCommunityCourseSectionVideoRequest $request)
    {
        $user = User::Find($request->user()->uuid);
        $section = CommunityCourseSection::find($request->section_id);
        $create = new CommunityCourseSectionVideo();
        $file = $request->file('video');
        $path = Storage::disk('local')->put('user/' . $user->uuid . '/community/course/seaction', $file);
        $create->video = '/uploads/' . $path;
        $thumbnailPath = $this->getVideoThumb($create->video);
        $create->thumbnail = $thumbnailPath;
        $create->title = $request->title;
        $create->course_id = $section->course_id;
        $create->duration = $request->duration;
        $create->description = $request->description;
        $create->section_id = $request->section_id;
        $create->save();
        return response()->json([
            'status' => true,
            'action' =>  'Section Video Added',
            'data' => $create
        ]);
    }

    public function editCourseSectionVideo(Request $request)
    {
        $user = User::Find($request->user()->uuid);

        $create = CommunityCourseSectionVideo::find($request->video_id);
        if ($create) {
            if ($request->has('video')) {
                $file = $request->file('video');
                $path = Storage::disk('local')->put('user/' . $user->uuid . '/community/course/seaction', $file);
                $create->video = '/uploads/' . $path;
                $thumbnailPath = $this->getVideoThumb($create->video);
                $create->thumbnail = $thumbnailPath;
            }
            if ($request->has('title')) {
                $create->title = $request->title;
            }
            if ($request->has('description')) {
                $create->description = $request->description;
            }
            if ($request->has('duration')) {
                $create->duration = $request->duration;
            }
            $create->save();
            return response()->json([
                'status' => true,
                'action' =>  'Section Video Edit',
                'data' => $create
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Video not found',
        ]);
    }

    public function deleteCourseSectionVideo($video_id)
    {
        $video = CommunityCourseSectionVideo::find($video_id);
        if ($video) {
            $video->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Section Video Deleted!',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Section Vidoe not found',
        ]);
    }


    public function publishCourse($course_id)
    {
        $course = CommunityCourse::find($course_id);
        if ($course) {
            $course->is_publish  = 1;
            $course->save();
            return response()->json([
                'status' => true,
                'action' =>  'Course Publish!',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Course not Found',
        ]);
    }

    public function generateCertificate()
    {
        $data = [
            'name' => 'John Doe',
            'course' => 'Laravel Development',
            'date' => date('m/d/Y')
        ];

        $pdf = PDF::loadView('certificate', $data);

        return $pdf->stream('.pdf');
    }

    public function seenSection(Request $request, $video_id)
    {
        $user = User::find($request->user()->uuid);
        $find = CommunityCourseSectionVideoSeen::where('user_id', $user->uuid)->where('video_id', $video_id)->first();
        if ($find) {
            return response()->json([
                'status' => true,
                'action' =>  'Section Video seen',
            ]);
        }
        $video = CommunityCourseSectionVideo::find($video_id);
        $section = CommunityCourseSection::find($video->section_id);

        $create =  new CommunityCourseSectionVideoSeen();
        $create->course_id = $section->course_id;
        $create->section_id = $section->id;
        $create->video_id = $video->id;
        $create->user_id = $user->uuid;
        $create->save();

        $videos_count = CommunityCourseSectionVideo::where('course_id', $video->course_id)->count();
        $seen_count = CommunityCourseSectionVideoSeen::where('user_id', $user->uuid)->where('course_id', $video->course_id)->count();
        if ($videos_count > 0) {
            $average_seen = $seen_count / $videos_count;
            $average_seen = $average_seen * 100;
            if ($average_seen == 100) {
                $check = CommunityCourseCertificate::where('user_id', $user->uuid)->where('course_id', $video->course_id)->first();
                if (!$check) {
                    $create = new CommunityCourseCertificate();
                    $create->user_id = $user->uuid;
                    $create->course_id = $video->course_id;
                    $create->time = time();
                    $create->save();
                }
            }
        }
        return response()->json([
            'status' => true,
            'action' =>  'Section Video seen',
        ]);
    }

    public function viewCourseCeritificate(Request $request, $course_id)
    {
        $user = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->where('uuid', $request->user()->uuid)->first();
        $find = CommunityCourseCertificate::where('user_id', $user->uuid)->where('course_id', $course_id)->first();
        $course = CommunityCourse::find($course_id);
        if ($course) {
            $course_by = User::select('uuid', 'first_name', 'last_name', 'image', 'email', 'verify', 'account_type', 'username', 'position')->where('uuid', $course->user_id)->first();
            $course->author = $course_by;
        }
        if ($find) {
            $find->course = $course;
            $find->user = $user;
            return response()->json([
                'status' => true,
                'action' =>  'Ceritficate',
                'data' =>  $find
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Ceritficate not Found',
        ]);
    }

    public function storeCourseCeritificate(Request $request, $crtf_id)
    {
        $user = User::find($request->user()->uuid);
        $find = CommunityCourseCertificate::find($crtf_id);
        if ($find) {
            if ($request->has('media')) {
                $file = $request->file('media');
                $path = Storage::disk('local')->put('user/' . $user->uuid . '/course/certificate', $file);
                $find->media  = '/uploads/' . $path;
                $find->save();
                return response()->json([
                    'status' => true,
                    'action' =>  'Ceritficate',
                    'data' =>  $find
                ]);
            }
            return response()->json([
                'status' => false,
                'action' =>  'Ceritficate not Found',
            ]);
        }
    }

    public function purchaseCourse(CommunityPurchaseCourseRequest $request)
    {
        $user = User::find($request->user()->uuid);
        $create = new CommunityCoursePurchase();
        $create->user_id = $user->uuid;
        $create->course_id = $request->course_id;
        $create->price = $request->price;
        $create->save();
        return response()->json([
            'status' => true,
            'action' =>  'Course Purchase',
            'data' => $create
        ]);
    }
}
