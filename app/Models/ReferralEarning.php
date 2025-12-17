<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class ReferralEarning extends Model
{
    protected $table = 'referral_commissions';

    protected $fillable = [
        'referrer_id',
        'referred_user_id',
        'user_plan_id',
        'amount'
    ];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function userPlan()
    {
        return $this->belongsTo(UserPlan::class, 'user_plan_id');
    }
}
