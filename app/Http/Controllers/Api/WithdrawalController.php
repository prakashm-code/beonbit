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
use Illuminate\Support\Facades\Validator;

class WithdrawalController extends Controller
{
    public function request(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'transaction_method' => 'required|string'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::guard('api')->user();
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0, 'locked_balance' => 0]
            );

            if ($wallet->balance < $request->amount) {
                DB::rollBack();
                return response()->json([
                    'status' => 1,
                    'message' => 'Insufficient wallet balance'
                ], 400);
            }

            $wallet->balance -= $request->amount;
            $wallet->locked_balance += $request->amount;
            $wallet->save();

            $withdrawal = WithdrawRequest::create([
                'user_id' => $user->id,
                'amount'  => $request->amount,
                'method'  => $request->transaction_method,
                'status'  => 'pending'
            ]);

            DB::commit();

            return response()->json([
                'status' => 0,
                'message' => 'Withdrawal request sent to admin',
                'data' => [
                    'withdrawal_id' => $withdrawal->id,
                    'amount' => $withdrawal->amount,
                    'status' => $withdrawal->status
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 1,
                'message' => 'Withdrawal request failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function withdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_plan_id' => 'required|exists:user_plans,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 200);
        }
        DB::beginTransaction();

        try {

            $user = Auth::guard('api')->user();
            $userPlan = UserPlan::where('id', $request->user_plan_id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // if ($userPlan->status !== 'completed') {
            //     DB::rollBack();
            //     return response()->json([
            //         'status' => 1,
            //         'message' => 'Plan not completed yet'
            //     ], 400);
            // }

            $wallet = Wallet::where('user_id', $user->id)->first();
            if (!$wallet) {
                DB::rollBack();
                return response()->json([
                    'status' => 1,
                    'message' => 'Wallet not found'
                ], 400);
            }

            $withdrawAmount = $userPlan->amount + $userPlan->total_interest;
            if ($wallet->locked_balance < $userPlan->amount) {
                DB::rollBack();
                return response()->json([
                    'status' => 1,
                    'message' => 'Invalid locked balance'
                ], 400);
            }

            $wallet->locked_balance -= $userPlan->amount;
            $wallet->balance += $withdrawAmount;
            $wallet->save();

            $userPlan->status = 'withdrawn';
            $userPlan->save();

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
                'status' => 0,
                'message' => 'Withdrawal successful',
                'data' => [
                    'withdraw_amount' => $withdrawAmount,
                    'wallet_balance' => $wallet->balance
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 1,
                'message' => 'Withdrawal failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function history(Request $request)
    {
        try {

            // ✅ compulsory params validation
            // $request->validate([
            //     'limit'  => 'required|integer|min:1|max:100',
            //     'page'   => 'required|integer|min:1',
            //     'search' => 'required|string',
            //     'sort'   => 'required|in:asc,desc',
            // ]);

            $user   = Auth::guard('api')->user();
            $limit  = $request->limit ?? 10;
            $page   = $request->page ?? 1;
            $search = $request->search ?? "";
            $sort   = $request->sort ?? 'desc';

            $withdrawals = WithdrawRequest::where('user_id', $user->id)
                ->when($search !== '', function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('amount', 'LIKE', "%{$search}%")
                            ->orWhere('status', 'LIKE', "%{$search}%")
                            ->orWhere('method', 'LIKE', "%{$search}%")
                            ->orWhereDate('created_at', $search);
                    });
                })
                ->orderBy('id', $sort)
                ->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'status' => 1,
                'message' => 'Withdrawal history fetched successfully',
                'data' => $withdrawals->items(),
                'pagination' => [
                    'current_page' => $withdrawals->currentPage(),
                    'last_page'    => $withdrawals->lastPage(),
                    'per_page'     => $withdrawals->perPage(),
                    'total'        => $withdrawals->total(),
                ]
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status'  => 0,
                'message' => 'Withdrawal failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
