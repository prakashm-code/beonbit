<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            // 'password'      => 'required|string|min:6|confirmed',
            'password'      => 'required|string|min:6',
            'referral_code' => 'nullable|exists:users,referral_code'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 1,
                'errors' => $validator->errors()
            ], 200);
        }
        DB::beginTransaction();

        try {
            $referrer = null;
            if ($request->referral_code) {
                $referrer = User::where('referral_code', $request->referral_code)->first();
            }
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'referral_code' => strtoupper(Str::random(8)),
                'referred_by' => $referrer?->id
            ]);
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'locked_balance' => 0
            ]);
            $token = $user->createToken('Personal Access Token')->accessToken;

            DB::commit();
            return response()->json([
                'status' => 0,
                'message' => 'Registration successful',
                'data' => [
                    'user_id' => $user->id,
                    'referral_code' => $user->referral_code,
                    'referred_by' => $user->referred_by,
                    'access_token' => $token,
                ]
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


    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => 1,
                'message' => 'Invalid credentials',
            ]);
        }

        $user = Auth::user();
        $walletbalance = $user->wallet()->firstOrCreate([
            'balance' => 0,
            'locked_balance' => 0
        ]);
        DB::table('oauth_access_tokens')
            ->where('user_id', $user->id)
            ->update([
                'revoked' => 0
            ]);

        $token = $user->createToken('api-token')->accessToken;
        $active_plans      = $user->userPlans()->where('status', 'active')->count();
        $total_transactions     = $user->transactions()->count();
        $total_withdrawals  = $user->userPlans()->where('status', 'withdrawn')->sum('amount');

        if ($user->role == "0") {
            return response()->json([
                'status'  => 0,
                'message' => 'Login successful',
                'token'   => $token,
                'data' => [
                    'user_id' => $user->id,
                    'user' => $user,
                    'wallet_balance' => $walletbalance->balance,
                    'active_plans' => $active_plans,
                    'total_transactions' => $total_transactions,
                    'total_withdrawals' => $total_withdrawals,
                ]
            ]);
        } else {
            return response()->json([
                'status'  => 1,
                'message' => 'Invalid user',
            ]);
        }
    }

    public function me()
    {
        /** @var User $user */
        $user = Auth::user();

        return new UserResource($user);
    }


    public function logout()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user && $token = $user->currentAccessToken()) {
            $user->tokens()->where('id', $token->id)->delete();
        }
        return response()->json([
            'status' => 0,
            'message' => 'Logged out successfully'
        ]);
    }

    public function dashboard()
    {
        /** @var User $u */
        $u = Auth::user();

        return response()->json([
            'balance'            => $u->wallet->balance ?? 0,
            'active_plans'       => $u->userPlans()->where('status', 'active')->count(),
            'total_deposits'     => $u->transactions()->count(),
            'total_withdrawals'  => $u->withdrawals()->where('status', 'approved')->sum('amount'),
        ]);
    }
}
