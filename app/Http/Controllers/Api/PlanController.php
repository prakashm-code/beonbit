<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\UserPlan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PlanResource;

class PlanController extends Controller
{
    /**
     * List all active plans
     */
    public function index()
    {
        $plans = Plan::where('status', '1')->get();
        return response()->json([
            'status' => true,
            'plans'  => PlanResource::collection($plans),
        ]);
    }


    public function show($id)
    {
        $plan = Plan::find($id);

        if (!$plan || !$plan->status) {
            return response()->json([
                'status' => false,
                'message' => 'Plan not available'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'plan'   => new PlanResource($plan),
        ]);
    }


    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'amount'  => 'nullable|numeric|min:1',
        ]);

        $user = Auth::guard('api')->user();

        $plan = Plan::where('id', $request->plan_id)
            ->where('status', "1")
            ->first();

        if (!$plan) {
            return response()->json([
                'status' => false,
                'message' => 'Plan not available'
            ], 404);
        }

        // Determine amount
        $amount = $request->amount ?? $plan->price;

        // Wallet balance check
        if ($user->wallet_balance < $amount) {
            return response()->json([
                'status'  => false,
                'message' => 'Insufficient balance'
            ], 400);
        }

        try {
            DB::transaction(function () use ($user, $plan, $amount) {

                $user->wallet_balance = $user->wallet_balance - $amount;

                UserPlan::create([
                    'user_id'    => $user->id,
                    'plan_id'    => $plan->id,
                    'amount'     => $amount,
                    'start_date' => now(),
                    'end_date'    => now()->addDays($plan->duration_days),
                    "daily_return_percent"=>$plan->daily_roi,
                    'status'     => 'active',
                ]);

                Transaction::create([
                    'user_id'        => $user->id,
                    'type'           => 'debit',
                    'amount'         => $amount,
                    'balance_after'  => $user->wallet_balance,
                    'transaction_reference'=>'pay'
                ]);
            });

            return response()->json([
                'status'  => true,
                'message' => 'Successfully subscribed to plan'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Subscription failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
