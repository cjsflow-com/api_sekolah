<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $administrator =
            Role::where(
                'name',
                'administrator'
            )->firstOrFail();

        $permissionIds =
            Permission::query()
                ->pluck('id');

        $administrator
            ->permissions()
            ->sync(
                $permissionIds
            );
    }
}