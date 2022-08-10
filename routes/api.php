<?php

use App\Http\Controllers\API\CustomerLoanViewController;
use App\Http\Controllers\API\LoanApproveController;
use App\Http\Controllers\API\LoanController;
use App\Http\Controllers\API\LoanRepaymentController;
use App\Http\Controllers\API\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('login', [LoginController::class, 'index']);
Route::post('register', [LoginController::class, 'register']);

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::get('loans', [CustomerLoanViewController::class, 'index'])->name('loans.index');
    Route::post('loan-requests', [LoanController::class, 'store'])->name('loan-requests');
    Route::post('loan-repayments/repayment/{repayment}', [LoanRepaymentController::class, 'store'])->name('loan-repayments.store');

    Route::middleware('admin')->group(function () {
        Route::post('loan-requests/approve/{loan}', [LoanApproveController::class, 'store'])->name('loan-requests.approve');
    });
});
