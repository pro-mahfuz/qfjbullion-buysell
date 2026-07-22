<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'student_id',
        'account_number_id',
        'created_by',
        'total_paid',
        'total_fine',
        'total_discount',
        'payment_method',
        'payment_note',
        'payment_attachment',
        'reference_no',
        'created_at',
    ];

    public function feesInvoices() {
        return $this->hasMany(FeesTransaction::class);
    }
    
    public function students() {
        return $this->belongsTo(Student::class,'student_id');
    }
    public function user() {
        return $this->belongsTo(User::class,'created_by');
    }
}
