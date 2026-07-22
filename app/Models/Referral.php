<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends BaseModel
{
    use HasFactory;

    protected $table = 'referrals';

    protected $primaryKey = 'referral_id';

    protected $fillable = [
        'title',
        'referral_amount',
        'first_person_reward',
        'second_person_reward',
        'third_person_reward',
        'other_person_reward',
        'total_referral_amount',
        'percentage',
        'first_person_amount'
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class, 'referral_id', 'id');
    }

}
