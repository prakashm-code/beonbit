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
use App\Models\Referral;
use App\Models\User;
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
                $plan->status = $plan->status == '1' ? 'active' : 'inactive';
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
                'message' => 'Plan Data',
                'data'   => new PlanResource($plan),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 1,
                'message' => 'Plan Data Not Fetched',
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
                ], 200);
            }

            $wallet = Wallet::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0, 'locked_balance' => 0]
            );

            $wallet->locked_balance = ($wallet->locked_balance ?? 0) + $request->amount;
            $wallet->save();

            $dailyReturnPercent = $plan->daily_roi;
            $dailyInterestAmount = ($request->amount * $dailyReturnPercent) / 100;

            UserPlan::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount'  => $request->amount,
                'daily_return_percent' => $dailyReturnPercent,
                'daily_interest' => $dailyInterestAmount,
                'total_interest' => 0,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays($plan->duration_days)->toDateString(),
                'status' => 'active'
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'category' => 'plan_purchase',
                'amount' => $request->amount,
                'balance_after' => $wallet->balance, // unchanged by design
                'transaction_reference' => 'plan_purchase',
                'description' => 'Plan purchased (amount locked)'
            ]);



       $planCount = UserPlan::where('user_id', $user->id)->count();

if ($planCount === 1) {   // FIRST PLAN ONLY
$joiningBonus = 6.9;

$userWallet = Wallet::firstOrCreate(
    ['user_id' => $user->id],
    ['balance' => 0, 'locked_balance' => 0]
);

$userWallet->balance += $joiningBonus;
$userWallet->save();

Transaction::create([
    'user_id'               => $user->id,
    'type'                  => 'credit',
    'category'              => 'joining_bonus',
    'amount'                => $joiningBonus,
    'balance_after'         => $userWallet->balance,
    'transaction_reference' => 'Joining Bonus',
    'description'           => 'Joining bonus on first plan purchase',
]);


if ($user->referred_by) {

    $level1Bonus = 5;
    $level1UserId = $user->referred_by;

    $level1Wallet = Wallet::firstOrCreate(
        ['user_id' => $level1UserId],
        ['balance' => 0, 'locked_balance' => 0]
    );

    $level1Wallet->balance += $level1Bonus;
    $level1Wallet->save();

    Transaction::create([
        'user_id'               => $level1UserId,
        'type'                  => 'credit',
        'category'              => 'direct_bonus',
        'amount'                => $level1Bonus,
        'balance_after'         => $level1Wallet->balance,
        'transaction_reference' => 'Joining Referral Bonus (Level 1)',
        'description'           => 'Fixed $5 direct referral bonus (Level 1)',
    ]);
}
    // ✅ STATIC LEVELS & PERCENTAGES
    $levels = [
        1 => 5, // Level 1 = 5%
        2 => 3, // Level 2 = 3%
        3 => 1, // Level 3 = 1%
    ];

    $currentUser = $user;

    foreach ($levels as $level => $percent) {

        if (!$currentUser->referred_by) {
            break;
        }

        $parentId = $currentUser->referred_by;

        $commission = round(($request->amount * $percent) / 100, 2);

        if ($commission > 0) {

            $refWallet = Wallet::firstOrCreate(
                ['user_id' => $parentId],
                ['balance' => 0, 'locked_balance' => 0]
            );

            $refWallet->balance += $commission;
            $refWallet->save();

            Referral::create([
                'referrer_id' => $parentId,
                'referred_id' => $user->id,
                'level' => $level,
                'income' => $commission,
            ]);

            Transaction::create([
                'user_id'               => $parentId,
                'type'                  => 'credit',
                'category'              => 'referral',
                'amount'                => $commission,
                'balance_after'         => $refWallet->balance,
                'transaction_reference' => "Direct Referral commission (Level {$level})",
                'description'           => "Direct Referral commission (Level {$level})",
            ]);
        }

        // Move up the referral chain
        $currentUser = User::find($parentId);
    }
}
            DB::commit();

            return response()->json([
                'status' => 0,
                'message' => 'Plan purchased successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 1,
                'message' => 'Plan not purchased successfully',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function myPlans(Request $request)
    {
        try {
            $user   = Auth::guard('api')->user();
            $limit  = $request->limit ?? 10;
            $page   = $request->page ?? 1;
            $search = $request->search ?? "";
            $sort   = $request->sort ?? 'desc';

            $plans = UserPlan::where('user_id', $user->id)
                ->with('plan')
                ->when($search !== '', function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->orWhereHas('plan', function ($p) use ($search) {
                            $p->where('name', 'LIKE', "%{$search}%");
                        });
                        $query->orWhere('amount', 'LIKE', "%{$search}%")
                            ->orWhere('status', 'LIKE', "%{$search}%")
                            ->orWhereDate('start_date', $search)
                            ->orWhereDate('end_date', $search);
                    });
                })
                ->orderBy('id', $sort)
                ->paginate($limit, ['*'], 'page', $page);
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
