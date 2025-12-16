<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WithdrawRequest;
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

    // Wallet balance check
    if ($user->wallet->balance < $request->amount) {
        return response()->json([
            'status'  => false,
            'message' => 'Insufficient balance'
        ], 400);
    }

    try {
        DB::transaction(function () use ($user, $request, &$withdrawal) {

            // Deduct wallet balance
            $user->wallet->decrement('balance', $request->amount);

            // Create withdrawal request (admin approves later)
            $withdrawal = WithdrawRequest::create([
                'user_id' => $user->id,
                'amount'  => $request->amount,
                'status'  => 'pending',
                'method'  => $request->method,
                'meta'    => [],
            ]);

            // Log transaction
            Transaction::create([
                'user_id'       => $user->id,
                'type'          => 'withdraw_request',
                'amount'        => $request->amount,
                'balance_after' => $user->wallet->fresh()->balance,
            ]);
        });

        return response()->json([
            'status'     => true,
            'message'    => 'Withdrawal requested successfully',
            'withdrawal' => $withdrawal
        ], 201);

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
