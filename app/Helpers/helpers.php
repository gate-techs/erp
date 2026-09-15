<?php

use App\Helpers\UploadHelper;
use Illuminate\Http\UploadedFile;

if (! function_exists('upload_file')) {
    /**
     * Upload any attachment/file centrally.
     *
     * @param UploadedFile|null $file
     * @param string $folder Destination directory (e.g., 'employees/documents', 'settings')
     * @param string|null $oldFile Path to old file to delete if replacing
     * @param string $disk Disk name (default: 'public')
     * @param int|null $maxSizeInKb Optional custom max size limit in KB (defaults to UploadHelper::DEFAULT_MAX_SIZE_KB)
     * @return string|null Relative path of uploaded file
     */
    function upload_file(
        ?UploadedFile $file,
        string $folder = 'uploads',
        ?string $oldFile = null,
        string $disk = 'public',
        ?int $maxSizeInKb = null
    ): ?string {
        return UploadHelper::upload($file, $folder, $oldFile, $disk, $maxSizeInKb);
    }
}

if (! function_exists('upload_image')) {
    /**
     * Upload an image file with image-specific size limits.
     *
     * @param UploadedFile|null $file
     * @param string $folder Destination directory (e.g., 'avatars', 'settings')
     * @param string|null $oldFile Path to old file to delete if replacing
     * @param string $disk Disk name (default: 'public')
     * @param int|null $maxSizeInKb Optional max size limit in KB (defaults to 2048 KB = 2MB)
     * @return string|null Relative path of uploaded image
     */
    function upload_image(
        ?UploadedFile $file,
        string $folder = 'images',
        ?string $oldFile = null,
        string $disk = 'public',
        ?int $maxSizeInKb = null
    ): ?string {
        return UploadHelper::uploadImage($file, $folder, $oldFile, $disk, $maxSizeInKb);
    }
}

if (! function_exists('upload_files')) {
    /**
     * Upload multiple files at once.
     *
     * @param array<UploadedFile> $files
     * @param string $folder Destination directory
     * @param string $disk Disk name (default: 'public')
     * @param int|null $maxSizeInKb Optional max size limit in KB
     * @return array<string> List of stored paths
     */
    function upload_files(
        array $files,
        string $folder = 'uploads',
        string $disk = 'public',
        ?int $maxSizeInKb = null
    ): array {
        return UploadHelper::uploadMultiple($files, $folder, $disk, $maxSizeInKb);
    }
}

if (! function_exists('delete_file')) {
    /**
     * Safely delete a file from storage.
     *
     * @param string|null $path Path to file
     * @param string $disk Disk name (default: 'public')
     * @return bool True if deleted, false otherwise
     */
    function delete_file(?string $path, string $disk = 'public'): bool
    {
        return UploadHelper::delete($path, $disk);
    }
}

if (! function_exists('file_url')) {
    /**
     * Get the publicly accessible URL of a stored file.
     *
     * @param string|null $path Path to file
     * @param string|null $default Default fallback URL if file doesn't exist
     * @param string $disk Disk name (default: 'public')
     * @return string|null Full URL or default
     */
    function file_url(?string $path, ?string $default = null, string $disk = 'public'): ?string
    {
        return UploadHelper::url($path, $default, $disk);
    }
}
