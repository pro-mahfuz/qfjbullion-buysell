<?php

namespace App\Services;
use App\Constants\Units;
use App\Models\Buysell;
use App\Models\Customer;
use App\Models\Ledger;
use App\Models\Pending;
use App\Models\Referral;
use App\Models\Reward;
use App\Models\Transaction;
use App\Traits\GolbalHelperTrait;
use Carbon\Carbon;
use DB;
use Log;
use Barryvdh\DomPDF\Facade as PDF;
use Str;
use Spatie\PdfToImage\Pdf as PdfToImage;

class TransactionService
{
    use GolbalHelperTrait;
    public function __construct(private WhatsappService $whatsappService, private GoldService $goldservice)
    {
        $this->whatsappService = $whatsappService;
        $this->goldservice = $goldservice;
    }





    public function updateDeposit($data)
    {

        $amount = abs($data->transaction_amount);

        $transaction = Transaction::findOrFail($data->id);
        $transactionDate = Carbon::parse($data->transaction_date)->format('Y-m-d');

        if ($data->type === 'withdraw') {
            $currentBalance = $this->getCurrentBalance($transaction->customer_id);

            if ($currentBalance < $amount) {
                throw new \Exception('Insufficient balance');
            }
            $transaction->transaction_amount = -$amount;
        } else {
            $transaction->transaction_amount = $amount;
        }
        $transaction->transaction_date = $transactionDate;
        $transaction->actual_amount = $data->actual_amount;
        $transaction->note = $data->note;
        $transaction->save();
    }


    public function getTranscations($id, $type, $needPagination = false)
    {
        $type = is_string($type) ? [$type] : (array) $type;

        $trans = Transaction::where('customer_id', $id)
            ->whereIn('transaction_type', $type)
            ->orderBy('transaction_date', 'desc');

        return $needPagination ? $trans->paginate(perPage: 25) : $trans->get();

    }


    public function getTotalAmount($transactions)
    {
        $deposit = $transactions->where('transaction_type', 'deposit')->sum('transaction_amount');
        $withdraw = $transactions->where('transaction_type', 'withdraw')->sum('transaction_amount');
        $buy = $transactions->where('transaction_type', 'buy')->sum('transaction_amount');
        $sell = $transactions->where('transaction_type', 'sell')->sum('transaction_amount');
        return [
            number_format($deposit, 3),
            number_format($withdraw, 3),
            number_format($buy, 3),
            number_format($sell, 3)
        ];
    }


    public function sendInvoice($type, $id, $goldValue, $marketPrice, $startDate = null, $endDate = null)
    {
        // $customer = Customer::find($id);

        return $this->buildInvoice($id, $type, $goldValue, $marketPrice, $startDate, $endDate);

        // list($path, $name) = $this->buildInvoice($id, $type, goldValue: $goldValue, marketPrice: $marketPrice);
        // $mediaId = $this->whatsappService->uploadDocument($path);

        // $id = $this->whatsappService->sendDocument('8801902559900', $mediaId, ucfirst(string: $type) . ' Invoice', $name);
        // dd($id);
        // delete file after sending
        // $this->deleteFile($path);
    }


    public function getStatement($id, $buyPrice, $buysellstartDate = null, $endDate = null): array
    {

        $query = Transaction::where('customer_id', $id)
            ->whereIn('transaction_type', ['sell', 'buy', 'deposit', 'withdraw']);

        $transactions = $query->orderBy('transaction_date', 'desc')->get();


        return [
            $this->generateInvoiceStatement(
                $transactions,
                'statement',
                $id,
                true,
                $buyPrice,
                $buysellstartDate,
                $endDate
            ),
            $transactions
        ];
    }
    private function buildInvoice($id, $type, $goldValue, $marketPrice, $buysellstartDate = null, $endDate = null)
    {

        $transaction = Transaction::where('customer_id', $id)
            ->whereIn('transaction_type', $type == 'statement' ? ['sell', 'buy', 'deposit', 'withdraw'] : [$type])
            ->orderBy('transaction_date', 'desc')
            ->get();


        return $type == 'statement' ? $this->generateInvoiceStatement(
            $transaction,
            $type,
            $id,
            false,
            $marketPrice,
            $buysellstartDate,
            $endDate
        ) : $this->generateInvoiceBuySell($transaction, $type);

    }

