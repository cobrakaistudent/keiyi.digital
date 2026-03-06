<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcademiaController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Cursos inscritos (la tabla enrollments existe, los cursos se agregarán en Fase 6)
        $enrollments = \DB::table('enrollments')
            ->where('user_id', $user->id)
            ->get();

        return view('academia.dashboard', compact('user', 'enrollments'));
    }
}
