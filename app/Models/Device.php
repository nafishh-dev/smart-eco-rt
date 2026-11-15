<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'hub_id',
        'name',
        'location',
        'wifi_ssid',
        'battery_percent',
        'last_seen_at',
    ];

    protected $casts = [
        'battery_percent' => 'integer',
        'last_seen_at' => 'datetime',
    ];
}
