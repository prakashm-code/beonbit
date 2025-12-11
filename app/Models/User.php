<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'referral_code',
        'referred_by',
        'status'
    ];

    protected $hidden = [
        'password',
    ];

    // Relationships
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function userPlans(): HasMany
    {
        return $this->hasMany(UserPlan::class);
    }

    public function kycDocuments(): HasMany
    {
        return $this->hasMany(KycDocument::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function withdrawRequests(): HasMany
    {
        return $this->hasMany(WithdrawRequest::class);
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(Earning::class);
    }

    public function referralEarnings(): HasMany
    {
        return $this->hasMany(ReferralEarning::class);
    }
}
