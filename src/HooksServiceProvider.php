<?php

namespace CreativeSoftTechSolutions\LaravelHooks;

use CreativeSoftTechSolutions\LaravelHooks\Facades\Hooks;
use CreativeSoftTechSolutions\LaravelHooks\Services\HooksService;
use Illuminate\Support\ServiceProvider;

class HooksServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->app->singleton(HooksService::class, function () {
            return new HooksService;
        });

        $this->app->singleton('Hooks', function () {
            return new HooksService();
        });
        $this->app->alias('Hooks', Hooks::class);

        // Load helpers
        if (file_exists(__DIR__ . '/Helpers/helpers.php')) {
            require_once __DIR__ . '/Helpers/helpers.php';
        }
    }

    /**
     * Register any application services.
     */
    public function register() {}
}
