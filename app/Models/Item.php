<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'id',
        'product_id',
        'net_amount',
        'net_quantity',
        'created_by',
        'created_at',
    ];

    public function product() {
        return $this->belongsTo(Product::class,'product_id');
    }


}
