<?php

use App\Http\Controllers\Admin\AccountControler;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BussinessController;
use App\Http\Controllers\Admin\BuySellController;
use App\Http\Controllers\Admin\BuySellDashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GoldPriceController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ReferralDashboardController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\SupplierDashBoardController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Client\Auth\RegistrationController;
use App\Http\Controllers\Client\ClientProductController;
use App\Http\Controllers\Client\TransactionController as ClientTransactionController;
use App\Http\Middleware\ClientPFCheker;
use App\Http\Middleware\otpcheckMiddleware;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Client\Auth\LoginController as ClientLoginController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/auto-logout', function () {
    Auth::logout();
    Session::flush();
    return redirect()->route('login');
})->name('auto.logout')->middleware('auth');


// Route::get('login', [ClientLoginController::class, 'showLoginForm'])->name('client.login');
// Route::post('login', [ClientLoginController::class, 'login'])->name('client.login.save');
// Route::get('register', [RegistrationController::class, 'showRegisterForm'])->name('client.register');

Route::get('privacypolicy', [RegistrationController::class, 'privacypolicy'])->name('privacypolicy');
Route::get('termsandconditions', [RegistrationController::class, 'termsandconditions'])->name('termsandconditions');
Route::get('accountdelation', [RegistrationController::class, 'accountdelation'])->name('accountdelation');
// Route::post(uri: 'register/businessID={}', [RegistrationController::class, 'register'])->name('client.register.save');


Route::post('register', [RegistrationController::class, 'register'])->name('client.register.save');


Route::middleware(['auth:client', ClientPFCheker::class])->prefix('client')->name('client.')->group(function () {
    Route::get('/', [ClientDashboardController::class, 'dashboard'])->name('dashboard')->withoutMiddleware(ClientPFCheker::class);
    Route::post('/logout', [ClientLoginController::class, 'logout'])->name('logout')->withoutMiddleware(ClientPFCheker::class);
    Route::get('/profile', [ClientDashboardController::class, 'profile'])->name('profile')->withoutMiddleware(ClientPFCheker::class);
    Route::post('/save-profile', [ClientDashboardController::class, 'profileUpdate'])->name('profile.save')->withoutMiddleware(ClientPFCheker::class);

    Route::prefix('deposit')->name('deposit.')->group(function () {
        Route::get('/', [ClientTransactionController::class, 'getDeposit'])->name('list');
        Route::get('/create', [ClientTransactionController::class, 'createDeposit'])->name('create');
        Route::post('/store', [ClientTransactionController::class, 'storeDeposit'])->name('store');
    });

    Route::get('/completed-transactions', [ClientTransactionController::class, 'getAllCompletedTransactions'])->name('completed.transactions');
    Route::get('/buy-sell', [ClientTransactionController::class, 'buySell'])->name('buysell');
    Route::post('/transaction-save', [ClientTransactionController::class, 'saveTransaction'])->name('transaction.save');
    Route::post('save-bid', [ClientTransactionController::class, 'saveBid'])->name('save.bid');
    Route::post('buy-sell-store', [ClientTransactionController::class, 'buySellStore'])->name('buysell.store');
    Route::get('/show-statement', [ClientTransactionController::class, 'showStatement'])->name('show.statement');

    //product
    Route::get('/shop', [ClientProductController::class, 'getShopItems'])->name('shop');
});


