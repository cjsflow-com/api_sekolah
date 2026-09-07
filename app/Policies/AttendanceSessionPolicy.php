<?php

namespace App\Policies;

use App\Models\AttendanceSession;
use App\Models\User;

class AttendanceSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(
            'attendance-sessions',
            'view'
        );
    }

    public function view(
        User $user,
        AttendanceSession $attendanceSession
    ): bool {
        return $user->hasPermission(
            'attendance-sessions',
            'view'
        );
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(
            'attendance-sessions',
            'create'
        );
    }

    public function update(
        User $user,
        AttendanceSession $attendanceSession
    ): bool {
        return $user->hasPermission(
            'attendance-sessions',
            'update'
        );
    }

    public function delete(
        User $user,
        AttendanceSession $attendanceSession
    ): bool {
        return $user->hasPermission(
            'attendance-sessions',
            'delete'
        );
    }
}