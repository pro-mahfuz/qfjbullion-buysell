<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
// use App\Services\CustomerService;
use App\Services\GoldService;
use App\Services\Api\TransactionService;
use App\Traits\ApiTrait;
use Auth;
use Illuminate\Http\Request;
use Log;
use Validator;

class TransactionController extends BaseController
{

    use ApiTrait;
    public function __construct(private TransactionService $transactionService, private GoldService $goldService)
    {
        $this->transactionService = $transactionService;
        $this->goldService = $goldService;

    }

    /**
     * Summary of deleteTransaction
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function deleteTransaction($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $transaction = $this->transactionService->deleteTransaction($id);
        return $this->sendResponse($transaction, 'Transaction deleted successfully.');
    }



    /**
     * Summary of transactionSearch
     * @param mixed $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */

    public function transactionSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference_no' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $transaction = $this->transactionService->transactionSearch($request->reference_no);

        return $transaction
            ? $this->sendResponse($transaction, 'Transaction found')
            : $this->sendError('No transaction found');
    }




    public function getTransactions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'business_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        try {
            $transactions = $this->transactionService->getTranscations($request->type, $request->business_id);
            return $this->sendResponse($transactions, 'Transaction List');
        } catch (\Exception $e) {
            return $this->sendError('No transaction found');
        }

    }

    public function getStatement(Request $request)
    {
        $startDate = isset($request->start_date) ? $request->start_date . ' 00:00:00' : null;
        $endDate = isset($request->end_date) ? $request->end_date . ' 23:59:59' : null;
        // dd($startDate, $endDate);
        try {
            $stat = $this->transactionService->generateStatement($startDate, $endDate);
            return $this->sendResponse($stat, 'Statement');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }


    public function getPendingList(Request $request)
    {

        $pendingList = $this->transactionService->getPendingList();
        return $this->sendResponse(
            $pendingList,
            'Pending List'
        );
    }


    public function getLastTenTransation()
    {
        $lastTen = $this->transactionService->getLastTenCompletedList();
        return $this->sendResponse($lastTen, 'Last Ten Transactions');
    }


    public function getDepositWithdrawList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required',
            'type' => 'required|in:deposit,withdraw',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $depositWithdrawList = $this->transactionService->getDepositWithdrawList($request->type, $request->business_id, $request->id);


        return $this->sendResponse($this->makePaginator($depositWithdrawList), 'Deposit Withdraw List');
    }


    public function getRunningList(Request $request)
    {
        $data = $this->transactionService->getRunningTradeByCustomer();
        return $this->sendResponse($data, 'Running List');
    }



    public function getDeposits(Request $request)
    {
        if ($request->type == null) {
            return $this->sendError('Validation Error.', 'Type is required');
        }

        $deposits = $this->transactionService->getDeposits($request->type);
        return $this->sendResponse($deposits, 'Deposits');
    }


    public function saveTransaction(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'transaction_amount' => ['required', 'numeric', 'min:0'],
            'transaction_type' => ['required', 'in:deposit,withdraw'],
            // 'note' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        try {
            $this->transactionService->saveTransaction($validator->validated());
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), []);
        }

        return $this->sendResponse([], 'Transaction saved successfully');
    }



    public function saveBid(Request $request)
    {

        $rules = [
            "tt_quantity" => "required|numeric|min:0",
            "close_quanntity" => "required|numeric|min:0",
            "current_rate" => "required",
            "type" => "required",
            "product_id" => "required|integer|exists:products,id",
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $customer = $this->customer();
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ]);
        }

        try {
            $data = $validator->validated();
            $data['business_id'] = $customer->business_id;
            $data['reference_no'] = auth('api')->user()->name . rand(100000, 999999);
            $data['created_at'] = now();
            $data['updated_at'] = now();
            $data['current_rate'] = $request->current_rate;
            $data['product_id'] = $request->product_id;
            $data['customer_id'] = $customer->id;
            $this->transactionService->saveBid($data);

        } catch (\Exception $e) {

            $this->sendError('Error saving bid: ' . $e->getMessage());
        }

        return $this->sendResponse([], 'Bid saved successfully');
    }

    public function getLiveData()
    {
        try {
            return $this->sendResponse($this->transactionService->getLiveData(), 'Live Data');
        } catch (\Exception $e) {

            return $this->sendError($e->getMessage());
        }
    }


    public function getDashboard()
    {
        try {
            return $this->sendResponse($this->transactionService->getDashboard(), 'Dashboard');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }


    public function getProductList(Request $request)
    {
        $customer = $this->customer();
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ]);
        }

        $products = $this->transactionService->getProducts($customer->business_id);
        return $this->sendResponse($products, 'Product List');
    }

    public function singleProduct($id)
    {
        $product = $this->transactionService->singleProduct($id);
        return response()->json(['success' => true, 'product' => $product]);
    }

    public function updateTradeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:buysells,id',
            'stop_loss' => 'required|numeric|min:0',
            'take_profit' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        try {
            $this->transactionService->updateTradeStatus($validator->validated());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Trade status updated successfully'
        ]);

    }

    public function storeSplitTrade(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:buysells,id',
            'split_quantity' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        try {
            $this->transactionService->storeSplitTrade($validator->validated());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Trade split successfully'
        ]);
    }


    public function downloadStatement(Request $request)
    {
        $startDate = isset($request->start_date) ? $request->start_date . ' 00:00:00' : null;
        $endDate = isset($request->end_date) ? $request->end_date . ' 23:59:59' : null;
        try {
            $filePath = $this->transactionService->downloadStatement($startDate, $endDate);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }
        return response()->json([
            'success' => false,
            'message' => 'File not found'
        ]);
    }
}
