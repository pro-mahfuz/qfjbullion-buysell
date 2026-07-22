<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Traits\ClientHttpTrait;
use Illuminate\Http\Request;

class BaseContrller extends Controller
{
    use ClientHttpTrait;
    protected $token = null;

    public function __construct()
    {
        $this->token = session()->get('token');
        if (!$this->token) {
            auth()->guard('client')->logout();
        }
    }


    public function errors($response)
    {
        if ($response->status() == 422) {
            $response = $response->json();
            $errors = $response['errors'] ?? [];
            $message = $response['message'] ?? 'Unprocessable Entity';
            return redirect()->back()->with('error', $message)->withErrors($errors);
        }

        return redirect()->back()->with('error', 'Something went wrong');
    }
}
