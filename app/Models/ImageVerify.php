<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageVerify extends Model
{
    use HasFactory;
    protected $hidden = [
       
        'updated_at',
        'created_at'
    ];
}