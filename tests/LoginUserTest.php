<?php

namespace Wingsline\PasswordlessAuth\Tests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Wingsline\PasswordlessAuth\Controllers\LoginController;
use Wingsline\PasswordlessAuth\Events\LoginUrlCreated;
use Wingsline\PasswordlessAuth\Tests\Stubs\User;

class LoginUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_attempt_to_login_with_invalid_data()
    {
        Event::fake();
        // create a response
        $response = $this->post('login', ['email' => 'foo']);
        $response->isOk();
        $response->assertRedirect('/');
        $response->assertSessionHasErrors(
            ['email' => 'The email must be a valid email address.']
        );
        Event::assertNotDispatched(LoginUrlCreated::class);
    }

    public function test_attempt_to_login_with_unverified_user()
    {
        Event::fake();
        // create a user
        $this->assertGuest();
        $user = factory(User::class)->create(['email_verified_at' => null]);
        // create a response
        $response = $this->post('login', ['email' => $user->email]);
        $response->isOk();
        $response->assertRedirect('/');
        $response->assertSessionHasErrors(
            ['email' => 'Your email address is not verified.']
        );
        Event::assertNotDispatched(LoginUrlCreated::class);
    }

    public function test_email_login_response()
    {
        $controller = app(LoginController::class);
        $redirect = $controller->sendEmailLoginResponse(request());
        self::assertSame(url('login/email/sent'), $redirect->getTargetUrl());
    }

    public function test_login()
    {
        Event::fake();
        // create a user
        $this->assertGuest();
        $user = factory(User::class)->create(['email_verified_at' => now()]);
        // create a response
        $response = $this->post('login', ['email' => $user->email]);
        $response->isOk();
        $response->assertRedirect(url('login/email/sent'));
        // check if event sent
        Event::assertDispatched(LoginUrlCreated::class, function ($e) use ($user) {
            return $e->url && $user->id === $e->user->id;
        });
    }

    public function test_show_login_form()
    {
        $response = $this->get('login');

        $response->isOk();
        $response->assertViewIs('wingsline::passwordlessauth.login');
    }

    public function test_show_login_url_sent()
    {
        $response = $this->get('login/email/sent');
        $response->isOk();
        $response->assertViewIs('wingsline::passwordlessauth.url-sent');
    }

    public function test_too_many_login_attempts()
    {
        // replace login controller with the too many attempts controller
        $this->app->bind(LoginController::class, function () {
            return new TooManyAttemptsLoginController();
        });

        Event::fake();
        // create a user
        $this->assertGuest();
        $user = factory(User::class)->create(['email_verified_at' => now()]);
        // create a response
        $response = $this->post('login', ['email' => $user->email]);
        $response->assertRedirect('/');

        Event::assertDispatched(Lockout::class);
    }
}

class TooManyAttemptsLoginController extends LoginController
{
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return true;
    }
}
