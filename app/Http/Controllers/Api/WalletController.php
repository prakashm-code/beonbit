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
use App\Models\ReferralEarning;

class WalletController extends Controller
{
    public function getWallet()
    {
        $user = Auth::guard('api')->user();

        if (!$user->wallet) {
            $user->wallet()->create([
                'balance' => 0,
                'locked_balance' => 0
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => "Wallet balance",
            'data' => [
                'balance' => $user->wallet->balance,
                'locked_balance' => $user->wallet->locked_balance
            ]
        ], 200);
    }

    public function addMoney(Request $request)
    {
        $request->validate([
            'amount'  => 'required|numeric|min:1'
        ]);

        $user = Auth::guard('api')->user();
        DB::beginTransaction();
        try {
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

            if ($user->referred_by) {
                $commission = ($request->amount * 5) / 100;
                $referrerWallet = Wallet::where('user_id', $user->referred_by)->first();
                if ($referrerWallet) {
                    $referrerWallet->balance += $commission;
                    $referrerWallet->save();
                    ReferralEarning::create([
                        'referrer_id'      => $user->referred_by,
                        'referred_user_id' => $user->id,
                        // 'user_plan_id'     => $userPlan->id,
                        'amount'           => $commission
                    ]);
                    Transaction::create([
                        'user_id' => $user->referred_by,
                        'type' => 'credit',
                        'amount' => $commission,
                        'balance_after' => $referrerWallet->balance,
                        'transaction_reference' => 'REFFERAL',
                        'description' => 'Referral commission'
                    ]);
                }
            }
            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Money added successfully',
                'data'    => [
                    'user_id' => $user->id,
                    'balance' => $userWallet->balance
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 1,
                'message' => 'Withdrawal request failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function transactions()
    {
        $user = Auth::guard('api')->user();

        $transactions = $user->transactions()
            ->orderBy('id', 'desc')
            ->get([
                'type',
                'amount',
                'balance_after',
                'transaction_reference',
                'description',
                'created_at'
            ]);

        return response()->json([
            'status' => 0,
            "message" => "All transaction",
            'data' => $transactions
        ], 200);
    }
}
