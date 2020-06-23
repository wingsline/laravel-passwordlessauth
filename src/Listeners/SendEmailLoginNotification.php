<?php

namespace Wingsline\PasswordlessAuth\Listeners;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Wingsline\PasswordlessAuth\Events\LoginUrlCreated;
use Wingsline\PasswordlessAuth\Notifications\LoginUrlNotification;

class SendEmailLoginNotification
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(LoginUrlCreated $event)
    {
        if ($event->user instanceof MustVerifyEmail && $event->user->hasVerifiedEmail()) {
            $event->user->notify(new LoginUrlNotification($event->url));
        }
    }
}