    private function generateInvoiceBuySell($transaction, $type): array
    {
        $invoice = 'Invoice-' . ucfirst(string: $type) . '-' . strtoupper(uniqid());
        $pdf = \PDF::loadView(
            'admin.invoice.buy_sell',
            [
                'transactions' => $transaction,
                'type' => $type,
            ]
        )->setPaper('a4', 'landscape');

        return $this->saveOnDisk($pdf, $invoice);

    }



    public function getNetMatched($transactions, $startDate = null, $endDate = null)
    {

        $netMatcheds = collect($transactions)->filter(function ($transaction) use ($startDate, $endDate) {
            $isMatchedType = in_array($transaction->transaction_type, ['buy', 'sell']);
            $isWithinDateRange = !$startDate || !$endDate || ($transaction->transaction_date >= $startDate && $transaction->transaction_date <= $endDate);

            return $isMatchedType && $isWithinDateRange;
        });


        $referenceIds = $netMatcheds->pluck('reference_row')->unique()->filter();

        // Fetch all linked Buysell records in a single query
        $linkedBuysells = Buysell::whereIn('id', $referenceIds)->get()->keyBy('id');

        $netMatcheds->transform(function ($transaction) use ($linkedBuysells) {
            $linkedTrade = $linkedBuysells->get($transaction->reference_row);
            $quantity = is_numeric($transaction->quantity) ? (float) $transaction->quantity : 0;
            if ($quantity <= 0 && $linkedTrade) {
                $quantity = max((float) $linkedTrade->tt_quantity - (float) $linkedTrade->close_quanntity, 0);
            }

            $transaction->linked_buy = $linkedTrade;
            $transaction->display_quantity = $quantity;
            return $transaction;
        });

        return $netMatcheds;
    }

    public function calculateRealisedProfitLoss($transactions): float
    {
        return (float) $this->getNetMatched($transactions)->sum(function ($detail) {
            $closedQuantity = (float) ($detail->display_quantity ?? $detail->quantity);
            $openingTrade = $detail->linked_buy;
            $openingRate = is_numeric($detail->starting_rate)
                ? (float) $detail->starting_rate
                : (float) ($openingTrade->current_rate ?? 0);
            $closingRate = is_numeric($detail->current_rate) ? (float) $detail->current_rate : 0;
            $serviceCharge = $closedQuantity * ((float) ($openingTrade->service_charge ?? 0) * 13.7639);
            $openingQuantity = (float) ($openingTrade->tt_quantity ?? $closedQuantity);
            $swapCharge = $openingQuantity > 0
                ? ((float) ($openingTrade->swap_charge ?? 0) * ($closedQuantity / $openingQuantity))
                : (float) ($openingTrade->swap_charge ?? 0);
            $chargeDirection = $detail->transaction_type === 'sell' ? -1 : 1;
            $totalValue = ($openingRate * 13.7639 * $closedQuantity)
                + ($chargeDirection * ($serviceCharge + $swapCharge));
            $currentValue = $closingRate * 13.7639 * $closedQuantity;

            return $detail->transaction_type === 'buy'
                ? $currentValue - $totalValue
                : $totalValue - $currentValue;
        });
    }

    public function calculateUnrealisedProfitLoss($runningTrades, float $marketPrice): float
    {
        return (float) collect($runningTrades)->sum(function ($trade) use ($marketPrice) {
            $quantity = max((float) $trade->tt_quantity - (float) $trade->close_quanntity, 0);
            $currentValue = $marketPrice * 13.7639 * $quantity;
            $serviceCharge = (float) ($trade->service_charge ?? 0) * 13.7639 * $quantity;
            $swapCharge = (float) ($trade->swap_charge ?? 0);
            $chargeDirection = $trade->type === 'sell' ? -1 : 1;
            $openingValue = ((float) $trade->current_rate * 13.7639 * $quantity)
                + ($chargeDirection * ($serviceCharge + $swapCharge));

            return $trade->type === 'buy'
                ? $currentValue - $openingValue
                : $openingValue - $currentValue;
        });
    }


