<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Ledger;
use App\Models\Pending;
use App\Services\CustomerService;
use App\Services\TransactionService;
use App\Traits\GolbalHelperTrait;
use Illuminate\Http\Request;
use App\Models\Buysell;
use App\Models\Transaction;
use App\Services\GoldService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use RateLimiter;
use Session;
use Validator;


class BuySellController extends Controller
{

    use GolbalHelperTrait;
    public function __construct(
        private CustomerService $customerService,
        private GoldService $goldService,
        private TransactionService $transactionService
    ) {
        $this->customerService = $customerService;
        $this->goldService = $goldService;
        $this->transactionService = $transactionService;
    }
    public function search(Request $request)
    {
        //dd($request);
        $customer = null;
        $lastTen = collect();
        $deposit = null;
        $withdraw = null;
        $runningBuySell = [];
        $runningBuy = 0;
        $runningSell = 0;
        $buy = null;
        $sell = null;
        $current_amount = null;
        $pending = null;
        if ($request->customer != null) {
            $customer = $this->customerService->customerSerach($request->customer);
            if ($customer) {
                $transactions = $this->transactionService->getTranscations($customer->id, ['deposit', 'withdraw', 'buy', 'sell']);
                list($deposit, $withdraw, $buy, $sell) = $this->transactionService->getTotalAmount($transactions);
                $current_amount = $this->transactionService->getCurrentBalance($customer->id);
                $runningBuySell = Buysell::where(['customer_id' => $customer->id, 'is_running' => 1])
                    ->orderBy('id', 'desc')
                    ->get();
                $runningBuy = $runningBuySell->where('type', 'buy')->sum('tt_quantity');
                $runningSell = $runningBuySell->where('type', 'sell')->sum('tt_quantity');
                $pending = $this->transactionService->getPendings($customer->id);
                $lastTen = $this->transactionService->getLastTenCompletedList($customer->id);
            }
        }
        
        
        
        

        $data = [
            'customer' => $customer,
            'tile' => 'Customer Search',
            'deposit' => $deposit,
            'withdraw' => $withdraw,
            'buy' => $buy,
            'sell' => $sell,
            'current_amount' => number_format($current_amount, 3),
            'runningBuySell' => $runningBuySell,
            'pending' => $pending,
            'lastTen' => $lastTen,
            'runningBuy' => $runningBuy,
            'runningSell' => $runningSell
        ];

        return view('admin.buy_sell.search', data: $data);
    }
    
    public function getTradesByCustomer($customer_id, $transaction_type, $transaction_qty){
        $trades = Buysell::where(['customer_id' => $customer_id, 'is_running' => 1])
                ->where('type', $transaction_type == 'buy' ? 'sell' : 'buy')
                ->where('tt_quantity', $transaction_qty)
                ->orderBy('id', 'desc')
                ->get();
                
        return response()->json([
            'trades' => $trades,
            'status' => 'success'
        ]);
    }
    
