<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $today = Carbon::today();

        $plans = UserPlan::where('status', 'active')
            ->whereDate('end_date', '<=', $today)
            ->with(['user.wallet'])
            ->get();

        $count = 0;

        foreach ($plans as $plan) {

            $days = Carbon::parse($plan->start_date)
                ->diffInDays(Carbon::parse($plan->end_date));

            $dailyProfit = ($plan->amount * $plan->daily_return_percent) / 100;
            $totalProfit = $dailyProfit * $days;

            $maturedAmount = $plan->amount + $totalProfit;

            $plan->status = 'completed';
            $plan->total_interest = $maturedAmount;
            $plan->save();

            $wallet = $plan->user->wallet;
            $wallet->locked_balance -= $plan->amount;
            $wallet->balance += $maturedAmount;
            $wallet->save();

            $count++;
        }

        $this->info("Plans completed: {$count}");
    }
}
