<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'min_amount',
        'max_amount',
        'daily_roi',
        'total_return',
        'status',
        'type'
    ];

    public function userPlans(): HasMany
    {
        return $this->hasMany(UserPlan::class);
    }
}
