<?php

namespace App\Actions;

use App\Models\BlockList;

class BlockedUser
{
    public static function handle($user_id){
        $blocked = BlockList::where('user_id', $user_id)->pluck('block_id');
        $blocked1 = BlockList::where('block_id', $user_id)->pluck('user_id');
        $blocked = $blocked->merge($blocked1);
        return $blocked;
    }
}