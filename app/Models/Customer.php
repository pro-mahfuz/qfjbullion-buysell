<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends BaseModel
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'customers';

    // The primary key for the model
    protected $primaryKey = 'id';

    // The attributes that are mass assignable
    protected $fillable = [
        'name',
        'email',
        'bussiness_id',
        'password',
        'avatar',
        'dob',
        'phone',
        'address',
        'remember_token',
        'created_at',
        'updated_at',
        'confirmed_at',
        'email_verify_token',
        'reference_id',
        'status',
        'private_notes',
        'trn_no',
        'id_proof',
        'valid_up_to',
        'id_number',
        'trade_license',
        'country',
        'land_phone',
        'city',
        'type',
        'referrer',
        'attachment',
        'customer_code',
        'maxtt_per_K',
        'service_charge',
        'referral_code',
        'cutposition'

    ];

    // The attributes that should be cast to native types
    protected $casts = [
        'dob' => 'date',
        'confirmed_at' => 'datetime',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    public $incrementing = true;

    public $timestamps = true;

    protected $attributes = [
        'status' => 'activated',
        'reference_id' => 0,
    ];


    public function referral()
    {
        return $this->belongsTo(Referral::class, 'referral_code', 'referral_id');
    }


    public function scopeActive($query)
    {
        return $query->where('status', 'activated');
    }



}
