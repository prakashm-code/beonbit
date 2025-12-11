<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

class WalletController extends Controller
{
    public function index(){ $u = auth()->user(); return ['balance'=>$u->wallet->balance ?? 0]; }
    public function transactions(){ return auth()->user()->transactions()->latest()->paginate(20); }
}
