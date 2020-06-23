<?php

namespace Wingsline\PasswordlessAuth\Tests\Notifications;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use ReflectionMethod;
use Wingsline\PasswordlessAuth\Notifications\VerifyEmail;
use Wingsline\PasswordlessAuth\Tests\Stubs\User;
use Wingsline\PasswordlessAuth\Tests\TestCase;

class VerifyEmailTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function test_verification_url()
    {
        $notifiable = factory(User::class)->make();
        $notifiable->setAttribute('id', 1);

        $n = new VerifyEmail();
        $reflection_method = new ReflectionMethod($n, 'verificationUrl');
        $reflection_method->setAccessible(true);

        $url = $reflection_method->invoke($n, $notifiable);

        $expected = url(
            'email/verify/'.
          $notifiable->id.
          '/'.
          sha1($notifiable->email).
          '?expires='.
          Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60))->unix().
          '&signature='
        );
        self::assertTrue(Str::startsWith($url, $expected));
    }
}
