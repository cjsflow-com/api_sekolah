<?php

namespace App\Policies;

use App\Models\StudentClass;
use App\Models\User;

class StudentClassPolicy
{
    public function viewAny(
        User $user
    ): bool {
        return $user->hasPermission(
            'student-classes',
            'view'
        );
    }

    public function view(
        User $user,
        StudentClass $studentClass
    ): bool {
        return $user->hasPermission(
            'student-classes',
            'view'
        );
    }

    public function create(
        User $user
    ): bool {
        return $user->hasPermission(
            'student-classes',
            'create'
        );
    }

    public function update(
        User $user,
        StudentClass $studentClass
    ): bool {
        return $user->hasPermission(
            'student-classes',
            'update'
        );
    }

    public function delete(
        User $user,
        StudentClass $studentClass
    ): bool {
        return $user->hasPermission(
            'student-classes',
            'delete'
        );
    }
}