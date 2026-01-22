<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\UsersDataTable;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\DataTables\UserPlanDataTable;
use App\Models\Plan;
use App\Models\ReferralSetting;
use App\Models\Transaction;
use App\Models\UserPlan;
use App\Models\Wallet;

class UserController extends Controller
{
    public function index(UsersDataTable $DataTable)
    {
        $title = 'Users';
        $page = 'admin.user.list';
        $js = ['user'];
        return $DataTable->render('layouts.admin.layout', compact('title', 'page', 'js'));
    }
    public function addUser()
    {
        $title = 'Add User';
        $page = 'admin.user.add';
        $js = ['user'];


        return view("layouts.admin.layout", compact(
            'title',
            'page',
            'js'
        ));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name'        => 'required|string|max:255',
                'last_name'         => 'required|string|max:255',
                'email'             => 'required|email|unique:users,email',
                'phone'             => 'nullable|string|max:20',
                'password'          => 'required|min:6',
                'country'           => 'nullable|string|max:255',
            ]);
            DB::beginTransaction();

            $newUser = new User();
            $newUser->first_name = $validated['first_name'];
            $newUser->last_name  = $validated['last_name'];
            $newUser->email      = $validated['email'];
            $newUser->phone      = $validated['phone'] ?? null;
            $newUser->country    = $validated['country'] ?? null;
            $newUser->password   = Hash::make($validated['password']);
            $newUser->role       = "0";
            $newUser->is_verified = "1";
            $newUser->save();
            DB::commit();
            return redirect()->route('admin.user')->with('msg_success', 'User added successfully !');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->with('msg_error', 'User not added' . $e->getMessage());
        }
    }


    public function delete(string $id)
    {
        try {
            DB::beginTransaction();
            $id = decrypt($id);
            // dd($id);
            $user = User::find($id)->delete();
            DB::commit();
            return redirect()->route('admin.user')
                ->with('msg_success', 'User deleted successfully');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route('admin.user')
                ->with('msg_error', 'User not deleted');
        }
    }

    public function checkUserIsExist(Request $request)
    {
        try {
            $category = User::where(['email' => $request->email])->get();
            if (count($category) > 0) {
                if (isset($request->id) && !empty($request->id)) {
                    if ($category[0]->id == decrypt($request->id)) {
                        $return =  true;
                        echo json_encode($return);
                        exit;
                    }
                }
                $return =  false;
            } else {
                $return = true;
            }
            echo json_encode($return);
            exit;
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(false);
        }
    }
    public function deleteMultiple(Request $request)
    {
        try {
            DB::beginTransaction();
            $ids = $request->ids;
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No IDs provided.'
                ]);
            }
            $users = User::whereIn('id', $ids)->get();

            if ($users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No users found.'
                ]);
            }

            User::whereIn('id', $ids)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'users deleted successfully.'
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting users.'
            ]);
        }
    }

    public function UserPlans(UserPlanDataTable $DataTable)
    {
        $title = 'Plans';
        $page = 'admin.user.user_plan_list';
        $js = ['user'];
        return $DataTable->render('layouts.admin.layout', compact('title', 'page', 'js'));
    }

    public function AddUserPlan(string $id)
    {
        $title = 'Add Plans';
        $page = 'admin.user.add_plan';
        $js = ['user'];
        $getplans = Plan::where('status', '1')->get();
        $user_id = decrypt($id);
        $user_email = User::where('id', $user_id)->select('email')->first();
        return view("layouts.admin.layout", compact(
            'title',
            'page',
            'js',
            'user_id',
            'user_email',
            'getplans'
        ));
    }
    public function StoreUserPlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'amount'  => 'required|numeric|min:1'
        ]);

        DB::beginTransaction();
        $user_id = $request->user_id;
        try {
            $plan = Plan::where('id', $request->plan_id)
                ->where('status', 1)
                ->firstOrFail();

            if ($request->amount < $plan->min_amount || $request->amount > $plan->max_amount) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Invalid amount for this plan'
                ], 200);
            }

            $wallet = Wallet::firstOrCreate(
                ['user_id' => $user_id],
                ['balance' => 0, 'locked_balance' => 0]
            );

            $wallet->locked_balance = ($wallet->locked_balance ?? 0) + $request->amount;
            $wallet->save();

            $dailyReturnPercent = $plan->daily_roi;
            $dailyInterestAmount = ($request->amount * $dailyReturnPercent) / 100;

            UserPlan::create([
                'user_id' => $user_id,
                'plan_id' => $plan->id,
                'amount'  => $request->amount,
                'daily_return_percent' => $dailyReturnPercent,
                'daily_interest' => $dailyInterestAmount,
                'total_interest' => 0,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays($plan->duration_days)->toDateString(),
                'status' => 'active'
            ]);

            Transaction::create([
                'user_id' => $user_id,
                'type' => 'debit',
                'category' => 'plan_purchase',
                'amount' => $request->amount,
                'balance_after' => $wallet->balance, // unchanged by design
                'transaction_reference' => 'plan_purchase',
                'description' => 'Plan purchased (amount locked)'
            ]);

            DB::commit();

            return redirect()->route('admin.user')
                ->with('msg_success', 'User plan added successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.user')
                ->with('msg_error', 'User plan not added successfully');
        }
    }

    public function getMyReferralTree(String $id)
    {
        $title = 'User Referral Tree';
        $page = 'admin.user.referral_tree';
        $js = ['user'];
        $user_id = decrypt($id);
        $get_username = User::findOrFail($user_id);
        $levels = ReferralSetting::where('status', '1')
            ->orderBy('from_level')
            ->get();
        $result = [];

        if ($levels->isEmpty()) {
            return $result;
        }

        $maxLevel = (int) $levels->max('to_level');

        $visited = [];

        $queue = [
            ['user_id' => $user_id, 'level' => 0]
        ];

        $visited[$user_id] = true;
        while (!empty($queue)) {
            $current = array_shift($queue);
            if ($current['level'] >= $maxLevel) {
                continue;
            }
            $children = User::where('referred_by', $current['user_id'])
                ->where('role', '0')
                ->select('id', 'first_name', 'last_name', 'email')
                ->get();
            foreach ($children as $child) {
                if (isset($visited[$child->id])) {
                    continue;
                }
                $visited[$child->id] = true;
                $level = $current['level'] + 1;
                $result["level_$level"][] = [
                    'id'    => $child->id,
                    'name'  => trim($child->first_name . ' ' . $child->last_name),
                    'email' => $child->email,
                ];
                $queue[] = [
                    'user_id' => $child->id,
                    'level'   => $level
                ];
            }
        }
        // dd($result);
        return view("layouts.admin.layout", compact(
            'title',
            'page',
            'js',
            'result',
            'get_username'
        ));
    }

    public function ChangeAdminPass(Request $request)
    {
        $title = 'Update Change Password';
        $page = 'admin.change_password';
        $js = ['user'];
        return view("layouts.admin.layout", compact(
            'title',
            'page',
            'js',

        ));
    }

    public function ChangeAdminStore(Request $request)
    {
        // dd($request);
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'password'          => 'required',
            ]);
            $updatepwd = User::where('role', '1')->first();
            $updatepwd->password = Hash::make($validated['password']);;
            $updatepwd->save();

            DB::commit();
            return redirect()->route('admin.dashboard')->with('msg_success', 'Password updated successfully !');
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->back()->with('msg_error', 'Password not updated successfully !' . $e->getMessage());
        }
    }
}
