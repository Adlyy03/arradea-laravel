<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageOptimizationService
{
    /**
     * Optimize and save uploaded image
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param int $maxWidth
     * @param int $quality
     * @return string Path to saved image
     */
    public static function optimizeAndSave($file, string $directory = 'products', int $maxWidth = 800, int $quality = 80): string
    {
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $directory . '/' . $filename;

        // Check if Intervention Image is available
        if (class_exists('Intervention\Image\Facades\Image')) {
            // Optimize with Intervention Image
            $image = Image::make($file);
            
            // Resize if width exceeds max
            if ($image->width() > $maxWidth) {
                $image->resize($maxWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            // Encode with quality
            $encodedImage = $image->encode($file->getClientOriginalExtension(), $quality);
            
            // Save to storage
            Storage::disk('public')->put($path, $encodedImage);
        } else {
            // Fallback: save without optimization
            $file->storeAs($directory, $filename, 'public');
        }

        return $path;
    }

    /**
     * Create thumbnail from image
     * 
     * @param string $imagePath
     * @param int $width
     * @param int $height
     * @return string|null Path to thumbnail
     */
    public static function createThumbnail(string $imagePath, int $width = 200, int $height = 200): ?string
    {
        if (!class_exists('Intervention\Image\Facades\Image')) {
            return null;
        }

        try {
            $fullPath = Storage::disk('public')->path($imagePath);
            
            if (!file_exists($fullPath)) {
                return null;
            }

            $image = Image::make($fullPath);
            $image->fit($width, $height);
            
            $thumbnailPath = str_replace('.', '_thumb.', $imagePath);
            $image->save(Storage::disk('public')->path($thumbnailPath), 75);
            
            return $thumbnailPath;
        } catch (\Exception $e) {
            \Log::error('Thumbnail creation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete image and its thumbnail
     * 
     * @param string $imagePath
     * @return bool
     */
    public static function deleteImage(string $imagePath): bool
    {
        try {
            // Delete main image
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            
            // Delete thumbnail if exists
            $thumbnailPath = str_replace('.', '_thumb.', $imagePath);
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Image deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get optimized image URL with lazy loading support
     * 
     * @param string|null $imagePath
     * @param string $size 'thumb' or 'full'
     * @return string
     */
    public static function getImageUrl(?string $imagePath, string $size = 'full'): string
    {
        if (!$imagePath) {
            return 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=500&h=500';
        }

        if ($size === 'thumb') {
            $thumbnailPath = str_replace('.', '_thumb.', $imagePath);
            if (Storage::disk('public')->exists($thumbnailPath)) {
                return Storage::url($thumbnailPath);
            }
        }

        return Storage::url($imagePath);
    }
}
