<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            [
                'module' => 'permissions',
                'name' => 'Lihat Permission',
                'action' => 'view',
            ],
            [
                'module' => 'permissions',
                'name' => 'Tambah Permission',
                'action' => 'create',
            ],
            [
                'module' => 'permissions',
                'name' => 'Ubah Permission',
                'action' => 'update',
            ],
            [
                'module' => 'permissions',
                'name' => 'Hapus Permission',
                'action' => 'delete',
            ],
        ];
         DB::table('permissions')->insert($permissions);
    }
}