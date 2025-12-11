<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'title',
        'amount',
        'daily_return_percent',
        'duration_days',
        'description',
        'status'
    ];

    public function userPlans(): HasMany
    {
        return $this->hasMany(UserPlan::class);
    }
}
