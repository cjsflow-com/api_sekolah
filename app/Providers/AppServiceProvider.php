<?php

namespace App\Providers;

use App\Session\DatabaseSessionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Session::extend(
            'multi_database',
            function (Application $app) {
                $databaseManager = $app->make(
                    DatabaseManager::class
                );

                $connection = $databaseManager->connection(
                    config('session.connection')
                );

                return new DatabaseSessionHandler(
                    $connection,
                    config('session.table'),
                    config('session.lifetime'),
                    $app
                );
            }
        );
    }
}