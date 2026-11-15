<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bin extends Model
{
    protected $fillable = [
        'category',
        'name',
        'subtitle',
        'unit',
        'current_weight',
        'capacity_percent',
        'status',
    ];

    protected $casts = [
        'current_weight' => 'float',
        'capacity_percent' => 'integer',
    ];
}
