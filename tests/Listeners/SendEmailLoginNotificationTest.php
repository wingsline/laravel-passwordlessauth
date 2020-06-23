<?php

namespace Wingsline\PasswordlessAuth\Tests\Listeners;

use Illuminate\Support\Facades\Notification;
use Wingsline\PasswordlessAuth\Events\LoginUrlCreated;
use Wingsline\PasswordlessAuth\Listeners\SendEmailLoginNotification;
use Wingsline\PasswordlessAuth\Notifications\LoginUrlNotification;
use Wingsline\PasswordlessAuth\Tests\Stubs\User;
use Wingsline\PasswordlessAuth\Tests\TestCase;

class SendEmailLoginNotificationTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_send_email_only_to_verified_user()
    {
        Notification::fake();
        // unverified user
        $user = factory(User::class)->make(['email_verified_at' => null]);
        (new SendEmailLoginNotification())
            ->handle(new LoginUrlCreated($user, 'foo-url'));
        Notification::assertNothingSent();

        // verified user
        $user = factory(User::class)->make(['email_verified_at' => now()]);
        (new SendEmailLoginNotification())
            ->handle(new LoginUrlCreated($user, 'foo-url'));
        Notification::assertSentTo(
            [$user],
            LoginUrlNotification::class
        );
    }
}
