<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Forum;
use App\Models\News;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function report($type)
    {

        if ($type == 'individual') {

            $reports = Report::where('type', 'individual')->paginate('100');

            foreach ($reports as $item) {
                $user = User::find($item->user_id);
                $reported_user = User::find($item->reported_id);
                $item->user = $user;
                $item->reported_user = $reported_user;
            }

            return view('report.user', compact('reports'));
        }

        if ($type == 'facility') {

            $reports = Report::where('type', 'facility')->paginate('100');

            foreach ($reports as $item) {
                $user = User::find($item->user_id);
                $reported_user = User::find($item->reported_id);
                $item->user = $user;
                $item->reported_user = $reported_user;
            }

            return view('report.facility', compact('reports'));
        }

        if ($type == 'organization') {

            $reports = Report::where('type', 'organization')->paginate('100');

            foreach ($reports as $item) {
                $user = User::find($item->user_id);
                $reported_user = User::find($item->reported_id);
                $item->user = $user;
                $item->reported_user = $reported_user;
            }

            return view('report.organization', compact('reports'));
        }
        

        if ($type == 'forum') {

            $reports = Report::where('type', 'forum')->paginate('100');

            foreach ($reports as $item) {
                $user = User::find($item->user_id);
                $reported_user = Forum::find($item->reported_id);
                $item->user = $user;
                $item->reported_user = $reported_user;
            }

            return view('report.forum', compact('reports'));
        }

        if ($type == 'news') {

            $reports = Report::where('type', 'news')->paginate('100');

            foreach ($reports as $item) {
                $user = User::find($item->user_id);
                $reported_user = News::find($item->reported_id);
                $item->user = $user;
                $item->reported_user = $reported_user;
            }

            return view('report.news', compact('reports'));
        }

        if ($type == 'event') {

            $reports = Report::where('type', 'event')->paginate('100');

            foreach ($reports as $item) {
                $user = User::find($item->user_id);
                $reported_user = Event::find($item->reported_id);
                $item->user = $user;
                $item->reported_user = $reported_user;
            }

            return view('report.event', compact('reports'));
        }


        // if ($type == 'post') {

        //     $reports = Report::where('type', 'post')->paginate('100');

        //     foreach ($reports as $item) {
        //         $user = User::find($item->user_id);
        //         $item->user = $user;
        //         $post = Post::where('id', $item->reported_id)->first();
        //         $item->post = $post;
        //     }

        //     return view('report.post', compact('reports'));
        // }
    }

    public function deleteReport($id)
    {
        $find = Report::find($id);
        $find->delete();
        return redirect()->back();
    }

    public function deleteUser($user_id, $report_id)
    {
        $find = User::find($user_id);
        Report::where('user_id',$user_id)->orWhere('reported_id',$user_id)->delete();
        $find->delete();
        return redirect()->back();
    }

    public function deleteForum($user_id, $report_id)
    {

        $find = Forum::find($user_id);
        Report::where('type', 'forum')->where('reported_id', $user_id)->delete();
        $find->delete();
        return redirect()->back();
    }

    public function deleteEvent($event_id, $report_id)
    {
        $find = Event::find($event_id);
        Report::where('type', 'event')->where('reported_id', $event_id)->delete();
        $find->delete();
        return redirect()->back();
    }

    public function deleteNews($news_id, $report_id)
    {
        $find = News::find($news_id);
        Report::where('type', 'news')->where('reported_id', $news_id)->delete();
        $find->delete();
        return redirect()->back();
    }

    public function deletePost($post_id, $report_id)
    {
        // $find = Post::find($post_id);
        // Report::where('type', 'post')->where('reported_id', $post_id)->deleye();
        // $find->delete();
        // return redirect()->back();
    }
}
