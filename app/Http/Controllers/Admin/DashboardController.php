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

        $totalCategory = 1;
        $totalVideo = 1;
        $totalImage = 2;
        $totalUser = 3;

        return view("layouts.admin.layout", compact(
            'title',
            'page',
            'totalCategory',
            'totalVideo',
            'totalImage',
            'totalUser',
        ));
    }
}
