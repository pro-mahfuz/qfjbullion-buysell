<?php

namespace App\Services\Api;
use DB;

class BaseService
{
    protected $customer = null;

    public function __construct()
    {
        $this->customer = DB::table('customers')->where('email', auth('api')->user()->email)->first();
    }

}
