<?php

namespace App\Services;


use Illuminate\Support\Facades\Storage;

class UploaderService
{
    public function upload($file, $path)
    {
        $filename = $file->getClientOriginalName();
        $file->storeAs($path, $filename, 'public');
        return $filename;
    }

    public function delete($path)
    {
        Storage::disk('public')->delete($path);
    }
}
