<?php

namespace App\Http\Requests\Admin;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
     
    public function rules(): array
    {
        $rules = [
            'full_name' => ['required', 'string', 'max:250'],
            
        ];

       

        return $rules;
    }
}
