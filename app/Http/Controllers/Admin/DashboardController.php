<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'Dashboard';
        $page = 'admin.dashboard';

        $totalUser = User::count();
        $totalPlans = Plan::count();
        $totalWithdrawRequest = WithdrawRequest::where('status', 'pending')->count();
        $totalDeposit = Transaction::where('transaction_reference', 'TOPUP')->sum('amount');
        $totalTransaction = Transaction::count();
        return view("layouts.admin.layout", compact(
            'title',
            'page',
            'totalUser',
            'totalPlans',
            'totalWithdrawRequest',
            'totalDeposit',
            'totalTransaction'
        ));
    }
}
