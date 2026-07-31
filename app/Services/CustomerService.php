<?php


namespace App\Services;
use App\Exceptions\RedirectException;
use App\Models\Bussiness;
use App\Models\Buysell;
use App\Models\Customer;
use App\Models\Ledger;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Request;
use Session;
use Illuminate\Support\Facades\Http;

class CustomerService
{

    public function __construct(private TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function getCustomers(): Collection
    {
        
        $marketPrice = 0;
        $response = Http::get('https://qfjbullion.com/rate');
        //dd($response->json());
        
        if ($response->successful()) {
            $data = $response->json();
            $marketPrice = isset($data['value']) ? ($data['value'] - 0.53) : 0;
        }
        
        $customers = Customer::orderBy('id', 'desc')->get()->map(function ($customer) use ($marketPrice) {
            $tran = Transaction::where('customer_id', $customer->id)
                ->selectRaw('
                    SUM(CASE WHEN transaction_type = "deposit" THEN transaction_amount ELSE 0 END) as sum_of_deposit,
                    SUM(CASE WHEN transaction_type = "withdraw" THEN transaction_amount ELSE 0 END) as sum_of_withdraw,
                    SUM(CASE WHEN transaction_type = "sell" THEN transaction_amount ELSE 0 END) as sum_of_sell_profit_loss,
                    SUM(CASE WHEN transaction_type = "buy" THEN transaction_amount ELSE 0 END) as sum_of_buy_profit_loss
                ')->first();
        
            $buySell = Buysell::where([
                'customer_id' => $customer->id,
                'is_running' => 1
            ])->selectRaw('
                    SUM(CASE WHEN type = "sell" THEN tt_quantity ELSE 0 END) as sum_of_running_sell_ttb,
                    SUM(CASE WHEN type = "buy" THEN tt_quantity ELSE 0 END) as sum_of_running_buy_ttb,
                    SUM(CASE WHEN type = "sell" THEN current_rate - ? ELSE 0 END) as sum_of_running_sell_profit,
                    SUM(CASE WHEN type = "buy" THEN ? - current_rate ELSE 0 END) as sum_of_running_buy_profit,
                    SUM(service_charge) as sum_of_running_service_charge
                ', [$marketPrice, $marketPrice])->first();
        
            // Default values if $buySell is null
            $sum_of_buy_ttb = $buySell->sum_of_running_buy_ttb ?? 0;
            $sum_of_sell_ttb = $buySell->sum_of_running_sell_ttb ?? 0;
            $active_ttb = $sum_of_buy_ttb - $sum_of_sell_ttb;
        
            $equity = app('App\Services\TransactionService')->getEquity($customer->id, $marketPrice);
            $margin_gap = 0;
            $margin = 0;
        
            if ($active_ttb != 0) {
                $margin_gap = ($equity / abs($active_ttb)) / 13.7639;
                $margin = $active_ttb > 0
                    ? $marketPrice - $margin_gap
                    : $margin_gap + $marketPrice;
            }
        
            if (!$tran) {
                $customer->sum_of_deposit = 0;
                $customer->sum_of_withdraw = 0;
                $customer->sum_of_sell_profit_loss = 0;
                $customer->sum_of_buy_profit_loss = 0;
                $customer->sum_of_running_buy_ttb = 0;
                $customer->sum_of_running_sell_ttb = 0;
                $customer->current_profit_loss = 0;
                $customer->current_balance = 0;
                $customer->equity = 0;
                $customer->margin_gap = 0;
                $customer->margin = 0;
                return $customer;
            }
        
            $customer->sum_of_deposit = number_format($tran->sum_of_deposit, 3);
            $customer->sum_of_withdraw = number_format($tran->sum_of_withdraw, 3);
            $customer->sum_of_sell_profit_loss = number_format($tran->sum_of_sell_profit_loss, 3);
            $customer->sum_of_buy_profit_loss = number_format($tran->sum_of_buy_profit_loss, 3);
            $customer->sum_of_running_buy_ttb = $sum_of_buy_ttb;
            $customer->sum_of_running_sell_ttb = $sum_of_sell_ttb;
            $customer->sum_of_running_buy_profit = $buySell->sum_of_running_buy_profit;
            $customer->sum_of_running_sell_profit = $buySell->sum_of_running_sell_profit;
            $customer->current_profit_loss = number_format(($tran->sum_of_sell_profit_loss + $tran->sum_of_buy_profit_loss), 3);
            $customer->current_balance = app('App\Services\TransactionService')->getCurrentBalance($customer->id);
            $customer->equity = $equity;
            $customer->margin_gap = $margin_gap;
            $customer->margin = $margin;
            $customer->sum_of_running_service_charge = $buySell->sum_of_running_service_charge * 13.7639;
        
            return $customer;
        });
        
        return $customers;
    }


    public function getRefferarCustomer()
    {
        return Customer::where('type', 'client')
            ->where('status', 'activated')
            ->get();
    }

    public function saveCustomer(array $data, $attachemtUrl): Customer
    {

        $data['status'] = 'deactived';
        $data['attachment'] = $attachemtUrl;
        try {
            return Customer::create(attributes: $data);
        } catch (\Exception $e) {
            // dd($e);
            throw new RedirectException('Something went wrong');
        }
    }

    public function updateCustomer(array $data, int $customerId): void
    {
        try {
            Customer::where([
                ['id', '=', $customerId]
            ])->update($data);
        } catch (\Exception $e) {
            // dd($e);
            throw new RedirectException('Something went wrong');
        }
    }

    public function getCustomerById(int $id): Customer
    {

        $customer = Customer::find($id);

        if ($customer && isset($customer->referrer)) {
            $customer->refer_user = Customer::find($customer->referrer);
        }

        return $customer;
    }


    public function customerSerach($query)
    {
        return Customer::
            // active()->
            where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'like', '%' . $query . '%')
                    ->orWhere('phone', 'like', '%' . $query . '%')
                    ->orWhere('customer_code', 'like', '%' . $query . '%');
            })
            ->first();

    }


