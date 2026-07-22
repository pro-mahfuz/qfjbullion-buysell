<?php

namespace App\Http\Controllers\Client;


use App\Traits\ClientTrait;
use Illuminate\Http\Request;
use Validator;

class TransactionController extends BaseContrller
{
    use ClientTrait;

    public function getDeposit(Request $request)
    {
        if (!$request->has('type')) {
            return redirect()->route('client.dashboard')->with('error', 'Invalid request');
        }

        $deposits = $this->getAllTransactions($request->type);
        // dd($deposits);

        return view('client.deposit.list')->with('deposits', $deposits)
            ->with('type', $request->type);
    }

    public function createDeposit(Request $request)
    {
        $customer_id = $this->getCustomerId();
        $business_id = $this->getBusinessId();
        $type = $request->get('type');
        return view('client.deposit.create', compact('type', 'customer_id', 'business_id'));
    }

    public function storeDeposit(Request $request)
    {
        $response = $this->post('/save-transaction', [], $request->all());

        if ($response->status() == 200) {
            return redirect()->route('client.deposit.list', ['type' => $request->transaction_type])->with('success', $request->transaction_type . 'created successfully');
        }
        return $this->errors($response);
    }


    public function getAllCompletedTransactions()
    {
        $transactions = $this->getAllTransactions(['buy', 'sell']);
        return view('client.transaction.list')->with('transactions', $transactions);
    }


    public function buySell()
    {
        // dd($this->getRunningBuySell());
        $customer = null;
        $lastTen = [];
        $runningBuySell = [];
        $current_amount = null;
        $pending = null;

        $runningBuySell = [];

        $customer = $this->customer();
        if ($customer) {
            $current_amount = $customer['current_amount'];
            $runningBuySell = $this->getRunningBuySell();
            $pending = $this->pendingLists();
            $lastTen = $this->getLastTenTransactions();
        }

        $data = [
            'customer' => $customer,
            'tile' => 'Customer Search',
            'current_amount' => number_format($current_amount, 2),
            'runningBuySell' => $runningBuySell,
            'pending' => $pending,
            'lastTen' => $lastTen,
            'conversion_rate' => $this->getConversionRate(),
            'currency' => $this->getCurrency(),
        ];
        // dd($data);

        return view('client.transaction.buy-sell', data: $data);
    }


    public function saveTransaction(Request $request)
    {
        $response = $this->post('/save-transaction', [], $request->all());
        if ($response->status() == 200) {
            return redirect()->route('client.buysell', ['type' => $request->transaction_type])->with('success', $request->transaction_type . 'created successfully');
        }
        return $this->errors($response);
    }

    public function buySellStore(Request $request)
    {
        dd($request->all());

        $validator = Validator::make($request->all(), [
            'reference_no' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:1',
            'current_rate' => 'required|numeric|min:0',
            'business_id' => 'required|integer',
            'starting_rate' => 'required|numeric|min:0',
            'customer_id' => 'required|integer',
            'transaction_type' => 'required|string|in:buy,sell',
            'reference_table' => 'required|string',
            'reference_row' => 'required|integer',
            'tnx_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }


        try {
            $result = $this->post('/store-buy-sell', [], $request->all());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error saving Transation: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Updated Successfully');
    }



    public function saveBid(Request $request)
    {

        $rules = [
            "tt_quantity" => "required|numeric|min:0",
            "current_rate" => "required|numeric|min:0",
            "total_amount_aed" => "required|numeric|min:0",
            "close_quanntity" => "required|numeric|min:0",
            "type" => "required|in:buy,sell",
            "cut_position" => "required|numeric|min:0",
            "customer_id" => "required|integer",
            "product_id" => "integer",
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }


        try {
            $this->post('/save-bid', [], $request->all());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving bid: ' . $e->getMessage()
            ]);
        }
        $data = null;
        $data['runningBuySell'] = $this->getRunningBuySell();
        return response()->json([
            'success' => true,
            'html' => view('client.transaction.trade-list', $data)->render()
        ]);
    }


    public function showStatement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'goldValue' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $response = $this->get('/get-statement', [
            'buy_price' => $request->goldValue,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        if ($response->status() == 200) {
            $data = $response->json();
            if ($data == null) {
                return redirect()->back()->with('error', 'No data found');
            }
            $statement = $data['data']['statement'];

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
            ) = $statement[0];
            $sumBuy = $data['data']['sumBuy'];
            $sumSell = $data['data']['sumSell'];
            $totalProfitLoss = $data['data']['totalProfitLoss'];
            $value = $data['data']['value'];
            $transactions = $statement[1];


            return view('client.statements.show', [
                'transactions' => $transactions,
                'type' => 'statement',
                'buy' => $buy,
                'sell' => $sell,
                'deposit' => $deposit,
                'withdraw' => $withdraw,
                'profit' => $profit,
                'loss' => $loss,
                'market_price' => $request->goldValue,
                'balance' => $currentBalance,
                'open_position_profit_or_loss' => $openPositionProfitOrLoss,
                'outstanding_positions' => $outSandingPostions,
                'net_matched' => $netMatched,
                'total_qty' => $totalQty,
                'cut_position' => $cutPosition,
                'equity' => $equity,
                'id' => session()->get('customer_id'),
                'value' => $value,
                'sumBuy' => $sumBuy,
                'sumSell' => $sumSell,
                'name' => session()->get('name'),
                'totalProfitLoss' => number_format($totalProfitLoss, 2)
            ]);

        }
        return $this->errors($response);
    }

}
