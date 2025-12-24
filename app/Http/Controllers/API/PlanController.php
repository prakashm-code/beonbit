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
use Carbon\Carbon;

class PlanController extends Controller
{
    /**
     * List all active plans
     */
public function index(Request $request)
{
    $plans = Plan::query()
        ->select(
            'id',
            'name',
            'description',
            'min_amount',
            'max_amount',
            'daily_roi',
            'duration_days',
            'total_return',
            'status',
            'type'
        )
        ->where('status', '1')
        ->orderBy('id', 'desc')
        ->get()
        ->map(function ($plan) {

            // status conversion
            $plan->status = $plan->status == '1' ? 'active' : 'inactive';

            // type conversion
            $types = [
                '1' => 'basic',
                '2' => 'advanced',
                '3' => 'premium',
                '4' => 'expert',
                '5' => 'master',
                '6' => 'professional',
            ];

            $plan->type = $types[$plan->type] ?? 'unknown';

            return $plan;
        });

    return response()->json([
        'status'  => 1,
        'message' => 'Plans fetched successfully',
        'data'    => $plans
    ], 200);
}





    public function show($id)
    {
        $plan = Plan::find($id);
        DB::beginTransaction();
        try {
            if (!$plan || !$plan->status) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Plan not available'
                ], 404);
            }
            DB::commit();

            return response()->json([
                'status' => 0,
                'plan'   => new PlanResource($plan),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 1,
                'message' => 'Withdrawal request failed',
                'error'   => $e->getMessage()
            ], 500);
        }
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



            $dailyReturnPercent = $plan->daily_roi;
            $dailyInterestAmount = ($request->amount * $dailyReturnPercent) / 100;

            $userPlan = UserPlan::create([
                'user_id'        => $user->id,
                'plan_id'        => $plan->id,
                'amount'         => $request->amount,
                'daily_return_percent' => $dailyReturnPercent,
                'daily_interest' => $dailyInterestAmount,
                'total_interest' => 0,
                'start_date'     => now()->toDateString(),
                'end_date'       => now()->addDays($plan->duration_days)->toDateString(),
                'status'         => 'active'
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $request->amount,
                'balance_after' => $wallet->balance,
                'transaction_reference' => 'PLAN-' . $userPlan->id,
                'description' => 'Plan purchase'
            ]);


            DB::commit();
            return response()->json([
                'status' => 0,
                'message' => 'Plan purchased successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 1,
                'message' => 'Withdrawal request failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

   public function myPlans(Request $request)
{
    try {

        $user   = Auth::guard('api')->user();
        $limit  = $request->limit??10;
        $page   = $request->page??1;
        $search = $request->search??"";
        $sort   = $request->sort ?? 'desc';

        $plans = UserPlan::where('user_id', $user->id)
            ->with('plan')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {

                    // search in related plan
                    $query->orWhereHas('plan', function ($p) use ($search) {
                        $p->where('name', 'LIKE', "%{$search}%");
                    });

                    // search in user_plans fields
                    $query->orWhere('amount', 'LIKE', "%{$search}%")
                          ->orWhere('status', 'LIKE', "%{$search}%")
                          ->orWhereDate('start_date', $search)
                          ->orWhereDate('end_date', $search);
                });
            })
            ->orderBy('id', $sort)
            ->paginate($limit, ['*'], 'page', $page);

        // ✅ transform paginated data
        $plans->getCollection()->transform(function ($userPlan) {

            $today = Carbon::today();
            $start = Carbon::parse($userPlan->start_date);
            $end   = Carbon::parse($userPlan->end_date);

            if ($today->gt($end)) {
                $daysCompleted = $end->diffInDays($start);
            } else {
                $daysCompleted = max(0, $today->diffInDays($start));
            }

            if ($userPlan->status === 'active') {
                $currentInterest = $daysCompleted * $userPlan->daily_interest_amount;
            } else {
                $currentInterest = $userPlan->total_interest;
            }

            return [
                'user_plan_id'   => $userPlan->id,
                'plan_name'      => $userPlan->plan->name,
                'plan_id'      => $userPlan->plan_id,
                'amount'         => (float) $userPlan->amount,
                'daily_interest' => (float) $userPlan->daily_interest_amount,
                'total_interest' => (float) $currentInterest,
                'start_date'     => $userPlan->start_date,
                'end_date'       => $userPlan->end_date,
                'status'         => $userPlan->status,
            ];
        });

        return response()->json([
            'status' => 0,
            'data'   => $plans->items(),
            'pagination' => [
                'current_page' => $plans->currentPage(),
                'last_page'    => $plans->lastPage(),
                'per_page'     => $plans->perPage(),
                'total'        => $plans->total(),
            ]
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => 1,
            'message' => 'Failed to fetch plans',
            'error'   => $e->getMessage()
        ], 500);
    }
}

}
