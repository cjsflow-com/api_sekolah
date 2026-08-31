<?php

namespace App\Policies;

use App\Models\Semester;
use App\Models\User;

class SemesterPolicy
{
    public function viewAny(
        User $user
    ): bool {
        return $user->hasPermission(
            'semesters',
            'view'
        );
    }

    public function view(
        User $user,
        Semester $semester
    ): bool {
        return $user->hasPermission(
            'semesters',
            'view'
        );
    }

    public function create(
        User $user
    ): bool {
        return $user->hasPermission(
            'semesters',
            'create'
        );
    }

    public function update(
        User $user,
        Semester $semester
    ): bool {
        return $user->hasPermission(
            'semesters',
            'update'
        );
    }

    public function delete(
        User $user,
        Semester $semester
    ): bool {
        return $user->hasPermission(
            'semesters',
            'delete'
        );
    }
}
