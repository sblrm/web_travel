<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageSeederService
{
    /**
     * Download sample image from Unsplash for a destination
     */
    public static function downloadDestinationImage(string $query, int $width = 1920, int $height = 1080): ?string
    {
        try {
            // Use Lorem Picsum as fallback (no API key needed)
            // Format: https://picsum.photos/{width}/{height}?random={seed}
            $seed = rand(1, 1000);
            $url = "https://picsum.photos/{$width}/{$height}?random={$seed}";

            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                $filename = 'destinations/'.Str::random(40).'.jpg';
                Storage::disk('public')->put($filename, $response->body());

                return $filename;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to download image: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Download multiple images for a destination
     */
    public static function downloadMultipleImages(string $query, int $count = 3): array
    {
        $images = [];

        for ($i = 0; $i < $count; $i++) {
            $image = self::downloadDestinationImage($query);
            if ($image) {
                $images[] = $image;
            }

            // Small delay to avoid rate limiting
            usleep(500000); // 0.5 second
        }

        return $images;
    }
}
