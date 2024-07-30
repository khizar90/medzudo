<?php

namespace App\Console\Commands;

use App\Models\CommunityMeetup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class StartMeetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:start-meetup';

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
        Log::info('start');
        $current_time = time();
        $meetups= CommunityMeetup::where('status', 0)->where('start_timestamp', '<=', $current_time)->get();
        foreach ($meetups as $meetup) {
            $meetup->status = 1;
            $end_time = Carbon::createFromTimestamp($meetup->end_timestamp);
            $new_end_time = $end_time->addDay();
            $endDate = $new_end_time->toDateString();
            Log::info($endDate);
            $obj = new \stdClass();
            $obj->endDate = $endDate;
            $obj->isLocked = false;
            $obj->roomMode = 'group';
            $obj->templateType = 'viewerMode';
            $obj->fields = array('hostRoomUrl');
            $convert = json_encode($obj);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.whereby.dev/v1/meetings');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $convert);
            $headers = [
                'Authorization: Bearer ' . config('app.whereby_key'),
                'Content-Type: application/json'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $data = json_decode($response);
            $viewerUrl = $data->viewerRoomUrl;

            $meetup->host_url =$data->hostRoomUrl;
            $meetup->viewer_url =$viewerUrl;
            $meetup->save();
        }
        Log::info('end');
    }
}
