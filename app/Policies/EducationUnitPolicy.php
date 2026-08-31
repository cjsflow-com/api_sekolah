<?php

namespace App\Policies;

use App\Models\EducationUnit;
use App\Models\User;

class EducationUnitPolicy
{
    public function viewAny(
        User $user
    ): bool {
        return $user->hasPermission(
            'education-units',
            'view'
        );
    }

    public function view(
        User $user,
        EducationUnit $educationUnit
    ): bool {
        return $user->hasPermission(
            'education-units',
            'view'
        );
    }

    public function create(
        User $user
    ): bool {
        return $user->hasPermission(
            'education-units',
            'create'
        );
    }

    public function update(
        User $user,
        EducationUnit $educationUnit
    ): bool {
        return $user->hasPermission(
            'education-units',
            'update'
        );
    }

    public function delete(
        User $user,
        EducationUnit $educationUnit
    ): bool {
        return $user->hasPermission(
            'education-units',
            'delete'
        );
    }
}