    private function generateInvoiceStatement($transactions, $type, $id, $isReturn = false, $buyPrice, $startDate = null, $endDate = null)
    {
        // dd($transactions,$startDate,$endDate);
        $netMatched = $this->getNetMatched($transactions, $startDate, $endDate);

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
            $cutPosition,
            $totalQty,
            $equity
        ) = $this->getProfitLoss($transactions, $id, $buyPrice, $startDate, $endDate);

        if ($isReturn) {
            return [
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
            ];
        }

        $invoice = 'Invoice-' . ucfirst($type) . '-' . strtoupper(uniqid());
        $customer = Customer::find($id);
        $marketPrice = $buyPrice;

        $sumBuy = $outSandingPostions->where('type', 'buy')->sum('tt_quantity');
        $sumSell = $outSandingPostions->where('type', 'sell')->sum('tt_quantity');
        $value = $sumBuy - $sumSell;
        $totalProfitLoss = $outSandingPostions->sum('profit_loss') ?? 0;
        $pending = $this->getPendings($customer->id);
        $netMatched = $startDate && $endDate ? $netMatched->whereBetween('transaction_date', [$startDate, $endDate]) : $netMatched;
        $outSandingPostions = $startDate && $endDate ? $outSandingPostions->whereBetween('created_at', [$startDate, $endDate]) : $outSandingPostions;

        // dd($marketPrice);
        // sleep(2);
        $pdf = \PDF::loadView(
            'admin.invoice.statement',
            [
                'transactions' => $transactions,
                'type' => $type,
                'buy' => $buy,
                'sell' => $sell,
                'deposit' => $deposit,
                'withdraw' => $withdraw,
                'profit' => $profit,
                'loss' => $loss,
                'market_price' => $buyPrice ?? $marketPrice,
                'balance' => $currentBalance,
                'open_position_profit_or_loss' => $openPositionProfitOrLoss,
                'outstanding_positions' => $outSandingPostions,
                'net_matched' => $netMatched,
                'cut_position' => $cutPosition,
                'total_qty' => $totalQty,
                'equity' => $equity,
                'customer' => $customer,
                'value' => $value,
                'sumBuy' => $sumBuy,
                'sumSell' => $sumSell,
                'totalProfitLoss' => $totalProfitLoss,
                'pending' => $pending
            ]
        )->setPaper('a4', 'landscape');
        // return $this->saveOnDisk($pdf, $invoice);
        $statementFilename = (preg_replace('/[^A-Za-z0-9_-]+/', '_', $customer->customer_code ?: 'customer-' . $customer->id) ?: 'customer-' . $customer->id) . '.pdf';

