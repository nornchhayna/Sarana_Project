<?php

namespace App\Traits;

use App\Helpers\ImageHelper;
use Illuminate\Http\UploadedFile;

trait HasImageUpload
{
    public function uploadImage(UploadedFile $file, string $folder): string
    {
        return ImageHelper::upload($file, $folder);
    }

    public function replaceImage(?string $oldPath, UploadedFile $file, string $folder): string
    {
        ImageHelper::delete($oldPath);

        return $this->uploadImage($file, $folder);
    }
}
