<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\School;
use App\Services\CustomerService;
use App\Services\FileUploadService;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Session;
use Validator;
class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService,
        private ReferralService $referralService
    ) {
        $this->customerService = $customerService;
        $this->referralService = $referralService;
    }

    public function customerView($customer_id)
    {

        return view('admin.customer.deposit', ['data' => $this->customerService->getCustomerById($customer_id)]);
    }


    public function customerList()
    {
        //dd($this->customerService->getCustomers());
        return view('admin.customer.list', ['customers' => $this->customerService->getCustomers()]);
    }


    public function customerCreate()
    {
        $customers = $this->customerService->getRefferarCustomer();
        $referrals = $this->referralService->getReferrals();

        return view('admin.customer.create')->with([
            "customers" => $customers,
            "referrals" => $referrals,
            "generatedCodeWithPrefixSJ" => $this->customerService->generateBusinessName(),

        ]);
    }


    public function customerStore(Request $request)
    {
        $validator = Validator::make($request->all(), $this->getRules());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $postData = array_merge($request->except('_token'));

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileSize = $file->getSize();
            if ($fileSize > 2097152) {
                return redirect()->back()->with('error', 'File size should be less than 2MB');
            }
        }

        $attachemtUrl = FileUploadService::handleFileUpload($request, 'document', 'customer_document/');

        $this->customerService->saveCustomer($postData, $attachemtUrl);

        return redirect()->route('admin.customer.list')->with('success', 'Created Successfully');
    }



    public function customerUpdate(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), $this->getRules(true));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $postData = array_merge($request->except('_token'));

        $this->customerService->updateCustomer($postData, $postData['id']);

        return redirect()->back()->with('success', 'Updated Successfully');
    }


    private function getRules($isEdit = false): array
    {
        $data = [
            'name' => ['required', 'string', 'max:255'],
            'customer_code' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'id_proof' => ['required', 'string', 'max:255'],
            'id_number' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255',],
            'maxtt_per_K' => ['required', 'numeric', 'decimal:0,3'],
            'service_charge' => ['required', 'numeric', 'decimal:0,3'],
        ];
        if (!$isEdit) {
          //  $data['phone'] = ['required', 'string', 'max:255', 'unique:customers,phone'];
            $data['customer_code'] = ['required', 'string', 'max:255', 'unique:customers,customer_code'];
        }
        return $data;
    }

    public function customerDetails($customer_id)
    {
        return view('admin.customer.details', ['customer' => $this->customerService->getCustomerById($customer_id)]);
    }

    public function customerEdit($id)
    {
        return view('admin.customer.edit', ['customer' => $this->customerService->getCustomerById($id)]);
    }

    public function disableCustomer(Request $request)
    {
        // dd($request->all());
        $this->customerService->disableCustomer($request->id);
        return redirect()->back()->with('success', 'Disabled Successfully');
    }

    public function enableCustomer(Request $request)
    {
        $this->customerService->enableCustomer($request->id);
        return redirect()->back()->with('success', 'Enabled Successfully');
    }

    public function deleteCustomer(Request $request)
    {
        $this->customerService->deleteCustomer($request->id);
        return response()->json(['success' => 'Deleted Successfully']);
    }

}