        return $pdf->download($statementFilename);
    }

    public function getProfitLoss($transactions, $id, $buyPrice, $startDate = null, $endDate = null): array
    {
        //dd($transactions);
        $buy = $transactions->where('transaction_type', 'buy')->sum('transaction_amount');
        $sell = $transactions->where('transaction_type', 'sell')->sum('transaction_amount');
        $deposit = $transactions->where('transaction_type', 'deposit')->sum('transaction_amount');
        $withdraw = $transactions->where('transaction_type', 'withdraw')->sum('transaction_amount');

        $currentBalance = 0;
        $profit = 0;
        $loss = 0;
        foreach ($transactions as $transaction) {
            if ($transaction && $transaction->transaction_type == 'deposit') {
                $currentBalance += $transaction->transaction_amount;
            } elseif ($transaction && $transaction->transaction_type == 'withdraw') {
                $currentBalance -= abs($transaction->transaction_amount);
            } elseif ($transaction && $transaction->transaction_type == 'buy' || $transaction->transaction_type == 'sell') {
                $currentBalance = $transaction->transaction_amount > 0 ? $currentBalance + abs($transaction->transaction_amount) : $currentBalance - abs($transaction->transaction_amount);
                if ($transaction && $transaction->transaction_amount > 0) {
                    $profit += $transaction->transaction_amount;
                } else {
                    $loss += abs($transaction->transaction_amount);
                }
            }
        }

        $runningBuySell = $this->getBuySell(isRunning: true, id: $id);
        $customer = Customer::find($id);
        $serviceCharge = $customer->service_charge;


        $running = $runningBuySell->map(function ($transaction) use ($buyPrice, $serviceCharge) {
            $qty = $transaction->tt_quantity - $transaction->close_quanntity;
            $newBalance = $this->goldservice->calculatepl($transaction->current_rate, $buyPrice, $transaction->type, $qty, 0);
            $transaction->profit_loss = $newBalance;
            return $transaction;
        });

        list($openPositionProfitOrLoss, $sum, $qtySum, $buyProfit, $sellProfit) = $this->getOpening($runningBuySell, $buyPrice, $serviceCharge);

        $equity = $currentBalance + $sum;

        $cutPositionCalulate = $customer->cutposition ?? 0;

        return [
            $buy,
            $sell,
            $deposit,
            $withdraw,
            $profit,
            $loss,
            number_format($openPositionProfitOrLoss, 3),
            number_format($currentBalance, 3),
            $running,
            number_format($cutPositionCalulate, 3),
            $qtySum,
            $equity
        ];
    }


    // public function getEquity($customerId, $buyPrice)
    // {
    //     $runningBuySell = $this->getBuySell(isRunning: true, id: $customerId);
    //     $currentBalance = $this->getCurrentAmount($customerId);
    //     list($openPositionProfitOrLoss, $sum, $qtySum) = $this->getOpening($runningBuySell, $buyPrice);

    //     $currentBalance + $sum;
    // }

    private function getOpening($buysells, $buyPrice, $serviceCharge): array
    {
        $openPositionProfitOrLoss = 0;
        $sum = 0;
        $qtySum = 0;
        $sellProfit = 0;
        $buyProfit = 0;

        $buysells->map(function ($transaction) use (&$openPositionProfitOrLoss, &$buyPrice, &$sum, &$qtySum, &$serviceCharge, &$sellProfit, &$buyProfit) {
            $qty = $transaction->tt_quantity - $transaction->close_quanntity;
            $newBalance = $this->goldservice->calculatepl($transaction->current_rate, $buyPrice, $transaction->type, $qty, 0);
            
            if ($transaction->type == 'buy') {
                $openPositionProfitOrLoss = $openPositionProfitOrLoss - $newBalance;
                $buyProfit = $openPositionProfitOrLoss - $newBalance;
            } elseif ($transaction->type == 'sell') {
                $openPositionProfitOrLoss = $openPositionProfitOrLoss + $newBalance;
                $sellProfit = $sellProfit + $newBalance;
            }
            // $newBalance
            $sum += $newBalance;
            
            
            $qtySum += $qty;
        });
        
        
        return [$sum, $sum, $qtySum, $buyProfit, $sellProfit];
    }


    private function getBuySell(bool $isRunning, int $id, $startDate = null, $endDate = null)
    {
        return DB::table('buysells')
            ->where('customer_id', $id)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->whereIn('is_running', $isRunning ? [1] : [0, 1])
            ->orderBy('id', 'asc')
            ->get();
    }



    public function getEquity($customerId, $buyPrice)
    {
        $runningBuySell = $this->getBuySell(isRunning: true, id: $customerId);
        $currentBalance = $this->getCurrentBalance($customerId);
        
        //$serviceCharge = DB::table('customers')->where('id', $customerId)->first()->service_charge ?? 0;
        $serviceCharge = 0;
        
        list($openPositionProfitOrLoss, $sum, $qtySum) = $this->getOpening($runningBuySell, $buyPrice, $serviceCharge);
        
        return $currentBalance + $sum;
    }



    private function saveOnDisk($pdf, $invoice)
    {
        $pdfPath = public_path(path: 'uploads/' . $invoice . '.pdf');
        $pdf->save($pdfPath);

        $pdfToImage = new PdfToImage($pdfPath);
        $imagePath = public_path('uploads/' . $invoice . '.png');
        $pdfToImage->saveImage($imagePath);
        $this->addWhiteBackgroundImagick($imagePath);


        $mediaId = $this->whatsappService->uploadDocument($imagePath);
        dd($mediaId);

        $id = $this->whatsappService->sendDocument('8801902559900', $mediaId, 'Statement  Invoice', 'test.png');

    }


    public function addWhiteBackgroundImagick($imagePath)
    {
        // Load the image with Imagick
        $imagick = new \Imagick($imagePath);

        // Ensure the image has an alpha channel
        $imagick->setImageAlphaChannel($imagick::ALPHACHANNEL_REMOVE);

        // Create a white background
        $whiteBg = new \Imagick();
        $whiteBg->newImage($imagick->getImageWidth(), $imagick->getImageHeight(), new \ImagickPixel('white'));

        // Composite the original image onto the white background
        $whiteBg->compositeImage($imagick, $imagick::COMPOSITE_OVER, 0, 0);

        // Save the new image
        $whiteBg->setImageFormat('png');
        $whiteBg->writeImage($imagePath);

        // Clean up
        $imagick->clear();
        $whiteBg->clear();
    }

    private function deleteFile($invoice)
    {
        try {
            unlink($invoice);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function getBuySellList($isRunning)
    {

        return Buysell::where(['is_running' => $isRunning, 'business_id' => session()->get(key: 'bussinessId')])
            ->with('customer')->orderBy('id','desc')->get();
            // ->paginate(25);

    }

    public function getPendingList()
    {
        return Pending::where(['business_id' => session()->get(key: 'bussinessId')])
            ->with('customer')
            ->paginate(25);
    }

    public function getRunningDetails($is_running = 1)
    {
        $businessId = session()->get('bussinessId');

        $runningData = Buysell::where(['is_running' => $is_running, 'business_id' => $businessId])
            ->selectRaw('COUNT(*) as total_orders')
            ->selectRaw('COUNT(DISTINCT customer_id) as total_customers')
            ->selectRaw('SUM(tt_quantity - close_quanntity) as total_tt_trade')
            ->selectRaw('SUM(CASE WHEN type = "buy" THEN (tt_quantity - close_quanntity) ELSE 0 END) as total_buy')
            ->selectRaw('SUM(CASE WHEN type = "sell" THEN (tt_quantity - close_quanntity) ELSE 0 END) as total_sell')
            ->first();

        return collect([
            'Total order' => $runningData->total_orders ?? 0,
            // 'Total Customer' => $runningData->total_customers ?? 0,
            'Total TTB' => $runningData->total_tt_trade ?? 0,
            //    'Total Buy' => $runningData->total_buy ?? 0,
            //      'Total Sell' => $runningData->total_sell ?? 0,
        ]);

    }

    public function getRunningDetailByType($is_running = 1, $type)
    {
        $businessId = session()->get('bussinessId');
        $runningData = Buysell::where([
            'is_running' => $is_running,
            'business_id' => $businessId,
            'type' => $type
        ])
            ->selectRaw('SUM(tt_quantity) as total_tt, SUM(current_rate * tt_quantity) / SUM(tt_quantity) as current_rate_avg')
            ->first();


        return collect([
            'total_tt' => $runningData->total_tt ?? 0,
            'avg' => $runningData->current_rate_avg ? number_format($runningData->current_rate_avg, 3) : 0,
            'sum' => $runningData->total_tt * $runningData->current_rate_avg
        ]);

    }



    public function getPendingDetails()
    {
        $businessId = session()->get('bussinessId');

        $runningData = Pending::where(['business_id' => $businessId])
            ->selectRaw('COUNT(*) as total_orders')
            ->selectRaw('COUNT(DISTINCT customer_id) as total_customers')
            ->selectRaw('SUM(tt) as total_tt_trade')
            ->selectRaw('SUM(CASE WHEN type = "buy" THEN (tt) ELSE 0 END) as total_buy')
            ->selectRaw('SUM(CASE WHEN type = "sell" THEN (tt) ELSE 0 END) as total_sell')
            ->first();

        return [
            'Total order' => $runningData->total_orders ?? 0,
            'Total Customer' => $runningData->total_customers ?? 0,
            'Total TTB' => $runningData->total_tt_trade ?? 0,
            'Total Buy' => $runningData->total_buy ?? 0,
            'Total Sell' => $runningData->total_sell ?? 0,
        ];

    }


    public function savePending($data)
    {

        if ($data['limit_']) {
            $pending = new Pending();
            $pending->business_id = session()->get('bussinessId');
            $pending->customer_id = $data['customer_id'];
            $pending->tt = $data['tt'];
            $pending->ticket_no = $data['ticket_no'];
            $pending->type = $data['type'];
            $pending->current_rate = $this->goldservice->fetchGoldPrice();
            $pending->trade_type = $data['type'];
            $pending->created_by = auth()->user()->full_name;
            $pending->limit = $data['limit_'];
            $pending->save();
        }

        if ($data['stop_']) {
            $pending = new Pending();
            $pending->business_id = session()->get('bussinessId');
            $pending->customer_id = $data['customer_id'];
            $pending->tt = $data['tt'];
            $pending->ticket_no = $data['ticket_no'];
            $pending->type = $data['type'];
            $pending->current_rate = $this->goldservice->fetchGoldPrice();
            $pending->trade_type = $data['type'];
            $pending->created_by = auth()->user()->full_name;
            $pending->stop = $data['stop_'];
            $pending->save();
        }




    }

    public function getPendings($id, $startDate = null, $endDate = null)
    {
        $query = Pending::where([
            'customer_id' => $id,
            'is_processed' => 0,
            'business_id' => session()->get('bussinessId')
        ]);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->orderBy('id', 'desc')->get();
    }


    private function getCutPosition($current_rate, $type, $tt_quantity, $close_quanntity, $currentBalance)
    {
        $buyPrice = $this->goldservice->fetchGoldPrice();
        $qty = $tt_quantity - $close_quanntity;

        if ($qty == 0) {
            return 0;
        }

        $rateDifference = $type === "buy"
            ? $buyPrice - $current_rate
            : $current_rate - $buyPrice;

        $conversionFactor = 3.7463 * 3.674;
        $newBalanceAdjustment = $rateDifference * $conversionFactor * $qty;

        $equity = $currentBalance + $newBalanceAdjustment;

        return $buyPrice - ($equity / (13.7639 * $qty));
    }



    public function getCompletedDetails()
    {
        $businessId = session()->get('bussinessId');

        $runningData = Transaction::where('business_id', $businessId)
            ->selectRaw('
            COUNT(*) as total_orders,
            COUNT(DISTINCT customer_id) as total_customers,
            SUM(quantity) as total_tt_trade,
            SUM(CASE WHEN transaction_type = "buy" THEN quantity ELSE 0 END) as total_buy,
            SUM(CASE WHEN transaction_type = "sell" THEN quantity ELSE 0 END) as total_sell,
            (
                SUM(CASE WHEN transaction_type = "buy" THEN transaction_amount ELSE 0 END) +
                SUM(CASE WHEN transaction_type = "sell" THEN transaction_amount ELSE 0 END)
            ) as total_transaction_amount,
            CASE WHEN COUNT(*) > 0 THEN
                (
                    SUM(CASE WHEN transaction_type = "buy" THEN transaction_amount ELSE 0 END) +
                    SUM(CASE WHEN transaction_type = "sell" THEN transaction_amount ELSE 0 END)
                ) / COUNT(*)
            ELSE 0 END as avg_profit
        ')
            ->first();

        return [
            'Total order' => $runningData->total_orders ?? 0,
            'Total Customer' => $runningData->total_customers ?? 0,
            'Total TTB' => $runningData->total_tt_trade ?? 0,
            'Total Buy' => $runningData->total_buy ?? 0,
            'Total Sell' => $runningData->total_sell ?? 0,
            'AVG Profit' => $runningData->avg_profit ? number_format($runningData->avg_profit, 3) : 0,
        ];

    }



    public function deleteTransaction($id)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::findOrFail($id);

            $buysell = Buysell::where('id', $transaction->reference_row)->first();

            if (!$buysell) {
                throw new \Exception('Buy-sell not found');
            }

            $buysell->close_quanntity -= $transaction->quantity;
            $buysell->is_running = 1;
            $buysell->save();

            if ($buysell->matched_id) {
                $matched = Buysell::where('id', $buysell->matched_id)->first();
                $matched->is_running = 1;
                $matched->save();



            }

            if ($transaction->delete()) {
                $running = Buysell::where(['customer_id' => $buysell->customer_id, 'is_running' => 1])
                    ->orderBy('id', 'desc')
                    ->get();
                $price = $this->goldservice->fetchGoldPrice();
                $equity = $this->getEquity($buysell->customer_id, $price);
                $this->cutPosition($running, $price, $equity, $buysell->customer_id);

            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['success' => 'Transaction deleted successfully'], 200);
    }

    public function approveTransaction($id)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::findOrFail($id);

            $transaction->status = 1;
            $transaction->approved_by = auth()->user()->id;
            $transaction->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['success' => 'Transaction Approved successfully'], 200);
    }

    public function proccessReferral($customerId, $tnxID, $qty = 1)
    {
        // dd($customerId, $tnxID, $qty);
        $referrers = $this->getAllReferrers($customerId);
        $customer = Customer::find($customerId);
        $referral = Referral::find($customer->referral_code);
        $rewardList = [];

        if ($referral) {

            foreach ($referrers as $key => $referrer) {
                if ($key === 'referrer_1') {
                    $rewardList[$referrer] = $referral->first_person_amount ?? 0;
                } else {
                    $numericKey = (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT);
                    $previousReferrerKey = 'referrer_' . ($numericKey - 1);

                    if (isset($rewardList[$referrers[$previousReferrerKey]])) {
                        $rewardList[$referrer] = $rewardList[$referrers[$previousReferrerKey]] * $referral->percentage;
                    }
                }
            }

            $rewardsToInsert = [];
            foreach ($rewardList as $referrer => $reward) {
                $rewardsToInsert[] = [
                    'customer_id' => $referrer,
                    'referral_id' => $referral->referral_id ?? 1,
                    'reward_amount' => ($reward * $qty),
                    'is_disbusted' => 1,
                    'transaction_id' => $tnxID,
                    'business_id' => session()->get('bussinessId')
                ];
            }

            Reward::insert($rewardsToInsert);
        }
    }

    public function getAllReferrers($customerId, $referrers = [])
    {
        $customer = Customer::where('id', $customerId)->first();

        if ($customer && $customer->referrer) {
            $referrerCount = count($referrers) + 1;

            $referrers["referrer_{$referrerCount}"] = $customer->referrer;

            return $this->getAllReferrers($customer->referrer, $referrers);
        }

        return $referrers;
    }


    public function getDepWithDetails($type)
    {
        $businessId = session()->get('bussinessId');

        $withDep = Transaction::where(['business_id' => $businessId])
            ->where('transaction_type', $type)
            ->selectRaw('COUNT(*) as total_orders')
            ->selectRaw('COUNT(DISTINCT customer_id) as total_customers')
            ->selectRaw('SUM(transaction_amount) as total_amount')
            ->first();

        return [
            'Total ' . $type . ' Count' => $withDep->total_orders ?? 0,
            'Total Customer' => $withDep->total_customers ?? 0,
            'Total ' . $type . ' Amount' => $withDep->total_amount ?? 0,
        ];

    }

    public function getWithDepList($type)
    {
        return Transaction::where(['business_id' => session()->get(key: 'bussinessId')])
            ->where('transaction_type', $type)
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }



    public function getAllClosedTransactions()
    {
        // Fetch the paginated transactions
        $transactions = Transaction::whereIn('transaction_type', ['buy', 'sell'])
            ->orderBy('transaction_date', 'desc')
            ->paginate(25);

        $customerIds = $transactions->pluck('customer_id')->unique();
        $customers = Customer::whereIn('id', $customerIds)->get()->keyBy('id');

        foreach ($transactions as $transaction) {
            $customer = $customers->get($transaction->customer_id);
            $transaction->linked_buy = Buysell::where('id', $transaction->reference_row)->first();
            $transaction->customer = $customer;
        }

        // Return the paginated transactions with modifications
        return $transactions;
    }

    public function transactionSearch($ticketNo)
    {
        $ticketNo = (string) $ticketNo;
        $transaction = Transaction::where(["reference_no" => $ticketNo])
            ->get();

        if (!$transaction) {
            return null;
        }
        return $transaction->map(function ($transaction) {
            $customer = Customer::find($transaction->customer_id);
            $transaction->linked_buy = Buysell::where('id', $transaction->reference_row)->first();
            $transaction->customer = $customer;
            return $transaction;
        })->first();

    }


    public function getCompletedList()
    {
        return Transaction::where(['business_id' => session()->get(key: 'bussinessId')])
            ->with('customer')
            ->paginate(25);
    }

    public function getLastTenCompletedList($customerId)
    {
        $trans = Transaction::query()
            ->select([
                'id', 'customer_id', 'reference_no', 'reference_row', 'quantity', 'current_rate',
                'starting_rate', 'transaction_amount', 'transaction_type', 'created_at',
            ])
            ->where(['business_id' => session()->get(key: 'bussinessId')])
            ->whereIn('transaction_type', ['buy', 'sell'])
            ->where('customer_id', $customerId)
            ->with('customer:id,name')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $linkedTrades = Buysell::whereIn('id', $trans->pluck('reference_row')->filter()->unique())
            ->get(['id', 'type', 'current_rate', 'created_at', 'service_charge', 'swap_charge', 'tt_quantity', 'close_quanntity'])
            ->keyBy('id');

        return $trans->each(function ($transaction) use ($linkedTrades) {
            $openingTrade = $linkedTrades->get($transaction->reference_row);
            $quantity = is_numeric($transaction->quantity) ? (float) $transaction->quantity : 0;
            if ($quantity <= 0 && $openingTrade) {
                $quantity = max((float) $openingTrade->tt_quantity - (float) $openingTrade->close_quanntity, 0);
            }
            $openingRate = is_numeric($transaction->starting_rate) ? (float) $transaction->starting_rate : (float) ($openingTrade->current_rate ?? 0);
            $closingRate = is_numeric($transaction->current_rate) ? (float) $transaction->current_rate : 0;
            $serviceCharge = round($quantity * ((float) ($openingTrade->service_charge ?? 0) * 13.7639), 3);
            $openingQuantity = (float) ($openingTrade->tt_quantity ?? $quantity);
            $swapCharge = round($openingQuantity > 0
                ? ((float) ($openingTrade->swap_charge ?? 0) * ($quantity / $openingQuantity))
                : (float) ($openingTrade->swap_charge ?? 0), 3);
            $openingType = $openingTrade->type ?? $transaction->transaction_type;
            $chargeDirection = $openingType === 'sell' ? -1 : 1;
            $totalValue = round(($openingRate * 13.7639 * $quantity) + ($chargeDirection * ($serviceCharge + $swapCharge)), 3);
            $currentValue = round($closingRate * 13.7639 * $quantity, 3);

            $transaction->setAttribute('linked_buy', $openingTrade);
            $transaction->setAttribute('display_quantity', $quantity);
            $transaction->setAttribute('calculated_profit_loss', round(($openingType === 'buy'
                ? $currentValue - $totalValue
                : $totalValue - $currentValue), 3));
        });
    }

    public function getRunningTradeByCustomer($request)
    {
        $customerId = $request->id;
        $transaction = Buysell::where('id', $request->transaction_id)->first();
        // dd($transaction);
        return [
            Buysell::where(['customer_id' => $customerId, 'is_running' => 1])
                ->where('type', $transaction->type == 'buy' ? 'sell' : 'buy')
                ->where('tt_quantity', $transaction->tt_quantity)
                ->orderBy('id', 'desc')
                ->get(),
            $transaction
        ];
    }

}
