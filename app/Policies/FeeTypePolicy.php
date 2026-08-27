<?php

namespace App\Policies;

use App\Models\FeeType;
use App\Models\User;

class FeeTypePolicy
{
    public function viewAny(
        User $user
    ): bool {
        return $user->hasPermission(
            'fee-types',
            'view'
        );
    }

    public function view(
        User $user,
        FeeType $feeType
    ): bool {
        return $user->hasPermission(
            'fee-types',
            'view'
        );
    }

    public function create(
        User $user
    ): bool {
        return $user->hasPermission(
            'fee-types',
            'create'
        );
    }

    public function update(
        User $user,
        FeeType $feeType
    ): bool {
        return $user->hasPermission(
            'fee-types',
            'update'
        );
    }

    public function delete(
        User $user,
        FeeType $feeType
    ): bool {
        return $user->hasPermission(
            'fee-types',
            'delete'
        );
    }
}