<?php

namespace Database\Seeders;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            [
                'name' => 'Extra Bed',
                'type' => 'item',
                'pricing_unit' => 'per_night',
                'price' => 100000,
                'description' => 'Kasur tambahan untuk tamu. Harga per unit per malam.',
                'is_active' => true,
            ],
            [
                'name' => 'Paket Sarapan',
                'type' => 'food',
                'pricing_unit' => 'per_person',
                'price' => 50000,
                'description' => 'Menu sarapan lengkap (nasi, lauk, minuman). Harga per orang per kali makan.',
                'is_active' => true,
            ],
            [
                'name' => 'Paket BBQ',
                'type' => 'food',
                'pricing_unit' => 'flat',
                'price' => 350000,
                'description' => 'Paket BBQ untuk 4-6 orang. Termasuk daging, sayur, bumbu, dan peralatan.',
                'is_active' => true,
            ],
            [
                'name' => 'Sewa Sepeda',
                'type' => 'activity',
                'pricing_unit' => 'per_night',
                'price' => 75000,
                'description' => 'Sewa sepeda gunung untuk eksplorasi area. Harga per unit per hari.',
                'is_active' => true,
            ],
            [
                'name' => 'Paket Camping',
                'type' => 'activity',
                'pricing_unit' => 'flat',
                'price' => 200000,
                'description' => 'Peralatan camping lengkap (tenda, sleeping bag, matras). Sekali bayar.',
                'is_active' => true,
            ],
            [
                'name' => 'Handuk Tambahan',
                'type' => 'item',
                'pricing_unit' => 'flat',
                'price' => 15000,
                'description' => 'Handuk bersih tambahan per set.',
                'is_active' => true,
            ],
            [
                'name' => 'Paket Makan Malam',
                'type' => 'food',
                'pricing_unit' => 'per_person',
                'price' => 75000,
                'description' => 'Menu makan malam spesial. Harga per orang.',
                'is_active' => true,
            ],
            [
                'name' => 'Bonfire Kit',
                'type' => 'activity',
                'pricing_unit' => 'flat',
                'price' => 150000,
                'description' => 'Paket api unggun termasuk kayu bakar dan marshmallow.',
                'is_active' => true,
            ],
        ];

        foreach ($addons as $addon) {
            Addon::updateOrCreate(
                ['name' => $addon['name']],
                $addon
            );
        }
    }
}
