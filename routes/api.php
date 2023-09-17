<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('account-list', [AccountController::class, 'postUserAccountList']);
Route::post('account-transactions', [TransactionController::class, 'postAccountTransactions']);
Route::post('transfer-fonds', [TransactionController::class, 'postTransferFonds']);


