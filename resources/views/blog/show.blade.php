<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }} — Keiyi Digital</title>
    <meta name="description" content="{{ $post->excerpt }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/style.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .article-content h2 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px;
            font-weight: 700;
            margin: 36px 0 16px;
            line-height: 1.2;
        }
        .article-content p {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 16px;
            color: #333;
            line-height: 1.8;
            margin: 0 0 16px;
        }
        .article-content ul, .article-content ol {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 16px;
            color: #333;
            line-height: 1.8;
            margin: 0 0 16px;
            padding-left: 24px;
        }
        .article-content li {
            margin-bottom: 8px;
        }
        .article-content strong {
            color: #000;
        }
        .article-content em {
            color: #555;
        }
    </style>
</head>
<body>

    <x-keiyi-nav />

    {{-- ARTICLE --}}
    <article style="max-width: 740px; margin: 0 auto; padding: 48px 32px;">

        {{-- Back link --}}
        <a href="/blog" style="font-family: 'Space Grotesk', sans-serif; font-size: 13px; font-weight: 600; color: #555; text-decoration: none; display: inline-block; margin-bottom: 32px;">
            ← Volver al blog
        </a>

        {{-- Badges --}}
        <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px;">
            <span style="background: #000; color: #fff; font-size: 10px; font-weight: 700; letter-spacing: 1px; padding: 4px 10px; font-family: 'Space Grotesk', sans-serif;">
                {{ strtoupper($post->category) }}
            </span>
            {{-- subreddit badge removido --}}
        </div>

        {{-- Title --}}
        <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: clamp(28px, 4vw, 42px); font-weight: 700; line-height: 1.15; margin: 0 0 16px;">
            {{ $post->title }}
        </h1>

        {{-- Excerpt --}}
        <p style="font-family: 'Space Grotesk', sans-serif; font-size: 18px; color: #555; line-height: 1.6; margin: 0 0 24px; border-left: 4px solid #000; padding-left: 16px;">
            {{ $post->excerpt }}
        </p>

        {{-- Meta --}}
        <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 40px; padding-bottom: 24px; border-bottom: 2px solid #000;">
            <span style="font-family: 'Space Grotesk', sans-serif; font-size: 13px; color: #888;">
                {{ $post->word_count }} palabras
            </span>
            <span style="font-family: 'Space Grotesk', sans-serif; font-size: 13px; color: #888;">
                {{ $post->published_at?->translatedFormat('d M Y') }}
            </span>
        </div>

        {{-- Content --}}
        <div class="article-content">
            {!! $post->content !!}
        </div>

        {{-- CTA --}}
        <div style="margin-top: 48px; border: 3px solid #000; padding: 32px; box-shadow: 5px 5px 0 #000; background: #fafafa;">
            <p style="font-family: 'Space Grotesk', sans-serif; font-size: 18px; font-weight: 700; margin: 0 0 8px;">
                ¿Quieres resultados como estos?
            </p>
            <p style="font-family: 'Space Grotesk', sans-serif; font-size: 14px; color: #555; margin: 0 0 16px;">
                En Keiyi Digital construimos sistemas de automatización e inteligencia con IA. Sin humo, sin promesas vacías.
            </p>
            <a href="{{ url('/') }}#contact"
               style="display: inline-block; background: #000; color: #fff; font-family: 'Space Grotesk', sans-serif; font-size: 13px; font-weight: 700; padding: 10px 24px; text-decoration: none; letter-spacing: 1px;">
                HABLEMOS →
            </a>
        </div>

    </article>

    <x-keiyi-footer />

</body>
</html>
