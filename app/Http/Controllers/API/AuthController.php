<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Mail\WithdrawalRequestedMail;
use App\Models\UserPlan;
use App\Models\WithdrawRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'          => 'required|string|max:255',
            'last_name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            // 'password'      => 'required|string|min:6|confirmed',
            'password'      => 'required|string|min:6',
            'referral_code' => 'nullable|exists:users,referral_code'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 1,
                'message' => $validator->errors()->first()
            ], 200);
        }
        DB::beginTransaction();

        try {
            $referrer = null;
            if ($request->referral_code) {
                $referrer = User::where('referral_code', $request->referral_code)->first();
            } else {
                $referrer = User::where('email', 'pratiks@yopmail.com')->first();
            }
            $user = User::create([
                'first_name'     => $request->first_name,
                'last_name'     => $request->last_name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'referral_code' => strtoupper(Str::random(8)),
                'referred_by' => $referrer ? $referrer->id : null
            ]);
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'locked_balance' => 0
            ]);
            $token = $user->createToken('Personal Access Token')->accessToken;

            // $hash = sha1($user->email);
            $verifyUrl = url('/verify-email/' . encrypt($user->id));

            Mail::send('emails.verify_email', [
                'name' =>  $user->first_name . ' ' . $user->last_name,
                'verifyUrl' => $verifyUrl
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Verify Your Email');
            });
            DB::commit();


            return response()->json([
                'status' => 0,
                'message' => 'Registration successful',
                'data' => [
                    'user_id' => $user->id,
                    'referral_code' => $user->referral_code,
                    'referred_by' => $user->referred_by,
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 1,
                'message' => 'Registration unsuccessful',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => 1,
                'message' => 'Invalid credentials',
            ]);
        }

        $user = Auth::user();

        DB::table('oauth_access_tokens')
            ->where('user_id', $user->id)
            ->update([
                'revoked' => 0
            ]);

        $token = $user->createToken('api-token')->accessToken;
        $active_plans      = $user->userPlans()->where('status', 'active')->count();
        $total_transactions     = $user->transactions()->count();
        $total_withdrawals  = $user->userPlans()->where('status', 'withdrawn')->sum('amount');

        if ($user->role == "0") {
            return response()->json([
                'status'  => 0,
                'message' => 'Login successful',
                'token'   => $token,
                'data' => [
                    'user_id' => $user->id,
                    'user' => $user,
                    'active_plans' => $active_plans,
                    'total_transactions' => $total_transactions,
                    'total_withdrawals' => $total_withdrawals,
                ]
            ]);
        } else {
            return response()->json([
                'status'  => 1,
                'message' => 'Invalid user',
            ]);
        }
    }

    public function me()
    {
        /** @var User $user */
        $user = Auth::user();

        return new UserResource($user);
    }


    public function profile()
    {
        try {
            $user = Auth::guard('api')->user();
            return response()->json([
                'status'  => 0,
                'message' => 'Profile fetched successfully',
                'data'    => [
                    'id'               => $user->id,
                    'first_name'       => $user->first_name,
                    'last_name'        => $user->last_name,
                    'name'             => $user->name,
                    'email'            => $user->email,
                    'phone'            => $user->phone,
                    'profile' => asset('assets/front/img/profile/' . $user->profile),
                    'country'          => $user->country,
                    'wallet_address'          => $user->address,
                    'id_proof'         => $user->id_proof,
                    'wallet_balance'   => (float)  $user->wallet->balance ?? 0,
                    // 'investment_amount' => (float) $user->investment_amount,
                    'role'             => $user->role == '1' ? 'admin' : 'user',
                    'is_verified'      => $user->is_verified == '1',
                    'referral_code'    => $user->referral_code,
                    'referred_by'      => $user->referred_by,
                    'last_login'       => $user->last_login,
                    'created_at'       => $user->created_at,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 1,
                'message' => 'Failed to fetch profile',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function updateProfile(Request $request)
    {
        // dd($request);
        try {
            DB::beginTransaction();
            $user = Auth::guard('api')->user();
            $validator = Validator::make($request->all(), [
                'first_name' => 'nullable|string|max:100',
                'last_name'  => 'nullable|string|max:100',
                'email'     => 'nullable|email|unique:users,email,' . $user->id,
                // 'mobile'    => 'nullable|string|max:15',
                // 'gender'    => 'nullable|in:male,female,other',
                // 'image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 1,
                    'message' => $validator->errors()->first()
                ], 200);
            }
            if ($request->hasFile('profile') && $request->file('profile')->isValid()) {
                $destinationPath = public_path('assets/front/img/profile');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0775, true);
                }
                if (!empty($user->profile)) {
                    $oldImagePath = $destinationPath . '/' . $user->image;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $image = $request->file('profile');
                $imageName = 'user_' . $user->id . '_' . time() . '.' . $image->extension();
                $image->move($destinationPath, $imageName);
                $user->profile = $imageName;
            }
            $user->first_name = $request->first_name ?? $user->first_name;
            $user->last_name  = $request->last_name ?? $user->last_name;
            $user->email     = $request->email ?? $user->email;
            $user->phone    = $request->phone ?? $user->phone;
            $user->save();
            DB::commit();

            return response()->json([
                'status' => 0,
                'message' => 'Profile updated successfully',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 1,
                'message' => 'Database error'
            ], 500);
        }
    }

    public function dashboard()
    {
        /** @var User $u */
        $u = Auth::guard('api')->user();
        return response()->json([
            'balance'            => $u->wallet->balance ?? 0,
            'active_plans'       => UserPlan::where('status', 'active')->where('user_id', $u->id)->count(),
            'total_withdrawals_approve'     =>  WithdrawRequest::where('user_id', $u->id)->where('status', 'complete')->count(),
            'total_withdrawals'  => WithdrawRequest::where('user_id', $u->id)->where('status', 'complete')->sum('amount') ?? 0,
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 1,
                'message' => $validator->errors()->first()
            ], 200);
        }
        $user = User::where('email', $request->email)->first();
        $token = Str::random(60);
        $minutes = 10;
        $user->reset_token = $token;
        $user->reset_token_expiry = Carbon::now()->addMinutes($minutes);
        $user->save();

        // $resetLink = url('/new-password?token=' . $token);

        $resetLink = 'https://app.infinitewealth.uk/new-password?token=' . $token;

        Mail::send('emails.forgot_password', [
            'user' => $user,
            'resetLink' => $resetLink,
            'minutes' => $minutes,
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Reset Your Password');
        });

        return response()->json([
            'status'  => 0,
            'message' => 'Password reset link sent to your email'
        ], 200);
    }


    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reset_token'    => 'required',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 1,
                'message' => $validator->errors()->first()
            ], 200);
        }

        $user = User::where('reset_token', $request->reset_token)
            ->where('reset_token_expiry', '>=', now())
            ->first();

        if (!$user) {
            return response()->json([
                'status'  => 1,
                'message' => 'Invalid or expired token'
            ], 200);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'reset_token' => null,
            'reset_token_expiry' => null,
        ]);

        return response()->json([
            'status'  => 0,
            'message' => 'Password reset successfully'
        ], 200);
    }

    public function resetNewPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|different:old_password',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 1,
                'message' => $validator->errors()->first()
            ], 200);
        }


        // Authenticated user (API)
        $user = Auth::guard('api')->user();


        // Match old password
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status' => 1,
                'message' => 'Old password is incorrect'
            ], 200);
        }


        $user->update([
            'password' => Hash::make($request->new_password),
        ]);


        return response()->json([
            'status' => 0,
            'message' => 'Password changed successfully'
        ], 200);
    }

    public function verifyEmail(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        $verifyUrl = url('/verify-email/' . encrypt($user->id));

        Mail::send('emails.verify_email', [
            'name' => $user->first_name . ' ' . $user->last_name,
            'verifyUrl' => $verifyUrl
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verify Your Email');
        });

        return response()->json([
            'status'  => 0,
            'message' => 'Verification mail sent successfully'
        ], 200);
    }
    public function verify(Request $request, $id)
    {
        $id = decrypt($id);
        $user = User::find($id);

        if ($user->email_verified_at != "") {
            return view('verification_success');
        }

        $user->update([
            'email_verified_at' => now()
        ]);

        return view('verification_success');
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user();

        if ($user && $user->token()) {
            $user->token()->revoke(); // revoke current token
        }

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function send_mail_default()
    {
        Mail::to('testxyz@yopmail.com')
            ->send(new WithdrawalRequestedMail([
                'email'        => 'ajkaila567@gmail.com',
                'amount'       => 10,
                'commission'   => 1,
                'net_amount'   => 9,
                'status'       => 'pending',
            ]));
        return response()->json([
            'status'  => 0,
            'message' => 'sent',
        ]);
    }
}
