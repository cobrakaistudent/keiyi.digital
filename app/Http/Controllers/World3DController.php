<?php

namespace App\Http\Controllers;

use App\Mail\DownloadLinkMail;
use App\Models\DownloadToken;
use App\Models\PricingConfig;
use App\Models\PrintCatalog;
use App\Models\PrintOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class World3DController extends Controller
{
    public function index()
    {
        $items = PrintCatalog::published()->latest()->get();
        return view('world3d.index', compact('items'));
    }

    public function requestDownload(Request $request, PrintCatalog $item)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        if (! $item->downloadable || ! $item->file_path) {
            return back()->with('error', 'Este archivo no está disponible para descarga.');
        }

        $token = DownloadToken::generate($item, $request->email);

        try {
            Mail::to($request->email)->send(new DownloadLinkMail($token, $item));
        } catch (\Exception) {
            // Fallo silencioso — el link igual se genera
        }

        return back()->with('download_sent', 'Te enviamos el link de descarga a ' . $request->email . '. Válido por 24 horas.');
    }

    public function download(string $token)
    {
        $record = DownloadToken::where('token', $token)->firstOrFail();

        if (! $record->isValid()) {
            abort(410, 'Este link ha expirado o ya fue utilizado.');
        }

        $item = $record->catalogItem;

        if (! $item || ! $item->file_path || ! Storage::exists($item->file_path)) {
            abort(404, 'Archivo no encontrado.');
        }

        $record->markUsed();

        return Storage::download($item->file_path, $item->file_name ?? basename($item->file_path));
    }

    /**
     * API: Cotización instantánea (llamada desde JS del frontend)
     */
    public function quote(Request $request, PrintCatalog $item)
    {
        $request->validate([
            'grams'    => 'required|numeric|min:1|max:10000',
            'hours'    => 'required|numeric|min:0.1|max:200',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $quote = PricingConfig::calculatePrintCost(
            (float) $request->grams,
            (float) $request->hours
        );

        $quote['quantity']    = (int) $request->quantity;
        $quote['unit_price']  = $quote['final_price'];
        $quote['total_price'] = round($quote['final_price'] * $request->quantity, 2);
        $quote['total_iva']   = round($quote['total_price'] * 1.16, 2);
        $quote['item_title']  = $item->title;

        return response()->json($quote);
    }

    /**
     * Solicitar cotización / orden de impresión (público, sin login)
     */
    public function requestOrder(Request $request, PrintCatalog $item)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'material' => 'required|string|max:100',
            'color'    => 'required|string|max:100',
            'quantity' => 'required|integer|min:1|max:100',
            'notes'    => 'nullable|string|max:1000',
        ]);

        if (! $item->orderable) {
            return back()->with('error', 'Este item no está disponible para cotización.');
        }

        // Calcular cotización automática si tenemos datos del modelo
        $quotedPrice = null;
        $quoteDetails = null;
        if ($item->price) {
            $quotedPrice = $item->price * $request->quantity;
            $quoteDetails = "Precio de catálogo: \${$item->price} × {$request->quantity} = \${$quotedPrice}";
        }

        $order = PrintOrder::create([
            'user_id'         => auth()->id(),
            'type'            => 'catalog',
            'catalog_item_id' => $item->id,
            'material'        => $request->material,
            'color'           => $request->color,
            'quantity'        => $request->quantity,
            'notes'           => "Nombre: {$request->name}\nEmail: {$request->email}\n" . ($request->notes ?? ''),
            'status'          => 'received',
            'quoted_price'    => $quotedPrice,
            'quote_details'   => $quoteDetails,
        ]);

        return back()->with('order_sent', 'Solicitud de cotización recibida. Te contactaremos a ' . $request->email . ' con el presupuesto.');
    }
}
