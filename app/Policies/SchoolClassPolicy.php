<?php

namespace App\Policies;

use App\Models\SchoolClass;
use App\Models\User;

class SchoolClassPolicy
{
    public function viewAny(
        User $user
    ): bool {
        return $user->hasPermission(
            'school-classes',
            'view'
        );
    }

    public function view(
        User $user,
        SchoolClass $schoolClass
    ): bool {
        return $user->hasPermission(
            'school-classes',
            'view'
        );
    }

    public function create(
        User $user
    ): bool {
        return $user->hasPermission(
            'school-classes',
            'create'
        );
    }

    public function update(
        User $user,
        SchoolClass $schoolClass
    ): bool {
        return $user->hasPermission(
            'school-classes',
            'update'
        );
    }

    public function delete(
        User $user,
        SchoolClass $schoolClass
    ): bool {
        return $user->hasPermission(
            'school-classes',
            'delete'
        );
    }
}
