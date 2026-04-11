<?php

namespace Database\Seeders;

use App\Models\Villa;
use App\Models\VillaUnit;
use Illuminate\Database\Seeder;

class VillaUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // For Bima, Gatot Kaca, Arjuna -> 1 unit each
        // For Bambu, Sadewa -> 3 units each

        $villas = Villa::all();

        foreach ($villas as $villa) {
            if ($villa->name === 'Villa Bambu') {
                for ($i = 1; $i <= 3; $i++) {
                    VillaUnit::create([
                        'villa_id' => $villa->id,
                        'unit_name' => $villa->name . ' Unit ' . $i,
                        'status' => 'available',
                        'notes' => 'Unit automatically generated',
                    ]);
                }
            } else {
                VillaUnit::create([
                    'villa_id' => $villa->id,
                    'unit_name' => $villa->name . ' - Inti',
                    'status' => 'available',
                    'notes' => 'Unit otomatis',
                ]);
            }
        }
    }
}
