<?php

namespace Wingsline\PasswordlessAuth\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Wingsline\PasswordlessAuth\PasswordlessAuthServiceProvider;
use Wingsline\PasswordlessAuth\Tests\Stubs\User;

class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/Factories');
        // for layout.app view
        view()->getFinder()->addLocation(__DIR__.'/views');
        // add the routes
        $this->app->make('passwordlessauth')->auth();
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $config = $app->make('config');

        $config->set([
            'auth.providers.users.model' => User::class,
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [TestingServiceProvider::class, PasswordlessAuthServiceProvider::class];
    }
}
