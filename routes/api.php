<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\checkUser;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ReferralController;
use App\Http\Controllers\API\WalletController;
use App\Http\Controllers\API\WithdrawalController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forget_password', [AuthController::class, 'forgotPassword']);
Route::post('/reset_password', [AuthController::class, 'resetPassword']);
Route::get('/user_profile', [AuthController::class, 'profile']);
Route::post('/CompressVideo', [ApiController::class, 'CompressVideo']);
Route::post('/CompressImage', [ApiController::class, 'CompressImage']);

Route::group(['middleware' => checkUser::class], function () {
    Route::post('/update_profile', [AuthController::class, 'updateProfile']);

    Route::post('/user_dashboard', [AuthController::class, 'dashboard']);
    Route::get('/get_plans', [PlanController::class, 'index']);
    Route::get('plans/{id}', [PlanController::class, 'show']);
    Route::post('/plan_subscribe', [PlanController::class, 'subscribe']);
    Route::get('/user_plans', [PlanController::class, 'myPlans']);

    Route::post('/deposite', [DepositController::class, 'request']);

    Route::post('/add_wallet_balance', [WalletController::class, 'addMoney']);
    Route::get('/get_wallet_balance', [WalletController::class, 'getWallet']);

    Route::get('/get_transactions', [WalletController::class, 'transactions']);
    Route::get('/referral_earning', [ReferralController::class, 'earnings']);


    Route::post('/withdraw_list', [WithdrawalController::class, 'history']);

    Route::post('/withdraw', [WithdrawalController::class, 'withdraw']);

    Route::get('/logout', [AuthController::class, 'logout']);
});
