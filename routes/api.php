<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\checkUser;
use App\Http\Controllers\Api\ApiController;



Route::get('/checkApi', [ApiController::class, 'checkApi']);
Route::post('/CompressVideo', [ApiController::class, 'CompressVideo']);
Route::post('/CompressImage', [ApiController::class, 'CompressImage']);

Route::group(['middleware' => checkUser::class], function () {});
