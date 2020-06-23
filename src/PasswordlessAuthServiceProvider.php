<?php

namespace Wingsline\PasswordlessAuth;

use Illuminate\Support\ServiceProvider;

class PasswordlessAuthServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'wingsline');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'wingsline');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['passwordlessauth'];
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/passwordlessauth.php', 'passwordlessauth');

        // Register the service the package provides.
        $this->app->singleton('passwordlessauth', function ($app) {
            $router = $this->app['router'];

            return new PasswordlessAuth($router);
        });
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/passwordlessauth.php' => config_path('passwordlessauth.php'),
        ], 'passwordlessauth');

        // Publishing the views.
        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/wingsline'),
        ], 'passwordlessauth');

        // Publishing the translation files.
        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/wingsline'),
        ], 'passwordlessauth');
    }
}
