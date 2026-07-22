<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\GoldService;
use App\Services\TransactionService;
use DB;
use Carbon\Carbon;
use Log;
class TransferPendingToBuysellCOPY extends Command
{
    protected $signature = 'transfer:pending-to-buysellCopy';
    protected $description = 'Transfer data from Pending table to Buysells table';

    public function __construct(private GoldService $goldService, private TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
        $this->goldService = $goldService;
        parent::__construct();
    }

    public function handle()
    {

        $pendingRecords = DB::table('pending')->where('is_processed', 0)
            ->get();

        if (!$pendingRecords->isEmpty()) {
            $fetchedIds = $pendingRecords->pluck('id')->toArray();
            $businessMsg = [];

            foreach ($pendingRecords as $record) {
                $this->info('Processing record: ' . $record->id);
                $goldPrice = $this->goldService->fetchGoldPrice();
                $currentAmount = $this->transactionService->getEquity($record->customer_id, $goldPrice);
                $cutomerName = DB::table(table: 'customers')->where('id', $record->customer_id)->first();
                $this->info('Gold price: ' . $goldPrice);

                if ($cutomerName) {
                    if ($goldPrice >= $record->threshold_rate || $goldPrice <= $record->threshold_rate) {
                        $total_amount_aed = ($goldPrice * 3.745 * 3.67 * $record->tt);
                        $this->info(string: 'Total amount: ' . $total_amount_aed);

                        $this->info('Customer name: ' . $cutomerName->name);
                        if ($currentAmount < $total_amount_aed) {
                            unset($fetchedIds[array_search($record->id, $fetchedIds)]);

                            $businessMsg[$record->business_id] = [
                                'message' => 'Insufficient balance',
                                'action_url' => env('APP_URL') . '/admin/buysell/search?customer=' . $cutomerName->name,
                            ];
                            $this->warn('Insufficient balance');
                            continue;
                        } else {
                            try {
                                $reference_no = 'Pending-' . $record->id . '-' . Carbon::parse($record->created_at)->format('Ymd');
                                DB::table('buysells')->insert([
                                    'reference_no' => $reference_no,
                                    'tt_quantity' => $record->tt,
                                    'current_rate' => $goldPrice,
                                    'customer_id' => $record->customer_id,
                                    'type' => $record->type,
                                    'total_amount_aed' => $total_amount_aed,
                                    'close_quanntity' => 0,
                                    'cut_position' => 0,
                                    'created_by' => $record->customer_id,
                                    'business_id' => $record->business_id,
                                    'trading_source' => 'Pending',
                                    'take_profit' => $record->take_profit,
                                    'stop_loss' => $record->stop_loss,
                                    'created_at' => now(),
                                    'updated_at' => now(),

                                ]);
                                $this->info('Trade executed successfully.');
                            } catch (\Exception $e) {
                                $this->warn('Erstring: ror: ' . $e->getMessage());
                            }
                            $businessMsg[$record->business_id] = [
                                'message' => 'Trade executed successfully.',
                                'action_url' => env('APP_URL') . '/admin/buysell/search?customer=' . $cutomerName->name,
                            ];
                        }
                    } else {
                        unset($fetchedIds[array_search($record->id, $fetchedIds)]);
                    }
                }
            }
            try {
                DB::table('pending')->whereIn('id', $fetchedIds)->update(['is_processed' => 1]);
                $this->info('Pending records updated successfully!');
            } catch (\Exception $e) {
                $this->warn('Error: ' . $e->getMessage());
            }

            // $this->sendNotificaion($businessMsg);

            $this->info('Pending records transferred to Buysells successfully!');
        } else {
            Log::info('No pending records found!');
        }
    }

    // private function sendNotificaion($message)
    // {
    //     if (empty($message)) {
    //         return;
    //     }

    //     foreach ($message as $businessId => $msg) {
    //         $users = UserBusinessMap::where('bussiness_id', $businessId)->get();
    //         foreach ($users as $user) {
    //             $user->notify(new JobNotification($msg));
    //         }
    //     }
//
    // }
}
