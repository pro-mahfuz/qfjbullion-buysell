<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountHead extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'title',
        'type'
    ];
    
}
