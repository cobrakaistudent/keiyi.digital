<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Check3DClient
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_3d_client || is_null($user->{'3d_client_approved_at'})) {
            return redirect()->route('taller.registro')
                ->with('info', 'Necesitas ser cliente 3D aprobado para acceder al Taller.');
        }

        return $next($request);
    }
}
