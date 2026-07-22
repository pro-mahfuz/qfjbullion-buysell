<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'description',
        'price_aed',
        'price_oz',
        'price_usd',
        'tax',
        'purity',
        'created_by',
        'created_at',
        'weight',
        'is_shop',
        'image',
        'qty'
    ];

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class, 'product_id');
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product_map', 'product_id', 'category_id');
    }
}
