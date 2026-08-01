<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buysell;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Services\TransactionService;
use App\Traits\GolbalHelperTrait;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Services\GoldService;

use Session;
use Validator;

class TransactionController extends Controller
{

    use GolbalHelperTrait;

    private $transactionService;
    private $customerService;
    private $goldService;

    public function __construct(
        TransactionService $transactionService,
        CustomerService $customerService,
        GoldService $goldService
    )
    {
        $this->transactionService = $transactionService;
        $this->customerService = $customerService;
        $this->goldService = $goldService;
    }

    public function saveTransaction(Request $request)
    {
        
        $postData = $request->except('_token');
        try {

            $customer_id = $postData['customer_id'];
            $price = $this->goldService->fetchGoldPrice();
            $equity = $this->transactionService->getEquity($customer_id, $price);

            // if (strtoupper($postData['transaction_type']) == "withdraw" || strtoupper($postData['transaction_type']) == strtoupper("withdraw")) {
            //     // $totalTransactionAmountRunning = Buysell::where('customer_id', $postData['customer_id'])->where('is_running', 1)->sum('total_amount_aed');
            //     // $convertToUsd = $totalTransactionAmountRunning / 3.765;
            //     // // dd($convertToUsd,$equity);
            //     // $remainingAmount = $equity - $convertToUsd;
            //     if ($equity - $postData['transaction_amount'] < 0) {
            //         return redirect()->back()->with('error', 'Insufficient balance');
            //     }

            //     $postData['transaction_amount'] = -($postData['transaction_amount']);
            // }
            $postData['business_id'] = \Request::session()->get('bussinessId');
            $postData['created_by'] = \Auth::user()->full_name;
            // Deposits and withdrawals do not have a trade quantity, but the
            // transaction table requires one for every record.
            $postData['quantity'] = is_numeric($postData['quantity'] ?? null) ? $postData['quantity'] : 0;
            //  dd($postData);
            Transaction::create($postData);

            $newEquity = $this->transactionService->getEquity($customer_id, $price);

            $running = Buysell::where(['customer_id' => $customer_id, 'is_running' => 1])
                ->orderBy('id', 'desc')->get();
            $this->cutPosition($running, $price, $newEquity, $customer_id);

            return redirect()->back()->with('success', 'Transaction saved successfully');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving bid: ' . $e->getMessage()
            ]);
        }
    }

    public function sendInvoice(Request $request)
    {
        
        
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'type' => 'required',
            'goldValue' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        $customer = Customer::findOrFail($request->id);
        $statementFilename = (preg_replace('/[^A-Za-z0-9_-]+/', '_', $customer->customer_code ?: 'customer-' . $customer->id) ?: 'customer-' . $customer->id) . '.pdf';

        $startDate = isset($request->start_date) ? $request->start_date . ' 00:00:00' : null;
        $endDate = isset($request->end_date) ? $request->end_date . ' 23:59:59' : null;
        $sellPrice = $request->goldValue;

        if ($sellPrice <= 0) {
            return redirect()->back()->with('error', 'Unable to fetch the gold price. Please try again later.');
        }
        
        $tran = Transaction::where('customer_id', $request->id)
            ->selectRaw('
                SUM(CASE WHEN transaction_type = "deposit" THEN transaction_amount ELSE 0 END) as sum_of_deposit,
                SUM(CASE WHEN transaction_type = "withdraw" THEN transaction_amount ELSE 0 END) as sum_of_withdraw,
                SUM(CASE WHEN transaction_type = "sell" THEN transaction_amount ELSE 0 END) as sum_of_sell_profit_loss,
                SUM(CASE WHEN transaction_type = "buy" THEN transaction_amount ELSE 0 END) as sum_of_buy_profit_loss
            ')->first();
            
        $buySell = Buysell::where(['customer_id' => $request->id, 'is_running' => 1])
            ->selectRaw('
                SUM(CASE WHEN type = "sell" THEN tt_quantity ELSE 0 END) as sum_of_running_sell_ttb,
                SUM(CASE WHEN type = "buy" THEN tt_quantity ELSE 0 END) as sum_of_running_buy_ttb,
                SUM(CASE WHEN type = "sell" THEN current_rate - ? ELSE 0 END) as sum_of_running_sell_profit,
                SUM(CASE WHEN type = "buy" THEN ? - current_rate ELSE 0 END) as sum_of_running_buy_profit,
                SUM(service_charge) as sum_of_running_service_charge
            ', [$sellPrice, $sellPrice])->first();
        
        $data = [
            'sum_of_deposit' => number_format($tran->sum_of_deposit, 3),
            'sum_of_withdraw' => number_format($tran->sum_of_withdraw, 3),
            'sum_of_sell_profit_loss' => number_format($tran->sum_of_sell_profit_loss, 3),
            'sum_of_buy_profit_loss' => number_format($tran->sum_of_buy_profit_loss, 3),
            'sum_of_running_buy_ttb' => $buySell->sum_of_running_buy_ttb,
            'sum_of_running_sell_ttb' => $buySell->sum_of_running_sell_ttb,
            'sum_of_running_buy_profit' => $buySell->sum_of_running_buy_profit,
            'sum_of_running_sell_profit' => $buySell->sum_of_running_sell_profit,
            'sum_of_running_running_profit_loss' => (($buySell->sum_of_running_buy_profit + $buySell->sum_of_running_sell_profit) * 13.7639),
            'current_profit_loss' => number_format(($tran->sum_of_sell_profit_loss + $tran->sum_of_buy_profit_loss), 3),
            'current_balance' => $this->transactionService->getCurrentBalance($request->id),
            'equity' => $this->transactionService->getEquity($request->id, $sellPrice),
            'sum_of_running_service_charge' => $buySell->sum_of_running_service_charge,
        ];
        
        $runningBuySell = Buysell::where(['customer_id' => $request->id, 'is_running' => 1])
                    ->orderBy('id', 'desc')
                    ->get();
    

        // Get statement
        $statement = $this->transactionService->getStatement($request->id, $sellPrice, $startDate, $endDate);
        $transactions = $statement['1'];
        $pending = $this->transactionService->getPendings($request->id, $startDate, $endDate);


        list(
            $buy,
            $sell,
            $deposit,
            $withdraw,
            $profit,
            $loss,
            $openPositionProfitOrLoss,
            $currentBalance,
            $outSandingPostions,
            $netMatched,
            $cutPosition,
            $totalQty,
            $equity
        ) = $statement['0'];
        $sumBuy = $outSandingPostions->where('type', 'buy')->sum('tt_quantity');
        $sumSell = $outSandingPostions->where('type', 'sell')->sum('tt_quantity');
        $value = $sumBuy - $sumSell;
        $totalProfitLoss = $outSandingPostions->sum('profit_loss') ?? 0;

        $netMatched = $startDate && $endDate ? $netMatched->whereBetween('created_at', [$startDate, $endDate]) : $netMatched;
        $outSandingPostions = $startDate && $endDate ? $outSandingPostions->whereBetween('created_at', [$startDate, $endDate]) : $outSandingPostions;
        
        
        // Return the view with all data
        // return view('admin.invoice.statement-new', [
        //     'transactions' => $transactions,
        //     'type' => 'statement',
        //     'buy' => $buy,
        //     'sell' => $sell,
        //     'deposit' => $deposit,
        //     'withdraw' => $withdraw,
        //     'profit' => $profit,
        //     'loss' => $loss,
        //     'market_price' => $sellPrice,
        //     'balance' => $currentBalance,
        //     'open_position_profit_or_loss' => $openPositionProfitOrLoss,
        //     'outstanding_positions' => $outSandingPostions,
        //     'net_matched' => $netMatched,
        //     'id' => $request->id,
        //     'total_qty' => $totalQty,
        //     'cut_position' => $cutPosition,
        //     'equity' => $equity,
        //     'customer' => $this->customerService->getCustomerById($request->id),
        //     'value' => $value,
        //     'sumBuy' => $sumBuy,
        //     'sumSell' => $sumSell,
        //     'totalProfitLoss' => number_format($totalProfitLoss, 3),
        //     'pending' => $pending,
        //     'data' => $data,
        //     'runningBuySell' => $runningBuySell,
        // ]);
        
        $pdf = \PDF::loadView(
            'admin.invoice.statement',
            [
                'transactions' => $transactions,
                'type' => 'statement',
                'buy' => $buy,
                'sell' => $sell,
                'deposit' => $deposit,
                'withdraw' => $withdraw,
                'profit' => $profit,
                'loss' => $loss,
                'market_price' => $sellPrice,
                'balance' => $currentBalance,
                'open_position_profit_or_loss' => $openPositionProfitOrLoss,
                'outstanding_positions' => $outSandingPostions,
                'net_matched' => $netMatched,
                'id' => $request->id,
                'total_qty' => $totalQty,
                'cut_position' => $cutPosition,
                'equity' => $equity,
                'customer' => $customer,
                'value' => $value,
                'sumBuy' => $sumBuy,
                'sumSell' => $sumSell,
                'totalProfitLoss' => number_format($totalProfitLoss, 3),
                'pending' => $pending,
                'data' => $data,
                'runningBuySell' => $runningBuySell,
            ]
        )->setPaper('a4', 'landscape');
        return $pdf->download($statementFilename);

        // $validator = Validator::make($request->all(), [
        //     'id' => 'required',
        //     'type' => 'required'
        // ]);

        // $startDate = isset($request->startDate) ? $request->startDate . ' 00:00:00' : null;
        // $endDate = isset($request->endDate) ? $request->endDate . ' 23:59:59' : null;
        // if ($validator->fails()) {
        //     return redirect()->back()->with('error', 'Invalid request');
        // }
        // return $this->transactionService->sendInvoice(
        //     $request->type,
        //     $request->id,
        //     $request->goldValue,
        //     $request->previousPrice,
        //     $startDate,
        //     $endDate
        // );
        // return response()->json(['success' => true, 'status' => "success", 'message' => 'Invoice sent successfully']);
        
        
        
        // $pdf = \PDF::loadView(
        //     'admin.invoice.statement',
        //     [
        //         'transactions' => $transactions,
        //         'type' => $type,
        //         'buy' => $buy,
        //         'sell' => $sell,
        //         'deposit' => $deposit,
        //         'withdraw' => $withdraw,
        //         'profit' => $profit,
        //         'loss' => $loss,
        //         'market_price' => $buyPrice ?? $marketPrice,
        //         'balance' => $currentBalance,
        //         'open_position_profit_or_loss' => $openPositionProfitOrLoss,
        //         'outstanding_positions' => $outSandingPostions,
        //         'net_matched' => $netMatched,
        //         'cut_position' => $cutPosition,
        //         'total_qty' => $totalQty,
        //         'equity' => $equity,
        //         'customer' => $customer,
        //         'value' => $value,
        //         'sumBuy' => $sumBuy,
        //         'sumSell' => $sumSell,
        //         'totalProfitLoss' => $totalProfitLoss,
        //         'pending' => $pending
        //     ]
        // )->setPaper('a4', 'landscape');
        // return $pdf->download('statement.pdf');


    }


    public function showStatement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'type' => 'required',
            'goldValue' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        $startDate = isset($request->start_date) ? $request->start_date . ' 00:00:00' : null;
        $endDate = isset($request->end_date) ? $request->end_date . ' 23:59:59' : null;
        $sellPrice = $request->goldValue;

        if ($sellPrice <= 0) {
            return redirect()->back()->with('error', 'Unable to fetch the gold price. Please try again later.');
        }
        
        $tran = Transaction::where('customer_id', $request->id)
            ->selectRaw('
                SUM(CASE WHEN transaction_type = "deposit" THEN transaction_amount ELSE 0 END) as sum_of_deposit,
                SUM(CASE WHEN transaction_type = "withdraw" THEN transaction_amount ELSE 0 END) as sum_of_withdraw,
                SUM(CASE WHEN transaction_type = "sell" THEN transaction_amount ELSE 0 END) as sum_of_sell_profit_loss,
                SUM(CASE WHEN transaction_type = "buy" THEN transaction_amount ELSE 0 END) as sum_of_buy_profit_loss
            ')->first();
            
        $buySell = Buysell::where(['customer_id' => $request->id, 'is_running' => 1])
            ->selectRaw('
                SUM(CASE WHEN type = "sell" THEN tt_quantity ELSE 0 END) as sum_of_running_sell_ttb,
                SUM(CASE WHEN type = "buy" THEN tt_quantity ELSE 0 END) as sum_of_running_buy_ttb,
                SUM(CASE WHEN type = "sell" THEN current_rate - ? ELSE 0 END) as sum_of_running_sell_profit,
                SUM(CASE WHEN type = "buy" THEN ? - current_rate ELSE 0 END) as sum_of_running_buy_profit,
                SUM(service_charge) as sum_of_running_service_charge
            ', [$sellPrice, $sellPrice])->first();
        
        $data = [
            'sum_of_deposit' => number_format($tran->sum_of_deposit, 3),
            'sum_of_withdraw' => number_format($tran->sum_of_withdraw, 3),
            'sum_of_sell_profit_loss' => number_format($tran->sum_of_sell_profit_loss, 3),
            'sum_of_buy_profit_loss' => number_format($tran->sum_of_buy_profit_loss, 3),
            'sum_of_running_buy_ttb' => $buySell->sum_of_running_buy_ttb,
            'sum_of_running_sell_ttb' => $buySell->sum_of_running_sell_ttb,
            'sum_of_running_buy_profit' => $buySell->sum_of_running_buy_profit,
            'sum_of_running_sell_profit' => $buySell->sum_of_running_sell_profit,
            'sum_of_running_running_profit_loss' => (($buySell->sum_of_running_buy_profit + $buySell->sum_of_running_sell_profit) * 13.7639),
            'current_profit_loss' => number_format(($tran->sum_of_sell_profit_loss + $tran->sum_of_buy_profit_loss), 3),
            'current_balance' => $this->transactionService->getCurrentBalance($request->id),
            'equity' => $this->transactionService->getEquity($request->id, $sellPrice),
            'sum_of_running_service_charge' => $buySell->sum_of_running_service_charge,
        ];
        
        $runningBuySell = Buysell::where(['customer_id' => $request->id, 'is_running' => 1])
                    ->orderBy('id', 'desc')
                    ->get();
        
        //dd($data);

        // Get statement
        $statement = $this->transactionService->getStatement($request->id, $sellPrice, $startDate, $endDate);
        $transactions = $statement['1'];
        $pending = $this->transactionService->getPendings($request->id, $startDate, $endDate);


        list(
            $buy,
            $sell,
            $deposit,
            $withdraw,
            $profit,
            $loss,
            $openPositionProfitOrLoss,
            $currentBalance,
            $outSandingPostions,
            $netMatched,
            $cutPosition,
            $totalQty,
            $equity
        ) = $statement['0'];
        $sumBuy = $outSandingPostions->where('type', 'buy')->sum('tt_quantity');
        $sumSell = $outSandingPostions->where('type', 'sell')->sum('tt_quantity');
        $value = $sumBuy - $sumSell;
        $totalProfitLoss = $outSandingPostions->sum('profit_loss') ?? 0;

        $netMatched = $startDate && $endDate ? $netMatched->whereBetween('created_at', [$startDate, $endDate]) : $netMatched;
        $outSandingPostions = $startDate && $endDate ? $outSandingPostions->whereBetween('created_at', [$startDate, $endDate]) : $outSandingPostions;
        // Return the view with all data
        return view('admin.invoice.statement-new', [
            'transactions' => $transactions,
            'type' => 'statement',
            'buy' => $buy,
            'sell' => $sell,
            'deposit' => $deposit,
            'withdraw' => $withdraw,
            'profit' => $profit,
            'loss' => $loss,
            'market_price' => $sellPrice,
            'balance' => $currentBalance,
            'open_position_profit_or_loss' => $openPositionProfitOrLoss,
            'outstanding_positions' => $outSandingPostions,
            'net_matched' => $netMatched,
            'id' => $request->id,
            'total_qty' => $totalQty,
            'cut_position' => $cutPosition,
            'equity' => $equity,
            'customer' => $this->customerService->getCustomerById($request->id),
            'value' => $value,
            'sumBuy' => $sumBuy,
            'sumSell' => $sumSell,
            'totalProfitLoss' => number_format($totalProfitLoss, 3),
            'pending' => $pending,
            'data' => $data,
            'runningBuySell' => $runningBuySell,
        ]);
    }


    private function getRules($type)
    {
        $depsoitWithdrawRule = [
            'amount' => 'required',
            'note' => 'string',
            'staff_note' => 'string',
            'id' => 'required'
        ];

        $buySellRule = [
            'id' => 'required',
            'type' => 'required',
        ];

        return $type == 'buy' || $type == 'sell' ? $buySellRule : $depsoitWithdrawRule;
    }

    public function runningTranShow(Request $request)
    {
        $type = $request->input('type', '1');
        $customers = $this->customerService->getCustomers();
        $query = Buysell::query()
            ->where('is_running', $type)
            ->where('business_id', session('bussinessId'))
            ->when($request->filled('customer_id'), fn ($builder) => $builder->where('customer_id', $request->customer_id))
            ->when(in_array($request->trade_type, ['buy', 'sell'], true), fn ($builder) => $builder->where('type', $request->trade_type))
            ->when($request->filled('start_date'), fn ($builder) => $builder->whereDate('created_at', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn ($builder) => $builder->whereDate('created_at', '<=', $request->end_date));

        $summary = (clone $query)->selectRaw('COUNT(*) as total_orders')
            ->selectRaw('SUM(tt_quantity - close_quanntity) as net_ttb')
            ->selectRaw('SUM(CASE WHEN type = "buy" THEN tt_quantity - close_quanntity ELSE 0 END) as buy_ttb')
            ->selectRaw('SUM(CASE WHEN type = "sell" THEN tt_quantity - close_quanntity ELSE 0 END) as sell_ttb')
            ->first();

        $transactions = $query->with('customer')->latest()->paginate(25)->withQueryString();

        return view('admin.transaction.running-opening', [
            'type' => $type,
            'customers' => $customers,
            'summary' => $summary,
            'transactions' => $transactions,
        ]);
    }

    public function getRunningOpening(Request $request)
    {
        $type = $request->type;
        if (!in_array($type, ['1', '3'])) {
            return response()->json(['success' => false, 'message' => 'Invalid type']);
        }

        $transactions = $this->transactionService->getBuySellList($type);
        // dd($transactions);
        return response()->json([
            'data' => $transactions->items(),
            'links' => (string) $transactions->links('pagination::bootstrap-4'),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'total_pages' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
            ],
        ]);
    }

    public function pendingTranShow(Request $request)
    {
        $type = $request->type;
        $customers = $this->customerService->getCustomers();
        $details = $this->transactionService->getPendingDetails();

        return view('admin.transaction.pending', [
            'type' => $type,
            'customers' => $customers,
            'details' => $details
        ]);
    }

    public function getRunningPending(Request $request)
    {
        $transactions = $this->transactionService->getPendingList();
        return response()->json([
            'data' => $transactions->items(),
            'links' => (string) $transactions->links('pagination::bootstrap-4'),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'total_pages' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
            ],
        ]);
    }

    public function completedTranShow(Request $request)
    {
        $query = Transaction::query()
            ->where('business_id', session('bussinessId'))
            ->whereIn('transaction_type', ['buy', 'sell'])
            ->when($request->filled('customer_id'), fn ($builder) => $builder->where('customer_id', $request->customer_id))
            ->when(in_array($request->trade_type, ['buy', 'sell'], true), fn ($builder) => $builder->where('transaction_type', $request->trade_type))
            ->when($request->filled('start_date'), fn ($builder) => $builder->whereDate('created_at', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn ($builder) => $builder->whereDate('created_at', '<=', $request->end_date));

        $summary = (clone $query)->selectRaw('COUNT(*) as total_trades')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(transaction_amount) as total_profit_loss')
            ->selectRaw('SUM(CASE WHEN transaction_type = "buy" THEN 1 ELSE 0 END) as buy_count')
            ->selectRaw('SUM(CASE WHEN transaction_type = "sell" THEN 1 ELSE 0 END) as sell_count')
            ->first();

        $transactions = $query->with('customer')->orderByDesc('created_at')->paginate(25)->withQueryString();
        $linkedBuys = Buysell::whereIn('id', $transactions->pluck('reference_row')->filter()->unique())->get()->keyBy('id');
        $transactions->getCollection()->each(fn ($transaction) => $transaction->linked_buy = $linkedBuys->get($transaction->reference_row));

        return view('admin.transaction.completed', [
            'customers' => Customer::orderBy('name')->get(['id', 'name', 'customer_code']),
            'summary' => $summary,
            'transactions' => $transactions,
        ]);
    }

    public function getCompletedTransactionList(Request $request)
    {
        $transactions = $this->transactionService->getAllClosedTransactions();
        return response()->json([
            'data' => $transactions->items(),
            'links' => (string) $transactions->links('pagination::bootstrap-4'),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'total_pages' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
            ],
        ]);
    }

    public function deleteTransaction(Request $request)
    {
        
        $transaction = Transaction::findOrFail($request->id);
        
        $customer_id = $transaction->customer_id;
        $trans = $this->transactionService->deleteTransaction($request->id);
        //dd($trans);
        $price = $this->goldService->fetchGoldPrice();
        $equity = $this->transactionService->getEquity($customer_id, $price);
        $running = Buysell::where(['customer_id' => $customer_id, 'is_running' => 1])
            ->orderBy('id', 'desc')->get();
        $this->cutPosition($running, $price, $equity, $customer_id);
        //return $trans;
        return view('admin.transaction.search', ['transaction' => null]);

    }

    public function approveTransaction(Request $request)
    {
        return $this->transactionService->approveTransaction($request->id);
    }



    public function transactionSearch(Request $request)
    {
        if (isset($request->ticketNo) && $request->ticketNo != null) {
            $transaction = $this->transactionService->transactionSearch($request->ticketNo);

            if ($transaction) {
                return view('admin.transaction.search', ['transaction' => $transaction]);
            } else {
                return redirect()->back()->with('error', 'No transaction found');
            }

        }


        return view('admin.transaction.search', ['transaction' => null]);

    }
}
