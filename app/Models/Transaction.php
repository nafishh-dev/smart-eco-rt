<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'resident_id',
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

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }
}
