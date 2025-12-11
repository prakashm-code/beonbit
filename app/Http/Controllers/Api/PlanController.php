<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\UserPlan;
use Illuminate\Http\Request;
use DB;

class PlanController extends Controller
{
    public function index(){ return Plan::where('is_active',1)->get(); }
    public function show(Plan $plan){ return $plan; }

    public function subscribe(Request $r, Plan $plan){
        $r->validate(['amount'=>'nullable|numeric|min:1']);
        $user = auth()->user();
        $amount = $r->amount ?? $plan->price;
        if ($user->wallet->balance < $amount) return response()->json(['message'=>'Insufficient balance'],400);

        DB::transaction(function() use($user,$plan,$amount){
            $user->wallet->decrement('balance',$amount);
            $up = UserPlan::create([
                'user_id'=>$user->id,
                'plan_id'=>$plan->id,
                'amount'=>$amount,
                'started_at'=>now(),
                'ends_at'=>now()->addDays($plan->duration_days),
                'status'=>'active',
            ]);
            // create transaction
            $user->transactions()->create(['type'=>'subscribe','amount'=>$amount,'balance_after'=>$user->wallet->balance]);
        });

        return response()->json(['message'=>'Subscribed']);
    }
}
