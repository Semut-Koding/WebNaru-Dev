<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Landing Page
            ['key' => 'hero_title', 'value' => 'Jelajahi Petualangan Alam', 'group' => 'landing_page', 'description' => 'Judul utama H1 di beranda'],
            ['key' => 'hero_subtitle', 'value' => 'Temukan pengalaman bermalam dan wahan menarik.', 'group' => 'landing_page', 'description' => 'Teks sub-judul H2 di beranda'],
            ['key' => 'hero_image_url', 'value' => null, 'group' => 'landing_page', 'description' => 'URL gambar background hero'],

            // SEO
            ['key' => 'seo_title', 'value' => 'Sims Resort & Wahana', 'group' => 'seo', 'description' => 'Title tag global'],
            ['key' => 'seo_description', 'value' => 'Resor ramah keluarga dengan berbagai pilihan wahana.', 'group' => 'seo', 'description' => 'Meta description global'],
            ['key' => 'seo_keywords', 'value' => 'resort, wahana alam, glamping, villa', 'group' => 'seo', 'description' => 'Meta keywords global'],

            // Payment
            ['key' => 'pg_fee_percent', 'value' => '2.5', 'group' => 'payment', 'description' => 'Persentase biaya layanan Payment Gateway (misal Midtrans)'],

            // Jam Operasional
            ['key' => 'operational_hour_weekday_open', 'value' => '08:00', 'group' => 'operational', 'description' => 'Jam Buka Weekday'],
            ['key' => 'operational_hour_weekday_close', 'value' => '17:00', 'group' => 'operational', 'description' => 'Jam Tutup Weekday'],
            ['key' => 'operational_hour_weekend_open', 'value' => '07:00', 'group' => 'operational', 'description' => 'Jam Buka Weekend'],
            ['key' => 'operational_hour_weekend_close', 'value' => '18:00', 'group' => 'operational', 'description' => 'Jam Tutup Weekend'],

            // Profil & Kontak
            ['key' => 'lokasi', 'value' => 'Kp Sims, Jln. Pesanggaran RT 014/004, Desa Karyamekar, Kec. Cariu, Bogor 16840', 'group' => 'contact', 'description' => 'Alamat lengkap resor'],
            ['key' => 'google_map_url', 'value' => 'https://maps.app.goo.gl/yFHehgAcGvCsiKWG9', 'group' => 'contact', 'description' => 'Tautan langsung ke Google Maps'],
            ['key' => 'kontak_wa', 'value' => '0813-8881-9088', 'group' => 'contact', 'description' => 'Nomor WhatsApp resmi'],
            ['key' => 'instagram', 'value' => 'https://instagram.com/sims', 'group' => 'contact', 'description' => 'Link Profil Instagram'],

            // General Info
            ['key' => 'about_content_long', 'value' => 'Sims adalah tempat wisata modern dengan konsep alam, rekreasi bermain, dan villa eksklusif yang menawarkan suasana pegunungan sejuk serta pemandangan asri. Berdiri dengan visi untuk menjadi destinasi liburan keluarga terpadu terdepan, tempat ini sangat ramah untuk kegiatan rekreasi, liburan sekolah, gathering keluarga besar, maupun sesi staycation romantis bagi pasangan di tengah keasrian alam.', 'group' => 'general', 'description' => 'Konten Deksripsi Profil Sims untuk halaman Tentang Kami'],
            ['key' => 'tiket_masuk_umum', 'value' => json_encode(['harga' => 35000, 'keterangan' => 'Sudah termasuk akses kolam renang']), 'group' => 'general', 'description' => 'Informasi Tiket Masuk Umum'],
            [
                'key' => 'fasilitas_umum',
                'value' => json_encode([
                    ['fasilitas' => 'Area wahana permainan', 'keterangan' => 'Tersedia beberapa wahana aktif'],
                    ['fasilitas' => 'Villa view pegunungan', 'keterangan' => 'Berbagai tipe dan kapasitas'],
                    ['fasilitas' => 'Area bersantai & spot foto', 'keterangan' => 'Latar alam yang estetik'],
                    ['fasilitas' => 'Area makan/kuliner', 'keterangan' => 'Tersedia di kawasan'],
                    ['fasilitas' => 'Area parkir', 'keterangan' => 'Tersedia'],
                    ['fasilitas' => 'Area kegiatan', 'keterangan' => 'Cocok untuk gathering & event keluarga/komunitas']
                ]),
                'group' => 'general',
                'description' => 'Daftar fasilitas resor'
            ],
            [
                'key' => 'faq',
                'value' => json_encode([
                    ['pertanyaan' => 'Apa itu Sims?', 'jawaban' => 'Sims adalah tempat wisata dengan konsep alam, rekreasi, dan villa yang menawarkan suasana pegunungan yang sejuk dan pemandangan alami. Tempat ini cocok untuk liburan keluarga, healing, gathering, maupun menginap di tengah alam.'],
                    ['pertanyaan' => 'Apa saja fasilitas yang tersedia?', 'jawaban' => 'Fasilitas yang tersedia antara lain: area wahana permainan, villa dengan pemandangan pegunungan, area bersantai dan spot foto alam, area makan/kuliner, area parkir, dan area kegiatan keluarga atau komunitas. Fasilitas terus berkembang.'],
                    ['pertanyaan' => 'Apakah Sims bisa untuk menginap?', 'jawaban' => 'Ya. Sims menyediakan villa dengan view pegunungan bagi pengunjung yang ingin menikmati suasana alam lebih lama — cocok untuk liburan keluarga, pasangan, gathering kecil, dan staycation.'],
                    ['pertanyaan' => 'Apakah cocok untuk konten media sosial?', 'jawaban' => 'Tentu. Banyak pengunjung datang untuk foto dengan latar pegunungan, konten Instagram/TikTok, foto keluarga atau prewedding, dan dokumentasi liburan. Panorama alam membuat setiap foto terlihat lebih natural dan estetik.'],
                    ['pertanyaan' => 'Apakah wahana cocok untuk anak-anak?', 'jawaban' => 'Ya. Banyak wahana dan area rekreasi yang ramah keluarga — anak-anak bisa bermain di ruang terbuka, menikmati udara segar, dan beraktivitas bersama keluarga.'],
                    ['pertanyaan' => 'Apakah wahana aman?', 'jawaban' => 'Semua area rekreasi dirancang agar pengunjung dapat menikmati aktivitas dengan nyaman. Pengunjung tetap disarankan untuk mengikuti aturan area wisata, mengawasi anak-anak, dan menjaga keselamatan.'],
                    ['pertanyaan' => 'Apakah akan ada wahana baru?', 'jawaban' => 'Ya. Tim terus mempertimbangkan berbagai masukan pengunjung untuk menghadirkan wahana baru yang menarik dan instagramable. Pengembangan dilakukan bertahap agar tetap menjaga konsep alam.']
                ]),
                'group' => 'general',
                'description' => 'Frequently Asked Questions'
            ]
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
