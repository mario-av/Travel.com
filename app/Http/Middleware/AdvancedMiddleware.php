<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AdvancedMiddleware - Restricts access to advanced users and admins.
 */
class AdvancedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request The incoming request.
     * @param Closure $next The next middleware.
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user != null && ($user->rol == 'admin' || $user->rol == 'advanced')) {
            return $next($request);
        } else {
            return redirect()->route('main.index');
        }
    }
}
