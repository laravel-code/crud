<?php

namespace LaravelCode\Crud;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use LaravelCode\Crud\Commands\CrudControllers;
use LaravelCode\Crud\Commands\CrudEvents;
use LaravelCode\Crud\Commands\CrudGenerate;
use LaravelCode\Crud\Commands\CrudRoutes;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/config/crud.php' => config_path('crud.php'),
        ]);

        $this->loadViewsFrom(__DIR__.'/resources/views', 'crud');
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/database/migrations');
            $this->loadViewsFrom(__DIR__.'/resources/views', 'laravelCrud');

            $this->commands([
                CrudGenerate::class,
                CrudRoutes::class,
                CrudEvents::class,
                CrudControllers::class,
            ]);
        }

        Event::listen('LaravelCode\Crud\Events\CrudEventLogger',
            'LaravelCode\Crud\Listeners\CrudLogListener');

        Validator::extend('chained', function () {
            return true;
        });
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/crud.php', 'crud'
        );
    }
}
