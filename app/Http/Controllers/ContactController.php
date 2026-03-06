<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:120',
            'email'   => 'required|email|max:200',
            'service' => 'nullable|string|max:60',
            'message' => 'required|string|max:2000',
        ]);

        DB::table('contact_messages')->insert([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'service'    => $validated['service'] ?? null,
            'message'    => $validated['message'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('contact_sent', true);
    }
}
