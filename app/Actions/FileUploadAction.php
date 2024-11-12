<?php

namespace App\Actions;

use Illuminate\Support\Facades\Storage;

class FileUploadAction
{
    public static function handle($path, $file)
    {
        $path = Storage::disk('s3')->putFile($path, $file);
        // $path = Storage::disk('s3')->url($path);
        return $path;
    }
}
