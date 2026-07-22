<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseItem extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'id',
        'purchase_id',
        'product_name',
        'supplier_id',
        'product_id',
        'quantity',
        'pure_quantity',
        'type',
        'discount_usd',
        'discount_aed',
        'unfix_rate_oz',
        'unfix_rate_gram',
        'unfix_subtotal',
        'created_by',
        'created_at',
    ];


    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
