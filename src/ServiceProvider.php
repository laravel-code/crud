<?php

namespace LaravelCode\Crud;

use Illuminate\Support\Facades\Event;
use LaravelCode\Crud\Commands\CrudControllers;
use LaravelCode\Crud\Commands\CrudEvents;
use LaravelCode\Crud\Commands\CrudGenerator;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/crud.php' => config_path('crud.php'),
            __DIR__.'/config/oauth.php' => config_path('middleware.php'),
        ]);

        $this->loadViewsFrom(__DIR__.'/resources/views', 'crud');
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/database/migrations');
            $this->loadViewsFrom(__DIR__.'/resources/views', 'laravelCrud');

            $this->commands([
                CrudGenerator::class,
                CrudEvents::class,
                CrudControllers::class,
            ]);
        }

        Event::listen('LaravelCode\Crud\Events\CrudEventLogger',
            'LaravelCode\Crud\Listeners\CrudLogListener');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/crud.php', 'crud'
        );
    }
}
