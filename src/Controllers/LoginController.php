<?php

namespace Wingsline\PasswordlessAuth\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Wingsline\PasswordlessAuth\Events\LoginUrlCreated;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('signed')->only('verify');
    }

    /**
     * Handle a login request to the application.
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|Response|\Symfony\Component\HttpFoundation\Response|void
     */
    public function login(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string|email|exists:users',
        ]);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // get the user
        /** @var Authenticatable|User $user */
        $user = config('auth.providers.users.model')::where($this->username(), $request->input($this->username()))->first();

        // Do we have a verified user?
        if ($user instanceof MustVerifyEmail && $user->hasVerifiedEmail()) {
            // send the signed url to the user
            if ($this->sendLoginEmail($request, $user)) {
                // redirect to email sent page
                return $this->sendEmailLoginResponse($request);
            }
        } else {
            // not verified?
            return $this->unferifiedEmailResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * When the login token was sent, we redirect to the confirmation page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendEmailLoginResponse(Request $request)
    {
        return redirect()->route('passwordless.email.sent');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('wingsline::passwordlessauth.login');
    }

    /**
     * Login url sent confirmation page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginUrlSent()
    {
        return view('wingsline::passwordlessauth.url-sent');
    }

    /**
     * Verifies the signed url and logs the user in.
     *
     * @throws AuthorizationException
     * @throws ValidationException
     *
     * @return Response|void
     */
    public function verify(Request $request)
    {
        /** @var Authenticatable $user */
        $user = config('auth.providers.users.model')::find($request->route('id'));

        if (!$user) {
            throw new AuthorizationException();
        }

        // check if user is verified
        if ($user instanceof MustVerifyEmail && $user->hasVerifiedEmail()) {
            // check the email
            if (!hash_equals((string) $request->route('hash'), $this->generateHashForUser($user, $request))) {
                throw new AuthorizationException();
            }

            $this->guard()->login($user, config('auth.passwordless.remember'));

            return $this->sendLoginResponse($request);
        }

        return $this->unferifiedEmailResponse($request);
    }

    /**
     * Generate the signed url hash for the user.
     *
     * @return string
     */
    protected function generateHashForUser(Authenticatable $user, Request $request)
    {
        return sha1($user->email . implode('/', $request->ips()));
    }

    /**
     * Generates a temporary signed url and fires the mail event.
     *
     * @return bool
     */
    protected function sendLoginEmail(Request $request, Authenticatable $user)
    {
        /** @var Authenticatable|User $user */
        if ($user instanceof MustVerifyEmail && $user->hasVerifiedEmail()) {
            $url = URL::temporarySignedRoute(
                'passwordless.email.verify',
                now()->addMinutes(15),
                [
                    'id' => $user->getKey(),
                    'hash' => $this->generateHashForUser($user, $request),
                ]
            );
            // Fire the event
            event(new LoginUrlCreated($user, $url));

            return true;
        }

        return false;
    }

    /**
     * Redirect the user after determining they are not verified their email.
     *
     * @throws ValidationException
     */
    protected function unferifiedEmailResponse(Request $request)
    {
        throw ValidationException::withMessages([$this->username() => [trans('wingsline::passwordlessauth.unverified')]]);
    }
}
