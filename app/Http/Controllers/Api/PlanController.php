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
        $plans = Plan::where('is_active', 1)->get();

        return response()->json([
            'status' => true,
            'plans'  => PlanResource::collection($plans),
        ]);
    }

    /**
     * Show single plan
     */
    public function show(Plan $plan)
    {
        if (!$plan->is_active) {
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

    public function subscribe(Request $request, Plan $plan)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:1',
        ]);

        $user = Auth::user();
        $amount = $request->amount ?? $plan->price;

        if ($user->wallet->balance < $amount) {
            return response()->json([
                'status'  => false,
                'message' => 'Insufficient balance'
            ], 400);
        }

        try {
            DB::transaction(function () use ($user, $plan, $amount) {
                $user->wallet->decrement('balance', $amount);

                UserPlan::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'amount' => $amount,
                    'started_at' => now(),
                    'ends_at' => now()->addDays($plan->duration_days),
                    'status' => 'active',
                ]);

                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'subscribe',
                    'amount' => 100,
                    'balance_after' => 500,
                ]);
            });

            return response()->json([
                'status'  => true,
                'message' => 'Successfully subscribed to plan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Subscription failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
