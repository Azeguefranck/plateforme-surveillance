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

        $fresh = \Illuminate\Support\Facades\DB::table('users')->where('id', session('user')->id)->first();
        if (!$fresh || $fresh->validation_status === 'bloque') {
            session()->forget('user');
            session()->invalidate();
            return redirect('/login')->with('error', 'Votre compte a été bloqué ou supprimé.');
        }
        session(['user' => $fresh]);

        return $next($request);
    }
}
