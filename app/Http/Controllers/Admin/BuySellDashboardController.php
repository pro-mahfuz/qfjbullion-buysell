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

        // customer data
        $totalCustomers = Customer::count();
        $activeCusomer = Customer::where('status', '=', 'activated')->count();
        $deactiveCusomer = Customer::where('status', '=', 'deactived')->count();
        
        $totalRunningBuyTTB = Buysell::where('is_running', '=', '1')
            ->where('type', '=', 'buy')
            ->select(DB::raw('SUM(tt_quantity - close_quanntity) as total'))
            ->value('total');
            
        $totalRunningSellTTB = Buysell::where('is_running', '=', '1')
            ->where('type', '=', 'sell')
            ->select(DB::raw('SUM(tt_quantity - close_quanntity) as total'))
            ->value('total');
            
        $totalRunningActiveTTB = $totalRunningBuyTTB - $totalRunningSellTTB;
            
       
        $totalDepositAmount = Transaction::where('transaction_type', '=', 'deposit')
            ->sum('transaction_amount');

        $totalWithDrawAmount = Transaction::where('transaction_type', '=', 'withdraw')
            ->select(DB::raw('SUM(ABS(transaction_amount)) as total'))
            ->value('total');
        
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
