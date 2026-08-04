<?php

namespace App\Services\Api;



use App\Models\Product;
use App\Services\GoldService;
use App\Traits\ApiTrait;
use App\Traits\GolbalHelperTrait;
use App\Traits\TransactionTrait;
use Carbon\Carbon;
use DB;
use Log;
use Barryvdh\DomPDF\Facade as PDF;
use Str;
use Spatie\PdfToImage\Pdf as PdfToImage;
use Predis\Client as RedisClient;
use Ramsey\Uuid\Uuid as UUID;

class TransactionService
{
    use ApiTrait, TransactionTrait, GolbalHelperTrait;

    private $redis = null;


    public function __construct(private GoldService $goldservice)
    {
        $this->goldservice = $goldservice;
        $this->redis = new RedisClient([
            'scheme' => 'tcp',
            'host' => '172.19.0.2',
            'port' => 6379,
            'password' => 'cloudy4next@@@'
        ]);
    }


    public function updateTradeStatus($data)
    {
        $customer = $this->customer();

        try {
            DB::table('buysells')
                ->where('id', $data['id'])
                ->where('customer_id', $customer->id)
                ->update([
                    'stop_loss' => $data['stop_loss'],
                    'take_profit' => $data['take_profit']
                ]);
        } catch (\Exception $e) {
            throw new \Exception('Failed to update trade status', 500);
        }
    }

    public function getTranscations($type, $businessId)
    {
        if (!$this->customer()) {
            return [];
        }

        $type = is_string($type) ? [$type] : $type;

        return DB::table('transaction')
            ->leftJoin('customers', 'transaction.customer_id', '=', 'customers.id')
            ->leftJoin('buysells', 'transaction.reference_row', '=', 'buysells.id')
            ->where('transaction.business_id', $businessId)
            ->where('transaction.customer_id', $this->customer()->id)
            ->whereIn('transaction.transaction_type', $type)
            ->orderBy('transaction.id', 'desc')
            ->select(
                'transaction.*',
                'customers.name',
                'buysells.created_at as buy_sell_date',
                'buysells.current_rate as buy_sell_rate',
                'buysells.type'
            )
            ->get();
    }





    private function getBuySell(bool $isRunning, int $id)
    {
        return DB::table('buysells')
            // ->select('buysells.*', 'products.slug as product_slug')
            // ->join('products', 'buysells.product_id', '=', 'products.id')
            ->where('customer_id', $id)
            ->whereIn('is_running', $isRunning ? [1] : [0, 1])
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->orderBy('id', 'desc')
            ->get();
    }



    public function getPendingList()
    {
        $customer = $this->customer();
        if (!$customer) {
            return [];
        }

        return DB::table('pending')->where('business_id', $customer->business_id)
            ->where('customer_id', auth()->user()->id)
            ->where('is_processed', 0)
            ->orderBy('id', 'desc')
            ->get();
    }