Route::middleware('guest')->get('/', function () {
    if (url()->current() !== route('client.dashboard')) {
        return redirect()->route('client.dashboard');
    }
});

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('admin/login', [LoginController::class, 'login'])->name('login.save');
Route::get('admin/register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::get('admin/password/reset', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('admin/password/reset', [LoginController::class, 'resetPassword'])->name('password.reset.save');
Route::post('/verify-2fa', [LoginController::class, 'verify2FA'])->name('verify-2fa');
Route::get('/enable2FA', [LoginController::class, 'enable2FA'])->name('enable2FA');
Route::get('/verify2FAform', [LoginController::class, 'verify2FAform'])->name('verify2FAform');

Route::get('validate-reference/{reference}', [BuySellController::class, 'validateReference'])->name('validate.reference');


// Group all admin routes
Route::middleware(['auth:web', 'otpcheckMiddleware'])->prefix('admin')->name('admin.')->group(function () {

    Route::post('password-check', [LoginController::class, 'checkPassword'])->name('password.check');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'changeBussiness'])->name('dashboard');

    // Route::get('/business', action: [DashboardController::class, 'index'])->name('bussiness.list');

    Route::post('/change-business', [DashboardController::class, 'changeBusiness'])->name('change.bussiness');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index')->middleware('acl:users');
        Route::get('/create', [UserController::class, 'create'])->name('create')->middleware('acl:user_action');
        Route::post('/store', [UserController::class, 'store'])->name('store')->middleware('acl:user_action');
        Route::get('/edit/{user}', [UserController::class, 'edit'])->name('edit')->middleware('acl:user_action');
        Route::put('/update/{user}', [UserController::class, 'update'])->name('update')->middleware('acl:user_action');
        Route::get('delete/{id}', [UserController::class, 'destroy'])->name('destroy')->middleware('acl:user_action');
        Route::post('change-password', [UserController::class, 'changePassword'])->name('change.password')->middleware('acl:user_action');
    });

    Route::prefix('project')->name('project.')->group(function () {
        Route::get('/', [ProjectController::class, 'projectList'])->name('list');
        // ->middleware('acl:projects');
        Route::get('/create', [ProjectController::class, 'projectCreate'])->name('create');
        // ->middleware('acl:projects_add');
        Route::post('/store', [ProjectController::class, 'storeProject'])->name('store');
        // ->middleware('acl:projects_add');
        Route::get('/edit/{id}', [ProjectController::class, 'editProject'])->name('edit');
        // ->middleware('acl:projects_edit');
        Route::put('/update/{id}', [ProjectController::class, 'updateProject'])->name('update');
        // ->middleware('acl:projects_edit');
    });


    Route::prefix('role')->name('role.')->group(function () {
        Route::get('/', [RolePermissionController::class, 'roleList'])->name('list')->middleware('acl:roles');
        Route::get('/create', [RolePermissionController::class, 'roleCreate'])->name('create')->middleware('acl:roles_add');
        Route::post('/store', [RolePermissionController::class, 'storeRole'])->name('store')->middleware('acl:roles_add');
        Route::get('/edit/{id}', [RolePermissionController::class, 'editRole'])->name('edit')->middleware('acl:roles_edit');
        Route::put('/update/{id}', [RolePermissionController::class, 'updateRole'])->name('update')->middleware('acl:roles_edit');
    });


    Route::prefix('permission')->name('permission.')->group(function () {
        Route::get('/', [RolePermissionController::class, 'permissionList'])->name('list')->middleware('acl:permissions');
        Route::get('/create', [RolePermissionController::class, 'permissionCreate'])->name('create')->middleware('acl:permissions');
        Route::post('/store', [RolePermissionController::class, 'storePermission'])->name('store')->middleware('acl:permissions');
    });



    Route::group(['prefix' => 'customer'], function () {
        Route::get('/', [CustomerController::class, 'customerList'])->name('customer.list')->middleware('acl:customer');
        Route::get('/customer-create', [CustomerController::class, 'customerCreate'])->name('customer.create')->middleware('acl:customer');
        Route::post('/customer-create-store', [CustomerController::class, 'customerStore'])->name('customer.store')->middleware('acl:customer_action');
        Route::get('/customer-edit/{id}', [CustomerController::class, 'customerEdit'])->name('customer.edit')->middleware('acl:customer_action');
        Route::post('/customer-update', [CustomerController::class, 'customerUpdate'])->name('customer.update')->middleware('acl:customer_action');
        Route::get('/customer-details/{id}', [CustomerController::class, 'customerDetails'])->name('customer.details')->middleware('acl:customer_action');
        Route::get('/customer/{id}', [CustomerController::class, 'customerView'])->name('customer')->middleware('acl:customer_action');
        Route::post('disable-customer', [CustomerController::class, 'disableCustomer'])->name('customer.disable')->middleware('acl:customer_action');
        Route::post('enable-customer', [CustomerController::class, 'enableCustomer'])->name('customer.enable')->middleware('acl:customer_action');
        Route::delete('delete-customer/{id}', [CustomerController::class, 'deleteCustomer'])->name('customer.delete')->middleware('acl:customer_action');
    });


    Route::group(['prefix' => 'buysell'], function () {
        Route::get('/search', [BuySellController::class, 'search'])->name('buysell.customer.search');
        Route::get('/buySellBox', [BuySellController::class, 'buySellBox'])->name('buysell.customer.buysell');
        Route::post('/getCustomerBuySell', [BuySellController::class, 'getCustomerBuySell'])->name('buysell.customer.get.buysell');
        Route::get('/pending', [BuySellController::class, 'pending'])->name('buysell.customer.pending');
        Route::get('/history', [BuySellController::class, 'history'])->name('buysell.customer.history');

        Route::post('/save-bid', [BuySellController::class, 'saveBid'])->name('buysell.save.bid');
        Route::get('/show-trade', [BuySellController::class, 'showTrade'])->name('buysell.showtrade');
        Route::get('/deposit', [BuySellController::class, 'deposit'])->name('buysell.deposit')->middleware('acl:deposit_list');
        // Route::post('/deposit-save', [BuySellController::class, 'depositStore'])->name('buysell.deposit.save')->middleware('acl:deposit_add');
        // Route::post('/deposit-update', [BuySellController::class, 'depositUpdate'])->name('buysell.deposit.update')->middleware('acl:deposit_add');
        Route::post('/deposit-save', [BuySellController::class, 'depositStore'])->name('buysell.deposit.save');
        Route::post('/deposit-update', [BuySellController::class, 'depositUpdate'])->name('buysell.deposit.update');
        Route::get('/show-preview', [BuySellController::class, 'showDepWithList'])->name('buysell.show.preview');
        Route::get('/depsit-withdarw/{customer_id}/{type}', [BuySellController::class, 'depositWithdrawShow'])->name('buysell.deposit_withdraw');
        Route::get('/get-list', [BuySellController::class, 'getDepWithListJson'])->name('buysell.deposit_withdraw_list');
        Route::get('/get-pending', [BuySellController::class, 'getPending'])->name('buysell.get.pending');
        Route::post('/store-pending', [BuySellController::class, 'storePending'])->name('buysell.store.pending');
        Route::get('/match-trade', [BuySellController::class, 'getMatchedTrade'])->name('buysell.match.trade');
        Route::get('/trades-customer/{customer_id}/{transaction_type}/{transaction_qty}', [BuySellController::class, 'getTradesByCustomer'])->name('buysell.trade.search');
        Route::post('/match-trade-store', [BuySellController::class, 'storeMatchedTrade'])->name('buysell.match.trade.store');
        Route::get('/split-trade', [BuySellController::class, 'getSplitTrade'])->name('buysell.split.trade');
        Route::post('/split-trade-store', [BuySellController::class, 'storeSplitTrade'])->name('buysell.split.store');
        Route::get('edit-price', [BuySellController::class, 'editPrice'])->name('buysell.edit.price');
        Route::post('edit-price', [BuySellController::class, 'storePrice'])->name('buysell.store.price');
        Route::post('delete-buy-sell', [BuySellController::class, 'deleteBuySell'])->name('buysell.delete');

        Route::get('edit-pending/{id}', [BuySellController::class, 'editPending'])->name('buysell.pending.edit');

        Route::post('update-pending', [BuySellController::class, 'updatePending'])->name('buysell.pending.update');
        Route::post('delete-pending', [BuySellController::class, 'deletePending'])->name('buysell.pending.delete');
        Route::post('delete-deposit', [BuySellController::class, 'deleteDeposit'])->name('buysell.deposit.delete');

    })->middleware('acl:buysell');


    Route::group(['prefix' => 'transaction'], function () {
        Route::post('/deposit-save-trasncation', [TransactionController::class, 'saveTransaction'])->name('transaction.save');
        Route::post('/send-invoice', [TransactionController::class, 'sendInvoice'])->name('transaction.send.invoice');
        Route::get('/show-statement', [TransactionController::class, 'showStatement'])->name('transaction.show.statement');
        Route::get('/show-running-opening', [TransactionController::class, 'runningTranShow'])->name('transaction.show.runningOpening');
        Route::get('/show-running-opening-list', [TransactionController::class, 'getRunningOpening'])->name('transaction.show.runningOpeningList');
        Route::get('/show-running-pending', [TransactionController::class, 'pendingTranShow'])->name('transaction.show.runningPending');
        Route::get('/show-running-pending-list', [TransactionController::class, 'getRunningPending'])->name('transaction.show.runningPendingList');
        Route::get('/show-running-with-dep-list', [BuySellController::class, 'getCompletedDepWithList'])->name('transaction.show.withDepList');

        Route::get('/search-transaction', [TransactionController::class, 'transactionSearch'])->name('transaction.show.search');
        //Route::post('/search-transaction', [TransactionController::class, 'transactionSearch'])->name('transaction.show.search');

        Route::get('/show-completed-transaction', [TransactionController::class, 'completedTranShow'])->name('transaction.show.completed');
        Route::get('/show-completed-transaction-list', [TransactionController::class, 'getCompletedTransactionList'])->name('transaction.completed.list');
        Route::post('/delete-transaction', [TransactionController::class, 'deleteTransaction'])->name('transaction.delete');
        Route::post('/approve-transaction', [TransactionController::class, 'approveTransaction'])->name('transaction.approve');

    })->middleware('acl:transaction');



    Route::group(['prefix' => 'bussiness'], function () {
        Route::get('/', [BussinessController::class, 'index'])->name('bussiness.list');
        Route::get('/create', [BussinessController::class, 'create'])->name('bussiness.create');
        Route::post('/store', [BussinessController::class, 'store'])->name('bussiness.store');
        Route::get('/edit/{bussiness}', [BussinessController::class, 'edit'])->name('bussiness.edit');
        Route::put('/update/{bussiness}', [BussinessController::class, 'update'])->name('bussiness.update');
        Route::get('/delete/{id}', [BussinessController::class, 'delete'])->name('bussiness.delete');
        Route::get('/map', [BussinessController::class, 'map'])->name('bussiness.map');
        Route::get('/create-map', [BussinessController::class, 'createMap'])->name('bussiness.create_map');
        Route::post('/store-map', [BussinessController::class, 'storeMap'])->name('bussiness.store_map');
        Route::get('/delete-map/{id}', [BussinessController::class, 'deleteMap'])->name('bussiness.delete_map');
    })->middleware('acl:system_settings');



    ////////////////////////////////////// ----------Supplier ---------------------------------------------//////////////////



    Route::prefix('supplier')->name('supplier.')->group(function () {
        Route::get('/create', [AccountControler::class, 'suplierCreate'])->name('create');
        Route::post('/store', [AccountControler::class, 'suplierStore'])->name('store');
        Route::get('update/{id}', [AccountControler::class, 'supplierUpdate'])->name('update');
        Route::post('edit', [AccountControler::class, 'supplierEdit'])->name('edit');
        Route::get('list', [AccountControler::class, 'suplierList'])->name('list');
        Route::get('details/{id}', [AccountControler::class, 'suplierDetails'])->name('details');
        Route::get('{id}', [AccountControler::class, 'suplierView'])->name('view');
    });


    Route::prefix('client')->name('client.')->group(function () {
        Route::get('details/{id}', [AccountControler::class, 'clientDetails'])->name('details');
    });



    Route::prefix('purchase')->name('purchase.')->group(function () {
        Route::get('list', [AccountControler::class, 'purchaseList'])->name('list');
        Route::get('create', [AccountControler::class, 'purchaseCreate'])->name('create');
        Route::post('store', [AccountControler::class, 'purchaseStore'])->name('store');
        Route::get('edit/{id}', [AccountControler::class, 'purchaseEdit'])->name('edit');
        Route::post('update', [AccountControler::class, 'purchaseUpdate'])->name('update');
        Route::post('additem', [AccountControler::class, 'purchaseAddItem'])->name('additem');
        Route::post('removeitem', [AccountControler::class, 'purchaseRemoveItem'])->name('removeitem');
    });


    Route::post('/listbysupplier', [AccountControler::class, 'viewBySupplier'])->name('listbysupplier');

    Route::get('/fixedpurchase-list', [AccountControler::class, 'fixedPurchaseList'])->name('fixedpurchase.list');
    Route::get('/sale-create', [AccountControler::class, 'sale'])->name('sale');
    Route::post('/sale-store', [AccountControler::class, 'saleStore'])->name('sale.store');
    Route::get('sale-edit/{id}', [AccountControler::class, 'saleEdit'])->name('sale.edit');
    Route::post('sale-update/{id}', [AccountControler::class, 'editStore'])->name('sale.update');


    Route::delete('/purchase-remove/{id}', [AccountControler::class, 'purchaseRemove'])->name('purchase-remove');


    Route::get('/supplier-deposit', [AccountControler::class, 'depositCreate'])->name('supplier.deposit');
    Route::post('/supplier-deposit-store', [AccountControler::class, 'depositStore'])->name('supplier.deposit.store');

    Route::get('/deposit-edit/{id}', [AccountControler::class, 'depositEdit'])->name('supplier.deposit.edit');
    Route::post('/deposit-update/{id}', [AccountControler::class, 'depositUpdate'])->name('supplier.deposit.update');

    Route::get('/depositlist', [AccountControler::class, 'depositlist'])->name('depositlist');
    Route::post('/depositListbysupplier', [AccountControler::class, 'depositListBySupplier'])->name('depositListbysupplier');


    Route::get('/supplier-withdraw', [AccountControler::class, 'withdrawCreate'])->name('supplier.withdraw');
    Route::post('/supplier-withdraw-store', [AccountControler::class, 'withdrawStore'])->name('supplier.withdraw.store');

    Route::get('/withdraw-edit/{id}', [AccountControler::class, 'withdrawEdit'])->name('supplier.withdraw.edit');
    Route::post('/withdraw-update/{id}', [AccountControler::class, 'withdrawUpdate'])->name('supplier.withdraw.update');


    Route::get('/withdrawlist', [AccountControler::class, 'withdrawlist'])->name('withdrawlist');
    Route::post('/withdrawlistbysupplier', [AccountControler::class, 'withdrawBySupplier'])->name('withdrawlistbysupplier');

    Route::delete('/deposit-remove/{id}', [AccountControler::class, 'depositRemove'])->name('deposit-remove');



    Route::prefix('refferal')->name('refferal.')->group(function () {
        Route::get('/dashboard', [ReferralDashboardController::class, 'show'])->name('dashboard');
        Route::get('history/{id}', [ReferralDashboardController::class, 'history'])->name('reward.show');
        Route::get('/', [ReferralDashboardController::class, 'listReferral'])->name('list');
        Route::get('/create', [ReferralDashboardController::class, 'create'])->name('create');
        Route::post('/store', [ReferralDashboardController::class, 'store'])->name('store');
        Route::delete('/{id}', [ReferralDashboardController::class, 'destroy'])->name('delete');
    });


    Route::prefix('product')->name('product.')->group(function () {
        Route::get('/', [ProductController::class, 'list'])->name('list');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/store', [ProductController::class, 'store'])->name('store');
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('delete');
    });

    Route::prefix('product-shop')->name('product.shop.')->group(function () {
        Route::get('/', [ProductController::class, 'shopList'])->name('list');
        Route::get('/create', [ProductController::class, 'shopCreate'])->name('create');
        Route::post('/store', [ProductController::class, 'shopStore'])->name('store');
        Route::delete('/{id}', [ProductController::class, 'shopDestroy'])->name('delete');
        Route::post('/admin/product/shop/update-qty', [ProductController::class, 'updateQty'])->name('updateQty');
    });



    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/buysell', [BuySellDashboardController::class, 'buySellDashboard'])->name('buysell');
        Route::get('/supplier', [SupplierDashBoardController::class, 'supplierDashboard'])->name('supplier');
    });


    Route::prefix('gold-rates')->name('gold-rates.')->group(function () {
        Route::get('/', [GoldPriceController::class, 'getGoldRates'])->name('list');
        Route::get('/update-view', [GoldPriceController::class, 'goldUpdateView'])->name('update.view');
        Route::post('/update-store', [GoldPriceController::class, 'updateGoldRates'])->name('update.store');
    })->middleware('acl:gold_management');

});
