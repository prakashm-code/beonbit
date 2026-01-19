<?php

namespace App\Console\Commands;

use App\Models\Transaction;
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
        // Log::info('Maturity cron start');

        $today = Carbon::today();

        $plans = UserPlan::where('status', 'active')
            ->with(['user.wallet'])
            ->get();

        $count = 0;

        foreach ($plans as $plan) {

            $wallet = $plan->user->wallet;

            if (!$wallet) {
                continue;
            }

            $start = Carbon::parse($plan->start_date);
            $end   = Carbon::parse($plan->end_date);

            // If plan not started yet, skip
            if ($today->lt($start)) {
                continue;
            }

            $dailyProfit = ($plan->amount * $plan->daily_return_percent) / 100;

            // Determine last credited date
            $lastCredited = $plan->last_interest_date
                ? Carbon::parse($plan->last_interest_date)
                : Carbon::parse($plan->start_date);

            /**
             * -----------------------------
             * CASE 1: DAILY INTEREST (till today but not beyond end date)
             * -----------------------------
             */
            if ($today->lte($end)) {

                $daysToCredit = $lastCredited->diffInDays($today);

                if ($daysToCredit > 0) {

                    $totalInterest = $dailyProfit * $daysToCredit;

                    $wallet->balance += $totalInterest;
                    $wallet->save();

                    // Track total earned interest
                    $plan->increment('total_interest', $totalInterest);

                    Transaction::create([
                        'user_id' => $plan->user_id,
                        'type' => 'credit',
                        'category' => 'Interest',
                        'amount' => $totalInterest,
                        'commission' => 0,
                        'balance_after' => $wallet->balance,
                        'transaction_reference' => 'Daily Interest',
                        'description' => 'Plan daily interest'
                    ]);

                    // Update last credited date to today
                    $plan->last_interest_date = $today;
                    $plan->save();

                    Log::info("Interest credited", [
                        'user_id' => $plan->user_id,
                        'days' => $daysToCredit,
                        'amount' => $totalInterest
                    ]);
                }
            }

            /**
             * -----------------------------
             * CASE 2: MATURITY (end date reached)
             * -----------------------------
             */
            if ($today->equalTo($end)) {

                if ($wallet->locked_balance < $plan->amount) {
                    // Log::error("Locked balance mismatch", [
                    //     'user_id' => $plan->user_id
                    // ]);
                    continue;
                }

                $wallet->locked_balance -= $plan->amount;
                $wallet->balance += $plan->amount;
                $wallet->save();

                $plan->status = 'completed';
                $plan->save();

                // Referral commission on principal only
                distributeReferralCommission($plan->user_id, $plan->amount);

                $count++;
                Transaction::create([
                    'user_id' => $plan->user_id,
                    'type' => 'credit',
                    'category' => 'Plan Maturity',
                    'amount' => $plan->amount,
                    'commission' => 0,
                    'balance_after' => $wallet->balance,
                    'transaction_reference' => 'Plan Maturity',
                    'description' => 'Plan Maturity(amount unlocked)'
                ]);
            }
        }

        // Log::info('Maturity cron finished', [
        //     'completed_plans' => $count
        // ]);

        $this->info("Plans completed: {$count}");
    }
}
