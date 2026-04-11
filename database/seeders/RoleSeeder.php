<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'super_admin',
            'admin_operasional',
            'kasir_tiket_box',
            'front_desk_villa',
            'operator_atraksi',
            'petugas_mobile',
            'housekeeping',
            'finance',
            'guest_pengunjung',
            'guest_villa'
        ];

        // Ensure "panel_user" exists as it is default panel_user_role in Shield config if applicable
        Role::firstOrCreate(['name' => 'panel_user', 'guard_name' => 'web']);

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // Jalankan Shield Generate secara otomatis
        \Illuminate\Support\Facades\Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin',
            '--option' => 'policies_and_permissions',
            '--ignore-existing-policies' => true
        ]);

        // Mapping Permission untuk admin_operasional (Attraction & Villa)
        $adminOperasional = Role::where('name', 'admin_operasional')->first();
        if ($adminOperasional) {
            $adminOperasional->syncPermissions([
                'ViewAny:Attraction', 'View:Attraction', 'Create:Attraction', 'Update:Attraction', 'Delete:Attraction',
                'ViewAny:Villa', 'View:Villa', 'Create:Villa', 'Update:Villa', 'Delete:Villa',
            ]);
        }

        // Mapping Permission untuk kasir_tiket_box (VisitorCounter)
        $kasirTiketBox = Role::where('name', 'kasir_tiket_box')->first();
        if ($kasirTiketBox) {
            $kasirTiketBox->syncPermissions([
                'ViewAny:VisitorCounter', 'View:VisitorCounter', 'Create:VisitorCounter', 'Update:VisitorCounter', 'Delete:VisitorCounter',
                'View:VisitorAgeChart', 'View:VisitorHourlyChart', 'View:VisitorStatsOverview', 
            ]);
        }

        // Mapping Permission untuk front_desk_villa (Reservation)
        $frontDeskVilla = Role::where('name', 'front_desk_villa')->first();
        if ($frontDeskVilla) {
            $frontDeskVilla->syncPermissions([
                'ViewAny:Reservation', 'View:Reservation', 'Create:Reservation', 'Update:Reservation', 'Delete:Reservation',
            ]);
        }

        // Mapping Permission untuk operator_atraksi (AttractionCounter)
        $operatorAtraksi = Role::where('name', 'operator_atraksi')->first();
        if ($operatorAtraksi) {
            $operatorAtraksi->syncPermissions([
                'ViewAny:AttractionCounter', 'View:AttractionCounter', 'Create:AttractionCounter', 'Update:AttractionCounter', 'Delete:AttractionCounter',
            ]);
        }

        // Mapping Permission untuk housekeeping (VillaUnit — update status unit)
        $housekeeping = Role::where('name', 'housekeeping')->first();
        if ($housekeeping) {
            $housekeeping->syncPermissions([
                'ViewAny:VillaUnit', 'View:VillaUnit', 'Update:VillaUnit',
            ]);
        }
    }
}
