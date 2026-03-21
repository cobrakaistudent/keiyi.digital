<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog — Keiyi Digital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/style.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <x-keiyi-nav />

    {{-- HERO --}}
    <section style="padding: 64px 32px 32px; max-width: 1100px; margin: 0 auto;">
        <div style="display: inline-block; background: #000; color: #fff; font-size: 11px; font-weight: 700; letter-spacing: 2px; padding: 4px 12px; margin-bottom: 16px; font-family: 'Space Grotesk', sans-serif;">
            BLOG KEIYI
        </div>
        <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: clamp(32px, 5vw, 56px); font-weight: 700; line-height: 1.1; margin: 0 0 16px;">
            Marketing que funciona.<br>Sin relleno.
        </h1>
        <p style="font-family: 'Space Grotesk', sans-serif; font-size: 18px; color: #555; max-width: 560px;">
            Análisis de tendencias reales de la comunidad. Cada artículo viene de datos, no de suposiciones.
        </p>
    </section>

    {{-- GRID DE ARTÍCULOS --}}
    <section style="padding: 32px; max-width: 1100px; margin: 0 auto;">
        @if($posts->isEmpty())
            <div style="border: 3px solid #000; padding: 48px; text-align: center; font-family: 'Space Grotesk', sans-serif; box-shadow: 6px 6px 0 #000;">
                <p style="font-size: 18px; margin: 0 0 8px; font-weight: 700;">Próximamente.</p>
                <p style="color: #555; margin: 0;">William está preparando los primeros artículos.</p>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
                @foreach($posts as $post)
                <article style="border: 3px solid #000; padding: 28px; box-shadow: 5px 5px 0 #000; background: #fff; display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <span style="background: #000; color: #fff; font-size: 10px; font-weight: 700; letter-spacing: 1px; padding: 3px 8px; font-family: 'Space Grotesk', sans-serif;">
                            {{ strtoupper($post->category) }}
                        </span>
                        {{-- subreddit badge removido — fuentes no se muestran en público --}}
                    </div>

                    <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 20px; font-weight: 700; margin: 0; line-height: 1.3;">
                        <a href="{{ route('blog.show', $post->slug) }}" style="color: #000; text-decoration: none;">
                            {{ $post->title }}
                        </a>
                    </h2>

                    <p style="font-family: 'Space Grotesk', sans-serif; font-size: 14px; color: #555; margin: 0; line-height: 1.6;">
                        {{ $post->excerpt }}
                    </p>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 12px; border-top: 1.5px solid #eee;">
                        <span style="font-family: 'Space Grotesk', sans-serif; font-size: 12px; color: #888;">
                            {{ $post->word_count }} palabras · {{ $post->published_at?->diffForHumans() }}
                        </span>
                        <a href="{{ route('blog.show', $post->slug) }}"
                           style="font-family: 'Space Grotesk', sans-serif; font-size: 12px; font-weight: 700; color: #000; text-decoration: none; border-bottom: 2px solid #000;">
                            LEER →
                        </a>
                    </div>
                </article>
                @endforeach
            </div>
        @endif
    </section>

    <x-keiyi-footer />

</body>
</html>
