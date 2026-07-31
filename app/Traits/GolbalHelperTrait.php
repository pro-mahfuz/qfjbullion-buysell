<?php

namespace App\Traits;

use App\Models\Customer;
use App\Services\Api\TransactionService;
use DB;

trait GolbalHelperTrait
{
    public static function cutPosition($running, $currentRate, $equity, $customerId)
    {
        $runningBuySell = collect($running);

        $sumBuy = $running->where('type', 'buy')->sum('tt_quantity');
        $sumSell = $running->where('type', 'sell')->sum('tt_quantity');

        $totalQty = abs($sumBuy - $sumSell);

        $type = $sumBuy > $sumSell ? 'buy' : 'sell';

        if ($totalQty != 0) {
            $cutPosition = $equity / 13.7639 / $totalQty;
        } else {
            $cutPosition = 0;
        }

        if ($sumBuy == $sumSell) {
            $cutPositionCalulate = 0;
        } else {
            $cutPositionCalulate = $type == 'buy' ? $currentRate - $cutPosition : $currentRate + $cutPosition;
        }

        $cut = abs(number_format((float)$cutPositionCalulate, 3, '.', ''));

 
         DB::table('customers')->where('id', $customerId)
            ->update(['cutposition' => $cut]);

    }

    public function getCurrentBalance($id): float
    {
        $transactions = DB::table('transaction')
            ->where('customer_id', $id)
            ->orderBy('id', 'desc')
            ->get();
        
        $depositAmount = 0;
        $withdrawAmount = 0;
        
        $currentAmount = 0;
        
        if ($transactions) {
            $transactions->map(function ($transaction) use (&$currentAmount) {
                if ($transaction->transaction_type == 'deposit') {
                    $currentAmount += abs($transaction->transaction_amount);
                    
                }
                elseif ($transaction->transaction_type == 'withdraw') {
                    $currentAmount -= abs($transaction->transaction_amount);
                    
                }
                elseif ($transaction->transaction_type == 'buy' || $transaction->transaction_type == 'sell') {
                    $currentAmount = $transaction->transaction_amount > 0 ? $currentAmount + abs($transaction->transaction_amount) : $currentAmount - abs($transaction->transaction_amount);
                }
            });
        }
        return $currentAmount;
    }
}
