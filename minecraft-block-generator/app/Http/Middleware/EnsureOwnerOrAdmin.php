<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnerOrAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            abort(403, 'Accès réservé aux utilisateurs authentifiés.');
        }

        $user = Auth::user();
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check ownership: allow if user is the creator
        $entity = $request->route('block') ?? $request->route('item') ?? $request->route('mob');
        if ($entity && $user->isOwnerOf($entity->creator_identifier)) {
            return $next($request);
        }

        abort(403, 'Vous ne pouvez modifier que vos propres créations.');
    }
}
