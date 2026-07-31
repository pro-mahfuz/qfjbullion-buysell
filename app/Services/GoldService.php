<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoldService
{
    private $isFetching;

    public function __construct()
    {
        $this->isFetching = false;
    }

    public function fetchGoldPrice($type = false, $needFullJson = false)
    {
        if ($this->isFetching) {
            return 0;
        }

        $this->isFetching = true;

        try {
            $buyPrice = retry(3, function () use (&$type, &$needFullJson) {
                $response = Http::withHeaders([
                    // 'x-access-token' => 'goldapi-7q9uy0tkwrfdtlo-io',
                    // 'Content-Type' => 'application/json',
                    //https://gold.shadhinportal.com/api/gold?currency=usd
                ])->get('https://qfjbullion.com/rate');
                //dd($response->json());

                if ($response->failed()) {
                    throw new \Exception('API call failed: ' . $response->status());
                }

                $data = $response->json();
                if (!isset($data['gold_sell_price']) || !is_numeric($data['gold_sell_price'])) {
                    throw new \Exception('Invalid gold price format');
                }

                if ($type) {
                    if (isset($data[$type]) && is_numeric($data[$type])) {
                        return $data[$type];
                    } else {
                        throw new \Exception('Invalid gold price format');
                    }
                }
                if ($needFullJson) {
                    return $data;
                }

                return ($data['gold_sell_price'] - 0.53) + 1;
            }, 1000); // Retry 3 times with a 1-second delay between attempts

            return $buyPrice; // Return the valid gold price
        } catch (\Exception $e) {
            \Log::error('Exception while fetching gold price: ' . $e->getMessage());
            return 0; // Return 0 if all retries fail
        } finally {
            $this->isFetching = false; // Reset the fetching flag
        }
    }


    public function calculatepl($start_rate, $current_rate, $type = "buy", $qty = 1, $service_charge = 0)
    {
        if ($current_rate == null) {
            $current_rate = $this->fetchGoldPrice();
        }
        $perTTPrice = $qty * 13.7639;

        return $type == "buy" ?
            ((($current_rate - $start_rate) - $service_charge) * $perTTPrice)
            : ((($start_rate - $current_rate) - $service_charge) * $perTTPrice);

    }

}
