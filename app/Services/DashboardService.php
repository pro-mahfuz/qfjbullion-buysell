<?php

namespace App\Services;

use App\Models\Buysell;
use App\Models\Customer;
use App\Models\Reward;
use App\Models\Transaction;



class DashboardService
{
    private $buySellModel;
    private $transactionModel;

    public function __construct()
    {
        $this->buySellModel = Buysell::query();
        $this->transactionModel = Transaction::query();
    }


    public function getDashboardData($dateRange = null)
    {
        if ($dateRange) {
            [$startDate, $endDate] = explode(' - ', $dateRange);
            $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', trim($startDate))->startOfDay();
            $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', trim($endDate))->endOfDay();
        } else {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        }

        $remaining = $this->buySellModel->where('is_running', 1)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('type')
            ->get(['type', \DB::raw('count(*) as total')])
            ->toArray();
            $customerList = Customer::whereBetween('created_at', [$startDate, $endDate])->paginate(5);
            $withDrawList = $this->transactionModel->where('transaction_type', 'withdraw')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->paginate(5);
            $referralList = Reward::with('customer')->whereBetween('created_at', [$startDate, $endDate])
            ->paginate(5);

        return [

            'remaining' => $remaining,
            'total_transactions' => $this->transactionModel
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'new_customer_count' => Customer::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_buy' => $this->transactionModel->where('transaction_type', 'buy')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'total_sell' => $this->transactionModel->where('transaction_type', 'sell')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'total_withdraw' => $this->transactionModel->where('transaction_type', 'withdraw')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'total_reward' => Reward::whereBetween('created_at', [$startDate, $endDate])->count(),
            'customer_list' => $customerList,
            'withdraw_list' => $withDrawList,
            'referral_list' => $referralList,

            'total_running' => $this->buySellModel->where('is_running', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'running_amount'=> $this->buySellModel->where('is_running', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_amount_aed'),
            'running_buy'=> $this->buySellModel->where('is_running', 1)->where('type', 'buy')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'running_sell'=> $this->buySellModel->where('is_running', 1)->where('type', 'sell')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),

        ];
    }

    public function getMonthWiseBuySellCount()
    {
        $buyData = Transaction::where('transaction_type', 'buy')
            ->select(
                \DB::raw('MONTH(created_at) as month'),
                \DB::raw('YEAR(created_at) as year'),
                \DB::raw('count(*) as total')
            )
            ->groupBy(\DB::raw('MONTH(created_at)'), \DB::raw('YEAR(created_at)'))
            ->get()
            ->toArray();

        $sellData = Transaction::where('transaction_type', 'sell')
            ->select(
                \DB::raw('MONTH(created_at) as month'),
                \DB::raw('YEAR(created_at) as year'),
                \DB::raw('count(*) as total')
            )
            ->groupBy(\DB::raw('MONTH(created_at)'), \DB::raw('YEAR(created_at)'))
            ->get()
            ->toArray();

        $buyData = collect($buyData)->mapWithKeys(function ($item) {
            return [$item['year'] . '-' . $item['month'] => $item['total']];
        });

        $sellData = collect($sellData)->mapWithKeys(function ($item) {
            return [$item['year'] . '-' . $item['month'] => $item['total']];
        });

        return [
            'buy' => $buyData,
            'sell' => $sellData,
        ];
    }

    public function monthWiseCustomerCount()
    {
        $customerData = Customer::select(
            \DB::raw('MONTH(created_at) as month'),
            \DB::raw('YEAR(created_at) as year'),
            \DB::raw('count(*) as total')
        )
            ->groupBy(\DB::raw('MONTH(created_at)'), \DB::raw('YEAR(created_at)'))
            ->get()
            ->toArray();

        $customerData = collect($customerData)->mapWithKeys(function ($item) {
            return [$item['year'] . '-' . $item['month'] => $item['total']];
        });

        return $customerData;
    }
}
