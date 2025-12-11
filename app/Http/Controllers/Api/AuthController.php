<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function register(Request $r){
        $r->validate(['name'=>'required','email'=>'required|email|unique:users','password'=>'required|min:6','referrer_code'=>'nullable']);
        $data = $r->only('name','email','password');
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $user->wallet()->create(['balance'=>0]);
        // handle referrer (simple)
        if ($r->referrer_code) {
            $ref = User::where('id',$r->referrer_code)->first();
            if ($ref) $user->referrer_id = $ref->id;
            $user->save();
        }
        $token = $user->createToken('api-token')->accessToken;
        return response()->json(['token'=>$token,'user'=>new UserResource($user)],201);
    }

    public function login(Request $r){
        $r->validate(['email'=>'required|email','password'=>'required']);
        if (!auth()->attempt($r->only('email','password'))) return response()->json(['message'=>'Invalid credentials'],401);
        $user = auth()->user();
        $token = $user->createToken('api-token')->accessToken;
        return response()->json(['token'=>$token,'user'=>new UserResource($user)]);
    }

    public function me(){ return new UserResource(auth()->user()); }
    public function logout(){ auth()->user()->token()->revoke(); return response()->json(['message'=>'Logged out']); }

    public function dashboard(){
        $u = auth()->user();
        return response()->json([
            'balance' => $u->wallet->balance ?? 0,
            'active_plans' => $u->plans()->where('status','active')->count(),
            'total_deposits' => $u->deposits()->where('status','approved')->sum('amount'),
            'total_withdrawals' => $u->withdrawals()->where('status','approved')->sum('amount'),
        ]);
    }
}