    public function getCustomerBuySell(Request $request){
        
            $tran = Transaction::where('customer_id', $request->customer_id)
                ->selectRaw('
                    SUM(CASE WHEN transaction_type = "deposit" THEN transaction_amount ELSE 0 END) as sum_of_deposit,
                    SUM(CASE WHEN transaction_type = "withdraw" THEN transaction_amount ELSE 0 END) as sum_of_withdraw,
                    SUM(CASE WHEN transaction_type = "sell" THEN transaction_amount ELSE 0 END) as sum_of_sell_profit_loss,
                    SUM(CASE WHEN transaction_type = "buy" THEN transaction_amount ELSE 0 END) as sum_of_buy_profit_loss
                ')->first();
                
            $buySell = Buysell::where(['customer_id' => $request->customer_id, 'is_running' => 1])
                ->selectRaw('
                    SUM(CASE WHEN type = "sell" THEN tt_quantity ELSE 0 END) as sum_of_running_sell_ttb,
                    SUM(CASE WHEN type = "buy" THEN tt_quantity ELSE 0 END) as sum_of_running_buy_ttb,
                    SUM(CASE WHEN type = "sell" THEN current_rate - ? ELSE 0 END) as sum_of_running_sell_profit,
                    SUM(CASE WHEN type = "buy" THEN ? - current_rate ELSE 0 END) as sum_of_running_buy_profit,
                    SUM(service_charge) as sum_of_running_service_charge
                ', [$request->sellPrice, $request->sellPrice])->first();
            
            $data = [
                'sum_of_deposit' => number_format($tran->sum_of_deposit, 3),
                'sum_of_withdraw' => number_format($tran->sum_of_withdraw, 3),
                'sum_of_sell_profit_loss' => number_format($tran->sum_of_sell_profit_loss, 3),
                'sum_of_buy_profit_loss' => number_format($tran->sum_of_buy_profit_loss, 3),
                'sum_of_running_buy_ttb' => $buySell->sum_of_running_buy_ttb,
                'sum_of_running_sell_ttb' => $buySell->sum_of_running_sell_ttb,
                'sum_of_running_buy_profit' => $buySell->sum_of_running_buy_profit,
                'sum_of_running_sell_profit' => $buySell->sum_of_running_sell_profit,
                'current_profit_loss' => number_format(($tran->sum_of_sell_profit_loss + $tran->sum_of_buy_profit_loss), 3),
                'current_balance' => $this->transactionService->getCurrentBalance($request->customer_id),
                'equity' => $this->transactionService->getEquity($request->customer_id, $request->sellPrice),
                'sum_of_running_service_charge' => $buySell->sum_of_running_service_charge,
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
    }
    
    public function buySellBox(Request $request)
    {
        $customer = null;
        $lastTen = collect();
        $deposit = null;
        $withdraw = null;
        $runningBuySell = [];
        $runningBuy = 0;
        $runningSell = 0;
        $buy = null;
        $sell = null;
        $current_amount = null;
        $pending = null;
        
        if ($request->customer != null) {
            $customer = $this->customerService->customerSerach($request->customer);
            if ($customer) {
                $transactions = $this->transactionService->getTranscations($customer->id, ['deposit', 'withdraw', 'buy', 'sell']);
                list($deposit, $withdraw, $buy, $sell) = $this->transactionService->getTotalAmount($transactions);
                $current_amount = $this->transactionService->getCurrentBalance($customer->id);
                $runningBuySell = Buysell::where(['customer_id' => $customer->id, 'is_running' => 1])
                    ->orderBy('id', 'desc')
                    ->get();
                $runningBuy = $runningBuySell->where('type', 'buy')->sum('tt_quantity');
                $runningSell = $runningBuySell->where('type', 'sell')->sum('tt_quantity');
                $pending = $this->transactionService->getPendings($customer->id);
                $lastTen = $this->transactionService->getLastTenCompletedList($customer->id);
            }
        }
        

        $data = [
            'customer' => $customer,
            'tile' => 'Customer Search',
            'deposit' => $deposit,
            'withdraw' => $withdraw,
            'buy' => $buy,
            'sell' => $sell,
            'current_amount' => number_format($current_amount, 3),
            'runningBuySell' => $runningBuySell,
            'pending' => $pending,
            'lastTen' => $lastTen,
            'runningBuy' => $runningBuy,
            'runningSell' => $runningSell,
        ];

        return view('admin.buy_sell.buy_sell', data: $data);
    }

    public function validateReference(Request $request, $reference)
    {
        $key = 'validate-reference:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.'
            ], 429);
        }

        RateLimiter::hit($key, 30);

        $exists = DB::table('buysells')->where('reference_no', $reference)->exists() ||
            DB::table('transaction')->where('reference_no', $reference)->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Reference number already used.'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reference number is valid'
        ]);
    }

    public function saveBid(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'reference_no' => 'required|unique:buysells,reference_no|unique:transaction,reference_no',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => $validator->errors()->first()
        //     ]);
        // }


        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'tt_quantity' => 'required|numeric|gt:0',
            'current_rate' => 'required|numeric|gt:0',
            'type' => 'required|in:buy,sell',
            'reference_no' => 'nullable|string|max:255',
        ]);

        $postData = $request->except('_token');
        $postData['tt_quantity'] = (float) $validated['tt_quantity'];

        try {
            // dd($postData);
            $postData['business_id'] = \Request::session()->get(key: 'bussinessId');
            $postData['created_by'] = auth()->user()->full_name;
            $postData['service_charge'] = Customer::find($request->customer_id)->service_charge;
            Buysell::create(attributes: $postData);

            $running = Buysell::where(['customer_id' => $request->customer_id, 'is_running' => 1])
                ->orderBy('id', 'desc')
                ->get();

            $equity = $this->transactionService->getEquity($request->customer_id, $postData['current_rate']);

            $this->cutPosition($running, $postData['current_rate'], $equity, $request->customer_id);


            $data['runningBuySell'] = $running;
            return response()->json([
                'success' => true,
                'html' => view('admin.buy_sell.trade-list', $data)->render()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving bid: ' . $e->getMessage()
            ]);
        }
    }


    public function showTrade(Request $request)
    {
        $data['runningBuySell'] = Buysell::find($request->id);
        $data['running_rate'] = $this->goldService->fetchGoldPrice();


        $customer = Customer::find($data['runningBuySell']->customer_id);
        $charge = isset($customer->service_charge) ? $customer->service_charge : 0;
        $qty = $data['runningBuySell']->tt_quantity - $data['runningBuySell']->close_quanntity;
        $data['transaction_amount'] = $this->goldService->calculatepl($data['runningBuySell']->current_rate, $data['running_rate'], $data['runningBuySell']->type, $qty, $charge);

        return view('admin.buy_sell.show-trade', $data);
    }


    public function deposit(Request $request)
    {
        $customers = null;
        if (isset($request->customerNeeded) && isset($request->customerNeeded) == 1) {
            $customers = $this->customerService->getCustomers();
        }
        return view('admin.buy_sell.deposit', ['id' => $request->id, 'type' => $request->type, 'business_id' => session()->get(key: 'bussinessId'), 'customers' => $customers]);
    }

    public function depositStore(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'quantity' => 'nullable|numeric',
            'starting_rate' => 'nullable|numeric',
            //  'take_profit' => 'nullable|numeric',
            //  'stop_loss' => 'nullable|numeric',
            'current_rate' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $postData = $request->except('_token');

        $businessId = session('bussinessId');
        $postData['business_id'] = $businessId;
        $postData['reference_no'] = 'Close' . time() . $postData['reference_no'];

        // if ($postData['current_rate'] == null) {
        //     $buysell = Buysell::find($postData['reference_row']);
        //     $buysell->take_profit = $postData['take_profit'];
        //     $buysell->stop_loss = $postData['stop_loss'];
        //     $buysell->save();
        //     return redirect()->back()->with('success', 'Updated Successfully');
        // } else

        if ($postData['current_rate'] != null) {
            try {
                $customer = Customer::find($postData['customer_id']);
                $charge = isset($customer->service_charge) ? $customer->service_charge : 0;
                $postData['transaction_amount'] = $this->goldService->calculatepl($postData['starting_rate'], $postData['current_rate'], $postData['transaction_type'], $postData['quantity'], $charge);

                DB::transaction(function () use ($postData) {
                    $trans = Transaction::create($postData);

                    $buysell = Buysell::find($postData['reference_row']);

                    $buysell->close_quanntity += $postData['quantity'];
                    if ($buysell->close_quanntity == $buysell->tt_quantity) {

                        $buysell->is_running = 0;

                        try {
                            $this->transactionService->proccessReferral($buysell->customer_id, $trans->id, $postData['quantity']);
                        } catch (\Exception $e) {
                            Log::error('Error in referral: ' . $e->getMessage());
                        }
                    }
                    $buysell->updated_by = auth()->user()->id;
                    $buysell->save();

                    $running = Buysell::where(['customer_id' => $postData['customer_id'], 'is_running' => 1])
                        ->orderBy('id', 'desc')
                        ->get();
                    $price = $this->goldService->fetchGoldPrice();
                    $equity = $this->transactionService->getEquity($postData['customer_id'], $price);

                    $this->cutPosition($running, $price, $equity, $postData['customer_id']);

                });
                return redirect()->back()->with('success', 'Trade Closed Successfully');
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error saving bid: ' . $e->getMessage()
                ]);
            }
        }
    }

    public function showDepWithList(Request $request)
    {
        $customer = $customer = Customer::find($request->id);
        $transactions = $this->transactionService->getTranscations($request->id, $request->type);
        return view('admin.buy_sell.withdep-preview', [
            'transactionsByType' => $transactions,
            'type' => $request->type,
            'customer' => $customer
        ]);
    }

    public function depositUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'transaction_amount' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        try {
            //  dd($request);
            $this->transactionService->updateDeposit($request);

            $transaction = Transaction::findOrFail($request->id);
            $customer_id = $transaction->customer_id;
            $running = Buysell::where(['customer_id' => $customer_id, 'is_running' => 1])
                ->orderBy('id', 'desc')->get();
            $price = $this->goldService->fetchGoldPrice();
            $equity = $this->transactionService->getEquity($customer_id, $price);
            $this->cutPosition($running, $price, $equity, $customer_id);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function depositWithdrawShow($customer_id, $type)
    {
         
        $customers = $this->customerService->getCustomers();
        $deposit = $this->transactionService->getDepWithDetails('deposit');
        $withdraw = $this->transactionService->getDepWithDetails('withdraw');
        //dd($details);
        $businessId = session('bussinessId');
        return view('admin.transaction.deposit-withdraw', [
            'type' => $type,
            'customers' => $customers,
            'customer_id' => $customer_id,
            'deposit' => $deposit,
            'withdraw' => $withdraw,
            'businessId' => $businessId
        ]);
    }

    public function depositWithdrawList(Request $request, string $type)
    {
        abort_unless(in_array($type, ['deposit', 'withdraw'], true), 404);

        $query = Transaction::query()
            ->with('customer')
            ->where('business_id', session('bussinessId'))
            ->where('transaction_type', $type)
            ->when($request->filled('customer_id'), fn ($query) => $query->where('customer_id', $request->customer_id))
            ->when($request->filled('start_date'), fn ($query) => $query->whereDate('created_at', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn ($query) => $query->whereDate('created_at', '<=', $request->end_date));

        $totalAmount = (clone $query)->sum('transaction_amount');
        $transactions = $query
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.transaction.deposit-withdraw-list', [
            'type' => $type,
            'transactions' => $transactions,
            'customers' => $this->customerService->getCustomers(),
            'totalAmount' => $totalAmount,
        ]);
    }


    public function getCompletedDepWithList(Request $request)
    {
        $transactions = $this->transactionService->getWithDepList($request->type);
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

    public function getDepWithListJson(Request $request)
    {

        $transactions = $this->transactionService->getTranscations($request->id, $request->type, true);

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

    public function getPending(Request $request)
    {
        $customerId = $request->id;
        return view('admin.buy_sell.pending', compact('customerId'));
    }

    public function storePending(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'tt' => 'required|numeric|gt:0',
            'type' => 'required|in:buy,sell',
            'limit_' => 'nullable|numeric',
            'stop_' => 'nullable|numeric',
        ]);

        $limit = $request->limit_;
        $stop_limit = $request->stop_;
        // dd($limit, $stop_limit);
        if ($limit == null && $stop_limit == null) {
            return redirect()->back()->with('error', 'You can not set both limit and stop limit too null');
        }

        $postData = $request->except('_token');


        try {
            $this->transactionService->savePending($postData);
            return redirect()->back()
                ->with('success', 'Pending Created Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error saving pending: ' . $e->getMessage());
        }
    }


    public function getMatchedTrade(Request $request)
    {
        list($trades, $transaction) = $this->transactionService->getRunningTradeByCustomer($request);

        return view('admin.buy_sell.match-trade', compact('trades', 'transaction'));
    }

    public function storeMatchedTrade(Request $request)
    {
        $reference = 'Matched' . time() . rand(1000, 9999);
        $businessId = session('bussinessId');
        $customer = Customer::find($request->customerId);
        $charge = $customer->service_charge ?? 0;
        $transactionType = $request->type;
        $startingRate = $request->starting_rate;
        $selectedTradeRate = $request->selectedTradeRate;
        $quantity = $request->quantity;

        $transactionAmount = $this->goldService->calculatepl(
            $startingRate,
            $selectedTradeRate,
            $transactionType,
            $quantity,
            $charge
        );

        $postData = [
            "reference_no" => $reference,
            "quantity" => $quantity,
            "current_rate" => $selectedTradeRate,
            "business_id" => $businessId,
            "starting_rate" => $startingRate,
            "customer_id" => $customer->id,
            "transaction_type" => $transactionType,
            "reference_table" => "buysells",
            "reference_row" => $request->transactionId,
            "tnx_id" => now(),
            "transaction_amount" => $transactionAmount
        ];

        try {
            DB::transaction(function () use ($postData, $request) {
                $transaction = Transaction::create($postData);
                $buySell = Buysell::find($postData['reference_row']);

                $buySell->increment('close_quanntity', $postData['quantity']);

                if ($buySell->close_quanntity == $buySell->tt_quantity) {
                    $buySell->is_running = 0;
                    $buySell->matched_id = $request->selectedTradeId;
                    Buysell::where('id', $request->selectedTradeId)->update(['is_running' => 3]);
                    $this->transactionService->proccessReferral($buySell->customer_id, $transaction->id, $postData['quantity']);
                }
                $buySell->save();

                $running = Buysell::where(['customer_id' => $transaction->customer_id, 'is_running' => 1])
                    ->orderBy('id', 'desc')
                    ->get();
                $price = $this->goldService->fetchGoldPrice();
                $equity = $this->transactionService->getEquity($transaction->customer_id, $price);

                $this->cutPosition($running, $price, $equity, $transaction->customer_id);


            });

            return redirect()->back()->with('success', 'Match Trade Saved Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error saving Match Trade: ' . $e->getMessage());
        }
    }

    public function getSplitTrade(Request $request)
    {
        $transaction = Buysell::where('is_running', 1)
            ->where('id', $request->transaction_id)->first();

        return view('admin.buy_sell.split', compact('transaction'));
    }

    public function storeSplitTrade(Request $request)
    {
        $transactionId = $request->transactionId;
        $splitQuantity = $request->split_quantity;

        $buysell = Buysell::find(id: $transactionId);
        $total_amount_aed = $splitQuantity * $buysell->current_rate * 3.745 * 3.67;
        

        $splitToSave = [
            "tt_quantity" => $splitQuantity,
            "current_rate" => $buysell->current_rate,
            "total_amount_aed" => $total_amount_aed,
            "close_quanntity" => "0",
            "type" => $buysell->type,
            "service_charge" => Customer::find($buysell->customer_id)->service_charge,
            "cut_position" => "0",
            "reference_no" => "Close" . time() . rand(1000, 9999),
            "customer_id" => $buysell->customer_id,
            "created_by" => auth()->user()->full_name,
            "business_id" => $buysell->business_id,
        ];


        Buysell::create($splitToSave);

        $buysell->decrement('tt_quantity', $splitQuantity);
        $buysell->decrement('total_amount_aed', $total_amount_aed);
        $buysell->save();

        return redirect()->back()->with('success', 'Split Trade Saved Successfully');
    }

    public function editPrice(Request $request)
    {
        $transaction = Buysell::find($request->transaction_id);
        return view('admin.buy_sell.edit-price', compact('transaction'));
    }

    public function storePrice(Request $request)
    {
        $request->validate([
            'transactionId' => 'required|exists:buysells,id',
            'rate' => 'required|numeric',
            'tt' => 'required|numeric|gt:0',
            'type' => 'required|in:buy,sell',
            'swap_charge' => 'required|numeric',
        ]);

        $transactionId = $request->transactionId;
        $rate = $request->rate;
        $buySell = Buysell::find($transactionId);
        $total_amount_aed = $this->goldService->calculatepl($rate, null, $buySell->type, $buySell->tt_quantity);
        $type = $request->type;
        $tt = $request->tt;
        $buySell->current_rate = $rate;
        $buySell->total_amount_aed = $total_amount_aed;
        $buySell->type = $type;
        $buySell->take_profit = $request->take_profit;
        $buySell->stop_loss = $request->stop_loss;
        $buySell->swap_charge = (float) $request->swap_charge;

        $buySell->tt_quantity = $tt;
        $buySell->created_at = $request->created_at;
        $buySell->save();

        $price = $this->goldService->fetchGoldPrice();
        $customer_id = $buySell->customer_id;
        $newEquity = $this->transactionService->getEquity($customer_id, $price);

        $running = Buysell::where(['customer_id' => $customer_id, 'is_running' => 1])
            ->orderBy('id', 'desc')->get();
        $this->cutPosition($running, $price, $newEquity, $customer_id);

        if ($request->expectsJson()) {
            $outstandingQuantity = $buySell->tt_quantity - $buySell->close_quanntity;
            $serviceCharge = $outstandingQuantity * ($buySell->service_charge * 13.7639);
            $totalValue = ($buySell->current_rate * 13.7639 * $outstandingQuantity) + $serviceCharge + $buySell->swap_charge;

            return response()->json([
                'success' => true,
                'trade' => [
                    'created_at' => $buySell->created_at->format('Y-m-d H:i'),
                    'type' => $buySell->type,
                    'quantity' => number_format($outstandingQuantity, 3, '.', ''),
                    'open_rate' => number_format($buySell->current_rate, 3, '.', ''),
                    'total_value' => number_format($totalValue, 3, '.', ''),
                    'service_charge' => number_format($serviceCharge, 3, '.', ''),
                    'swap_charge' => number_format($buySell->swap_charge, 3, '.', ''),
                    'take_profit' => $buySell->take_profit ?? 0,
                    'stop_loss' => $buySell->stop_loss ?? 0,
                ],
            ]);
        }



        return redirect()->back()->with('success', 'Price Updated Successfully');
    }

    public function deleteBuySell(Request $request)
    {
        $transaction = Buysell::find($request->id);
        if ($transaction->delete()) {
            $running = Buysell::where(['customer_id' => $transaction->customer_id, 'is_running' => 1])
                ->orderBy('id', 'desc')
                ->get();
            $price = $this->goldService->fetchGoldPrice();
            $equity = $this->transactionService->getEquity($transaction->customer_id, $price);

            $this->cutPosition($running, $price, $equity, $transaction->customer_id);

            return response()->json(['success' => 'Deleted successfully'], 200);
        } else {

            return response()->json(['error' => 'Try again later'], 200);
        }
    }


    public function editPending($id)
    {
        $transaction = Pending::find($id);
        return view('admin.buy_sell.pending-edit', compact('transaction'));
    }


    public function updatePending(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'tt' => 'sometimes|numeric',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $data = $request->except('_token');
        // dd($data);
        $transaction = Pending::find($data['id']);
        $transaction->update($data);

        return redirect()->back()->with('success', 'Updated Successfully');
    }


    public function deletePending(Request $request)
    {
        $data = $request->validate([
            'id' => 'required'
        ]);

        Pending::destroy($data['id']);
        return redirect()->back()->with('success', 'Deleted Successfully');
    }


    public function deleteDeposit(Request $request)
    {
        $data = $request->validate([
            'id' => 'required'
        ]);

        $transaction = Transaction::findOrFail($data['id']);

        $customer_id = $transaction->customer_id;

        Transaction::destroy($data['id']);

        $price = $this->goldService->fetchGoldPrice();
        $equity = $this->transactionService->getEquity($customer_id, $price);
        $running = Buysell::where(['customer_id' => $customer_id, 'is_running' => 1])
            ->orderBy('id', 'desc')->get();
        $this->cutPosition($running, $price, $equity, $customer_id);
        return response()->json([
            'success' => true,
        ], 200);


    }
}
