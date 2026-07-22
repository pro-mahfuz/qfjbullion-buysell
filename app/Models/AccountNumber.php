<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Session;

class AccountNumber extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'account_title',
        'account_number',
        'account_holder',
        'init_balance'
    ];

}
