<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }
        $user = (object)(array)$user;
        if (($user->role ?? '') !== 'admin' && ($user->role ?? '') !== 'administrateur') {
            abort(403, 'Accès réservé aux administrateurs.');
        }
        return $next($request);
    }
}
