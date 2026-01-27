<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\UserPlan;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'phone',
        'profile',
        'email',
        'address',
        'password',
        'referral_code',
        'referred_by',
        'email_verified_at'
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
