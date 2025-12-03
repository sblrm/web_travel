<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DestinationSeeder extends Seeder
{
    /**
     * Parse coordinate from CSV format to decimal
     * CSV uses dots inconsistently:
     * Latitude: -76.079 means -7.6079, 3.5753 means 3.5753
     * Longitude: 1.102.038 means 110.2038, 986.839 means 98.6839
     */
    private function parseCoordinate(string $coord): float
    {
        $coord = trim($coord);
        $isNegative = str_starts_with($coord, '-');
        $coord = ltrim($coord, '-');

        $dotCount = substr_count($coord, '.');

        if ($dotCount === 0) {
            $result = (float) $coord;
        } elseif ($dotCount === 1) {
            // Single dot: could be decimal OR thousand separator
            $parts = explode('.', $coord);
            $beforeDot = $parts[0];
            $afterDot = $parts[1];

            // If first digit + remaining = valid coordinate range
            // Latitude: -90 to 90, Longitude: -180 to 180
            // Pattern: if 2-3 digits before dot, it's likely thousand separator
            if (strlen($beforeDot) >= 2) {
                // Insert decimal after first 1-2 digits
                // 76.079 -> 7.6079, 986.839 -> 98.6839
                if (strlen($beforeDot) == 2) {
                    $result = (float) ($beforeDot[0].'.'.$beforeDot[1].$afterDot);
                } else {
                    $result = (float) ($beforeDot[0].$beforeDot[1].'.'.$beforeDot[2].$afterDot);
                }
            } else {
                // Normal decimal
                $result = (float) $coord;
            }
        } else {
            // Multiple dots: 1.102.038 -> 110.2038
            $cleaned = str_replace('.', '', $coord);
            $result = (float) (substr($cleaned, 0, strlen($cleaned) - 4).'.'.substr($cleaned, -4));
        }

        return $isNegative ? -$result : $result;
    }

    public function run(): void
    {
        $csvFile = base_path('dataset wisata.csv');

        if (! File::exists($csvFile)) {
            $this->command->error('File dataset wisata.csv tidak ditemukan!');

            return;
        }

        $file = fopen($csvFile, 'r');
        $header = fgetcsv($file); // Skip header row

        $ticketPrices = [25000, 30000, 35000, 40000, 45000, 50000];

        while (($data = fgetcsv($file)) !== false) {
            // Skip jika data tidak lengkap
            if (count($data) < 10) {
                continue;
            }

            $provinceName = trim($data[3]);
            $categoryName = trim($data[4]);

            $province = Province::where('name', $provinceName)->first();
            $category = Category::where('name', $categoryName)->first();

            if (! $province || ! $category) {
                continue;
            }

            $destinationName = trim($data[1]);

            // Parse coordinates - remove dots used as thousand separator
            $latStr = str_replace([',', '.'], ['', '.'], $data[5]);
            $lngStr = str_replace([',', '.'], ['', '.'], $data[6]);

            // Convert to proper decimal format
            // Format in CSV: 3.5753 or 986.839 (should be 98.6839)
            $latitude = $this->parseCoordinate($latStr);
            $longitude = $this->parseCoordinate($lngStr);

            Destination::create([
                'name' => $destinationName,
                'slug' => Str::slug($destinationName),
                'city' => trim($data[2]),
                'province_id' => $province->id,
                'category_id' => $category->id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'opening_hours' => trim($data[7]),
                'closing_hours' => trim($data[8]),
                'est_visit_duration' => (int) $data[9],
                'ticket_price' => $ticketPrices[array_rand($ticketPrices)],
                'rating' => rand(40, 50) / 10, // 4.0 - 5.0
                'description' => "Destinasi budaya {$destinationName} merupakan salah satu warisan budaya Indonesia yang terletak di {$data[2]}, {$provinceName}. Tempat ini menawarkan pengalaman unik dalam kategori {$categoryName}.",
                'is_active' => true,
            ]);
        }

        fclose($file);

        $this->command->info('Berhasil mengimport '.Destination::count().' destinasi!');
    }
}
