<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralSetting extends Model
{
    protected $fillable = [
        'from_level',
        'to_level',
        'percentage',
        'status'
    ];

    protected $casts = [
        'percentage' => 'float',
        'status' => 'boolean'
    ];
}
