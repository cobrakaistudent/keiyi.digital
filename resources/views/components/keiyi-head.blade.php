{{-- Head tags reutilizables: fonts, CSS, OG meta --}}
@props(['title' => 'Keiyi Digital', 'description' => 'Marketing Digital + Educación con IA para LATAM', 'image' => null])

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">

{{-- Open Graph --}}
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
@if($image)
<meta property="og:image" content="{{ $image }}">
@endif
<meta property="og:site_name" content="Keiyi Digital">
<meta property="og:locale" content="es_LA">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
@if($image)
<meta name="twitter:image" content="{{ $image }}">
@endif

{{-- Canonical --}}
<link rel="canonical" href="{{ url()->current() }}">

{{-- Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Gloria+Hallelujah&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">

{{-- CSS --}}
<link rel="stylesheet" href="{{ asset('style.css') }}">
