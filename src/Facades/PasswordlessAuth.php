<?php

namespace Wingsline\PasswordlessAuth\Facades;

use Illuminate\Support\Facades\Facade;

class PasswordlessAuth extends Facade
{
    /**
     * Register the typical authentication routes for an application.
     *
     * @return void
     */
    public static function routes(array $options = [])
    {
        static::$app->make('passwordlessauth')->auth($options);
    }

    /**
     * Get the registered name of the component.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'passwordlessauth';
    }
}
