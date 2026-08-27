<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Melihat daftar role.
     */
    public function viewAny(
        User $user
    ): bool {
        return $user->hasPermission(
            'roles',
            'view'
        );
    }

    /**
     * Melihat detail role.
     */
    public function view(
        User $user,
        Role $role
    ): bool {
        return $user->hasPermission(
            'roles',
            'view'
        );
    }

    /**
     * Membuat role.
     */
    public function create(
        User $user
    ): bool {
        return $user->hasPermission(
            'roles',
            'create'
        );
    }

    /**
     * Mengubah role.
     */
    public function update(
        User $user,
        Role $role
    ): bool {
        return $user->hasPermission(
            'roles',
            'update'
        );
    }

    /**
     * Menghapus role.
     */
    public function delete(
        User $user,
        Role $role
    ): bool {
        return $user->hasPermission(
            'roles',
            'delete'
        );
    }
}