    public function disableCustomer(int $id): void
    {
        try {
            Customer::where([
                ['id', '=', $id]
            ])->update([
                        'status' => 'deactived',
                        'buy_rate' => (new GoldService())->fetchGoldPrice(),
                    ]);
        } catch (\Exception $e) {
            throw new RedirectException('Something went wrong');
        }
    }

    public function enableCustomer(int $id): void
    {
        try {
            Customer::where([
                ['id', '=', $id]
            ])->update([
                        'status' => 'activated',
                        'buy_rate' => (new GoldService())->fetchGoldPrice(),
                    ]);
        } catch (\Exception $e) {
            throw new RedirectException('Something went wrong');
        }
    }

    public function generateBusinessName()
    {
        // Retrieve the current business based on session ID
        $business = Bussiness::find(request()->session()->get('bussinessId'));

        if (!$business) {
            return null; // Handle the case where the business doesn't exist
        }

        // Extract the first two words of the business name
        $words = collect(explode(' ', $business->name))
            ->filter() // Remove empty values (if business name has extra spaces)
            ->take(2);

        if ($words->isEmpty()) {
            $prefix = 'CUST';
        } else {
            $prefix = $words
                ->map(fn($word) => strtoupper($word[0])) // Take the first letter of each word
                ->join('');
        }


        // Fetch the last customer for this business with the same prefix
        $lastCustomer = Customer::where('customer_code', 'LIKE', $prefix . '%')
            ->where('business_id', $business->id)
            ->orderBy('id', 'desc')
            ->first();

        // Determine the next customer number
        if ($lastCustomer) {
            // Extract the numeric part of the customer code
            $lastNumber = (int) substr($lastCustomer->customer_code, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1; // Start from 1 if no customers found
        }

        // Format the customer number to be 3 digits (e.g., 001, 002)
        $customerNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Combine the prefix with the customer number
        return $prefix . $customerNumber;
    }


    public function deleteCustomer(int $id): void
    {
        try {
            \DB::beginTransaction();
            Transaction::where('customer_id', $id)->delete();

            Buysell::where('customer_id', $id)->delete();

            Customer::where([
                ['id', '=', $id]
            ])->delete();

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw new RedirectException('Something went wrong');
        }
    }

}
