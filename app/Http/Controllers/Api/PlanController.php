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
            'amount'  => 'required|numeric|min:1'
        ]);

        $user = Auth::user();

        $plan = Plan::where('id', $request->plan_id)
            ->where('status', 1)
            ->firstOrFail();

        // Amount range check
        if ($request->amount < $plan->min_amount || $request->amount > $plan->max_amount) {
            return response()->json([
                'message' => 'Invalid investment amount for this plan'
            ], 422);
        }

        $wallet = $user->wallet;

        if ($wallet->balance < $request->amount) {
            return response()->json([
                'message' => 'Insufficient wallet balance'
            ], 400);
        }

        DB::transaction(function () use ($request, $plan, $wallet, $user) {

            // Lock amount
            $wallet->decrement('balance', $request->amount);
            $wallet->increment('locked_balance', $request->amount);

            $dailyInterest = ($request->amount * $plan->daily_interest_percent) / 100;

            UserInvestment::create([
                'user_id'        => $user->id,
                'plan_id'        => $plan->id,
                'amount'         => $request->amount,
                'daily_interest' => $dailyInterest,
                'total_interest' => 0,
                'start_date'     => Carbon::today(),
                'end_date'       => Carbon::today()->addDays($plan->duration_days),
                'status'         => 'active'
            ]);
        });

        return response()->json([
            'message' => 'Investment purchased successfully'
        ]);
    }
}
