<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'email' => 'superadmin@naruforest.com',
                'name' => 'Super Administrator',
                'role' => 'super_admin'
            ],
            [
                'email' => 'kasir@naruforest.com',
                'name' => 'Kasir Tiket Box',
                'role' => 'kasir_tiket_box'
            ],
            [
                'email' => 'resepsionis@naruforest.com',
                'name' => 'Resepsionis Villa',
                'role' => 'front_desk_villa'
            ],
            [
                'email' => 'operator@naruforest.com',
                'name' => 'Operator Wahana',
                'role' => 'operator_atraksi'
            ],
            [
                'email' => 'admin@naruforest.com',
                'name' => 'Admin Operasional',
                'role' => 'admin_operasional'
            ],
            [
                'email' => 'manajer@naruforest.com',
                'name' => 'Manajer Operasional',
                'role' => 'manajer_operasional'
            ],
            [
                'email' => 'dev@naruforest.com',
                'name' => 'Developer',
                'role' => 'super_admin'
            ]
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'phone' => '081234567890'
                ]
            );

            // Give them their primary role and the default panel_user role 
            // so they can access the panel.
            $roleName = $userData['role'];
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();

            if ($role) {
                $user->assignRole([$role, 'panel_user']);
            } else {
                // assigned default fallback
                $user->assignRole(['panel_user']);
            }
        }
    }
}
