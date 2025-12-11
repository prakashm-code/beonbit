<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Earning extends Model
{
    protected $fillable = [
        'user_id',
        'user_plan_id',
        'amount',
        'date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date'   => 'date'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userPlan(): BelongsTo
    {
        return $this->belongsTo(UserPlan::class);
    }
}
