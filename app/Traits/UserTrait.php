<?php

namespace App\Traits;

trait UserTrait
{
    public function getRules(): array
    {
        $data = [
            'address' => ['required', 'string', 'max:255'],
            'id_proof' => ['required', 'string', 'max:255'],
            'id_number' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255', 'unique:customers,phone'],
            'currency' => ['required'],
        ];

        return $data;
    }
}
