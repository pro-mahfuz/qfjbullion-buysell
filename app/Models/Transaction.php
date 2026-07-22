<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends BaseModel
{
    use HasFactory;

    protected $table = 'transaction';
    protected $guarded = ['*'];
    protected $fillable = [
        'id',
        'customer_id',
        'business_id',
        'reference_no',
        'type',
        'quantity',
        'current_rate',
        'starting_rate',
        'transaction_amount',
        'transaction_type',
        'reference_table',
        'reference_row',
        'note',
        'tnx_id',
        'created_by',
        'created_at',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function rewards()
    {
        return $this->hasMany(Reward::class, 'transaction_id');
    }
}
