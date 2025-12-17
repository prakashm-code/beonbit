<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'Dashboard';
        $page = 'admin.dashboard';

        $totalUser = 1;
        $totalPlans = 1;
        $totalWithdraw = 2;
        $totalDeposit = 3;

        return view("layouts.admin.layout", compact(
            'title',
            'page',
            'totalUser',
            'totalPlans',
            'totalWithdraw',
            'totalDeposit',
        ));
    }
}
