<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            // 'password'      => 'required|string|min:6|confirmed',
            'password'      => 'required|string|min:6',
            'referrer_code' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 200);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if (method_exists($user, 'wallet')) {
            $user->wallet()->create(['balance' => 0]);
        }

        if ($request->referrer_code) {
            $ref = User::find($request->referrer_code);
            if ($ref) {
                $user->referrer_id = $ref->id;
                $user->save();
            }
        }

        $token = $user->createToken('Personal Access Token')->accessToken;

        return response()->json([
            'status' => true,
            'message' => 'Registration successful',
            'user'   => new UserResource($user),
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
