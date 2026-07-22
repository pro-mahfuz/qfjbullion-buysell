<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountInvoice extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'account_number_id',
        'reference_no',
        'invoice_type',
        'total_amount',
        'payment_method',
        'note',
        'attachments',
        'create_date',
        'created_by'
    ];

    public function items() {
        return $this->hasMany(AccountInvoiceItem::class);
    }
}
