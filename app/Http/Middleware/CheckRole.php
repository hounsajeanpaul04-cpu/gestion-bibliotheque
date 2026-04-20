<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // ✅ IMPORTANT
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next,... $roles): Response
    {

       // 1. Vérifier si l'utilisateur est connecté

        if (!auth::check()) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        // 2. Vérifier si le rôle de l'utilisateur est dans la liste autorisée
        // On suppose que votre table 'users' a une colonne 'role'
       
       if (!in_array(Auth::user()->role, $roles)) {
            abort(403, "Accès refusé : vous n'avez pas le rôle requis.");
        }
        return $next($request);
    }
}
