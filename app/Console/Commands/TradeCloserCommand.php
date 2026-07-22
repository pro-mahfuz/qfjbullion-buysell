<?php

namespace App\Console\Commands;

use App\Models\Buysell;
use App\Services\GoldService;
use DB;
use Illuminate\Console\Command;
use Log;
use Predis\Client as RedisClient;

class TradeCloserCommand extends Command
{
    private $redis = null;

    public function __construct(private GoldService $goldService)
    {
        $this->goldService = $goldService;
        parent::__construct();

        $this->redis = new RedisClient([
            'scheme' => 'tcp',
            'host' => '172.19.0.2',
            'port' => 6379,
            'password' => 'cloudy4next@@@'
        ]);
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:trade-closer-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching open trades...');
        // Get all open trades with necessary fields and eager load customer data
        $trades = DB::table('buysells')
            ->where('is_running', 1)
            ->join('customers', 'buysells.customer_id', '=', 'customers.id')
            ->where(function ($query) {
                $query->where('take_profit', '!=', 0)
                    ->orWhere('stop_loss', '!=', 0);
            })
            ->select('buysells.*', 'customers.service_charge', 'buysells.id as trade_id')
            ->get();


        $this->info('Processing ' . $trades->count() . ' open trades...');

        if ($trades->isEmpty()) {
            Log::info('No open trades found.');
        }

        foreach ($trades as $trade) {
            $this->info('Processing trade: ' . $trade->id);

            DB::beginTransaction();
            try {
                $priceData = $this->goldService->fetchGoldPrice(false, true);

                // $priceData = $this->retunGoldPriceFromCache();
                $goldPrice = $priceData['price'];
                $ask = $priceData['ask'];
                $bid = $priceData['bid'];



                $flag = false;
                $transction_amount = null;
                $atPrice = null;
                $takeProfit = $trade->take_profit;
                $stop_loss = $trade->stop_loss;
                $type = $trade->type;

                if ($takeProfit != 0 || $stop_loss != 0) {
                    if ($type == 'buy') {
                        if ($takeProfit != 0 && $ask >= $takeProfit) {
                            $flag = true;
                            $atPrice = $takeProfit;
                        } elseif ($stop_loss !=0 && $ask <= $stop_loss) {
                            $flag = true;
                            $atPrice = $stop_loss;
                        }
                    } else {
                        if ($takeProfit != 0 && $bid <= $takeProfit) {
                            $flag = true;
                            $atPrice = $takeProfit;
                        } elseif ($stop_loss !=0 && $bid >= $stop_loss) {
                            $flag = true;
                            $atPrice = $stop_loss;
                        }
                    }
                }

                if ($flag) {

                    $transction_amount = $this->goldService->calculatepl(
                        $trade->current_rate,
                        $atPrice,
                        $trade->type,
                        $trade->tt_quantity,
                        $trade->service_charge
                    );
                    $this->info(string: 'Transaction amount: ' . $transction_amount);

                    $referenceNo = 'AutoClose-' . $trade->id . '-' . time();
                    DB::table('transaction')->insert([
                        "reference_no" => $referenceNo,
                        "quantity" => $trade->tt_quantity,
                        "current_rate" => $atPrice,
                        "business_id" => $trade->business_id,
                        "starting_rate" => $trade->current_rate,
                        "customer_id" => $trade->customer_id,
                        "transaction_type" => $trade->type,
                        "reference_table" => "buysells",
                        "reference_row" => $trade->id,
                        "tnx_id" => rand(100000, 999999) . time(),
                        "transaction_amount" => $transction_amount,
                    ]);

                    // Mark trade as closed
                    DB::table('buysells')->where('id', $trade->id)->update([
                        'is_running' => 0,
                        'close_quanntity' => $trade->tt_quantity,
                        'updated_at' => now(),
                    ]);

                    Log::info('BuySell id ' . $trade->id . ' closed successfully.');
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error('Error processing trade: ' . $trade->id . '. ' . $e->getMessage());
                Log::error('Error: BuySell Id ' . $trade->id . '---------' . $e->getMessage());
            }
        }

        $this->info('Trade processing completed.');
    }

    private function retunGoldPriceFromCache()
    {
        $keys = $this->redis->get(key: 'gold_api_data');

        return json_decode($keys, true);
    }


}
