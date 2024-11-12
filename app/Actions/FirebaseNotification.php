<?php

namespace App\Actions;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseNotification
{

    public static function handle($tokens, $body, $title, $arr,$badge)
    {
        $firebase = (new Factory)->withServiceAccount(__DIR__ . '/../../public/firebase.json');
        $notification = Notification::create($title, $body);
        $messaging = $firebase->createMessaging();
        $message = CloudMessage::withTarget('topic', 'global')
            ->withNotification($notification)->withData($arr)->withDefaultSounds()->withApnsConfig(
                ApnsConfig::new()->withBadge($badge)
            );
        $messaging->sendMulticast($message, $tokens);
        Log::info('Notifications sent successfully', [
            'title' => $title,
            'body' => $body,
            'tokens' => $tokens,
            'data' => $arr,
            'badge' => $badge,
            'response' => 'Notification send '
        ]);
        return true;
    }
}
