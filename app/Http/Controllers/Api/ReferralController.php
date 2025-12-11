<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

class ReferralController extends Controller
{
    public function index(){ return auth()->user()->referrals()->with('wallet')->get(); }
}
