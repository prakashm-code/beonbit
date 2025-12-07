<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Image;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'Dashboard';
        $page = 'admin.dashboard';

        $totalCategory = Category::count();
        $totalVideo = Video::count();
        $totalImage = Image::count();
        $totalUser = User::count();

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
