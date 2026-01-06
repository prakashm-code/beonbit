<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompleteUserPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:complete-user-plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Maturity cron start');
        $today = Carbon::today();

        $plans = UserPlan::where('status', 'active')
            ->whereDate('end_date', '<=', $today)
            ->with(['user.wallet'])
            ->get();

        $count = 0;

        foreach ($plans as $plan) {
            $wallet = $plan->user->wallet;

            if (!$wallet) {
                continue;
            }
            $days = Carbon::parse($plan->start_date)
                ->diffInDays(Carbon::parse($plan->end_date));

            $dailyProfit = ($plan->amount * $plan->daily_return_percent) / 100;
            $totalProfit = $dailyProfit * $days;

            $maturedAmount = $plan->amount + $totalProfit;

            $wallet->locked_balance = $wallet->locked_balance - $plan->amount;
            $wallet->balance = $wallet->balance + $plan->amount + $totalProfit;
            $wallet->save();

            // update plan
            $plan->status = 'completed';
            $plan->total_interest = $totalProfit; // only interest
            $plan->save();

            $count++;

            distributeReferralCommission($plan->user_id, $maturedAmount);
        }
        Log::info('Maturity cron finished', [
            'completed_plans' => $count
        ]);
        $this->info("Plans completed: {$count}");
    }
}
