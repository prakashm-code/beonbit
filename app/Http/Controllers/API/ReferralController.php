<?php

namespace App\Http\Controllers\API;

use App\Models\ReferralEarning;
use App\Http\Controllers\Controller;
use App\Models\ReferralSetting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function earnings()
    {
        $user = Auth::guard('api')->user();
        $earnings = $user->referralEarnings()
            ->with('referredUser:id,name')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($earning) {
                return [
                    'referred_user_id'   => $earning->referredUser?->id,
                    'referred_user_name' => $earning->referredUser?->name,
                    'amount'             => $earning->amount,
                    'created_at'         => $earning->created_at
                ];
            });

        return response()->json([
            'status' => 0,
            'data' => [
                'total_earned' => $earnings->sum('amount'),
                'list' => $earnings
            ]
        ], 200);
    }

    public function myReferralsLevelWise()
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return response()->json([
                    'status'  => 1,
                    'message' => 'Unauthorized user'
                ], 401);
            }

            $levels = ReferralSetting::where('status', '1')
                ->orderBy('from_level')
                ->get();

            if ($levels->isEmpty()) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'Referral levels not configured',
                    'data'    => []
                ]);
            }

            $maxLevel = (int) $levels->max('to_level');

            $result = [];
            $visited = [];

            $queue = [
                ['user_id' => $user->id, 'level' => 0]
            ];

            $visited[$user->id] = true;
            while (!empty($queue)) {
                $current = array_shift($queue);
                if ($current['level'] >= $maxLevel) {
                    continue;
                }
                $children = User::where('referred_by', $current['user_id'])
                    ->where('role', '0')
                    ->select('id', 'first_name', 'last_name', 'email')
                    ->get();
                foreach ($children as $child) {
                    if (isset($visited[$child->id])) {
                        continue;
                    }
                    $visited[$child->id] = true;
                    $level = $current['level'] + 1;
                    $result["level_$level"][] = [
                        'id'    => $child->id,
                        'name'  => trim($child->first_name . ' ' . $child->last_name),
                        'email' => $child->email,
                    ];
                    $queue[] = [
                        'user_id' => $child->id,
                        'level'   => $level
                    ];
                }
            }
            return response()->json([
                'status' => 0,
                'data'   => $result
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 1,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }
}
