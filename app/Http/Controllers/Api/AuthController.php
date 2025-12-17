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
                'status' => false,
                'errors' => $validator->errors()
            ], 200);
        }


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

        return response()->json([
            'status' => true,
            'message' => 'Registration successful',
            'data' => [
                'user_id' => $user->id,
                'referral_code' => $user->referral_code,
                'referred_by' => $user->referred_by,
                'access_token' => $token
            ]
        ], 200);
    }


    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        $user = Auth::user();
        DB::table('oauth_access_tokens')
            ->where('user_id', $user->id)
            ->update([
                'revoked' => true
            ]);

        $token = $user->createToken('api-token')->accessToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => $user,
        ]);
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
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function dashboard()
    {
        /** @var User $u */
        $u = Auth::user();

        return response()->json([
            'balance'            => $u->wallet->balance ?? 0,
            'active_plans'       => $u->plans()->where('status', 'active')->count(),
            'total_deposits'     => $u->deposits()->where('status', 'approved')->sum('amount'),
            'total_withdrawals'  => $u->withdrawals()->where('status', 'approved')->sum('amount'),
        ]);
    }
}
