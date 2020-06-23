<?php

namespace Wingsline\PasswordlessAuth;

use Illuminate\Routing\Router;
use Wingsline\PasswordlessAuth\Controllers\LoginController;
use Wingsline\PasswordlessAuth\Controllers\RegisterController;
use Wingsline\PasswordlessAuth\Controllers\VerificationController;

class PasswordlessAuth
{
    /**
     * PasswordlessAuth constructor.
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Passwordless authentication routes.
     */
    public function auth(array $options = [])
    {
        $this->router->group(['as' => 'passwordless.', 'middleware' => 'web'], function () use ($options) {
            // login routes
            $this->router->get('login', [LoginController::class, 'showLoginForm'])->name('login');
            $this->router->get('login/email/sent', [LoginController::class, 'showLoginUrlSent'])->name('email.sent');
            $this->router->get('login/email/verify/{id}/{hash}', [LoginController::class, 'verify'])->name('email.verify');

            $this->router->post('login', [LoginController::class, 'login'])->name('login.store');
            $this->router->post('logout', [LoginController::class, 'logout'])->name('logout');

            // register
            if ($options['register'] ?? true) {
                $this->router->get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
                $this->router->post('register', [RegisterController::class, 'register'])->name('register.store');
            }

            // email verify
            $this->router->get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
            $this->router->get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
            $this->router->post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
        });
    }
}
