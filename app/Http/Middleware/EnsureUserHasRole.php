<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Vérifier si l'utilisateur est authentifié
        if (!$request->user()) {
            return redirect()->route('login');
        }
        
        // Vérifier si l'utilisateur a un des rôles requis
        foreach ($roles as $role) {
            if ($request->user()->role === $role) {
                return $next($request);
            }
        }
        
        // Rediriger vers le dashboard approprié ou Erreur 403
        $userRole = $request->user()->role;
        
        return match($userRole) {
            'admin' => redirect()->route('admin.dashboard'),
            'creator' => redirect()->route('creator.dashboard'),
            'logistic' => redirect()->route('logistics.dashboard'),
            default => abort(403, 'Unauthorized access.'),
        };
    }
}