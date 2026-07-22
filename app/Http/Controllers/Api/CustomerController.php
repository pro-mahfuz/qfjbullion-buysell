<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Services\Api\TransactionService;
use App\Services\FileUploadService;
use App\Traits\ApiTrait;
use DB;
use Illuminate\Http\Request;
use Validator;

class CustomerController extends BaseController
{
    use ApiTrait;
    public $customerModel;
    protected $user;
    public function __construct()
    {
        $this->customerModel = DB::table('customers');
        $this->user = auth('api')->user();
    }

    /**
     * Summary of getCustomers
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getCustomers()
    {
        $customers = $this->customerModel->get();
        return $this->sendResponse($customers->toArray(), 'Customers List.');
    }

    /**
     * Summary of deleteCustomer
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getCustomer($id)
    {
        if (!$id) {
            return $this->sendError('Customer not found.');
        }

        $customer = $this->customerModel->find($id);

        if (is_null($customer)) {
            return $this->sendError('Customer not found.');
        }

        $customer->current_amount = $this->getCurrentAmount() ?? null;


        return $this->sendResponse($customer, 'Single Customer.');
    }

    /**
     * Summary of createCustomer
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function createCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), $this->getRules());

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        $attachemtUrl = FileUploadService::handleFileUpload($request, 'document', 'customer_document/');

        $this->customerService->saveCustomer($request->all(), $attachemtUrl);

        return $this->sendResponse(null, 'Customer created successfully.');
    }

    /**
     * Summary of getRules
     * @param mixed $isEdit
     * @return array
     */
    private function getRules($isEdit = false): array
    {
        $data = [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'id_proof' => ['required', 'string', 'max:255'],
            'id_number' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255',],
            'maxtt_per_K' => ['required', 'string', 'max:255'],
            'service_charge' => ['required', 'string', 'max:255'],
        ];
        if (!$isEdit) {
            $data['phone'] = ['required', 'string', 'max:255', 'unique:customers,phone'];
            $data['customer_code'] = ['required', 'string', 'max:255', 'unique:customers,customer_code'];
        }
        return $data;
    }


    public function getBusiness()
    {
        
    }

}
