<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuthAdminController extends Controller
{

    public function index(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        $title = 'Login';
        $page = 'auth.admin.login'; // your custom Blade
            $js = ['login'];

        return view("layouts.admin.auth_layout", compact(
            'title',
            'page',
            'js'
        ));
    }
    public function checkLogin(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email|exists:users,email',
                'password' => 'required',
            ],
            [
                'email.required' => 'Please enter email!',
                'email.exists' => 'This email is not registered!',
                'password.required' => 'Please enter password!',
            ]
        );

        $user = User::where('email', $request->email)
            ->where('role', "1")
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('msg_error', 'Invalid credentials!');
        }

        Auth::guard('admin')->login($user);

        if ($request->has('remember')) {
            Cookie::queue('admin_email', $request->email, 120);
            Cookie::queue('admin_password', $request->password, 120);
        } else {
            Cookie::queue(Cookie::forget('admin_email'));
            Cookie::queue(Cookie::forget('admin_password'));
        }

        $sessionArray = [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
        ];

        Session::put('admin', $sessionArray);
        Session::save();
        return redirect()->route('admin.dashboard')->with('msg_success', 'Login successful.');
    }

    public function adminLogout()
    {
        Auth::guard('admin')->logout();
        Session::invalidate();
        Session::regenerateToken();

        return redirect()->route('admin.login');
    }
}
