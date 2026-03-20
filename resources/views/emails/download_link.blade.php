<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Courier New', monospace; background: #f5f5f0; margin: 0; padding: 40px 20px; }
        .card { background: #fff; border: 3px solid #000; max-width: 560px; margin: 0 auto; padding: 40px; box-shadow: 6px 6px 0 #000; }
        h1 { font-size: 22px; margin: 0 0 8px; }
        .tag { display: inline-block; background: #000; color: #fff; font-size: 11px; padding: 3px 10px; letter-spacing: 1px; margin-bottom: 24px; }
        .item-box { border: 2px solid #000; padding: 16px; margin: 24px 0; background: #fafaf5; }
        .item-box strong { display: block; font-size: 16px; margin-bottom: 4px; }
        .item-box span { font-size: 13px; color: #555; }
        .btn { display: block; background: #000; color: #fff; text-decoration: none; text-align: center; padding: 16px; font-size: 15px; font-weight: bold; letter-spacing: 1px; margin: 24px 0; }
        .warning { font-size: 12px; color: #777; border-top: 1px solid #ddd; margin-top: 24px; padding-top: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="tag">KEIYI 3D WORLD</div>
        <h1>Tu archivo está listo.</h1>
        <p style="color:#555;font-size:14px;">Haz clic en el botón para descargar tu modelo 3D. El link es de un solo uso y expira en 24 horas.</p>

        <div class="item-box">
            <strong>{{ $item->title }}</strong>
            <span>{{ $item->material ?? 'Archivo 3D' }} · {{ $item->file_name }}</span>
        </div>

        <a href="{{ url('/3d-world/download/' . $downloadToken->token) }}" class="btn">
            DESCARGAR ARCHIVO
        </a>

        <p class="warning">
            Este link expira el {{ $downloadToken->expires_at->format('d/m/Y') }} a las {{ $downloadToken->expires_at->format('H:i') }}.<br>
            Solo puede usarse una vez. Si tienes problemas, visita <a href="{{ url('/3d-world') }}">keiyi.digital/3d-world</a> y solicita un nuevo link.
        </p>
    </div>
</body>
</html>
