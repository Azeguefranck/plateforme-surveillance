<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('user')) {
            return redirect('/login')->with('error', 'Veuillez vous authentifier pour accéder à cette page.');
        }

        return $next($request);
    }
}
