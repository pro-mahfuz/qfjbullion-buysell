<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use App\Traits\ClientTrait;
use App\Traits\UserTrait;
use DB;
use Illuminate\Http\Request;
use Validator;

class DashboardController extends BaseContrller
{
    use UserTrait;
    use ClientTrait;

    public function dashboard(Request $request)
    {
        $deposits = collect($this->getAllTransactions('deposit'));
        $totalDepositInCompleted = $deposits->count();
        $totalDepositInCompletedAvg = $deposits->avg('transaction_amount');
        $totalDepositApproved = $deposits->where('status', 1)->count();

        $withdraws = collect($this->getAllTransactions('withdraw'));
        $totalWithDrawInCompleted = $withdraws->count();
        $totalWithDrawInCompletedAvg = $withdraws->avg('transaction_amount');
        $totalWithDrawApproved = $withdraws->where('status', 1)->count();



        $transactions = $this->getAllTransactions(['buy', 'sell']);
        $transactions = collect($transactions);

        $totalTransactions = $transactions->count();
        $sumTransactions = $transactions->sum('transaction_amount');
        $profit = $transactions->filter(function ($transaction) {
            // dd($transaction);
            return $transaction['transaction_amount'] > 0;
        })->sum('transaction_amount');
        $loss = $transactions->filter(function ($transaction) {
            return $transaction['transaction_amount'] < 0;
        })->sum('transaction_amount');

        return view('client.dashboard')->
            with('totalDepositInCompleted', $totalDepositInCompleted)->
            with('totalDepositInCompletedAvg', $totalDepositInCompletedAvg)->
            with('totalDepositApproved', $totalDepositApproved)->
            with('totalWithDrawInCompleted', $totalWithDrawInCompleted)->
            with('totalWithDrawInCompletedAvg', $totalWithDrawInCompletedAvg)->
            with('totalWithDrawApproved', $totalWithDrawApproved)->
            with('totalTransactions', $totalTransactions)->
            with('sumTransactions', $sumTransactions)->
            with('profit', $profit)->
            with('loss', $loss)->
            with('success', 'User login successfully.');
    }


    public function profile()
    {
        $business = $this->get('/business');
        $b = $business->json();

        if ($b['data']['currency'] == null) {
            return redirect()->route('client.dashboard')->with('error', 'No currency found');
        }

        return view('client.profile')->with('currencies', json_decode($b['data']['currency']));
    }

    public function profileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), $this->getRules());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $postData = array_merge($request->except('_token'));
        $attachemtUrl = FileUploadService::handleFileUpload($request, 'document', 'customer_document/');
        $postData['attachment'] = $attachemtUrl ?? 'N/A';
        $postData['service_charge'] = 1;
        $postData['maxtt_per_K'] = 2;
        $postData['customer_code'] = 'C' . rand(1000, 99999);
        try {
            $data = $this->post('/profile-update', [], $postData);

            if ($data->status() != 200) {
                return redirect()->back()->with('error', $data->json()['message']);
            }
            session()->put('is_completed', 1);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('client.dashboard')->with('success', 'Profile Update successful');
    }

}
