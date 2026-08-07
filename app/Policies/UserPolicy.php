<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $authenticatedUser): bool
    {
        return $this->allowed(
            $authenticatedUser,
            'view'
        );
    }

    public function view(
        User $authenticatedUser,
        User $targetUser
    ): bool {
        return $authenticatedUser->is($targetUser)
            || $this->allowed($authenticatedUser, 'view');
    }

    public function create(User $authenticatedUser): bool
    {
        return $this->allowed(
            $authenticatedUser,
            'create'
        );
    }

    public function update(
        User $authenticatedUser,
        User $targetUser
    ): bool {
        return $authenticatedUser->is($targetUser)
            || $this->allowed($authenticatedUser, 'update');
    }

    public function delete(
        User $authenticatedUser,
        User $targetUser
    ): bool {
        /*
         * User tidak boleh menghapus akunnya sendiri.
         */
        if ($authenticatedUser->is($targetUser)) {
            return false;
        }

        return $this->allowed(
            $authenticatedUser,
            'delete'
        );
    }

    private function allowed(
        User $user,
        string $action
    ): bool {
        /*
         * Super admin mendapat semua akses.
         */
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->hasPermission(
            module: 'users',
            action: $action
        );
    }
}