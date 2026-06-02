<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Normalise the phone so spacing/format doesn't block a valid login.
        $phone = preg_replace('/\s+/', '', $credentials['phone']);

        // Throttle brute-force attempts: max 5 per minute per phone+ip.
        $key = $phone.'|'.$request->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'phone' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        if (Auth::attempt(['phone' => $phone, 'password' => $credentials['password']], $request->boolean('remember'))) {
            \Illuminate\Support\Facades\RateLimiter::clear($key);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        \Illuminate\Support\Facades\RateLimiter::hit($key, 60);

        throw ValidationException::withMessages([
            'phone' => 'These credentials do not match our records.',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
