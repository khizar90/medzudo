<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $hidden = [
       
        'updated_at',
        'created_at'
    ];

    public function messageReads()
    {
        return $this->hasMany(MessageRead::class, 'message_id');
    }
}
