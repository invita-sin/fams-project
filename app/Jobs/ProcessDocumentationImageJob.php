<?php

namespace App\Jobs;

use App\Models\MediaFile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ProcessDocumentationImageJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly MediaFile $mediaFile,
    ) {}

    public function handle(): void
    {
        $disk = Storage::disk($this->mediaFile->storage_disk);

        if (!$disk->exists($this->mediaFile->storage_path)) {
            return;
        }

        $fullPath = $disk->path($this->mediaFile->storage_path);
        $info = getimagesize($fullPath);

        if (!$info) {
            return;
        }

        [$width, $height] = $info;
        $mime = $info['mime'];

        if ($width > 1920) {
            $newWidth = 1920;
            $newHeight = (int) ($height * ($newWidth / $width));

            $this->resizeImage($fullPath, $mime, $newWidth, $newHeight);
        }

        $thumbnailPath = dirname($this->mediaFile->storage_path) . '/thumb_' . basename($this->mediaFile->storage_path);
        $thumbFullPath = $disk->path($thumbnailPath);

        $thumbWidth = 320;
        $thumbHeight = 240;

        if ($width > $height) {
            $thumbWidth = 320;
            $thumbHeight = (int) ($height * (320 / $width));
        } else {
            $thumbHeight = 240;
            $thumbWidth = (int) ($width * (240 / $height));
        }

        $this->resizeImage($fullPath, $mime, $thumbWidth, $thumbHeight, $thumbFullPath);
    }

    private function resizeImage(string $sourcePath, string $mime, int $newWidth, int $newHeight, ?string $destPath = null): void
    {
        $destPath = $destPath ?? $sourcePath;

        $srcImage = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            'image/gif' => imagecreatefromgif($sourcePath),
            default => null,
        };

        if (!$srcImage) {
            return;
        }

        $dstImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, imagesx($srcImage), imagesy($srcImage));

        match ($mime) {
            'image/jpeg' => imagejpeg($dstImage, $destPath, 85),
            'image/png' => imagepng($dstImage, $destPath, 6),
            'image/webp' => imagewebp($dstImage, $destPath, 85),
            'image/gif' => imagegif($dstImage, $destPath),
            default => null,
        };

        imagedestroy($srcImage);
        imagedestroy($dstImage);
    }
}
