<?php

namespace App\Http\Controllers\API;
use App\Models\ReferralEarning;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
  public function earnings()
{
    $user = Auth::guard('api')->user();

    $earnings = $user->referralEarnings()
        ->with('referredUser:id,name')
        ->orderBy('id', 'desc')
        ->get()
        ->map(function ($earning) {
            return [
                'referred_user_id'   => $earning->referredUser?->id,
                'referred_user_name' => $earning->referredUser?->name,
                'amount'             => $earning->amount,
                'created_at'         => $earning->created_at
            ];
        });

    return response()->json([
        'status' => 0,
        'data' => [
            'total_earned' => $earnings->sum('amount'),
            'list' => $earnings
        ]
    ], 200);
}

}
