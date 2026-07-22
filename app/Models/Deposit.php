<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deposit extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'id',
        'business_id',
        'deposit_date',
        'deposit_amount',
        'withdraw_amount',
        'type',
        'purchase_id',
        'supplier_id',
        'staff_note',
        'note',
        'ref_no',
        'payment_account_id',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];
    public function supplier() {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }
}
