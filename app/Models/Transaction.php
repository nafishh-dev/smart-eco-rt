<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'hub_id',
        'category',
        'description',
        'amount',
        'unit',
        'points_earned',
        'otp_code',
        'citizen_name',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'points_earned' => 'integer',
        'expires_at' => 'datetime',
    ];
}
