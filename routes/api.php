<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\LoansController;
use App\Http\Controllers\RepaymentsController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [UsersController::class, 'register']);
Route::post('/loan-request', [LoansController::class, 'create']);
Route::get('/loan-list', [LoansController::class, 'index']);
Route::post('/aproved-loan-status', [LoansController::class, 'aprovedLoanStatus']);
Route::get('customer-view-loan/{id}', [LoansController::class, 'customerViewLoan']);
Route::get('/loan-repayments-list/{id}', [RepaymentsController::class, 'index']);
Route::post('/repayment-customer', [RepaymentsController::class, 'repaymentCustomer']);
