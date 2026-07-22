<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Client\BaseContrller;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;




class LoginController extends BaseContrller
{
    public function showLoginForm()
    {
        return view('client.auth.login');
    }


    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $apiUrl = url('api/login');

        $response = Http::withoutVerifying()->
            post($apiUrl, [
                'email' => $request->email,
                'password' => $request->password,
            ]);

        // Check if the API response is successful
        if ($response->successful()) {
            $responseData = $response->json();

            $token = $responseData['data']['token'] ?? null;
            $customer_id = $responseData['data']['customer_id'] ?? null;
            $business_id = $responseData['data']['business_id'] ?? null;
            if (!$token) {
                return redirect()->back()->with('error', 'Invalid credentials');
            }

            if (!$customer_id) {
                return redirect()->back()->with('error', 'Could not find customer. Contact support for help!!!');
            }
            if (!$business_id) {
                return redirect()->back()->with('error', 'Could not find business. Contact support for help!!!');
            }

            Auth::guard('client')->loginUsingId($responseData['data']['id']);
            session()->put('token', $token);
            session()->put('customer_id', value: $customer_id);
            session()->put('business_id', $business_id);
            session()->put('is_completed', $responseData['data']['is_completed'] ?? null);
            session()->put('name', value: $responseData['data']['name'] ?? null);
            session()->put('email', value: $responseData['data']['email'] ?? null);
            session()->put('conversion_rate', value: $responseData['data']['conversion_rate'] ?? null);
            session()->put('currency', value: $responseData['data']['currency'] ?? null);
            return redirect()->route('client.dashboard')->with('success', 'Login successful');
        }

        return redirect()->back()->with('error', 'Invalid credentials');
    }


    public function logout(Request $request)
    {
        $response = $this->post('/logout');
        if ($response->status() != 200) {
            return redirect()->back()->with('error', 'Failed to logout');
        }

        Auth::guard('client')->logout();
        session()->flush();

        return redirect()->route('client.login');
    }

}
