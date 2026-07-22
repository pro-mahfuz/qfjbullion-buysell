<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Traits\ClientHttpTrait;
use App\Traits\ClientTrait;
use Request;


class ClientProductController extends Controller
{
    use ClientTrait, ClientHttpTrait;

    protected $token = null;

    public function __construct()
    {
        $this->token = session()->get('token');
        if (!$this->token) {
            auth()->guard('client')->logout();
        }
    }

    public function getShopItems(Request $request)
    {
        $products = $this->getShopProducts();

        return view('client.transaction.product', compact('products'));
    }
}
