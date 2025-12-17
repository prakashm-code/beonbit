<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'referral_code',
        'referred_by'
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function deposits()
    {
        return $this->hasMany(\App\Models\Deposit::class);
    }
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }


    public function userPlans()
    {
        return $this->hasMany(UserPlan::class);
    }

    public function referralEarnings()
    {
        return $this->hasMany(ReferralEarning::class, 'referrer_id');
    }
}
