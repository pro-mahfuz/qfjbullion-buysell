<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends BaseModel
{
    use HasFactory;

    protected $table = 'rewards';

    protected $fillable = ['referral_id', 'user_id', 'reward_amount', 'reward_level', 'is_disbusted'];


    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function referrals()
    {
        return $this->belongsTo(Referral::class, 'referral_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
