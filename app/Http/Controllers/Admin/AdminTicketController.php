<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use stdClass;

class AdminTicketController extends Controller
{
    public function ticket($status)
    {
       
        if ($status == 'active') {
            $reports = Ticket::where('status', 1)->get();

            foreach ($reports as $report) {
                $user = User::find($report->user_id);
                $category = Category::find($report->category_id);
                $report->user = $user;
                $report->category = $category;
            }
        } else {
            $reports = Ticket::where('status', 0)->get();

            foreach ($reports as $report) {
                $user = User::find($report->user_id);
                $category = Category::find($report->category_id);

                $report->user = $user;
                $report->category = $category;
            }
        }
        return view('ticket.index', compact('reports', 'status'));
    }

    public function messages($ticket_id)
    {
        

        $conversation = Message::where('ticket_id', $ticket_id)
            ->orderBy('created_at', 'asc')
            ->get();

        $ticket = Ticket::find($ticket_id);
        $findUser = User::find($ticket->user_id)->first();
        $cat = Category::find($ticket->category_id);
        return view('ticket.show', compact('conversation', 'findUser', 'cat', 'ticket'));
    }

    public function closeTicket($report_id)
    {
        $obj = new stdClass();
        $report = Ticket::find($report_id);
        if ($report) {
            $report->status = 0;
            $report->save();
            return redirect()->route('dashboard-ticket-ticket', 'active');
        }
    }


    public function sendMessage(Request $request)
    {
        $message = new Message();
        $message->ticket_id = $request->ticket_id;
        $message->to = $request->user_id;
        $message->from = '';
        $message->message = $request->message;
        $message->type = 'text';
        $message->time = strtotime(date('Y-m-d H:i:s'));
        $message->save();
        return response()->json($message);
    }
}
