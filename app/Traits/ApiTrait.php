<?php

namespace App\Traits;

use App\Constants\EndPoints;
use DB;
use Http;
use Request;
trait ApiTrait
{
    // public function customerByID($id = null)
    // {
    //     $id = $id ?? auth('api')->user()->id;
    //     return DB::table('customers')->where('id', $id)->first() ?? null;
    // }
    public function customer($onlyCheck = false)
    {
        if ($onlyCheck) {
            return DB::table('customers')->where('email', auth('api')->user()->email)->first() ?? null;
        }

        $customers = DB::table('customers')
            ->select('customers.*', 'currencies.code', 'currencies.conversion_rate')
            ->leftJoin('currencies', 'currencies.id', '=', 'customers.currency_id') // Adjust join condition
            ->where('customers.email', auth('api')->user()->email)
            ->first() ?? null;

        if ($customers == null) {
            throw new \Exception('No Currency Found!!!.Please contact with admin');
        }

        return $customers;
    }

    public function getCurrentAmount(): float
    {
        if (!$this->customer()) {
            return 0;
        }
        $transactions = DB::table('transaction')->where('customer_id', $this->customer()->id)->orderBy('id', 'desc')->get();
        $currentAmount = 0;
        if ($transactions) {
            $transactions->map(function ($transaction) use (&$currentAmount) {
                if ($transaction->transaction_type == 'deposit' || $transaction->transaction_type == 'withdraw') {
                    if ($transaction->status == 1) {
                        $currentAmount += $transaction->transaction_amount;
                    }
                } elseif ($transaction->transaction_type == 'buy' || $transaction->transaction_type == 'sell') {
                    $currentAmount = $transaction->transaction_amount > 0 ? $currentAmount + abs($transaction->transaction_amount) : $currentAmount - abs($transaction->transaction_amount);
                }
            });
        }
        return $currentAmount;
    }


    public function converToAed($amount, $isMulti = false)
    {
        $customer = $this->customer();
        if ($customer == null) {
            throw new \Exception('Conversion rate is not available');
        }
        $conversionRate = $customer->conversion_rate;

        if ($customer->code == 'AED') {
            return $amount;
        }
        return $isMulti == true ? $amount * ($conversionRate * 100) : $amount / ($conversionRate * 100);
    }


    public function getPricefromShadhin(): float
    {

        $goldPrice = $this->retunGoldPriceFromCache();


        if ($goldPrice == null) {
            throw new \Exception('Gold Price is not available');
        }
        return $goldPrice ? $goldPrice['price'] : 0;

    }


    private function retunGoldPriceFromCache()
    {
        $keys = $this->redis->get(key: 'gold_api_data');

        return json_decode($keys, true);
    }

}
