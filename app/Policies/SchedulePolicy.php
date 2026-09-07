<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;

class SchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(
            'schedules',
            'view'
        );
    }

    public function view(
        User $user,
        Schedule $schedule
    ): bool {
        return $user->hasPermission(
            'schedules',
            'view'
        );
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(
            'schedules',
            'create'
        );
    }

    public function update(
        User $user,
        Schedule $schedule
    ): bool {
        return $user->hasPermission(
            'schedules',
            'update'
        );
    }

    public function delete(
        User $user,
        Schedule $schedule
    ): bool {
        return $user->hasPermission(
            'schedules',
            'delete'
        );
    }
}