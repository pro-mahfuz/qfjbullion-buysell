<?php

namespace App\Traits;

use DB;

trait ClientTrait
{
    public function getCustomerId()
    {
        return session('customer_id');
    }

    public function getBusinessId()
    {
        return session('business_id');
    }

    public function getConversionRate()
    {
        return session('conversion_rate');
    }
    public function getCurrency()
    {
        return session('currency');
    }

    public function customer()
    {
        $customer = $this->get('/customer/' . $this->getCustomerId());
        return $customer['data'] ?? [];
    }

    public function getShopProducts($page = 1)
    {
        $products = $this->get('/product-list', ['business_id' => $this->getBusinessId()]);
        return $products ? $products['data'] : [];
    }


    public function getAllTransactions($type)
    {
        $transactions = $this->get('/transactions', ['type' => $type, 'business_id' => $this->getBusinessId()]);

        return $transactions ? $transactions['data'] : [];
    }


    public function pendingLists()
    {
        $transactions = $this->get('/get-pendings');
        return $transactions['data'] ?? [];
    }

    public function getLastTenTransactions()
    {
        $transactions = $this->get('/last-ten-transactions');
        return $transactions['data'] ?? [];
    }

    public function getRunningBuySell()
    {
        $transactions = $this->get('/get-runnings');
        return $transactions['data'] ?? [];
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


}
