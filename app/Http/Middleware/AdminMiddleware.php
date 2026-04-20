<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Gère une requête entrante.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. On vérifie si l'utilisateur est connecté (Auth::check())
        // 2. On vérifie explicitement si is_admin est à true ou 1
        if (Auth::check() && (bool) Auth::user()->is_admin === true) {
            return $next($request);
        }

        // Si l'utilisateur n'est pas admin, on bloque l'accès
        // Note : En production, rediriger vers '/' est souvent plus "propre" qu'une 403
        abort(403, "Accès interdit : cette zone est réservée aux administrateurs.");
    }
}