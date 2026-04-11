<?php

namespace Database\Seeders;

use App\Models\Villa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VillaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $villas = [
            [
                'name' => 'Villa Bima',
                'bedroom_count' => 2,
                'bathroom_count' => 3,
                'capacity' => 30,
                'description' => 'Akomodasi berkapasitas sangat luas dengan ruang berkumpul yang lega, sempurna untuk acara gathering perusahaan, reuni akbar keluarga, maupun kegiatan outbound.',
                'base_price_weekday' => 1500000,
                'base_price_weekend' => 2000000,
                'amenities' => ['Aula Serbaguna', 'Dapur Ekstra Besar', 'Extra Bed', 'Sound System (Opsional)'],
                'benefits' => ['Gratis Akses Area Terbuka', 'Prioritas Penggunaan Lapangan'],
                'sort_order' => 3,
            ],
            [
                'name' => 'Villa Gatot Kaca',
                'bedroom_count' => 2,
                'bathroom_count' => 3,
                'capacity' => 25,
                'description' => 'Ruangan bergaya modern minimalis yang menyajikan kenyamanan paripurna bagi rombongan atau keluarga besar, dilengkapi dapur luas dan area bersantai komunal terbaik.',
                'base_price_weekday' => 1200000,
                'base_price_weekend' => 1500000,
                'amenities' => ['Ruang Tamu Luas', 'Dapur Lengkap', 'Set Meja Makan', 'Teras Berjemur'],
                'benefits' => ['Gratis Wi-Fi', 'Diskon Wahana 10%'],
                'sort_order' => 4,
            ],
            [
                'name' => 'Villa Arjuna',
                'bedroom_count' => 1,
                'bathroom_count' => 2,
                'capacity' => 7,
                'description' => 'Desain elegan dengan privasi maksimal, ideal untuk keluarga kecil atau trip singkat bersama pasangan dengan suasana pegunungan yang menenangkan.',
                'base_price_weekday' => 800000,
                'base_price_weekend' => 1000000,
                'amenities' => ['AC', 'Smart TV', 'Water Heater', 'Kulkas Mini', 'Teras Pribadi'],
                'benefits' => ['Gratis Sarapan', 'Akses Kolam Renang Bersama'],
                'sort_order' => 1,
            ],
            [
                'name' => 'Villa Sadewa',
                'bedroom_count' => 2,
                'bathroom_count' => 1,
                'capacity' => 10,
                'description' => 'Menyediakan beragam unit dinamis dengan variasi desain arsitektur unik serta tata ruang fungsional, menawarkan pengalaman menginap berbeda dalam setiap kunjungannya.',
                'base_price_weekday' => 1000000,
                'base_price_weekend' => 1200000,
                'amenities' => ['Dapur Standar', 'Ruang Keluarga', 'Perlengkapan Mandi Dasar', 'Smart TV'],
                'benefits' => ['Pemesanan Fleksibel', 'Bebas Pilih Tipe Unit (Tergantung Ketersediaan)'],
                'sort_order' => 5,
            ],
            [
                'name' => 'Villa Bambu',
                'bedroom_count' => 1,
                'bathroom_count' => 1,
                'capacity' => 3,
                'description' => 'Sensasi menginap bernuansa alam tradisional nan romantis, sangat cocok bagi pasangan untuk momen berbulan madu atau sekadar melepaskan penat dari hiruk pikuk kota.',
                'base_price_weekday' => 500000,
                'base_price_weekend' => 700000,
                'amenities' => ['Kipas Angin', 'TV Lokal', 'Kamar Mandi Dalam', 'Kelambu', 'Balkon Pemandangan Alam'],
                'benefits' => ['Minuman Selamat Datang', 'Suasana Tenang'],
                'sort_order' => 2,
            ],
        ];

        foreach ($villas as $villa) {
            Villa::create([
                'name' => $villa['name'],
                'slug' => Str::slug($villa['name']),
                'bedroom_count' => $villa['bedroom_count'],
                'bathroom_count' => $villa['bathroom_count'],
                'capacity' => $villa['capacity'],
                'description' => $villa['description'],
                'base_price_weekday' => $villa['base_price_weekday'],
                'base_price_weekend' => $villa['base_price_weekend'],
                'amenities' => collect($villa['amenities'])->values()->toArray(),
                'benefits' => collect($villa['benefits'])->values()->toArray(),
                'status' => 'available',
                'sort_order' => $villa['sort_order'],
            ]);
        }
    }
}
