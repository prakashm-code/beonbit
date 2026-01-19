<?php

use App\Models\Referral;
use App\Models\ReferralSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

function getCommission()
{
    $get_commission = 0;
    $getdata = ReferralSetting::where('level', '1')->first();
    if ($getdata != "") {
        $get_commission = $getdata->percentage;
    }
    return $get_commission;
}


function distributeReferralCommission(
    int $userId,
    float $amount,
) {
    DB::beginTransaction();
    try {

        $levels = ReferralSetting::where('status', "1")
            ->orderBy('from_level')
            ->get();

        if ($levels->isEmpty()) {
            DB::commit();
            return;
        }

        $maxLevel = $levels->max('to_level');

        $currentUser = User::find($userId);
        if (!$currentUser || !$currentUser->referred_by) {
            DB::commit();
            return;
        }

        $parentId = $currentUser->referred_by;

        for ($level = 1; $level <= $maxLevel; $level++) {

            if (!$parentId) break;
            $percentage = 0;

            foreach ($levels as $row) {
                if ($level >= $row->from_level && $level <= $row->to_level) {
                    $percentage = $row->percentage;
                    break;
                }
            }

            if ($percentage > 0) {

                $commission = round(($amount * $percentage) / 100, 2);

                if ($commission > 0) {

                    Wallet::firstOrCreate(
                        ['user_id' => $parentId],
                        ['balance' => 0]
                    );

                    Wallet::where('user_id', $parentId)
                        ->increment('balance', $commission);

                    $wallet = Wallet::where('user_id', $parentId)->first();

                    Referral::create([
                        'referrer_id' => $parentId,
                        'referred_id' => $userId,
                        'level' => $level,
                        'income' => $commission,
                        // 'source_type' => $sourceType,
                        // 'source_id' => $sourceId,
                    ]);

                    Transaction::create([
                        'user_id'               => $parentId,
                        'type'                  => 'credit',
                        'category'              => 'referral',
                        'amount'                => $commission,
                        'balance_after'         => $wallet->balance,
                        'transaction_reference' => 'Referral commission (Level ' . $level . ')',
                        'description'           => 'Referral commission (Level ' . $level . ')',
                    ]);
                }
            }

            // Move up referral chain
            $parentId = User::where('id', $parentId)
                ->value('referred_by');
        }

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
