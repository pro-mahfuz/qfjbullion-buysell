<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'business_id',
        'mobile_number',
        'address',
        'init_balance',
        'sell_amount',
        'deposit_amount',
        'withdraw_amount',
        'balance',
        'email',
        'trn_no',
        'narration',
        'created_by',
        'created_at',
        'type',
    ];

}
