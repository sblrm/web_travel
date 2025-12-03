<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            'North Sumatra',
            'Jambi',
            'West Sumatra',
            'Aceh',
            'Central Java',
            'DI Yogyakarta',
            'West Java',
            'South Kalimantan',
            'West Kalimantan',
            'Central Kalimantan',
            'South Sulawesi',
            'Southeast Sulawesi',
            'Gorontalo',
            'East Nusa Tenggara',
            'Maluku',
            'Papua',
            'West Nusa Tenggara',
            'Bali',
            'DKI Jakarta',
            'Riau',
            'East Java',
            'East Kalimantan',
            'North Maluku',
            'North Sulawesi',
            'Bangka Belitung',
            'South Sumatra',
            'Riau Islands',
        ];

        foreach ($provinces as $province) {
            Province::firstOrCreate(
                ['name' => $province],
                ['slug' => Str::slug($province)]
            );
        }
    }
}
