<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class FileUploadService
{
    // public static function handleFileUpload(Request $request, $fileName, $uploadFolder = '', $existingFile = null)
    // {
    //     if ($request->hasFile($fileName)) {
    //         $createdDirectiry = self::createDirectory($uploadFolder);

    //         $files = $request->file($fileName);
    //         $imageUrl = $createdDirectiry . Str::random() . time() . '.' . $files->getClientOriginalExtension();
    //         $files->move(public_path('uploads/' . $createdDirectiry), $imageUrl);

    //         if (!empty($existingFile)) {
    //             self::deleteExistingFile($existingFile);
    //         }
    //     } else {
    //         $imageUrl = $existingFile;
    //     }

    //     return $imageUrl;
    // }

    public static function handleFileUpload(Request $request, $fileName, $uploadFolder = '', $existingFile = null, $isCompressed= false)
    {
        if ($request->hasFile($fileName)) {
            $createdDirectory = self::createDirectory($uploadFolder);

            $files = $request->file(key: $fileName);
            $imageName =  Str::random() . time() . '.' . $files->getClientOriginalExtension();
            $imageUrl = $createdDirectory . $imageName;
            $uploadPath = public_path('uploads/' . $createdDirectory);
            $files->move($uploadPath, $imageUrl);

            if ($isCompressed) {
                $imagePath = $uploadPath.$imageName;
                self::compressImage($imagePath);
            }

            if (!empty($existingFile)) {
                self::deleteExistingFile($existingFile);
            }
        } else {
            $imageUrl = $existingFile;
        }

        return $imageUrl;
    }

    private static function compressImage($sourcePath) {
        list($sourceWidth, $sourceHeight, $sourceType) = getimagesize($sourcePath);

        switch ($sourceType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                return;
        }

        $destImage = imagecreatetruecolor($sourceWidth, $sourceHeight);

        imagecopyresampled(
            $destImage,
            $sourceImage,
            0, 0, 0, 0,
            $sourceWidth, $sourceHeight,
            $sourceWidth, $sourceHeight
        );

        switch ($sourceType) {
            case IMAGETYPE_JPEG:
                imagejpeg($destImage, $sourcePath, 60);
                break;
            case IMAGETYPE_PNG:
                imagepng($destImage, $sourcePath);
                break;
            case IMAGETYPE_GIF:
                imagegif($destImage, $sourcePath);
                break;
        }

        imagedestroy($sourceImage);
        imagedestroy($destImage);
    }

    public static function handleWebsiteFileUpload(Request $request, $fileName, $existingFile = null)
    {
        if ($request->hasFile($fileName)) {
            $uploadFolder = 'website/';
            $files = $request->file($fileName);
            $imageUrl = $uploadFolder . Str::random() . time() . '.' . $files->getClientOriginalExtension();
            $files->move(public_path('uploads/' . $uploadFolder), $imageUrl);

            if (!empty($existingFile)) {
                self::deleteExistingFile($existingFile);
            }
        } else {
            $imageUrl = $existingFile;
        }

        return $imageUrl;
    }


    private static function deleteExistingFile($existingFile)
    {
        $path = public_path('uploads/' . $existingFile);

        if (file_exists($path)) {
            unlink($path);
        }
    }

    private static function createDirectory($uploadFolder = '')
    {
        if(Session::has('currentSchoolId')) {
            $schoolId = Session::get('currentSchoolId').'/';
        } elseif(auth()->user()) {
            $schoolId = auth()->user()->school_id.'/';
        } else {
            $schoolId = '';
        }

        $directoryPath = public_path('uploads/'.$schoolId.$uploadFolder);

        if (!File::isDirectory($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true, true);
        }

        $uploadDirectory = $schoolId.$uploadFolder;

        return $uploadDirectory;
    }
}
