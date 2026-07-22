<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use App\Traits\UserTrait;
use Auth;
use DB;
use Log;
use Validator;
use Crypt;

class ProfileController extends BaseController
{
    use UserTrait;
    use ApiTrait;


    public function me(Request $request)
    {
        $user = Auth::user();
        return $this->sendResponse($user, 'User details.');
    }


    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'id_proof_document' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $data = [];
        if ($request->file('image')) {
            $imagePath = FileUploadService::handleFileUpload($request, 'image', uploadFolder: 'customer_image/');
            $data['image'] = asset($imagePath);
        }
        if ($request->file('id_proof_document')) {
            $idProof = FileUploadService::handleFileUpload($request, 'id_proof_document', uploadFolder: 'customer_documents/');
            $data['id_proof_document'] = asset($idProof);
        }
        if ($data) {
            DB::table('customers')->where('email', $request->user()->email)->update(
                $data
            );
        } else {
            return $this->sendError('Validation Error.', 'Please select image or id proof document.', 422);
        }
        // now update customer table with image path and  id_proof path


        return $this->sendResponse($data, 'Image uploaded successfully.');
    }

    public function profileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), $this->getRules());

        if ($validator->fails()) {
            Log::error($validator->errors());

            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        $data = $request->except('_token');
        $data['is_completed'] = 1;
        $data['type'] = 'customer';
        unset($data['currency']);
        unset($data['phone']);

        if ($request->file('image')) {
            $data['image'] = FileUploadService::handleFileUpload($request, 'image', uploadFolder: 'customer_image/');

        }
        // if ($request->file('id_proof')) {
        //     $data['id_proof'] = FileUploadService::handleFileUpload($request, 'id_proof', uploadFolder: 'customer_documents/');

        // }


        // $currency = DB::table('currencies')->where('code', $data['currency'])->first();
        $data['currency_id'] = 2;//$currency->id;
        try {
            DB::transaction(function () use ($request, &$success, $data) {
                DB::table('customers')->where('email', $request->user()->email)->update(
                    $data
                );
                //  DB::table('users')->where('email', $request->user()->email)->update(["full_name"=>$data['name']]);
                if (isset($data['name'])) {
                    DB::table('users')
                        ->where('email', $request->user()->email)
                        ->update(['full_name' => $data['name']]);
                }

            });
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.',
                'error' => $e->getMessage(), // Optional: include error details for debugging
            ], 500);
        }

        return $this->sendResponse($success, 'Profile Updated successfully.');

    }



    public function profileDelete(Request $request)
    {


        return $this->sendResponse([], 'Profile Delate successfully.');

    }

    public function getBusiness()
    {
        $customer = $this->customer();
        if (!$customer) {
            return $this->sendError('Customer not found.');
        }
        $business = DB::table('bussiness')->where('id', $customer->business_id)->first();
        return $this->sendResponse($business, 'Business List.');
    }

}
