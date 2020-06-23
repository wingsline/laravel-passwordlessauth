<?php

namespace Wingsline\PasswordlessAuth\Tests\Notifications;

use Illuminate\Support\Facades\Notification;
use Wingsline\PasswordlessAuth\Notifications\LoginUrlNotification;
use Wingsline\PasswordlessAuth\Tests\Stubs\User;
use Wingsline\PasswordlessAuth\Tests\TestCase;

class LoginUrlNotificationTest extends TestCase
{
    public function test_ensure_login_email_is_sent()
    {
        Notification::fake();

        $user = factory(User::class)->make();
        $user->notify(new LoginUrlNotification('foo-url'));

        Notification::assertSentTo($user, LoginUrlNotification::class, function ($notification, $channels) use ($user) {
            $mailData = $notification->toMail($user)->toArray();
            self::assertSame('Sign in to ' . config('app.name'), $mailData['subject']);
            self::assertSame('foo-url', $mailData['actionUrl']);
            self::assertSame('Sign In', $mailData['actionText']);
            self::assertSame('foo-url', $mailData['displayableActionUrl']);
            self::assertSame(['Click the link below to sign in to your Laravel account.'], $mailData['introLines']);

            return 'foo-url' === $notification->url;
        });
    }
}
