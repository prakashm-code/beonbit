<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\checkUser;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\WalletController;
use App\Http\Controllers\API\WithdrawalController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/CompressVideo', [ApiController::class, 'CompressVideo']);
Route::post('/CompressImage', [ApiController::class, 'CompressImage']);

Route::group(['middleware' => checkUser::class], function () {

    Route::get('/get_plan', [PlanController::class, 'index']);
    Route::get('plans/{id}', [PlanController::class, 'show']);
    Route::post('/plan_subscribe', [PlanController::class, 'subscribe']);

    Route::post('/deposite', [DepositController::class, 'request']);

    Route::post('/add_wallet_balance', [WalletController::class, 'addMoney']);
    Route::get('/deposite_history', [DepositController::class, 'history']);

    Route::post('/withdraw', [WithdrawalController::class, 'request']);

    Route::get('/logout', [AuthController::class, 'logout']);


});
