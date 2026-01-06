<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawRequest extends Model
{
    protected $table = 'withdrawals';

    protected $fillable = [
        'user_id',
        'amount',
        'commission',
        'wallet_address',
        'method',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
