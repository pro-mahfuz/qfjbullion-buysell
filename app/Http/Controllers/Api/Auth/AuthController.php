<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Traits\UserTrait;
use Auth;
use DB;
use Illuminate\Http\Request;
use Log;
use Validator;
use Crypt;
class AuthController extends BaseController
{

    use UserTrait;
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $customer = DB::table('customers')
                ->select('customers.*', 'currencies.code', 'currencies.conversion_rate')
                ->leftJoin('currencies', 'currencies.id', '=', 'customers.currency_id')
                ->where('email', $request->email)->first();

            if (!$customer || !$customer->id) {
                return $this->sendError('Unauthorised.', ['error' => 'Customer not found.Contact with admin!!!']);
            }

            if (!$customer->business_id) {
                return $this->sendError('Unauthorised.', ['error' => 'Business not found.Contact with admin!!!']);
            }

            $success['token'] = $user->createToken('Shadinportal', [])->accessToken;
            $success['name'] = $user->full_name;
            $success['id'] = $user->id;
            $success['customer_id'] = $customer->id ?? null;
            $success['business_id'] = $customer->business_id ?? null;
            $success['is_completed'] = $customer->is_completed ?? null;
            $success['email'] = $user->email;
            $success['currency'] = $customer->code ?? 'AED';
            $success['conversion_rate'] = $customer->conversion_rate ?? 1;
            // dd($customer);
            // read image from public folder   $data['image'] = $request->file('image')->store('public');
            $success['image'] = $customer->image ? asset($customer->image) : null;

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $request->user()->token()->revoke();
            return $this->sendResponse([], 'User logout successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }


    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'password_confirmation' => 'required|same:password',
                'link' => 'required',
            ]
        );

        if ($validator->fails()) {
            // Log::error($validator->errors());
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        if ($request->password != $request->password_confirmation) {
            return $this->sendError('Password does not match.', ['error' => 'Password does not match'], 422);
        }

        try {
            DB::transaction(function () use ($request, &$success) {
                // Insert user record
                $userId = DB::table('users')->insertGetId([
                    'full_name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'self' => 1,
                ]);


                DB::table('customers')->insert([
                    'business_id' => Crypt::decrypt($request->link),
                    'email' => $request->email,
                    'name' => $request->name,
                    'customer_code' => "NU" . $userId,
                    'status' => 'activated',
                    'currency_id' => 2,
                    'is_completed' => 1,
                ]);

            });
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getCode() == 23000 ? "Already Registered!" : "An error occurred while processing your request!",
                'error' => "An error occurred while processing your request!",
            ], 500);
        }

        return $this->sendResponse($success, 'User register successfully.');
    }




}
