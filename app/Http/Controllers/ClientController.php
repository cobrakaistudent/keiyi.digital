<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $projects = DB::table('agency_projects')
            ->join('agency_clients', 'agency_projects.client_id', '=', 'agency_clients.id')
            ->where('agency_clients.email', $user->email)
            ->select('agency_projects.*')
            ->get();

        return view('cliente.dashboard', [
            'user' => $user,
            'projects' => $projects,
        ]);
    }
}
