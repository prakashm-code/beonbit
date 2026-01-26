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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\WithdrawalRequestedMail;
class WithdrawalController extends Controller
{
    public function request(Request $request)
    {
        // dd($request);
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'transaction_method' => 'required',
            'address' => 'required',

        ]);

        DB::beginTransaction();

        try {
            $user = Auth::guard('api')->user();

            // 1️⃣ Minimum withdrawal check
            if ($request->amount < 10) {
                DB::rollBack();

                return response()->json([
                    'status' => 1,
                    'message' => 'Minimum withdrawal amount is $10'
                ], 200);
            }

            $commissionPercentage = 10; // 10%
            $commissionAmount = round(($request->amount * $commissionPercentage) / 100, 2);

            $netAmount = $request->amount - $commissionAmount;

            $wallet = Wallet::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0, 'locked_balance' => 0]
            );

            // dd(1);
            if ($wallet->balance < $request->amount) {
                DB::rollBack();
                return response()->json([
                    'status' => 1,
                    'message' => 'Insufficient wallet balance'
                ], 200);
            }



            // 1. Validate the user input
            $request->validate([
                'address' => 'required',
                'amount' => 'required|numeric'
            ]);


                $wallet->balance -= $request->amount;
                $wallet->locked_balance += $request->amount;
                $wallet->save();

                $withdrawal = WithdrawRequest::create([
                    'user_id'    => $user->id,
                    'amount'     => $request->amount,
                    // 'commissio   ./n' => $commissionAmount,
                    'method'     => $request->transaction_method,
                    'status'     => 'pending',
                ]);
                // ]);
                // dd(1);
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'debit',
                    'category' => 'withdrawal',
                    'isEarning' => '0',
                    'amount' => $request->amount,
                    'commission' => $commissionAmount,
                    'balance_after' => $wallet->balance,
                    'transaction_reference' => 'Withdrawal',
                    'description' => 'Wallet withdrawal'
                ]);

// Mail::to('testxyz@yopmail.com')
Mail::to('infinitewealth3195@gmail.com')
    ->send(new WithdrawalRequestedMail([
        'email'        => $user->email,
        'amount'       => $request->amount,
        'commission'   => $commissionAmount,
        'net_amount'   => $netAmount,
        'status'       => $withdrawal->status,
        'withdrawal_id'=> $withdrawal->id,
    ]));

                DB::commit();
                return response()->json([
                    'status' => 0,
                    'message' => 'Withdrawal successful',
                    'data' => [
                        'withdrawal_id'     => $withdrawal->id,
                        'requested_amount' => $request->amount,
                        'commission'       => $commissionAmount,
                        'net_amount'        => $netAmount,
                        'status'            => $withdrawal->status
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
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::guard('api')->user();
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->firstOrFail();

            if ($wallet->balance < $request->amount) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Insufficient wallet balance'
                ], 200);
            }

            $wallet->balance -= $request->amount;
            $wallet->save();

            // Create withdrawal request
            // Withdrawal::create([
            //     'user_id' => $user->id,
            //     'amount' => $request->amount,
            //     'status' => 'pending'
            // ]);

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'category' => 'withdrawal',
                'amount' => $request->amount,
                'balance_after' => $wallet->balance,
                'transaction_reference' => 'WD-' . uniqid(),
                'description' => 'Wallet withdrawal request'
            ]);

            DB::commit();

            return response()->json([
                'status' => 0,
                'message' => 'Withdrawal request submitted',
                'wallet_balance' => $wallet->balance
            ]);
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
