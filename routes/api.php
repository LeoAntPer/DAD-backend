<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\VcardController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\StatisticController;
use App\Http\Controllers\api\TransactionController;
use App\Http\Controllers\api\DefaultCategoryController;

Route::post('auth/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->group(function() {
    Route::apiResource('categories', CategoryController::class);
    Route::get('auth/me', [AuthController::class, 'show_me']);
});
//User Routes
Route::get('users', [UserController::class, 'index']);
Route::get('users/{user}', [UserController::class, 'show']);
Route::post('users', [UserController::class, 'store']);
Route::put('users/{user}', [UserController::class, 'update']);
Route::delete('users/{user}', [UserController::class, 'destroy']);
Route::patch('users/{user}/password', [UserController::class, 'update_password']);
//Vcard Routes
Route::get('vcards', [VcardController::class, 'index']);
Route::get('vcards/{vcard}', [VcardController::class, 'show']);
Route::post('vcards', [VcardController::class, 'store']);
Route::put('vcards/{vcard}', [VcardController::class, 'update']);
Route::delete('vcards/{vcard}', [VcardController::class, 'destroy']);
Route::patch('vcards/{vcard}/password', [VcardController::class, 'update_password']);
Route::patch('vcards/{vcard}/confirmationCode', [VcardController::class, 'update_confirmation_code']);

Route::resource('categories', CategoryController::class);

Route::resource('defaultCategories', DefaultCategoryController::class);

Route::get('transactions', [TransactionController::class, 'index']);
Route::get('transactions/{transaction}', [TransactionController::class, 'show']);
Route::post('transactions', [TransactionController::class, 'store']);
Route::put('transactions/{transaction}', [TransactionController::class, 'update']);
Route::delete('transactions/{transaction}', [TransactionController::class, 'destroy']);
Route::get('vcards/{vcard}/transactions', [TransactionController::class, 'getTransactionsOfVCard']);
Route::get('vcards/{vcard}/latest-transactions', [TransactionController::class, 'getLatestVCardTransactions']);

Route::get('statistics', [StatisticController::class, 'getAdminStatistics']);
Route::get('statistics/{id}', [StatisticController::class, 'getVcardStatistics']);



Route::middleware('auth:api')->post(
    'auth/logout',
    [AuthController::class, 'logout']
);
