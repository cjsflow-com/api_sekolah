<?php

namespace App\Session;

use Illuminate\Session\DatabaseSessionHandler as BaseDatabaseSessionHandler;

class DatabaseSessionHandler extends BaseDatabaseSessionHandler
{
    /**
     * Menambahkan informasi account yang sedang login
     * ke tabel sessions.
     */
    protected function addUserInformation(&$payload)
    {
        if (! $this->container) {
            return $this;
        }

        $auth = $this->container->make('auth');

        $guards = [
            'web' => 'user',
            'student' => 'student',
            'teacher' => 'teacher',
        ];

        foreach ($guards as $guardName => $accountType) {
            $guard = $auth->guard($guardName);

            if (! $guard->check()) {
                continue;
            }

            $account = $guard->user();

            $payload['authenticatable_id'] =
                $account->getAuthIdentifier();

            $payload['authenticatable_type'] =
                $accountType;

            $payload['guard'] =
                $guardName;

            return $this;
        }

        $payload['authenticatable_id'] = null;
        $payload['authenticatable_type'] = null;
        $payload['guard'] = null;

        return $this;
    }
}