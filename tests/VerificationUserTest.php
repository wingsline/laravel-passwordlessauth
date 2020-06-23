<?php

namespace Wingsline\PasswordlessAuth\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Wingsline\PasswordlessAuth\Tests\Stubs\User;

class VerificationUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_with_verified_user()
    {
        $user = factory(User::class)->create(['email_verified_at' => now()]);

        $response = $this->be($user)->get('email/verify');
        $response->assertRedirect('home');
    }

    public function test_show_with_unverified_user()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        $response = $this->be($user)->get('email/verify');
        $response->isOk();
        $response->assertViewIs('wingsline::passwordlessauth.verify');
    }
}
