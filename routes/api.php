<?php

use App\Http\Controllers\Admin\GoldPriceController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\BuySellController;
use App\Http\Controllers\Admin\BussinessController;

Route::post('receive-gold-price', [GoldPriceController::class, 'store']);
Route::get('gold-price', [GoldPriceController::class, 'getgoldprice']);
    
    



Route::middleware('auth:api')->get('/verify-token', function (Request $request) {
    return response()->json(['message' => 'Token is valid'], 200);
});


Route::get('gold-rates', [GoldPriceController::class, 'getGoldRatesApi']);
Route::get('privacy-policy', [BussinessController::class, 'privacyPolicy']);
Route::get('terms-and-conditions', [BussinessController::class, 'termsAndConditions']);



Route::post('login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('customers', [CustomerController::class, 'getCustomers']);
    Route::get('customer/{id}', [CustomerController::class, 'getCustomer']);
    Route::post('customers', [CustomerController::class, 'createCustomer']);
    Route::delete('customers/{id}', [CustomerController::class, 'deleteCustomer']);
    Route::post('/profile-update', [ProfileController::class, 'profileUpdate']);
    Route::post('/profile-delete', [ProfileController::class, 'profileDelete']);
    Route::post('upload-image', [ProfileController::class, 'uploadImage']);

    // transaction part
    Route::get('delete-transaction/{id}', [TransactionController::class, 'deleteTransaction']);
    Route::get('transaction-search/{reference_no}', [TransactionController::class, 'transactionSearch']);
    Route::get('get-pendings', [TransactionController::class, 'getPendingList']);
    Route::get('get-depositWithdaws/{id}/{business_id}', [TransactionController::class, 'getDepositWithdrawList']);
    Route::get('get-runnings', [TransactionController::class, 'getRunningList']);
    Route::get('get-pendings/{id}/{business_id}', [TransactionController::class, 'getStatement']);
    Route::get('deposits', [TransactionController::class, 'getDeposits']);
    Route::post('save-transaction', [TransactionController::class, 'saveTransaction']);
    Route::get('transactions', [TransactionController::class, 'getTransactions']);
    Route::get('last-ten-transactions', [TransactionController::class, 'getLastTenTransation']);
    Route::post('save-bid', [TransactionController::class, 'saveBid']);
    Route::get('get-statement', [TransactionController::class, 'getStatement'])->name('transaction.get.statement');

    //buySell part
    Route::post('split-trade', [TransactionController::class, 'storeSplitTrade']);
    Route::post('edit-price', [TransactionController::class, 'editPrice']);
    Route::post('match-trade', [BuySellController::class, 'storeMatchedTrade']);
    Route::post('store-buy-sell', [BuySellController::class, 'depositStore']);
    Route::get('business', [ProfileController::class, 'getBusiness']);
    Route::get('live-data', [TransactionController::class, 'getLiveData']);
    Route::get('dashboard', [TransactionController::class, 'getDashboard']);
    Route::get('product-list', [TransactionController::class, 'getProductList']);
    Route::post('update-trade-status', [TransactionController::class, 'updateTradeStatus']);
    Route::get('download-statement', [TransactionController::class, 'downloadStatement']);
});


