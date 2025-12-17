<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'locked_balance'
    ];

    protected $casts = [
        'balance' => 'float',
        'locked_balance' => 'float'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
