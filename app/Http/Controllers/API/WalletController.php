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
                'category' => 'topup',
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
                'status'  => 0,
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
                'message' => 'Money Not added successfully',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function transactions(Request $request)
    {
        $user   = Auth::guard('api')->user();
        $limit  = $request->limit ?? 10;
        $page   = $request->page ?? 1;
        $search = $request->search ?? "";
        $sort   = $request->sort ?? 'desc';

        $transactions = $user->transactions()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('type', 'LIKE', "%{$search}%")
                        ->orWhere('category', 'LIKE', "%{$search}%")
                        ->orWhere('amount', 'LIKE', "%{$search}%")
                        ->orWhere('balance_after', 'LIKE', "%{$search}%")
                        ->orWhere('transaction_reference', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhereDate('created_at', $search);
                });
            })
            ->orderBy('id', $sort)
            ->paginate($limit, [
                'type',
                'category',
                'amount',
                'balance_after',
                'transaction_reference',
                'description',
                'created_at'
            ], 'page', $page);

        return response()->json([
            'status'  => 1,
            'message' => 'All transactions',
            'data'    => $transactions->items(),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page'    => $transactions->lastPage(),
                'per_page'     => $transactions->perPage(),
                'total'        => $transactions->total(),
            ]
        ], 200);
    }
}
