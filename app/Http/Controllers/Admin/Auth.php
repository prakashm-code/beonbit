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

class Auth extends Controller
{
    public function checkLogin(Request $req)
    {
        // dd($req);

        $req->validate(
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

        $adminData = User::where('email', $req->email)->where('role', '1')->first();

        if ($adminData && Hash::check($req->password, $adminData->password)) {
            if ($req->has('remember')) {
                Cookie::queue('admin_email', $adminData->email, 120);
                Cookie::queue('admin_password', $req->password, 120);
            } else {
                Cookie::queue(Cookie::forget('admin_email'));
                Cookie::queue(Cookie::forget('admin_password'));
            }

            $sessionArray = [
                'id' => $adminData->id,
                'name' => $adminData->name,
                'role' => $adminData->role,
            ];

            Session::put('admin', $sessionArray);
            Session::save();

            return redirect()->route('admin.dashboard')->with('msg_success', 'Login successful.');
        } else {
            return redirect()->back()->with('msg_error', 'Invalid credentials!');
        }
    }
}
