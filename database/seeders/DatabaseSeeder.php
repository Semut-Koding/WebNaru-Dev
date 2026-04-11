<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            AttractionSeeder::class,
            VillaSeeder::class,
            VillaUnitSeeder::class,
            AddonSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
