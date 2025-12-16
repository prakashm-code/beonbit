<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\TransactionsDataTable;


class TransactionController extends Controller
{
    public function index(TransactionsDataTable $DataTable)
    {
        $title = 'Transactions';
        $page = 'admin.transaction.list';
        $js = ['transaction'];
        return $DataTable->render('layouts.admin.layout', compact('title', 'page', 'js'));
    }
}
