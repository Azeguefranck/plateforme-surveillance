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

        $sessionUser = (object)(array)session('user');
        $fresh = \Illuminate\Support\Facades\DB::table('users')->where('id', $sessionUser->id ?? 0)->first();
        if (!$fresh || $fresh->validation_status === 'bloque') {
            session()->forget('user');
            return redirect('/login')->with('error', 'Votre compte a été bloqué ou supprimé.');
        }
        session(['user' => $fresh]);

        $response = $next($request);
        return $response
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
