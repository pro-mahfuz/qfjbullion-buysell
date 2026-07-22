<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'id',
        'invoice_no',
        'ref_no',
        'supplier_id',
        'discount',
        'staff_note',
        'note',
        'type',
        'unfix_total',
        'created_by',
        'created_at',
    ];
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id');
    }
}
