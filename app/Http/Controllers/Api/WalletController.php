<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\UserPlan;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PlanResource;

class WalletController extends Controller
{
    public function addMoney(Request $request)
    {
        $request->validate([
            'amount'  => 'required|numeric|min:1'
        ]);

        $user = Auth::guard('api')->user();

        $userWallet = Wallet::where('user_id', $user->id)->first();
        $userWallet->balance += $request->amount;
        $userWallet->save();

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'credit',
            'amount' => $request->amount,
            'balance_after' => $userWallet->balance,
            'transaction_reference' => 'TOPUP',
            'description' => 'Wallet top-up'
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Money added successfully',
            'data'    => [
                'user_id' => $user->id,
                'balance' => $userWallet->balance
            ]
        ]);
    }
}
