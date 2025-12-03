<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Warisan Kerajaan & Kolonial',
                'description' => 'Peninggalan kerajaan dan masa kolonial yang bersejarah',
            ],
            [
                'name' => 'Situs Sejarah & Arkeologi',
                'description' => 'Tempat bersejarah dan situs arkeologi penting',
            ],
            [
                'name' => 'Desa Adat & Kehidupan Tradisional',
                'description' => 'Perkampungan adat yang mempertahankan budaya tradisional',
            ],
            [
                'name' => 'Seni, Kerajinan & Pasar',
                'description' => 'Sentra seni, kerajinan tangan, dan pasar tradisional',
            ],
            [
                'name' => 'Museum & Monumen',
                'description' => 'Museum dan monumen bersejarah',
            ],
            [
                'name' => 'Festival & Taman Budaya',
                'description' => 'Lokasi festival budaya dan taman budaya',
            ],
            [
                'name' => 'Situs & Arsitektur Religi',
                'description' => 'Bangunan dan kompleks keagamaan bersejarah',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                [
                    'slug' => Str::slug($category['name']),
                    'description' => $category['description'],
                ]
            );
        }
    }
}
