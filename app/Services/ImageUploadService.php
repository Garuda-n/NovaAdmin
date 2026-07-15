<?php
namespace App\Services;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class ImageUploadService
{
    /**
     * Upload an image and return the relative storage path.
     */
    public static function upload(UploadedFile $file, string $folder): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = Str::uuid() . '.' . $extension;
        return $file->storeAs($folder, $fileName, 'public');
    }
    /**
     * Delete an image from storage.
     */
    public static function delete(?string $path): bool
    {
        if (!$path) {
            return false;
        }
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }
    /**
     * Replace an existing image.
     */
    public static function update(
        ?UploadedFile $file,
        ?string $oldPath,
        string $folder
    ): ?string {
        if (!$file) {
            return $oldPath;
        }
        self::delete($oldPath);
        return self::upload($file, $folder);
    }
}