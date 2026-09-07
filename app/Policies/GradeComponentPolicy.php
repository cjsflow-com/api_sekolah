<?php

namespace App\Policies;

use App\Models\GradeComponent;
use App\Models\User;

class GradeComponentPolicy
{
    public function viewAny(
        User $user
    ): bool {
        return $user->hasPermission(
            'grade-components',
            'view'
        );
    }

    public function view(
        User $user,
        GradeComponent $gradeComponent
    ): bool {
        return $user->hasPermission(
            'grade-components',
            'view'
        );
    }

    public function create(
        User $user
    ): bool {
        return $user->hasPermission(
            'grade-components',
            'create'
        );
    }

    public function update(
        User $user,
        GradeComponent $gradeComponent
    ): bool {
        return $user->hasPermission(
            'grade-components',
            'update'
        );
    }

    public function delete(
        User $user,
        GradeComponent $gradeComponent
    ): bool {
        return $user->hasPermission(
            'grade-components',
            'delete'
        );
    }
}