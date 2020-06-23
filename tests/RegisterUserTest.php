<?php

namespace Wingsline\PasswordlessAuth\Tests;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Wingsline\PasswordlessAuth\Tests\Stubs\User;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_validation()
    {
        factory(User::class)->create(['email' => 'foo@example.com']);
        // invalid data
        $response = $this->post('register', ['email' => 'foo', 'name' => '']);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'The email must be a valid email address.',
            'name' => 'The name field is required.',
        ]);

        // user exists
        $response = $this->post('register', ['email' => 'foo@example.com', 'name' => '']);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'The email has already been taken.',
            'name' => 'The name field is required.',
        ]);
    }

    public function test_register_new_user()
    {
        Event::fake();
        // submit form with user credentials
        $response = $this->post('register', ['email' => 'foo@example.com', 'name' => 'Foo']);
        $response->assertStatus(302);
        $response->assertRedirect('email/verify');
        $response->assertSessionHasNoErrors();
        // check if user is in the db
        $this->assertDatabaseHas('users', ['email' => 'foo@example.com', 'name' => 'Foo']);
        // logged in
        /** @var MustVerifyEmail|User $user */
        $user = User::where(['email' => 'foo@example.com', 'name' => 'Foo'])->first();
        $this->assertAuthenticatedAs($user);
        // make sure the user is not activated
        self::assertFalse($user->hasVerifiedEmail());
        self::assertNull($user->password);
        // email verification sent
        Event::assertDispatched(Registered::class, function ($created_user) use ($user) {
            return $created_user->id = $user->id;
        });
    }
}
