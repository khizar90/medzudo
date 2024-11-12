<?php

namespace App\Actions;

use App\Models\AppSetting;
use stdClass;

class BetaCode
{
    public static function handle()
    {
        $beta_code = AppSetting::where('name', 'beta-code')->first();
        $beta_code_message = AppSetting::where('name', 'beta-code-message')->first();
        $betCodeObj = new stdClass();

        if ($beta_code && $beta_code_message) {
            $betCodeObj->check = $beta_code->value;
            $betCodeObj->value = $beta_code_message->value;
        } elseif ($beta_code) {
            $betCodeObj->check = $beta_code->value;
            $betCodeObj->value = '';
        } elseif ($beta_code_message) {
            $betCodeObj->check = '0';
            $betCodeObj->value = $beta_code_message->value;
        } else {
            $betCodeObj->check = '0';
            $betCodeObj->value = '';
        }
        return $betCodeObj;
    }
}
