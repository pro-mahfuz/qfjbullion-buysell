<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bussiness extends Model
{
    use HasFactory;

    protected $table = 'bussiness';

    protected $guarded = [];

    protected $casts = [
        'currency' => 'array',
    ];
}
