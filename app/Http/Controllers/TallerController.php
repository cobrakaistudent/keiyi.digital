<?php

namespace App\Http\Controllers;

use App\Models\PrintOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TallerController extends Controller
{
    public function registro()
    {
        return view('world3d.taller_registro');
    }

    public function storeRegistro(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'message' => 'required|string|max:1000',
        ]);

        // Si el usuario está autenticado, marcamos la solicitud en su cuenta
        if (auth()->check()) {
            auth()->user()->update(['is_3d_client' => true]);
            // Admin aprueba manualmente vía Filament — is_3d_client = true pero 3d_client_approved_at = null
        }

        return back()->with('registro_sent', 'Recibimos tu solicitud. Revisamos en 24 horas.');
    }

    public function index()
    {
        $orders = PrintOrder::where('user_id', auth()->id())
            ->with('catalogItem')
            ->latest()
            ->get();

        return view('world3d.taller', compact('orders'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file'     => 'required|file|mimes:stl,obj,3mf|max:51200', // 50MB
            'material' => 'required|string|max:100',
            'color'    => 'required|string|max:100',
            'quantity' => 'required|integer|min:1|max:100',
            'notes'    => 'nullable|string|max:1000',
        ]);

        $file = $request->file('file');
        $path = $file->store('taller/uploads', 'local');

        PrintOrder::create([
            'user_id'   => auth()->id(),
            'type'      => 'custom',
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'material'  => $request->material,
            'color'     => $request->color,
            'quantity'  => $request->quantity,
            'notes'     => $request->notes,
        ]);

        return back()->with('upload_sent', 'Archivo recibido. Te enviamos la cotización pronto.');
    }
}
