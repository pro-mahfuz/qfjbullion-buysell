<?php

namespace App\Console\Commands;

use App\Models\Pending;
use Illuminate\Console\Command;

use App\Services\GoldService;
use App\Services\TransactionService;
use DB;
use Carbon\Carbon;
use Log;
use Predis\Client as RedisClient;

class TransferPendingToBuysell extends Command
{
    protected $signature = 'transfer:pending-to-buysell';
    protected $description = 'Transfer data from Pending table to Buysells table';
    private $redis = null;

    public function __construct(private GoldService $goldService, private TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
        $this->goldService = $goldService;
        parent::__construct();

        $this->redis = new RedisClient([
            'scheme' => 'tcp',
            'host' => '172.19.0.2',
            'port' => 6379,
            'password' => 'cloudy4next@@@'
        ]);
    }

    public function handle()
    {

        $pendingRecords = DB::table('pending')->where('is_processed', 0)->get();

        if(!$pendingRecords->isEmpty()) {
            $fetchedIds = $pendingRecords->pluck('id')->toArray();
            $businessMsg = [];

            foreach ($pendingRecords as $record) {
                $this->info('Processing record Id: ' . $record->id);
                $customer = DB::table(table: 'customers')->where('id', $record->customer_id)->first();
                if ($customer) {


                    $priceData = $this->goldService->fetchGoldPrice(false, true);
                    // $priceData = $this->retunGoldPriceFromCache();
                    $goldPrice = $priceData['price'];
                    $ask = $priceData['ask'];
                    $bid = $priceData['bid'];

                    $currentAmount = $this->transactionService->getEquity($record->customer_id, $goldPrice);
                    $availableTTB = $currentAmount / (1000 / $customer->maxtt_per_K);



                    // if (type == 'buy') {
                    //     exicution_ttb = (runningBuyTTB > runningSellTTBValue) ? (getAvailableTT - ((runningBuyTTB -
                    //         runningSellTTBValue > 0) ? runningBuyTTB - runningSellTTBValue : 0)) : (getAvailableTT + (
                    //         runningSellTTBValue - runningBuyTTB));
                    // } else {
                    //     exicution_ttb = (runningSellTTBValue > runningBuyTTB) ? (getAvailableTT - (runningSellTTBValue -
                    //         runningBuyTTB)) : (getAvailableTT + (runningBuyTTB - runningSellTTBValue));

                    // }



                    $runningTrade = DB::table('buysells')->where('customer_id', $record->customer_id)
                        ->where('is_running', '1')->get();

                    $runningTrade = collect($runningTrade);

                    $totalBuyTTb = $runningTrade->where('type', 'buy')->sum('tt_quantity');
                    $totalSellTTb = $runningTrade->where('type', 'sell')->sum('tt_quantity');

                    $totalTTB = abs($totalBuyTTb - $totalSellTTb);

                    $this->info('Available TTB: ' . $availableTTB);

                    $remainingTTB = $availableTTB - $totalTTB;

                    // if ($remainingTTB < 0) {
                    //     $this->warn('No available TTB');
                    //     unset($fetchedIds[array_search($record->id, $fetchedIds)]);
                    //     continue;
                    // }

                    if ($customer && $availableTTB > 0) {
                        $flag = false;
                        $valueFlag = null;

                        if ($record->type == 'buy') {
                            if ($record->limit != 0 && $ask <= $record->limit) {
                                $flag = true;
                                $valueFlag = $record->limit;
                            } elseif ($record->stop != 0 && $ask >= $record->stop) {
                                $flag = true;
                                $valueFlag = $record->stop;
                            }
                        } else {
                            if ($record->limit != 0 && $bid >= $record->limit) {
                                $flag = true;
                                $valueFlag = $record->limit;
                            } elseif ($record->stop != 0 && $bid <= $record->stop) {
                                $flag = true;
                                $valueFlag = $record->stop;
                            }
                        }
                    }

                    try {
                
                    $reference_no = 'Pending-' . $record->id . '-' . Carbon::parse($record->created_at)->format('Ymd');
                    DB::table('buysells')->insert([
                        'reference_no' => $reference_no,
                        'tt_quantity' => $record->tt,
                        'current_rate' => $valueFlag,
                        'customer_id' => $record->customer_id,
                        'type' => $record->type,
                        'total_amount_aed' => 0,
                        'close_quanntity' => 0,
                        'cut_position' => 0,
                        'created_by' => $record->created_by,
                        'business_id' => $record->business_id,
                        'trading_source' => 'Pending',
                        'take_profit' => $record->take_profit,
                        'stop_loss' => $record->stop_loss,
                        'pending_id' => $record->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Pending::where('id', $record->id)->update(['market_started' => $market_started]);
                    // DB::table('pending')->where('id', $record->id)->update(['market_started' => $record->type]);
                    $this->info('Trade executed successfully.');
               
                        DB::table('pending')->where('id', $record->id)->update(['is_processed' => 1]);
        
                      //  $this->info('Pending records updated successfully!');
                    } catch (\Exception $e) {
                        $this->warn('Error: ' . $e->getMessage());
                    }
              
                }
                $this->info('Pending records transferred to Buysells successfully!');
            }
                           
        }else {
            $this->info('No pending records found!');
            Log::info('No pending records found!');
        }
    }

    private function retunGoldPriceFromCache()
    {
        $keys = $this->redis->get(key: 'gold_api_data');

        return json_decode($keys, true);
    }

}
