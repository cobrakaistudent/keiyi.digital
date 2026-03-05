<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScoutSource;
use App\Models\ScoutInsight;

class ScoutApiController extends Controller
{
    /**
     * Entrega a la Mac M2 qué URLs debe investigar hoy.
     */
    public function getPendingSources()
    {
        $sources = ScoutSource::where('is_active', true)->get()->map(function($source) {
            $source->timestamp = now()->toDateTimeString();
            return $source;
        });

        return response()->json([
            'status' => 'success',
            'data' => $sources
        ]);
    }

    /**
     * Recibe el reporte JSON dictado por Ollama gemma3 en la Mac local.
     */
    public function receiveInsight(Request $request)
    {
        $validated = $request->validate([
            'detected_trends' => 'required|array',
            'recommended_actions' => 'required|array',
            'raw_sources_used' => 'nullable|string'
        ]);

        $insight = ScoutInsight::create([
            'report_date' => now()->toDateString(),
            'detected_trends' => $validated['detected_trends'],
            'recommended_actions' => $validated['recommended_actions'],
            'raw_sources_used' => $validated['raw_sources_used'] ?? 'Análisis Local - Keiyi Brain M2',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Insight guardado correctamente en Hostinger.',
            'insight_id' => $insight->id
        ], 201);
    }
}
