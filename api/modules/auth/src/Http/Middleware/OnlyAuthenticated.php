<?php

namespace Module\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class OnlyAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([], Response::HTTP_UNAUTHORIZED);
            }

            return redirect('/home');
        }

        return $next($request);
    }
}
