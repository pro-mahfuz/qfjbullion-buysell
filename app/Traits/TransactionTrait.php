<?php

namespace App\Traits;

use App\Services\GoldService;
use DB;
use Illuminate\Support\Collection;

trait TransactionTrait
{
    public function getAllTransactions(int $id)
    {
        return DB::table('transaction')->where('customer_id', $id)
            ->whereIn('transaction_type', ['buy', 'sell', 'deposit', 'withdraw'])
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    public function balanceLossProfit(Collection $transactions): array
    {
        $currentBalance = 0;
        $profit = 0;
        $loss = 0;
        foreach ($transactions as $transaction) {
            if ($transaction->transaction_type == 'deposit') {
                $currentBalance += $transaction->transaction_amount;
            } elseif ($transaction->transaction_type == 'withdraw') {
                $currentBalance -= abs($transaction->transaction_amount);
            } elseif ($transaction->transaction_type == 'buy' || $transaction->transaction_type == 'sell') {
                $currentBalance = $transaction->transaction_amount > 0 ? $currentBalance + abs($transaction->transaction_amount) : $currentBalance - abs($transaction->transaction_amount);
                if ($transaction->transaction_amount > 0) {
                    $profit += $transaction->transaction_amount;
                } else {
                    $loss += abs($transaction->transaction_amount);
                }
            }
        }
        return [
            $currentBalance,
            $profit,
            $loss
        ];
    }


    public function sumBuySellDepositWithdraw(Collection $transactions): array
    {
        $buy = $transactions->where('transaction_type', 'buy')->sum('transaction_amount');
        $sell = $transactions->where('transaction_type', 'sell')->sum('transaction_amount');
        $deposit = $transactions->where('transaction_type', 'deposit')->sum('transaction_amount');
        $withdraw = $transactions->where('transaction_type', 'withdraw')->sum('transaction_amount');

        return [
            $buy,
            $sell,
            $deposit,
            $withdraw
        ];
    }


    public function getRunningWithTqtyOpenPostion(Collection $runningBuySellCollection, $serviceCharge)
    {
        $goldPrices = (new GoldService())->fetchGoldPrice(false, true);

        $openPositionProfitOrLoss = 0;
        $sumBalance = 0;
        $qtySum = 0;
        $running = $runningBuySellCollection->map(function ($transaction) use (&$openPositionProfitOrLoss, &$goldPrices, &$sumBalance, &$qtySum, &$serviceCharge) {
            $qty = $transaction->tt_quantity - $transaction->close_quanntity;
            // $sluggedValue = $goldPrices[$transaction->product_slug];
            $sluggedValue = null;
            $productPrice = isset($sluggedValue) ? $sluggedValue : $goldPrices['price'];
            $newBalance = (new GoldService())->calculatepl($transaction->current_rate, $productPrice, $transaction->type, $qty, $serviceCharge);
            if ($transaction->type == 'buy') {
                $openPositionProfitOrLoss = $openPositionProfitOrLoss - $newBalance;
            } elseif ($transaction->type == 'sell') {
                $openPositionProfitOrLoss = $openPositionProfitOrLoss + $newBalance;
            }
            $sumBalance += $newBalance;
            $qtySum += $qty;
            $transaction->profit_loss = $newBalance;
            return $transaction;
        });

        return [
            $openPositionProfitOrLoss,
            $sumBalance,
            $qtySum,
            $running
        ];
    }

}
