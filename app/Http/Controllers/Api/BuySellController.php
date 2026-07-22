<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\TransactionService;
use DB;
use Illuminate\Http\Request;
use Validator;

class BuySellController extends BaseController
{

    public function __construct(private TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }


    public function storeSplitTrade(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|array|string',
            'split_quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            $this->transactionService->storeSplitTrade($request->all());
        } catch (\Exception $e) {
            return $this->sendError('Error.', $e->getMessage());
        }

        return $this->sendResponse(null, 'Split Trade Created Successfully');
    }




    public function editPrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required',
            'rate' => 'required',
            'type' => 'required',
            'tt' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            $this->transactionService->editPrice(
                $request->transaction_id,
                $request->rate,
                $request->type,
                $request->tt
            );
        } catch (\Exception $e) {
            return $this->sendError('Error.', $e->getMessage());
        }

        return $this->sendResponse(null, 'Price Updated Successfully');
    }




    public function storeMatchedTrade(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|integer|exists:businesses,id',
            'transaction_id' => 'required|integer|exists:transactions,id',
            'customer_id' => 'required|integer|exists:customers,id',
            'transaction_type' => 'required|string|in:buy,sell',
            'starting_rate' => 'required|numeric',
            'selected_trade_rate' => 'required|numeric',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Store the matched trade
        try {
            $this->transactionService->storeMatchedTrade(
                $request->business_id,
                $request->transaction_id,
                $request->customer_id,
                $request->transaction_type,
                $request->starting_rate,
                $request->selected_trade_rate,
                $request->quantity
            );
        } catch (\Exception $e) {
            return $this->sendError('Error.', $e->getMessage());
        }

        return $this->sendResponse(null, 'Matched Trade Created Successfully');
    }

    public function depositStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|min:1',
            'business_id' => 'required|integer',
            'transaction_type' => 'required|string|in:buy,sell',
            'reference_row' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }
        try {
            $this->transactionService->handleDepositStore($validator->validated());
        } catch (\Exception $e) {
            return $this->sendError('Error.', $e->getMessage());
        }

        return $this->sendResponse(null, 'Updated Successfully');
    }




}
