<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckUser;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\DepositController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ReferralController;
use App\Http\Controllers\API\WalletController;
use App\Http\Controllers\API\WithdrawalController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forget_password', [AuthController::class, 'forgotPassword']);
Route::post('/reset_password', [AuthController::class, 'resetPassword']);
Route::get('/user_profile', [AuthController::class, 'profile']);
Route::post('/send_verify_email', [AuthController::class, 'verifyEmail']);

Route::group(['middleware' => CheckUser::class], function () {
    Route::post('/update_profile', [AuthController::class, 'updateProfile']);

    Route::post('/user_dashboard', [AuthController::class, 'dashboard']);
    Route::get('/get_plans', [PlanController::class, 'index']);
    Route::get('plans/{id}', [PlanController::class, 'show']);
    Route::post('/plan_subscribe', [PlanController::class, 'subscribe']);
    Route::post('/user_plans', [PlanController::class, 'myPlans']);

    Route::post('/deposite', [DepositController::class, 'request']);

    Route::post('/add_wallet_balance', [WalletController::class, 'addMoney']);
    Route::get('/get_wallet_balance', [WalletController::class, 'getWallet']);
    Route::post('/get_transactions', [WalletController::class, 'transactions']);

    Route::get('/referral_earning', [ReferralController::class, 'earnings']);
    Route::get('/my_referrals_levelwise', [ReferralController::class, 'myReferralsLevelWise']);


    Route::post('/withdraw_list', [WithdrawalController::class, 'history']);
    Route::post('/withdraw_request', [WithdrawalController::class, 'request']);
    Route::post('/withdraw', [WithdrawalController::class, 'withdraw']);

    Route::get('/logout', [AuthController::class, 'logout']);
});
