<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\User;

class GradePolicy
{
    public function viewAny(
        User $user
    ): bool {
        return $user->hasPermission(
            'grades',
            'view'
        );
    }

    public function view(
        User $user,
        Grade $grade
    ): bool {
        return $user->hasPermission(
            'grades',
            'view'
        );
    }

    public function create(
        User $user
    ): bool {
        return $user->hasPermission(
            'grades',
            'create'
        );
    }

    public function update(
        User $user,
        Grade $grade
    ): bool {
        return $user->hasPermission(
            'grades',
            'update'
        );
    }

    public function delete(
        User $user,
        Grade $grade
    ): bool {
        return $user->hasPermission(
            'grades',
            'delete'
        );
    }
}