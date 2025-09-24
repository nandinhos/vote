<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        /** @var User $user */
        $user = Auth::user();

        if ($role === 'admin' && ! $user->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem acessar esta área.');
        }

        if ($role === 'voter' && ! $user->isVoter()) {
            abort(403, 'Acesso negado. Apenas eleitores podem acessar esta área.');
        }

        return $next($request);
    }
}