    public function getLastTenCompletedList()
    {
        $customer = $this->customer();
        if (!$customer) {
            return [];
        }

        $trans = DB::table('transaction')->where(['business_id' => $customer->business_id])
            ->whereIn('transaction_type', ['buy', 'sell'])
            ->where('customer_id', $customer->id)
            // ->with(relations: 'customer')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return $trans->map(function ($transaction) {
            $customer = DB::table('customers')->where('id', $transaction->customer_id)->first();
            $transaction->linked_buy = DB::table('buysells')->where('id', $transaction->reference_row)->first();
            $transaction->customer = $customer;
            return $transaction;
        });
    }



    public function getDepositWithdrawList($type, $businessId, $customerId)
    {
        return DB::table('tansaction')->where(['business_id' => $businessId])
            ->where('transaction_type', $type)
            ->where('customer_id', auth()->user()->id)
            ->paginate(10);
    }


    public function transactionSearch($ticketNo)
    {
        return DB::table('tansaction')->where(["reference_no" => $ticketNo])
            ->where('customer_id', auth()->user()->id)
            ->get();

    }
    private function getGoldPrice()
    {
        $goldPrice = $this->redis->get('gold_api_data');

        if ($goldPrice == null) {
            throw new \Exception('Gold price not found');
        }
        $price = json_decode($goldPrice);
        return $goldPrice ? $price->price : 0;
    }


    public function getRunningTradeByCustomer()
    {
        $customer = $this->customer();
        if (!$customer) {
            return [];
        }

        $buySells = DB::table('buysells')->where(['customer_id' => $customer->id, 'is_running' => 1])
            ->where('business_id', $customer->business_id)
            ->orderBy('id', 'desc')
            ->get();

        $goldPrice = $this->getGoldPrice();
        $perQtyPrice = 3.745 * 3.67 * $customer->conversion_rate;
        $serviceCharge = $customer->service_charge;

        return $buySells->map(function ($buySell) use ($serviceCharge, $perQtyPrice, $goldPrice) {
            $proitLoss = 0;

            if ($buySell->type == 'sell') {
                $proitLoss = (($buySell->current_rate - $goldPrice) - $serviceCharge) * $perQtyPrice;
            } else {
                $proitLoss = (($goldPrice - $buySell->current_rate) - $serviceCharge) * $perQtyPrice;
            }

            $buySell->profit_loss = $proitLoss;
            return $buySell;
        });
    }



    public function editPrice($transactionId, $rate, $type, $tt)
    {
        $buySell = DB::table('buysells')->find($transactionId);

        if (!$buySell) {
            throw new \Exception('Transaction not found');
        }

        $total_amount_aed = $this->goldservice->calculatepl($rate, null, $buySell->type, $buySell->tt_quantity);
        $buySell->current_rate = $rate;
        $buySell->total_amount_aed = $total_amount_aed;
        $buySell->type = $type;
        $buySell->tt_quantity = $tt;
        $buySell->save();
    }




    public function storeMatchedTrade($businessId, $transactionId, $customerId, $transactionType, $startingRate, $selectedTradeRate, $quantity)
    {
        $reference = 'Close' . time() . rand(1000, 9999);

        // Ensure the business ID is passed or retrieved correctly
        $businessId = $businessId ?? session('businessId');

        // Fetch customer details
        $customer = DB::table('customers')->where('id', $customerId)->first();
        if (!$customer) {
            throw new \Exception('Customer not found.');
        }

        $charge = $customer->service_charge ?? 0;

        // Calculate transaction amount
        $transactionAmount = $this->goldservice->calculatepl(
            $startingRate,
            $selectedTradeRate,
            $transactionType,
            $quantity,
            $charge
        );

        // Prepare transaction data
        $postData = [
            "reference_no" => $reference,
            "quantity" => $quantity,
            "current_rate" => $selectedTradeRate,
            "business_id" => $businessId,
            "starting_rate" => $startingRate,
            "customer_id" => $customer->id,
            "transaction_type" => $transactionType,
            "reference_table" => "buysells",
            "reference_row" => $transactionId,
            "tnx_id" => now(),
            "transaction_amount" => $transactionAmount,
        ];

        try {
            DB::transaction(function () use ($postData) {
                // Insert transaction data
                $transactionId = DB::table('transaction')->insertGetId($postData);

                // Fetch the buy-sell record
                $buySell = DB::table('buysells')->where('id', $postData['reference_row'])->first();
                if (!$buySell) {
                    throw new \Exception('BuySell record not found.');
                }

                // Update buy-sell record
                DB::table('buysells')->where('id', $buySell->id)->increment('close_quanntity', $postData['quantity']);

                // Check if the trade is fully closed
                $updatedBuySell = DB::table('buysells')->where('id', $buySell->id)->first();
                if ($updatedBuySell->close_quanntity == $updatedBuySell->tt_quantity) {
                    DB::table('buysells')->where('id', $buySell->id)->update(['is_running' => 3]);

                    // Process referral if applicable
                    $this->proccessReferral($updatedBuySell->customer_id, $transactionId, $postData['quantity'], $postData['businessId']);
                }
            });

        } catch (\Exception $e) {
            // Log the exception and throw a meaningful error
            throw new \Exception('Unable to save matched trade.');
        }
    }


    public function proccessReferral($customerId, $tnxID, $qty = 1, $businessId)
    {
        $referrers = $this->getAllReferrers($customerId);
        $customer = DB::table('customers')->where('id', $customerId)->first();


        if (isset($customer->referral_code)) {


            $referral = DB::table('referrals')->where('referral_code', $customer->referral_code)->first();
            $rewardList = [];

            foreach ($referrers as $key => $referrer) {
                if ($key === 'referrer_1') {
                    $rewardList[$referrer] = $referral->first_person_amount;
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
                    'referral_id' => $referral->referral_id,
                    'reward_amount' => ($reward * $qty),
                    'is_disbusted' => 1,
                    'transaction_id' => $tnxID,
                    'business_id' => $businessId
                ];
            }
            DB::table('reward')->insert($rewardsToInsert);
        }
    }

    public function getAllReferrers($customerId, $referrers = [])
    {
        $customer = DB::table('customers')->where('id', $customerId)->first();

        if ($customer && $customer->referrer) {
            $referrerCount = count($referrers) + 1;

            $referrers["referrer_{$referrerCount}"] = $customer->referrer;

            return $this->getAllReferrers($customer->referrer, $referrers);
        }

        return $referrers;
    }


    public function handleDepositStore(array $postData)
    {
        $customer = $this->customer();
        $charge = $customer->service_charge;
        $buysell = DB::table('buysells')->where('id', $postData['reference_row'])->first();
        $postData['reference_no'] = "Close-" . auth('api')->user()->id . '-' . rand(100000, 999999);
        $postData['reference_table'] = 'buysells';
        $postData['tnx_id'] = UUID::uuid4();
        $postData['reference_row'] = $buysell->id;
        $postData['starting_rate'] = $buysell->current_rate;
        $postData['customer_id'] = $customer->id;

        $product = Db::table('products')->where('id', $buysell->product_id)->first();

        $currentRate = $this->goldservice->fetchGoldPrice($product->slug);

        $postData['current_rate'] = $currentRate;


        $transactionAmount = $this->goldservice->calculatepl(
            $buysell->current_rate,
            $currentRate,
            $postData['transaction_type'],
            $postData['quantity'],
            $charge
        );

        $postData['actual_amount'] = $transactionAmount;
        $postData['transaction_amount'] = $this->converToAed($transactionAmount, true);

        DB::transaction(function () use ($postData, $buysell) {
            $trans = DB::table('transaction')->insertGetId($postData);
            if ($buysell) {
                $newCloseQuantity = $buysell->close_quanntity + $postData['quantity'];

                $updateData = [
                    'close_quanntity' => $newCloseQuantity
                ];

                if ($newCloseQuantity == $buysell->tt_quantity) {
                    $updateData['is_running'] = 0;
                    // $this->proccessReferral($buysell->customer_id, $trans, $postData['quantity'], $postData['business_id']);
                }

                DB::table('buysells')->where('id', $postData['reference_row'])->update($updateData);
            }
        });

    }


    public function getDeposits($type)
    {
        if (!$this->customer()) {
            return [];
        }
        return DB::table('transaction')->where('business_id', $this->customer()->business_id)
            ->where('customer_id', $this->customer()->id)
            ->whereIn('transaction_type', $type)
            ->orderBy('id', 'desc')
            ->get();
    }


    public function saveTransaction($postData)
    {
        $customer = $this->customer();
        $postData['reference_no'] = $postData['transaction_type'] . auth('api')->user()->id . '-' . rand(100000, 999999);
        $postData['business_id'] = $customer->business_id;
        $postData['customer_id'] = $customer->id;
        $postData['tnx_id'] = UUID::uuid4();
        $postData['actual_amount'] = $postData['transaction_amount'];
        $postData['transaction_amount'] = $this->converToAed($postData['transaction_amount']);
        if (strtoupper($postData['transaction_type']) == "WITHDRAW" || strtoupper($postData['transaction_type']) == "withdraw") {

            $checkLastTransaction = DB::table('transaction')->where('customer_id', $postData['customer_id'])
                ->orderBy('id', 'desc')
                ->where('status', 1)->first();

            $lastAmount = $checkLastTransaction->transaction_amount ?? 0;
            if ($lastAmount - $postData['transaction_amount'] < 0) {
                throw new \Exception('Insufficient balance', 422);

            }
            $lastAmount = -($postData['transaction_amount']);
            $checkLastTransaction->actual_amount = -($postData['actual_amount']);

        }
        $postData['status'] = '0';

        try {
            DB::table(table: 'transaction')->insert($postData);
        } catch (\Exception $e) {
            throw new \Exception('Failed to save transaction', 500);
        }

    }


    public function saveBid($data)
    {
       // $product = DB::table('products')->where('id', $data['product_id'])->first();
       // $data['current_rate'] = $this->goldservice->fetchGoldPrice($product->slug);
        $amount = $data['tt_quantity'] * $data['current_rate'] * 3.745;
        $data['actual_amount'] = $amount;
        $data['total_amount_aed'] = $this->converToAed($amount);
        $data['cut_position'] = 0;
        $data['trading_source'] = 'api';
        try {
            DB::table('buysells')->insert($data);

            $customer = DB::table('customers')->where('id', $data['customer_id'])->first();
            $runningBuySell = $this->getBuySell(isRunning: true, id: $data['customer_id']);

            list($openPositionProfitOrLoss, $sum, $qtySum, $running) = $this->getRunningWithTqtyOpenPostion($runningBuySell, $customer->service_charge);

            $equity = $this->getCurrentBalance($customer->id) + $sum;


            $this->cutPosition($running, $data['current_rate'], $equity, $customer->id);


        } catch (\Exception $e) {
            throw new \Exception('Failed to save bid', 500);
        }
    }


    public function getCutPosition($id)
    {

    }


    public function generateStatement($startDate, $endDate)
    {
        $customer = $this->customer();

        if (!$customer) {
            return [];
        }
        // dd($customer);
        $statement = $this->getStatement();

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
        ) = $statement;

        $sumBuy = $outSandingPostions->where('type', 'buy')->sum('tt_quantity');
        $sumSell = $outSandingPostions->where('type', 'sell')->sum('tt_quantity');
        $value = $sumBuy - $sumSell;
        $totalProfitLoss = $outSandingPostions->sum('profit_loss') ?? 0;
        // dd($statement);
        return [
            'buy' => $buy,
            'sell' => $sell,
            'deposit' => $deposit,
            'withdraw' => $withdraw,
            'profit' => $profit,
            'loss' => $loss,
            'open_position_profit_or_loss' => $openPositionProfitOrLoss,
            'balance' => $currentBalance,
            'outstanding_positions' => $startDate && $endDate ? $outSandingPostions : $outSandingPostions->whereBetween('created_at', [$startDate, $endDate]),
            'net_matched' => $startDate && $endDate ? $netMatched : $netMatched->whereBetween('created_at', [$startDate, $endDate]),
            'cut_position' => $cutPosition,
            'total_qty' => $totalQty,
            'equity' => $equity,
            'sumBuy' => $sumBuy,
            'sumSell' => $sumSell,
            'value' => $value,
            'totalProfitLoss' => $totalProfitLoss,
        ];
    }


    public function getStatement()
    {
        $customer = $this->customer();
        $transactions = $this->getAllTransactions($customer->id);

        return $this->generateInvoiceStatement(
            $transactions,
            'statement',
            $customer,
            true,
            null,
            null
        );

    }



    public function getNetMatched($transactions)
    {
        $netMatcheds = collect($transactions)->filter(function ($transaction)  {
            $isMatchedType = in_array($transaction->transaction_type, ['buy', 'sell']);
            return $isMatchedType;
        });


        $referenceIds = $netMatcheds->pluck('reference_row')->unique()->filter();

        $linkedBuysells = DB::table('buysells')->whereIn('id', $referenceIds)->get()->keyBy('id');

        $netMatcheds->transform(callback: function ($transaction) use ($linkedBuysells) {
            $transaction->linked_buy = $linkedBuysells->get($transaction->reference_row);
            return $transaction;
        });

        return $netMatcheds;
    }

    private function generateInvoiceStatement($transactions, $type, $customer, $isReturn = false,$startDate,$endDate)
    {
        $netMatched = $this->getNetMatched(transactions: $transactions);
        $buyPrice = $this->goldservice->fetchGoldPrice();
        // $buyPrice = 2629;
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
        ) = $this->getProfitLoss($transactions, $customer, $buyPrice);

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

        $marketPrice = $buyPrice;

        $sumBuy = $outSandingPostions->where('type', 'buy')->sum('tt_quantity');
        $sumSell = $outSandingPostions->where('type', 'sell')->sum('tt_quantity');
        $value = $sumBuy - $sumSell;
        $totalProfitLoss = $outSandingPostions->sum('profit_loss') ?? 0;
        $netMatched = $startDate && $endDate ? $netMatched->whereBetween('created_at', [$startDate, $endDate]) : $netMatched;
        $outSandingPostions = $startDate && $endDate ? $outSandingPostions->whereBetween('created_at', [$startDate, $endDate]) : $outSandingPostions;

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
                'pending' => $this->getPendingList(),
                'show_service_charge' => false,
            ]
        )->setPaper('a4', 'landscape');
        // $pdf->save(storage_path('app/public/statement.pdf'));

        $pdfPath = public_path('uploads/' . $customer->id . '_Statement_' . date('Y-m-d') . '.pdf');
        $pdf->save($pdfPath);
        return $pdfPath;
    }

    private function getProfitLoss($transactions, $customer, $buyPrice): array
    {
        list($buy, $sell, $deposit, $withdraw) = $this->sumBuySellDepositWithdraw($transactions);

        list($currentBalance, $profit, $loss) = $this->balanceLossProfit($transactions);

        $runningBuySell = $this->getBuySell(isRunning: true, id: $customer->id);

        $serviceCharge = $customer->service_charge;

        list($openPositionProfitOrLoss, $sum, $qtySum, $running) = $this->getRunningWithTqtyOpenPostion($runningBuySell, $serviceCharge);

        $equity = $currentBalance + $sum;

        $cutPositionCalulate = $this->customer()->cutposition;
        return [
            $buy,
            $sell,
            $deposit,
            $withdraw,
            $profit,
            $loss,
            $openPositionProfitOrLoss,
            $currentBalance,
            $running,
            $cutPositionCalulate,
            $qtySum,
            $equity
        ];
    }


    // marketprice(gold-rate),customer balance ,equity, cut position, active buy, active sell, available ttb

    public function getLiveData()
    {
        $customer = $this->customer();
        if (!$customer) {
            return [];
        }

        try {
            $price = $this->getPricefromShadhin();
        } catch (\Exception $e) {
            throw new \Exception('Failed to get market price', 404);
        }

        $transactions = $this->getAllTransactions($customer->id);
        list(
            $buy,
            $sell,
            $deposit,
            $withdraw,
            $profit,
            $loss,
            $openPositionProfitOrLoss,
            $currentBalance,
            $running,
            $cutPositionCalulate,
            $qtySum,
            $equity
        ) = $this->getProfitLoss($transactions, $customer, $price);

        $maxTTB = $equity ? $equity / (1000 / $customer->maxtt_per_K) : 0;
        return [
            'market_price' => $price,
            'balance' => $currentBalance,
            'equity' => $equity,
            'cut_position' => $cutPositionCalulate,
            'active_buy' => $running->where('type', 'buy')->sum('tt_quantity'),
            'active_sell' => $running->where('type', 'sell')->sum('tt_quantity'),
            'available_ttb' => $maxTTB < 0 ? 0 : $maxTTB,
        ];
    }
    public function getDashboard()
    {
        $customer = $this->customer();
        if (!$customer) {
            return [];
        }

        $transactions = $this->getAllTransactions($customer->id);


        list($buy, $sell, $deposit, $withdraw) = $this->sumBuySellDepositWithdraw($transactions);
        $totalClosedTT = $transactions->where('transaction_type', 'buy')->sum('quantity') + $transactions->where('transaction_type', 'sell')->sum('quantity');
        list($currentBalance, $profit, $loss) = $this->balanceLossProfit($transactions);


        return [
            'total_buy' => $buy,
            'total_sell' => $sell,
            'total_closed_tt' => $totalClosedTT,
            'profit_loss' => $profit - $loss,
            'total_deposit' => $deposit,
            'total_withdraw' => $withdraw,
        ];


    }


    public function getProducts($businessId)
    {
        $products = DB::table('products')
            // ->select('id', 'title', 'description', 'image', 'weight', 'weight_type')
            ->where('business_id', $businessId)
            // ->where('is_shop', 1)
            ->orderBy('id', 'desc')
            ->get();

        $products = $products->map(function ($product) {
            $product->image = asset('images/shop/' . $product->image);
            return $product;
        });

        return $products;
    }

    public function singleProduct($productId)
    {
        return DB::table('products')->where('id', $productId)->first() ?? [];
    }

    public function storeSplitTrade($data)
    {
        $customer = $this->customer();
        $customerId = $customer->id;
        $buySell = DB::table('buysells')->where('id', $data['id'])
            ->where('customer_id', $customerId)
            ->where('is_running', 1)
            ->first();
        if (!$buySell) {
            throw new \Exception('Trade not found');
        }

        if ($data['split_quantity'] > $buySell->tt_quantity) {
            throw new \Exception('Split quantity must be less than total quantity');
        }

        $splitAmount = $buySell->tt_quantity - $data['split_quantity'];
        if ($splitAmount <= 0) {
            throw new \Exception('Split quantity must be less than quantity');
        }
        // now copy all data for new buy sell updating the old one quantity

        $amount = $data['split_quantity'] * $buySell->current_rate * 3.745;
        $actual_amount = $amount;
        $totalAmountAed = $this->converToAed($amount);

        $splitToSave = [
            "tt_quantity" => $data['split_quantity'],
            "current_rate" => $buySell->current_rate,
            "total_amount_aed" => $totalAmountAed,
            "close_quanntity" => 0,
            "type" => $buySell->type,
            "actual_amount" => $amount,
            "cut_position" => 0,
            "reference_no" => "Close" . time() . rand(1000, 9999),
            "customer_id" => $customerId,
            "product_id" => $buySell->product_id,
            "business_id" => $customer->business_id,
        ];

        DB::table('buysells')->insert($splitToSave);

        // $buySell->tt_quantity -= $data['split_quantity'];
        // $buySell->total_amount_aed -= $totalAmountAed;
        // $buySell->actual_amount -= $actual_amount;
        // $buySell->save();
        DB::table('buysells')->where('id', $data['id'])->update([
            'tt_quantity' => $buySell->tt_quantity - $data['split_quantity'],
            'total_amount_aed' => $buySell->total_amount_aed - $totalAmountAed,
            'actual_amount' => $buySell->actual_amount - $actual_amount
        ]);
    }


    public function downloadStatement($startDate, $endDate)
    {
        $customer = $this->customer();
        if (!$customer) {
            return [];
        }

        $transactions = $this->getAllTransactions($customer->id);
        $pdf = $this->generateInvoiceStatement($transactions, 'statement', $customer, false, $startDate, $endDate);

        return $pdf;
    }

    public function deleteTransaction($id)
    {
        $customer = $this->customer();
        if (!$customer) {
            return [];
        }

        $transaction = DB::table('transaction')->where('id', $id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$transaction) {
            throw new \Exception('Transaction not found', 404);
        }

        DB::table('transaction')->where('id', $id)->delete();
    }

}
