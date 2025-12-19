<?php

namespace App\Http\Controllers\admin;

use App\DataTables\WithdrawDataTable;
use App\Http\Controllers\Controller;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function index(Request $request, WithdrawDataTable $DataTable)
    {
        $title = 'Withdraw Request';
        $page = 'admin.withdraw_request.list';
        $js = ['withdraw'];
        return $DataTable->render('layouts.admin.layout', compact('title', 'page', 'js'));
    }
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:withdrawals,id',
            'status' => 'required|in:pending,approved,reject'
        ]);

        $withdrawal = WithdrawRequest::findOrFail($request->id);
        $withdrawal->status = $request->status;
        $withdrawal->save();

        return response()->json([
            'status' => 0,
            'message' => 'Status updated successfully'
        ]);
    }
}
