<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WithdrawRequest;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\UserPlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function withdraw(Request $request)
    {
        $request->validate([
            'user_plan_id' => 'required|exists:user_plans,id'
        ]);

        DB::beginTransaction();

        try {

            $user = Auth::guard('api')->user();

            // 🔹 Get user plan
            $userPlan = UserPlan::where('id', $request->user_plan_id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // 🔹 Check status
            if ($userPlan->status !== 'completed') {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Plan not completed yet'
                ], 400);
            }

            // 🔹 Get wallet
            $wallet = Wallet::where('user_id', $user->id)->first();

            if (!$wallet) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Wallet not found'
                ], 400);
            }

            // 🔹 Calculate withdraw amount
            $withdrawAmount = $userPlan->amount + $userPlan->total_interest;

            // 🔹 Release locked balance
            if ($wallet->locked_balance < $userPlan->amount) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid locked balance'
                ], 400);
            }

            $wallet->locked_balance -= $userPlan->amount;
            $wallet->balance += $withdrawAmount;
            $wallet->save();

            // 🔹 Mark plan withdrawn
            $userPlan->status = 'withdrawn';
            $userPlan->save();

            // 🔹 Log wallet transaction
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $withdrawAmount,
                'balance_after' => $wallet->balance,
                'transaction_reference' => 'WITHDRAW-' . $userPlan->id,
                'description' => 'Plan withdrawal'
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Withdrawal successful',
                'data' => [
                    'withdraw_amount' => $withdrawAmount,
                    'wallet_balance' => $wallet->balance
                ]
            ], 200);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Withdrawal failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function history()
    {
        $user = Auth::guard('api')->user();

        $withdrawals = WithdrawRequest::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => true,
            'data'   => $withdrawals
        ]);
    }
}
