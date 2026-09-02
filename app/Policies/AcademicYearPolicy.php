<?php

namespace App\Policies;

use App\Models\AcademicYear;
use App\Models\User;

class AcademicYearPolicy
{
    public function viewAny(
        User $user
    ): bool {
        return $user->hasPermission(
            'academic-years',
            'view'
        );
    }

    public function view(
        User $user,
        AcademicYear $academicYear
    ): bool {
        return $user->hasPermission(
            'academic-years',
            'view'
        );
    }

    public function create(
        User $user
    ): bool {
        return $user->hasPermission(
            'academic-years',
            'create'
        );
    }

    public function update(
        User $user,
        AcademicYear $academicYear
    ): bool {
        return $user->hasPermission(
            'academic-years',
            'update'
        );
    }

    public function delete(
        User $user,
        AcademicYear $academicYear
    ): bool {
        return $user->hasPermission(
            'academic-years',
            'delete'
        );
    }
}