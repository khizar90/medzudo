<?php

namespace App\Console\Commands;

use App\Models\CommunityMeetup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MeetupStatusChange extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:meetup-status-change';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Meetup Change Status Start');
        $current_timestamp = time();

        CommunityMeetup::where('end_timestamp', '<', $current_timestamp)->where('status','!=',2)->update(['status' => 2]);
        Log::info('Meetup Change Status End');

    }
}
