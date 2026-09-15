<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UploadHelper
{
    /**
     * Default maximum file size in Kilobytes (5 MB).
     * You can adjust this centrally at any time.
     */
    public const DEFAULT_MAX_SIZE_KB = 5120; // 5MB

    /**
     * Default maximum image size in Kilobytes (2 MB).
     */
    public const DEFAULT_IMAGE_MAX_SIZE_KB = 2048; // 2MB

    /**
     * Upload a single file with optional replacement of an old file.
     *
     * @param UploadedFile|null $file
     * @param string $folder Directory inside the disk (e.g., 'settings', 'employees/avatars')
     * @param string|null $oldFile Path to old file to delete on replacement
     * @param string $disk Disk name ('public', 'local', etc.)
     * @param int|null $maxSizeInKb Maximum allowed size in KB (null uses DEFAULT_MAX_SIZE_KB)
     * @return string|null Relative path to the uploaded file, or null if no file provided
     *
     * @throws ValidationException
     */
    public static function upload(
        ?UploadedFile $file,
        string $folder = 'uploads',
        ?string $oldFile = null,
        string $disk = 'public',
        ?int $maxSizeInKb = null
    ): ?string {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $allowedLimit = $maxSizeInKb ?? self::DEFAULT_MAX_SIZE_KB;
        $fileSizeKb = (int) round($file->getSize() / 1024);

        // Enforce maximum file size check centrally
        if ($fileSizeKb > $allowedLimit) {
            $readableLimit = $allowedLimit >= 1024 ? ($allowedLimit / 1024) . ' MB' : $allowedLimit . ' KB';
            throw ValidationException::withMessages([
                'file' => [__("حجم الملف يتجاوز الحد المسموح به (:limit).", ['limit' => $readableLimit])],
            ]);
        }

        // Delete old file if present
        if ($oldFile) {
            self::delete($oldFile, $disk);
        }

        // Generate clean unique filename
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $cleanOriginalName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = time() . '_' . Str::random(8) . ($cleanOriginalName ? '_' . substr($cleanOriginalName, 0, 25) : '') . '.' . $extension;

        // Store file
        return $file->storeAs($folder, $filename, $disk);
    }

    /**
     * Upload an image file with image-specific size limits.
     *
     * @param UploadedFile|null $file
     * @param string $folder
     * @param string|null $oldFile
     * @param string $disk
     * @param int|null $maxSizeInKb
     * @return string|null
     */
    public static function uploadImage(
        ?UploadedFile $file,
        string $folder = 'images',
        ?string $oldFile = null,
        string $disk = 'public',
        ?int $maxSizeInKb = null
    ): ?string {
        return self::upload(
            file: $file,
            folder: $folder,
            oldFile: $oldFile,
            disk: $disk,
            maxSizeInKb: $maxSizeInKb ?? self::DEFAULT_IMAGE_MAX_SIZE_KB
        );
    }

    /**
     * Upload multiple files.
     *
     * @param array<UploadedFile> $files
     * @param string $folder
     * @param string $disk
     * @param int|null $maxSizeInKb
     * @return array<string> List of stored paths
     */
    public static function uploadMultiple(
        array $files,
        string $folder = 'uploads',
        string $disk = 'public',
        ?int $maxSizeInKb = null
    ): array {
        $paths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $path = self::upload($file, $folder, null, $disk, $maxSizeInKb);
                if ($path) {
                    $paths[] = $path;
                }
            }
        }

        return $paths;
    }

    /**
     * Delete a file safely from disk.
     *
     * @param string|null $path
     * @param string $disk
     * @return bool
     */
    public static function delete(?string $path, string $disk = 'public'): bool
    {
        if (empty($path)) {
            return false;
        }

        // Avoid deleting default placeholders or remote URLs
        if (Str::startsWith($path, ['http://', 'https://', 'assets/'])) {
            return false;
        }

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Get the public URL for a file.
     *
     * @param string|null $path
     * @param string|null $default Fallback URL if file doesn't exist
     * @param string $disk
     * @return string|null
     */
    public static function url(?string $path, ?string $default = null, string $disk = 'public'): ?string
    {
        if (empty($path)) {
            return $default;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->url($path);
        }

        return $default;
    }
}
