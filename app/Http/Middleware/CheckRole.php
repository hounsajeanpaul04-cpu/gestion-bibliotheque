<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Vérifier si l utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Vérifier si le rôle est autorisé
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, "Accès refusé : vous n avez pas le rôle requis.");
        }

        return $next($request);
    }
}