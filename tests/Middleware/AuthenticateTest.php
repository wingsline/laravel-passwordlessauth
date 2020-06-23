<?php

namespace Wingsline\PasswordlessAuth\Tests\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Route;
use Wingsline\PasswordlessAuth\Middleware\Authenticate;
use Wingsline\PasswordlessAuth\Tests\TestCase;

class AuthenticateTest extends TestCase
{
    public function test_ensure_visitor_gets_redirected_to_proper_route()
    {
        Route::middleware(Authenticate::class)->any('/_test/auth', function () {
            return 'OK';
        });

        $response = $this->get('/_test/auth');
        $response->assertStatus(302);
        $response->assertRedirect('login');

        // test json
        $this->expectException(AuthenticationException::class);
        $response->json('/_test/auth');
        $response->assertOk();
    }
}
