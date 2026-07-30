<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buysell;
use App\Models\Customer;
use App\Models\Pending;
use App\Models\Supplier;
use App\Models\Transaction;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class BuySellDashboardController extends Controller
{

    public function buySellDashboard(Request $request)
    {

        if (!$request->session()->has('bussinessId')) {
            return redirect()->route('admin.dashboard')->with('error', 'Please select a business first.');
        }

        $businessId = $request->session()->get('bussinessId');
        $customerStats = Customer::where('business_id', $businessId)
            ->selectRaw('COUNT(*) as total, SUM(status = "activated") as active, SUM(status = "deactived") as inactive')
            ->first();
        $runningStats = Buysell::where('business_id', $businessId)->where('is_running', 1)
            ->selectRaw('SUM(CASE WHEN type = "buy" THEN tt_quantity - close_quanntity ELSE 0 END) as buy_ttb')
            ->selectRaw('SUM(CASE WHEN type = "sell" THEN tt_quantity - close_quanntity ELSE 0 END) as sell_ttb')
            ->first();
        $transactionStats = Transaction::where('business_id', $businessId)
            ->selectRaw('SUM(CASE WHEN transaction_type = "deposit" THEN transaction_amount ELSE 0 END) as deposits')
            ->selectRaw('SUM(CASE WHEN transaction_type = "withdraw" THEN ABS(transaction_amount) ELSE 0 END) as withdrawals')
            ->first();

        $totalCustomers = $customerStats->total ?? 0;
        $activeCusomer = $customerStats->active ?? 0;
        $deactiveCusomer = $customerStats->inactive ?? 0;
        $totalRunningBuyTTB = $runningStats->buy_ttb ?? 0;
        $totalRunningSellTTB = $runningStats->sell_ttb ?? 0;
        $totalRunningActiveTTB = $totalRunningBuyTTB - $totalRunningSellTTB;
        $totalDepositAmount = $transactionStats->deposits ?? 0;
        $totalWithDrawAmount = $transactionStats->withdrawals ?? 0;
        $totalTransactionAmount = $totalDepositAmount + $totalWithDrawAmount;


        return view('admin.dashboard.buy-sell', compact(
            'totalCustomers',
            'activeCusomer',
            'deactiveCusomer',
            'totalRunningBuyTTB',
            'totalRunningSellTTB',
            'totalRunningActiveTTB',
            'totalDepositAmount',
            'totalWithDrawAmount',
            'totalTransactionAmount'
        ));
    }
}
