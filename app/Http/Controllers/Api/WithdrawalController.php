<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Withdrawal;

class WithdrawalController extends Controller
{
    public function request(Request $r){
        $r->validate(['amount'=>'required|numeric|min:1','method'=>'required']);
        $user = auth()->user();
        if ($user->wallet->balance < $r->amount) return response()->json(['message'=>'Insufficient balance'],400);
        // reduce balance and create withdrawal (admin approves later)
        $user->wallet->decrement('balance',$r->amount);
        $w = Withdrawal::create([
            'user_id'=>$user->id,'amount'=>$r->amount,'status'=>'pending','method'=>$r->method,'meta'=>[]
        ]);
        $user->transactions()->create(['type'=>'withdraw_request','amount'=>$r->amount,'balance_after'=>$user->wallet->balance]);
        return response()->json(['message'=>'Withdrawal requested','withdrawal'=>$w],201);
    }

    public function history(){ return auth()->user()->withdrawals()->latest()->paginate(20); }
}
