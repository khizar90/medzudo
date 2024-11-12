<?php

namespace App\Actions;

use App\Models\AppSetting;
use stdClass;

class AppVersions
{
    public static function handle()
    {
        $androidNew = AppSetting::where('name', 'android-new-version')->first();
        $androidOld = AppSetting::where('name', 'android-old-version')->first();
        $androidMsg = AppSetting::where('name', 'android-version-message')->first();
        $iosNew = AppSetting::where('name', 'ios-new-version')->first();
        $iosOld = AppSetting::where('name', 'ios-old-version')->first();
        $iosMsg = AppSetting::where('name', 'ios-version-message')->first();
        $iosObj = new stdClass();
        $iosObj->new = $iosNew->value;
        $iosObj->old = $iosOld->value;
        $iosObj->message = $iosMsg->value;
        $androidObj = new stdClass();
        $androidObj->new = $androidNew->value;
        $androidObj->old = $androidOld->value;
        $androidObj->message = $androidMsg->value;
        return array(
            'ios' => $iosObj,
            'android' => $androidObj
        );
    }
}
