<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Store an uploaded image to a given disk folder and return its path.
     */
    public static function upload(UploadedFile $file, string $folder, string $disk = 'public'): string
    {
        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();

        return $file->storeAs($folder, $filename, $disk);
    }

    /**
     * Delete an image from storage (ignores missing files silently).
     */
    public static function delete(?string $path, string $disk = 'public'): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    /**
     * Return the public URL for a stored path, or a placeholder.
     */
    public static function url(?string $path): string
    {
        return $path ? Storage::url($path) : asset('images/placeholder.png');
    }
}
