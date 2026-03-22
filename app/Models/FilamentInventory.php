<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class FilamentInventory extends Model
{
    protected $table = 'filament_inventory';

    protected $fillable = [
        'brand', 'material', 'color', 'color_hex', 'weight_grams', 'remaining_grams',
        'cost_per_kg', 'diameter', 'status', 'purchased_at', 'notes',
        'purchase_url', 'store', 'source',
    ];

    protected $casts = [
        'cost_per_kg'  => 'decimal:2',
        'purchased_at' => 'date',
    ];

    public function getCostPerGramAttribute(): float
    {
        return $this->cost_per_kg / 1000;
    }

    public function getRemainingPercentAttribute(): int
    {
        if ($this->weight_grams <= 0) return 0;
        return (int) round(($this->remaining_grams / $this->weight_grams) * 100);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Intenta extraer datos de producto desde una URL de Amazon/MercadoLibre
     */
    public static function scrapeFromUrl(string $url): array
    {
        $data = ['purchase_url' => $url, 'source' => 'url_scrape'];

        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)'])
                ->get($url);

            if (!$response->ok()) {
                return $data;
            }

            $html = $response->body();

            // Detectar tienda
            if (str_contains($url, 'amazon')) {
                $data['store'] = 'Amazon';
                $data = array_merge($data, self::parseAmazon($html));
            } elseif (str_contains($url, 'mercadolibre') || str_contains($url, 'mercadoli')) {
                $data['store'] = 'MercadoLibre';
                $data = array_merge($data, self::parseMercadoLibre($html));
            } else {
                $data['store'] = parse_url($url, PHP_URL_HOST);
            }
        } catch (\Exception $e) {
            $data['notes'] = 'Error al leer URL: ' . $e->getMessage();
        }

        return $data;
    }

    private static function parseAmazon(string $html): array
    {
        $result = [];

        // Título del producto
        if (preg_match('/<span id="productTitle"[^>]*>\s*(.+?)\s*<\/span>/s', $html, $m)) {
            $title = strip_tags(trim($m[1]));
            $result['notes'] = $title;

            // Extraer marca del título
            $brands = ['eSUN', 'Bambu', 'Hatchbox', 'Overture', 'Creality', 'Sunlu', 'Polymaker', 'Prusament', 'PolyLite', 'Eryone'];
            foreach ($brands as $brand) {
                if (stripos($title, $brand) !== false) {
                    $result['brand'] = $brand;
                    break;
                }
            }

            // Extraer material del título
            $materials = ['PLA+', 'PLA', 'PETG', 'TPU', 'ABS', 'ASA', 'Nylon', 'Resina'];
            foreach ($materials as $mat) {
                if (stripos($title, $mat) !== false) {
                    $result['material'] = str_replace('+', '', $mat); // PLA+ → PLA
                    break;
                }
            }

            // Extraer color
            if (preg_match('/(?:color|colour)[:\s]*([a-záéíóú]+)/i', $title, $cm)) {
                $result['color'] = ucfirst($cm[1]);
            }

            // Extraer peso
            if (preg_match('/(\d+(?:\.\d+)?)\s*(?:kg|KG)/i', $title, $wm)) {
                $result['weight_grams'] = (int) ($wm[1] * 1000);
                $result['remaining_grams'] = $result['weight_grams'];
            }
        }

        // Precio
        if (preg_match('/<span class="a-price-whole">([0-9,]+)</', $html, $pm)) {
            $price = (float) str_replace(',', '', $pm[1]);
            $weightKg = ($result['weight_grams'] ?? 1000) / 1000;
            $result['cost_per_kg'] = round($price / $weightKg, 2);
        }

        return $result;
    }

    private static function parseMercadoLibre(string $html): array
    {
        $result = [];

        // Título
        if (preg_match('/<h1[^>]*class="[^"]*title[^"]*"[^>]*>(.+?)<\/h1>/s', $html, $m)) {
            $title = strip_tags(trim($m[1]));
            $result['notes'] = $title;

            $brands = ['eSUN', 'Bambu', 'Hatchbox', 'Overture', 'Creality', 'Sunlu', 'Polymaker'];
            foreach ($brands as $brand) {
                if (stripos($title, $brand) !== false) {
                    $result['brand'] = $brand;
                    break;
                }
            }

            $materials = ['PLA+', 'PLA', 'PETG', 'TPU', 'ABS', 'ASA', 'Nylon', 'Resina'];
            foreach ($materials as $mat) {
                if (stripos($title, $mat) !== false) {
                    $result['material'] = str_replace('+', '', $mat);
                    break;
                }
            }

            if (preg_match('/(\d+(?:\.\d+)?)\s*(?:kg|KG)/i', $title, $wm)) {
                $result['weight_grams'] = (int) ($wm[1] * 1000);
                $result['remaining_grams'] = $result['weight_grams'];
            }
        }

        // Precio
        if (preg_match('/<span[^>]*class="[^"]*price-tag-fraction[^"]*"[^>]*>([0-9,.]+)</', $html, $pm)) {
            $price = (float) str_replace([',', '.'], ['', ''], $pm[1]);
            $weightKg = ($result['weight_grams'] ?? 1000) / 1000;
            $result['cost_per_kg'] = round($price / $weightKg, 2);
        }

        return $result;
    }
}
