<?php

namespace App\Actions;

use App\Models\Notification;

class NewNotification
{
    public static function handle($user, $other, $post, $body, $type, $notificationType)
    {

        $notification = new Notification();
        if ($post) {
            $notification->common = date('Y-m-d') . "+" . $type . "-" . $post;
        } else
            $notification->common = date('Y-m-d') . "+" . $type . "-" . $user;
        $notification->user_id = $user;
        $notification->person_id = $other;
        $notification->body = $body;
        $notification->type = $type;
        $notification->data_id = $post;
        $notification->notification_type = $notificationType;
        $notification->date = date('Y-m-d');
        $notification->time = time();
        $notification->save();
        return true;
    }
}
