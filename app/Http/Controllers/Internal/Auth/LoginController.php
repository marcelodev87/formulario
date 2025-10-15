<?php

namespace App\Http\Controllers\Internal\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(Request $request): RedirectResponse|View
    {
        if (Auth::guard('internal')->check()) {
            return redirect()->route('etika.dashboard');
        }

        return view('internal.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::guard('internal')->attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Credenciais invalidas.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::guard('internal')->user();

        if ($user && $user->isDirty('last_login_at')) {
            $user->save();
        } elseif ($user) {
            $user->forceFill(['last_login_at' => now()])->save();
        }

        return redirect()->intended(route('etika.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('internal')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('etika.login');
    }
}
