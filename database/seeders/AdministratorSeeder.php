<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdministratorSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * Buat role Administrator jika belum ada.
         */
        $administratorRole = Role::firstOrCreate([
            'name' => 'administrator',
        ]);

        /**
         * Buat user Administrator.
         */
        User::updateOrCreate(
            [
                'email' => 'admin@sekolah.com',
            ],
            [
                'name' => 'Administrator',
                'password' => bcrypt('admin123'),
                'is_active' => true,
                'role_id' => $administratorRole->id,
                'email_verified_at' => now(),
            ]
        );
    }
}