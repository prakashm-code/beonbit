<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralEarning extends Model
{
    protected $table = 'referral_earnings';

    protected $fillable = [
        'user_id',
        'referred_user_id',
        'level',
        'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
