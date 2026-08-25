<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Allowed image MIME types for processing.
     */
    protected const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    /**
     * Compress and sanitize an uploaded image, returning base64 data.
     * Used for uploads stored directly in the database (BYTEA/base64).
     *
     * @param  UploadedFile  $file       The uploaded file
     * @param  int           $maxWidth   Maximum width in pixels (maintains aspect ratio)
     * @param  int           $quality    WebP compression quality (0-100)
     * @return array{data: string, mime: string}  ['data' => base64_encoded_string, 'mime' => 'image/webp']
     *
     * @throws \InvalidArgumentException  If the file is not a valid image
     */
    public static function compressAndSanitize(UploadedFile $file, int $maxWidth = 1200, int $quality = 75): array
    {
        // Validate real MIME type using finfo (not trusting client-provided MIME)
        $realMime = self::detectRealMime($file);

        if (!in_array($realMime, self::ALLOWED_MIMES)) {
            throw new \InvalidArgumentException(
                'File bukan gambar yang valid. Tipe yang diizinkan: JPEG, PNG, GIF, WebP.'
            );
        }

        // Create GD image from the uploaded file (re-encoding strips EXIF and neutralizes exploits)
        $sourceImage = self::createImageFromFile($file->getRealPath(), $realMime);

        // Resize if necessary
        $sourceImage = self::resizeImage($sourceImage, $maxWidth);

        // Encode to WebP and get the binary data
        $webpData = self::encodeToWebp($sourceImage, $quality);

        // Free memory
        imagedestroy($sourceImage);

        return [
            'data' => base64_encode($webpData),
            'mime' => 'image/webp',
        ];
    }

    /**
     * Compress and sanitize an uploaded image, storing it to the filesystem.
     * Used for uploads stored on disk (e.g., karyawan photos).
     *
     * @param  UploadedFile  $file       The uploaded file
     * @param  string        $directory  Storage directory (e.g., 'karyawan-photos')
     * @param  string        $disk       Storage disk (e.g., 'public')
     * @param  int           $maxWidth   Maximum width in pixels (maintains aspect ratio)
     * @param  int           $quality    WebP compression quality (0-100)
     * @return array{path: string, mime: string}  ['path' => 'directory/filename.webp', 'mime' => 'image/webp']
     *
     * @throws \InvalidArgumentException  If the file is not a valid image
     */
    public static function compressAndStore(
        UploadedFile $file,
        string $directory,
        string $disk = 'public',
        int $maxWidth = 1200,
        int $quality = 75
    ): array {
        // Validate real MIME type using finfo
        $realMime = self::detectRealMime($file);

        if (!in_array($realMime, self::ALLOWED_MIMES)) {
            throw new \InvalidArgumentException(
                'File bukan gambar yang valid. Tipe yang diizinkan: JPEG, PNG, GIF, WebP.'
            );
        }

        // Create GD image from the uploaded file
        $sourceImage = self::createImageFromFile($file->getRealPath(), $realMime);

        // Resize if necessary
        $sourceImage = self::resizeImage($sourceImage, $maxWidth);

        // Encode to WebP
        $webpData = self::encodeToWebp($sourceImage, $quality);

        // Free memory
        imagedestroy($sourceImage);

        // Generate a unique filename and store
        $filename = $directory . '/' . Str::uuid() . '.webp';
        Storage::disk($disk)->put($filename, $webpData);

        return [
            'path' => $filename,
            'mime' => 'image/webp',
        ];
    }

    /**
     * Detect the real MIME type of a file using PHP's finfo extension.
     * This does not trust the client-provided Content-Type header.
     */
    protected static function detectRealMime(UploadedFile $file): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($file->getRealPath());
    }

    /**
     * Create a GD image resource from a file path based on its MIME type.
     * Re-encoding through GD automatically strips all EXIF metadata
     * and neutralizes any embedded malicious content.
     *
     * @throws \InvalidArgumentException  If the image cannot be created
     */
    protected static function createImageFromFile(string $path, string $mime): \GdImage
    {
        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png'  => @imagecreatefrompng($path),
            'image/gif'  => @imagecreatefromgif($path),
            'image/webp' => @imagecreatefromwebp($path),
            default      => false,
        };

        if ($image === false) {
            throw new \InvalidArgumentException(
                'Gagal memproses file gambar. File mungkin rusak atau bukan gambar yang valid.'
            );
        }

        // Preserve alpha transparency for PNG/WebP/GIF
        if (in_array($mime, ['image/png', 'image/webp', 'image/gif'])) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }

        return $image;
    }

    /**
     * Resize an image if its width exceeds the maximum, maintaining aspect ratio.
     */
    protected static function resizeImage(\GdImage $image, int $maxWidth): \GdImage
    {
        $origWidth = imagesx($image);
        $origHeight = imagesy($image);

        if ($origWidth <= $maxWidth) {
            return $image;
        }

        $ratio = $maxWidth / $origWidth;
        $newHeight = (int) round($origHeight * $ratio);

        $resized = imagecreatetruecolor($maxWidth, $newHeight);

        // Preserve transparency
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefill($resized, 0, 0, $transparent);

        imagecopyresampled(
            $resized, $image,
            0, 0, 0, 0,
            $maxWidth, $newHeight,
            $origWidth, $origHeight
        );

        // Free the original image
        imagedestroy($image);

        return $resized;
    }

    /**
     * Encode a GD image to WebP format and return the raw binary data.
     */
    protected static function encodeToWebp(\GdImage $image, int $quality): string
    {
        ob_start();
        imagewebp($image, null, $quality);
        $data = ob_get_clean();

        if ($data === false || strlen($data) === 0) {
            throw new \RuntimeException('Gagal mengkompresi gambar ke format WebP.');
        }

        return $data;
    }
}
