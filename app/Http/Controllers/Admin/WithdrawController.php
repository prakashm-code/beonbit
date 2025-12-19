<?php

namespace App\Http\Controllers\admin;

use App\DataTables\WithdrawDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function index(Request $request,WithdrawDataTable $DataTable)
    {
        $title = 'Referral Setting';
        $page = 'admin.withdraw_request.list';
        $js = ['referral'];
        return $DataTable->render('layouts.admin.layout', compact('title', 'page', 'js'));
    }
}
