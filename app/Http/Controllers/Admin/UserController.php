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
            $newUser->name = $validated['first_name'];
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
}
