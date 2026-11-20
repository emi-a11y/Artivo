<?php

namespace App\Support;

use App\Models\Artwork;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Keeps the full-quality artwork private and shows only a small preview in the shop.
 *
 *   original_path  -> private disk ("local", storage/app/artworks/originals), served only by the download route
 *   image_path     -> public disk (storage/app/public/previews), a small JPEG used on every page
 */
class ArtworkImages
{
    public const PREVIEW_MAX = 500;   // longest side in pixels
    public const PREVIEW_QUALITY = 65;

    /** Stores an upload and returns the two paths to save on the artwork. */
    public static function store(UploadedFile $file): array
    {
        // Preferred: the watermarked preview the admin's browser made (public/js/preview-maker.js).
        $preview = self::clientPreview(request()->file('preview'));

        if (! $preview) {
            // Fallback (JavaScript off or blocked): the server makes a plain preview, if its GD can read the file.
            try {
                $preview = self::previewData($file->getRealPath());
            } catch (\Throwable $e) {
                throw ValidationException::withMessages(['image' => 'The preview could not be created (' . $e->getMessage() . '). Reload the page and try again.'])
                    ->errorBag('artwork');
            }
        }

        $originalPath = $file->store('artworks/originals', 'local');
        $previewPath = 'previews/' . Str::random(40) . '.' . $preview['ext'];
        Storage::disk('public')->put($previewPath, $preview['bytes']);

        return ['original_path' => $originalPath, 'image_path' => $previewPath];
    }

    /** Accepts the browser-made preview only if it really is a small JPEG. */
    private static function clientPreview($file): ?array
    {
        if (! $file instanceof UploadedFile || ! $file->isValid() || $file->getSize() > 1536 * 1024) {
            return null;
        }
        $info = @getimagesize($file->getRealPath());
        if (! $info || $info[2] !== IMAGETYPE_JPEG || max($info[0], $info[1]) > self::PREVIEW_MAX + 200) {
            return null;
        }

        return ['bytes' => file_get_contents($file->getRealPath()), 'ext' => 'jpg'];
    }

    /** Deletes both files of an artwork (used on replace and delete). */
    public static function delete(Artwork $artwork): void
    {
        if ($artwork->original_path) {
            Storage::disk('local')->delete($artwork->original_path);
        }
        if ($artwork->image_path) {
            Storage::disk('public')->delete($artwork->image_path);
        }
    }

    /** Makes a small preview of the image at $absolutePath: ['bytes' => ..., 'ext' => 'jpg'|'png']. No framework needed. */
    public static function previewData(string $absolutePath): array
    {
        if (! extension_loaded('gd')) {
            throw new \RuntimeException('the PHP "gd" extension is not enabled');
        }
        $info = @getimagesize($absolutePath);
        if (! $info) {
            throw new \RuntimeException('not a readable image');
        }
        [$w, $h, $type] = $info;
        @ini_set('memory_limit', '512M');

        $reader = [IMAGETYPE_JPEG => 'imagecreatefromjpeg', IMAGETYPE_PNG => 'imagecreatefrompng',
                   IMAGETYPE_GIF => 'imagecreatefromgif', IMAGETYPE_WEBP => 'imagecreatefromwebp'][$type] ?? null;
        if (! $reader || ! function_exists($reader)) {
            throw new \RuntimeException('this PHP build cannot read this image type');
        }
        $src = @$reader($absolutePath);
        if (! $src) {
            throw new \RuntimeException('this image could not be read');
        }

        $scale = min(1, self::PREVIEW_MAX / max($w, $h));   // never enlarge
        $nw = max(1, (int) round($w * $scale));
        $nh = max(1, (int) round($h * $scale));

        $dst = imagecreatetruecolor($nw, $nh);
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255)); // transparent PNG/GIF/WEBP -> white
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

        $asJpeg = function_exists('imagejpeg');
        ob_start();
        $asJpeg ? imagejpeg($dst, null, self::PREVIEW_QUALITY) : imagepng($dst, null, 6);
        $bytes = ob_get_clean();
        imagedestroy($src);
        imagedestroy($dst);

        return ['bytes' => $bytes, 'ext' => $asJpeg ? 'jpg' : 'png'];
    }
}
