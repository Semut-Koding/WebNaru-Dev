<?php

namespace Database\Seeders;

use App\Models\Attraction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AttractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attractions = [
            [
                'name' => 'Kolam Renang',
                'description' => 'Nikmati kesegaran berenang di kolam renang kami yang luas dengan air jernih dan lingkungan yang asri, dirancang khusus untuk kenyamanan bersantai keluarga Anda.',
                'status' => 'active',
                'is_free' => false,
                'base_price' => 15000,
                'sort_order' => 3,
            ],
            [
                'name' => 'Rainbow Slide',
                'description' => 'Wahana seluncur warna-warni berukuran raksasa yang ceria dan seru. Segera hadir untuk memberikan momen liburan ceria bagi seluruh anggota keluarga.',
                'status' => 'coming_soon',
                'is_free' => false,
                'base_price' => 25000,
                'sort_order' => 5,
            ],
            [
                'name' => 'Flying Fox',
                'description' => 'Rasakan sensasi meluncur dari ketinggian dengan wahana Flying Fox kami. Uji keberanian Anda dan nikmati hamparan pemandangan alam dari udara.',
                'status' => 'active',
                'is_free' => false,
                'base_price' => 20000,
                'sort_order' => 2,
            ],
            [
                'name' => 'ATV',
                'description' => 'Tantang adrenalin Anda dengan mengendarai ATV di lintasan off-road kami yang dirancang dengan rute menantang untuk pengalaman seru yang tak terlupakan.',
                'status' => 'coming_soon',
                'is_free' => false,
                'base_price' => 50000,
                'sort_order' => 1,
            ],
            [
                'name' => 'Playground',
                'description' => 'Area bermain interaktif yang aman dan menyenangkan, didesain khusus untuk melatih motorik serta kreativitas anak-anak di ruang terbuka hijau.',
                'status' => 'coming_soon',
                'is_free' => false,
                'base_price' => 0,
                'sort_order' => 4,
            ],
        ];

        foreach ($attractions as $attraction) {
            Attraction::create([
                'name' => $attraction['name'],
                'slug' => Str::slug($attraction['name']),
                'description' => $attraction['description'],
                'status' => $attraction['status'],
                'is_free' => $attraction['is_free'],
                'base_price' => $attraction['base_price'] ?? 0,
                'sort_order' => $attraction['sort_order']
            ]);
        }
    }
}
