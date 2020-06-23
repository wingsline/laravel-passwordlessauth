<?php

namespace Wingsline\PasswordlessAuth;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Wingsline\PasswordlessAuth\Events\LoginUrlCreated;
use Wingsline\PasswordlessAuth\Listeners\SendEmailLoginNotification;

class PasswordlessAuthEventServiceProvider extends EventServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        LoginUrlCreated::class => [SendEmailLoginNotification::class],
    ];

    /**
     * Register the application's event listeners.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
