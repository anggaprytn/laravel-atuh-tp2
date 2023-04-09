<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $maxAttempts = 3; // Maximum login attempts allowed
    protected $decayMinutes = 0.5; // The number of minutes to throttle after max attempts reached

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'g-recaptcha-response' => 'required|captcha',
        ]);

        $credentials = $request->only('email', 'password');

        // check if the user has too many failed login attempts
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            $seconds = $this->limiter()->availableIn(
                $this->throttleKey($request)
            );
            $message = 'Terlalu banyak percobaan login gagal, silakan coba lagi dalam ' . $seconds . ' detik.';
            return redirect()->back()->withErrors(['email' => $message])->withInput($request->except('password'));
        }

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            $request->session()->regenerate();
            $this->clearLoginAttempts($request);
            return redirect()->intended('dashboard');
        }

        // increment the login attempts
        $this->incrementLoginAttempts($request);

        throw ValidationException::withMessages([
            'email' => [Lang::get('auth.failed')],
        ]);

    }

    protected function hasTooManyLoginAttempts(Request $request)
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            $this->maxAttempts,
            $this->decayMinutes
        );
    }

    protected function sendLockoutResponse(Request $request)
    {
        $this->incrementLoginAttempts($request);
    
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );
    
        return redirect()
            ->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => 'Too many login attempts. Please try again in ' . $seconds . ' seconds.',
            ])->with('seconds', $seconds);
    }
}
