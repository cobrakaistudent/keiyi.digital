<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApproved
{
    /**
     * Maneja una solicitud entrante.
     * Si el usuario no está aprobado, lo redirige a una vista de espera.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->role !== 'admin' && !$request->user()->is_approved) {
            // El usuario está logueado pero no aprobado (y no es admin)
            return response()->view('academy.pending_approval');
        }

        return $next($request);
    }
}
