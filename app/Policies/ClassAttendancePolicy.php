<?php

namespace App\Policies;

use App\Models\User;

class ClassAttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(
            'class-attendances',
            'view'
        );
    }

    public function updateAny(User $user): bool
    {
        return $user->hasPermission(
            'class-attendances',
            'update'
        );
    }
}