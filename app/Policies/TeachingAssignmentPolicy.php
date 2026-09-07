<?php

namespace App\Policies;

use App\Models\TeachingAssignment;
use App\Models\User;

class TeachingAssignmentPolicy
{
    public function viewAny(
        User $user
    ): bool {
        return $user->hasPermission(
            'teaching-assignments',
            'view'
        );
    }

    public function view(
        User $user,
        TeachingAssignment $teachingAssignment
    ): bool {
        return $user->hasPermission(
            'teaching-assignments',
            'view'
        );
    }

    public function create(
        User $user
    ): bool {
        return $user->hasPermission(
            'teaching-assignments',
            'create'
        );
    }

    public function update(
        User $user,
        TeachingAssignment $teachingAssignment
    ): bool {
        return $user->hasPermission(
            'teaching-assignments',
            'update'
        );
    }

    public function delete(
        User $user,
        TeachingAssignment $teachingAssignment
    ): bool {
        return $user->hasPermission(
            'teaching-assignments',
            'delete'
        );
    }
}