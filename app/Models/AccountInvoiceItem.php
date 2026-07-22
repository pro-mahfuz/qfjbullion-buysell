<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountInvoiceItem extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'account_invoice_id',
        'account_head_id',
        'amount',
        'note',
    ];

    public function item() {
        return $this->belongsTo(AccountHead::class, 'account_head_id');
    }
}
