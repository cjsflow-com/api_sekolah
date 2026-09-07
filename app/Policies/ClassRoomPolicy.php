<?php

namespace App\Policies;

use App\Models\ClassRoom;
use App\Models\User;

class ClassRoomPolicy
{
    public function viewAny(
        User $user
    ): bool {
        return $user->hasPermission(
            'class-rooms',
            'view'
        );
    }

    public function view(
        User $user,
        ClassRoom $classRoom
    ): bool {
        return $user->hasPermission(
            'class-rooms',
            'view'
        );
    }

    public function create(
        User $user
    ): bool {
        return $user->hasPermission(
            'class-rooms',
            'create'
        );
    }

    public function update(
        User $user,
        ClassRoom $classRoom
    ): bool {
        return $user->hasPermission(
            'class-rooms',
            'update'
        );
    }

    public function delete(
        User $user,
        ClassRoom $classRoom
    ): bool {
        return $user->hasPermission(
            'class-rooms',
            'delete'
        );
    }
}