<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Récupérer les identifiants
        $credentials = $request->only('email', 'password');
        $plainPassword = $request->input('password');

        // Vérifier si le mot de passe saisi est "password"
        if ($plainPassword === 'password') {
            // Vérifier si l'utilisateur existe avec cet email
            $user = \App\Models\User::where('email', $credentials['email'])->first();

            // Si l'utilisateur existe ET que son mot de passe est "password" (vérification avec Hash::check)
            if ($user && \Illuminate\Support\Facades\Hash::check('password', $user->password)) {
                // Connecter l'utilisateur manuellement
                Auth::login($user);
                $request->session()->regenerate();

                // Rediriger vers la page de changement de mot de passe
                return redirect()->route('profile.password.change');
            }
        }

        // Sinon, procéder à l'authentification normale
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        return match ($user->role) {
            'admin'    => redirect()->route('admin.dashboard'),
            'creator'  => redirect()->route('creator.dashboard'),
            'logistic' => redirect()->route('logistics.dashboard'),
            default    => redirect()->route('dashboard'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
