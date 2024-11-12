<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBusinessDetail extends Model
{
    use HasFactory;
    protected $hidden = [
        'updated_at',
        'created_at'
    ];
    protected $attributes = [
        'customer_problem' => '',
        'business_model' => '',
        'market_description' => '',
        'customer_focus' => '',
        'technology_description' => '',
        'usp' => '',
        'member_benefits' => '',
        'working_groups' => '',
        'association_engagement' => '',
        'member_fee' => '',
    ];
}
