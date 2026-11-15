<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'bin_category',
        'capacity_snapshot',
        'is_read',
        'is_resolved',
    ];

    protected $casts = [
        'capacity_snapshot' => 'integer',
        'is_read' => 'boolean',
        'is_resolved' => 'boolean',
    ];
}
