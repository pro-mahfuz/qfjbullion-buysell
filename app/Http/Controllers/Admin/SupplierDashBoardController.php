<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierDashBoardController extends Controller
{

    public function supplierDashboard()
    {
        $clientsBalance = 0;
        $suppliersBalance = 0;
        $clientsIds = [];
        $suppliersIds = [];

        $allSuppliers = Supplier::all();

        $clients = $allSuppliers->filter(function ($supplier) {
            return $supplier->type == '1';
        });

        $suppliers = $allSuppliers->filter(function ($supplier) {
            return $supplier->type == '0';
        });

        $totalClients = $clients->count();
        $totalSuppliers = $suppliers->count();
        // withdaraw - depost - sell amount

        $totalClientBalance = $clients->map(function ($client) use (&$clientsBalance, &$clientsIds) {
            $clientsBalance += $client->deposit_amount - $client->withdraw_amount - $client->sell_amount;
            $clientsIds[] = $client->id;

            return $client;
        });

        $totalSupplierBalance = $suppliers->map(function ($supplier) use (&$suppliersBalance, &$suppliersIds) {
            $suppliersBalance += $supplier->deposit_amount - $supplier->withdraw_amount - $supplier->sell_amount;
            $suppliersIds[] = $supplier->id;
            return $supplier;
        });

        $companyBalance = $clientsBalance - $suppliersBalance;

        $allPurchase = Purchase::all();

        $fixedPurchase = $allPurchase->filter(function ($purchase) {
            return $purchase->type == 'sale';
        });

        $unfixedPurchase = $allPurchase->filter(function ($purchase) {
            return $purchase->type == 'buy';
        });


        $clientFixedPurchase = $fixedPurchase->filter(function ($purchase) use ($clientsIds) {
            return in_array($purchase->client_id, $clientsIds);
        });

        $supplierFixedPurchase = $fixedPurchase->filter(function ($purchase) use ($suppliersIds) {
            return in_array($purchase->supplier_id, $suppliersIds);
        });

        $clientUnfixedPurchase = $unfixedPurchase->filter(function ($purchase) use ($clientsIds) {
            return in_array($purchase->client_id, $clientsIds);
        });

        $supplierUnfixedPurchase = $unfixedPurchase->filter(function ($purchase) use ($suppliersIds) {
            return in_array($purchase->supplier_id, $suppliersIds);
        });


        $totalClientFixedPurchase = $clientFixedPurchase->count();
        $totalSupplierFixedPurchase = $supplierFixedPurchase->count();
        $totalClientFixedPurchaseAmount = $clientFixedPurchase->sum('unfix_total');
        $totalSupplierFixedPurchaseAmount = $supplierFixedPurchase->sum('unfix_total');


        $totalClientUnfixedPurchase = $clientUnfixedPurchase->count();
        $totalSupplierUnfixedPurchase = $supplierUnfixedPurchase->count();
        $totalClientUnfixedPurchaseAmount = $clientUnfixedPurchase->sum('unfix_total');
        $totalSupplierUnfixedPurchaseAmount = $supplierUnfixedPurchase->sum('unfix_total');


        $deposits = Deposit::all();
        $totalDeposit = $deposits->where('type', 'deposit')->sum('deposit_amount');
        $depositCount = $deposits->where('type', 'deposit')->count();

        $totalWithdraw = $deposits->where('type', 'withdraw')->sum('withdraw_amount');
        $withdrawCount = $deposits->where('type', 'withdraw')->count();

        $supplier = Supplier::all();
        $supplierDeposit = $supplier->sum('deposit_amount');

        $supplierDepositCount = $supplier->where('deposit_amount', '>', '0')->count();

        $supplierWithdraw = $supplier->sum('withdraw_amount');

        $supplierWithdrawCount = $supplier->where('withdraw_amount', '>', '0')->count();


        return view('admin.dashboard.supplier')->with([
            'totalClients' => $totalClients,
            'totalSuppliers' => $totalSuppliers,
            'clientsBalance' => $clientsBalance,
            'suppliersBalance' => $suppliersBalance,
            'companyBalance' => $companyBalance,
            'totalClientFixedPurchase' => $totalClientFixedPurchase,
            'totalSupplierFixedPurchase' => $totalSupplierFixedPurchase,
            'totalClientFixedPurchaseAmount' => $totalClientFixedPurchaseAmount,
            'totalSupplierFixedPurchaseAmount' => $totalSupplierFixedPurchaseAmount,
            'totalClientUnfixedPurchase' => $totalClientUnfixedPurchase,
            'totalSupplierUnfixedPurchase' => $totalSupplierUnfixedPurchase,
            'totalClientUnfixedPurchaseAmount' => $totalClientUnfixedPurchaseAmount,
            'totalSupplierUnfixedPurchaseAmount' => $totalSupplierUnfixedPurchaseAmount,
            'totalDeposit' => $totalDeposit,
            'depositCount' => $depositCount,
            'totalWithdraw' => $totalWithdraw,
            'withdrawCount' => $withdrawCount,
            'supplierDeposit' => $supplierDeposit,
            'supplierDepositCount' => $supplierDepositCount,
            'supplierWithdraw' => $supplierWithdraw,
            'supplierWithdrawCount' => $supplierWithdrawCount,
        ]);
    }






}
