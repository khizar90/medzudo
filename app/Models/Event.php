<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }
    use HasFactory;
    protected $hidden = [
       
        'updated_at',
        'created_at'
    ];
}
