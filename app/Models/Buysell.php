<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buysell extends BaseModel
{
    use HasFactory;

    protected $table = 'buysells';

    protected $fillable = [
        'id',
        'reference_no',
        'business_id',
        'customer_id',
        'type',
        'close_quanntity',
        'total_amount_aed',
        'tt_quantity',
        'current_rate',
        'service_charge',
        'cut_position',
        'is_running',
        'created_by',
        'created_at',
    ];

    public function customer() {
        return $this->belongsTo(Customer::class,'customer_id');
    }


}
