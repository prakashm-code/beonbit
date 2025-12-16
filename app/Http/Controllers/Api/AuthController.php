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
class AuthController extends Controller
{
    public function register(Request $r)
    {
        $r->validate([
            'name'          => 'required',
            'email'         => 'required|email|unique:users',
            'password'      => 'required|min:6',
            'referrer_code' => 'nullable',
        ]);

        $data = $r->only('name', 'email', 'password');
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->wallet()->create(['balance' => 0]);
        if ($r->referrer_code) {
            $ref = User::find($r->referrer_code);
            if ($ref) {
                $user->referrer_id = $ref->id;
                $user->save();
            }
        }
        $token = $user->createToken('api-token')->accessToken;

        return response()->json([
            'token' => $token,
            'user'  => new UserResource($user),
        ], 201);
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

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->revoke();
        }

        return response()->json(['message' => 'Logged out']);
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
