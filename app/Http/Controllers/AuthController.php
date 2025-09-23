<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Mail\MagicLoginMail;
use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function requestLink(LoginRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            ['name' => null]
        );

        $plainToken = Str::random(64);
        $hashedToken = hash('sha256', $plainToken);

        LoginToken::create([
            'email' => $user->email,
            'token' => $hashedToken,
            'expires_at' => now()->addMinutes(30),
            'ip' => $request->ip(),
            'user_agent' => (string) $request->header('User-Agent'),
        ]);

        $loginUrl = url(route('auth.magic', [
            'token' => $plainToken,
            'email' => $user->email,
        ], false));

        Mail::to($user->email)->queue(new MagicLoginMail($loginUrl, $user->name ?? ''));

        return back()->with('status', 'Enviamos um link de acesso para o e-mail informado. Confira sua caixa de entrada.');
    }

    public function magic(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
        ]);

        $hashedToken = hash('sha256', $data['token']);

        $loginToken = LoginToken::query()
            ->where('email', $data['email'])
            ->where('token', $hashedToken)
            ->valid()
            ->latest('created_at')
            ->first();

        if (!$loginToken) {
            return redirect()->route('auth.error')->with('error', 'Link inválido ou expirado. Solicite um novo acesso.');
        }

        $user = User::firstOrCreate(['email' => $data['email']]);

        $loginToken->markUsed();

        Auth::login($user, true);
        $request->session()->regenerate();

        if ($user->institution) {
            return redirect()->route('dashboard')->with('status', 'Bem-vindo de volta!');
        }

        return redirect()->route('setup.create');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}