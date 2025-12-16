<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WithdrawRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function request(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|string|max:255',
        ]);

        $user = Auth::guard('api')->user();

        if ($user->wallet_balance < $request->amount) {
            return response()->json([
                'status'  => false,
                'message' => 'Insufficient balance'
            ], 400);
        }

        try {
            DB::transaction(function () use ($user, $request, &$withdrawal) {
                $user->wallet_balance = $user->wallet_balance - $request->amount;
                $withdrawal = WithdrawRequest::create([
                    'user_id' => $user->id,
                    'amount'  => $request->amount,
                    'status'  => 'pending',
                    'method'  => $request->method,
                    'meta'    => [],
                ]);

                Transaction::create([
                    'user_id'       => $user->id,
                    'type'          => 'debit',
                    'amount'        => $request->amount,
                    'balance_after' => $user->wallet_balance,
                    'transaction_reference' => 'sdsdsdsds'

                ]);
            });

            return response()->json([
                'status'     => true,
                'message'    => 'Withdrawal requested successfully',
                'withdrawal' => $withdrawal
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Withdrawal request failed',
                'error'   => $e->getMessage()
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
