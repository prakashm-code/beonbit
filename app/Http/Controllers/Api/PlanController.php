<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\UserPlan;
use App\Models\Wallet;
use App\Models\ReferralEarning;
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
        DB::beginTransaction();

        try {

            $user = Auth::guard('api')->user();
            $plan = Plan::where('id', $request->plan_id)
                ->where('status', 1)
                ->firstOrFail();

            if ($request->amount < $plan->min_amount || $request->amount > $plan->max_amount) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Invalid amount for this plan'
                ], 500);
            }

            $wallet = Wallet::where('user_id', $user->id)->first();

            if ($wallet->balance < $request->amount) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Insufficient wallet balance'
                ], 500);
            }
            $wallet->balance -= $request->amount;
            $wallet->locked_balance += $request->amount;
            $wallet->save();

            $dailyInterest = $plan->daily_roi;

            $userPlan = UserPlan::create([
                'user_id'        => $user->id,
                'plan_id'        => $plan->id,
                'amount'         => $request->amount,
                'daily_return_percent' => $dailyInterest,
                'start_date'     => now()->toDateString(),
                'end_date'       => now()->addDays($plan->duration_days)->toDateString(),
                'status'         => 'active'
            ]);

            if ($user->referred_by) {
                $commission = ($request->amount * 5) / 100;
                Wallet::where('user_id', $user->referred_by)
                    ->increment('balance', $commission);
                ReferralEarning::create([
                    'referrer_id'      => $user->referred_by,
                    'referred_user_id' => $user->id,
                    'user_plan_id'     => $userPlan->id,
                    'amount'           => $commission
                ]);
            }
            DB::commit();
            return response()->json([
                'status' => 0,
                'message' => 'Plan purchased successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Withdrawal request failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
