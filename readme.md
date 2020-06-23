# PasswordlessAuth

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This package will enable passwordless authentication using temporary signed routes. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Create a new laravel application with:

```bash
laravel new myapp --auth
```


Via Composer

``` bash
$ composer require wingsline/passwordlessauth
```

Remove the `Auth::routes();` from the `routes/web.php` and replace it with:

```php
use Wingsline\PasswordlessAuth\Facades\PasswordlessAuth;

PasswordlessAuth::routes();
```


Replace the following route names in the original views with the `passwordless.` prefix:

* login -> passwordless.login
* register -> passwordless.register
* logout -> passwordless.logout
* verification.resend -> passwordless.verification.resend

Replace the `auth` middleware in `app/Http/Kernel.php`:

``` php
protected $routeMiddleware = [
        'auth' => \Wingsline\PasswordlessAuth\Middleware\Authenticate::class,
    ]
```

Add the following method to the `User` model and make sure the model 
implements the `Illuminate\Contracts\Auth\MustVerifyEmail` interface:

```php
use Wingsline\PasswordlessAuth\Notifications\VerifyEmail;

/**
 * Send the email verification notification.
 *
 * @return void
 */
public function sendEmailVerificationNotification()
{
    $this->notify(new VerifyEmail);
}
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email wingsline@gmail.com instead of using the issue tracker.

## Credits

- [Arpad Olasz][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/wingsline/passwordlessauth.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/wingsline/passwordlessauth.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/wingsline/laravel-passwordlessauth/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/274481196/shield

[link-packagist]: https://packagist.org/packages/wingsline/passwordlessauth
[link-downloads]: https://packagist.org/packages/wingsline/passwordlessauth
[link-travis]: https://travis-ci.org/wingsline/laravel-passwordlessauth
[link-styleci]: https://styleci.io/repos/274481196
[link-author]: https://github.com/wingsline
[link-contributors]: ../../contributors
