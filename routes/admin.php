<?php

use App\Http\Controllers\Admin\Auth;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\PrivacyPolicyController;
use App\Http\Controllers\Admin\TermConditionController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\VideoStreamController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuth;
use App\Models\WebPages;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Session;

Route::middleware(AdminAuth::class)->group(function () {
    Route::get('admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('admin/user', [UserController::class, 'index'])->name('admin.user');
    Route::get('admin/add_user', [UserController::class, 'addUser'])->name('admin.user_add');
    Route::post('admin/store_user', [UserController::class, 'store'])->name('admin.user_store');
    Route::get('admin/edit_user/{id}', [UserController::class, 'edit'])->name('admin.user_edit');
    Route::post('admin/update_user', [UserController::class, 'update'])->name('admin.user_update');
    Route::post('admin/delete_user/{id}', [UserController::class, 'delete'])->name('admin.user_delete');
    Route::post('admin/delete_multiple_user', [UserController::class, 'deleteMultiple'])->name('admin.delete_multiple_user');
    Route::post('admin/check_user_is_exist', [UserController::class, 'checkUserIsExist'])->name('admin.user_check_exist');

    Route::get('admin/term_conditions', [TermConditionController::class, 'index'])->name('admin.term_conditions');
    Route::post('admin/term_conditions_store', [TermConditionController::class, 'store'])->name('admin.term_conditions_store');
    Route::post('admin/term_conditions_edit', [TermConditionController::class, 'edit'])->name('admin.term_conditions_edit');

    Route::get('admin/privacy_policy', [PrivacyPolicyController::class, 'index'])->name('admin.privacy_policy');
    Route::post('admin/privacy_policy_store', [PrivacyPolicyController::class, 'store'])->name('admin.privacy_policy_store');
    Route::post('admin/privacy_policy_edit', [PrivacyPolicyController::class, 'edit'])->name('admin.privacy_policy_edit');


});

Route::get('/admin', function () {
    $title = 'Login';
    $page = 'auth.admin.login';
    $js = ['login'];

    return view("layouts.admin.auth_layout", compact(
        'title',
        'page',
        'js'
    ));
});

Route::controller(Auth::class)->group(function () {
    Route::post('admin/login', 'checkLogin')->name('admin.login');

    Route::get('admin/logout', function () {
        Session::forget('admin');
        return redirect('/admin');
    })->name('admin.logout');
});
