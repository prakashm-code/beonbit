<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPlan extends Model
{
    protected $table = 'user_plans';

    protected $fillable = [
        'user_id',
        'plan_id',
        'start_date',
        'end_date',
        'amount',
        'daily_return_percent',
        'daily_interest',
        'total_interest',
        'status'
    ];

    protected $dates = [
        'start_date',
        'end_date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
