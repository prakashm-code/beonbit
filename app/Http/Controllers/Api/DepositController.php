<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deposit;

class DepositController extends Controller
{
    public function request(Request $r){
        $r->validate(['amount'=>'required|numeric|min:1','method'=>'required']);
        $d = Deposit::create([
            'user_id'=>auth()->id(),
            'amount'=>$r->amount,
            'status'=>'pending',
            'meta'=>['method'=>$r->method]
        ]);
        return response()->json(['message'=>'Deposit requested','deposit'=>$d],201);
    }

    public function history(){ return auth()->user()->deposits()->latest()->paginate(20); }
}
