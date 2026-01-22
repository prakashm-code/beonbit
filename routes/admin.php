<?php

use App\Http\Controllers\Admin\AuthAdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\PrivacyPolicyController;
use App\Http\Controllers\Admin\TermConditionController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\VideoStreamController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ReferralController;
use App\Http\Controllers\Admin\WithdrawController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\NoCache;
use App\Models\WebPages;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Session;

Route::middleware(AdminAuth::class, NoCache::class)->group(function () {
    Route::get('admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('admin/user', [UserController::class, 'index'])->name('admin.user');
    Route::get('admin/add_user', [UserController::class, 'addUser'])->name('admin.user_add');
    Route::post('admin/store_user', [UserController::class, 'store'])->name('admin.user_store');
    Route::get('admin/edit_user/{id}', [UserController::class, 'edit'])->name('admin.user_edit');
    Route::post('admin/update_user', [UserController::class, 'update'])->name('admin.user_update');
    Route::post('admin/delete_user/{id}', [UserController::class, 'delete'])->name('admin.user_delete');
    Route::post('admin/delete_multiple_user', [UserController::class, 'deleteMultiple'])->name('admin.delete_multiple_user');
    Route::post('admin/check_user_is_exist', [UserController::class, 'checkUserIsExist'])->name('admin.user_check_exist');
    Route::get('admin/user_plans', [UserController::class, 'UserPlans'])->name('admin.user_plan');
    Route::get('admin/add_user_plans/{id}', [UserController::class, 'AddUserPlan'])->name('admin.add_user_plan');
    Route::post('admin/user_plan_store', [UserController::class, 'StoreUserPlan'])->name('admin.user_plan_store');
    Route::get('admin/get_my_referral/{id}', [UserController::class, 'getMyReferralTree'])->name('admin.get_my_referral');

    Route::get('admin/term_conditions', [TermConditionController::class, 'index'])->name('admin.term_conditions');
    Route::post('admin/term_conditions_store', [TermConditionController::class, 'store'])->name('admin.term_conditions_store');
    Route::post('admin/term_conditions_edit', [TermConditionController::class, 'edit'])->name('admin.term_conditions_edit');

    Route::get('admin/privacy_policy', [PrivacyPolicyController::class, 'index'])->name('admin.privacy_policy');
    Route::post('admin/privacy_policy_store', [PrivacyPolicyController::class, 'store'])->name('admin.privacy_policy_store');
    Route::post('admin/privacy_policy_edit', [PrivacyPolicyController::class, 'edit'])->name('admin.privacy_policy_edit');

    Route::get('admin/transactions', [TransactionController::class, 'index'])->name('admin.transaction');

    Route::get('admin/plans', [PlanController::class, 'index'])->name('admin.plan_index');
    Route::get('admin/plans_create', [PlanController::class, 'create'])->name('admin.plan_create');
    Route::post('admin/plans_store', [PlanController::class, 'store'])->name('admin.plan_store');
    Route::get('admin/plans_edit/{id}', [PlanController::class, 'edit'])->name('admin.plan_edit');
    Route::post('admin/plans_update/{id}', [PlanController::class, 'update'])->name('admin.plan_update');
    Route::post('admin/plans_delete/{id}', [PlanController::class, 'destroy'])->name('admin.plan_destroy');
    Route::post('admin/plans_update_status', [PlanController::class, 'updateStatus'])->name('admin.plan_update_status');

    Route::get('admin/referral_setting', [ReferralController::class, 'referralSetting'])->name('admin.referral_setting');
    Route::get('admin/referral_setting_create', [ReferralController::class, 'create'])->name('admin.referral_setting_create');
    Route::post('admin/referral_setting_store', [ReferralController::class, 'store'])->name('admin.referral_setting_store');
    Route::get('admin/referral_setting_edit/{id}', [ReferralController::class, 'edit'])->name('admin.referral_setting_edit');
    Route::post('admin/referral_setting_update/{id}', [ReferralController::class, 'update'])->name('admin.referral_setting_update');
    Route::post('admin/referral_setting_delete/{id}', [ReferralController::class, 'destroy'])->name('admin.referral_setting_destroy');
    Route::post('admin/referral_setting_update_status', [ReferralController::class, 'updateStatus'])->name('admin.referral_setting_update_status');

    Route::get('admin/withdraw_request', [WithdrawController::class, 'index'])->name('admin.withdraw_request');
    Route::post('admin/update_withdraw_status', [WithdrawController::class, 'updateStatus'])->name('admin.update_withdraw_status');


    Route::get('/admin/logout', [AuthAdminController::class, 'adminLogout'])->name('admin.logout');
});

Route::get('/admin', [AuthAdminController::class, 'index'])->name('admin.login');
Route::post('/admin/login', [AuthAdminController::class, 'checkLogin'])->name('admin.checkLogin');
