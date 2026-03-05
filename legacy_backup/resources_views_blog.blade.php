@extends('layouts.app')

@section('title', 'Blog Keiyi | Ideas, Tutoriales y Cultura Pop')
@section('description', 'Artículos sobre marketing, diseño 3D y tendencias digitales.')

@section('content')
    <header class="hero" style="min-height: 50vh;">
        <div class="container hero-container">
            <div class="hero-bubble">
                <div class="hero-content text-center">
                    <div class="hand-note">Read me! 📖</div>
                    <h1 class="hero-title">Keiyi <span class="highlight-scribble">Blog</span></h1>
                    <p class="hero-desc">Un espacio para compartir lo que aprendemos, lo que creamos y lo que nos inspira.</p>
                </div>
            </div>
        </div>
    </header>

    <section class="section">
        <div class="container">
            <div class="blog-grid">
                
                @forelse($posts as $post)
                    <article class="blog-card funky-card">
                        @if($post->image)
                            <div style="height: 250px; overflow: hidden;">
                                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" style="width: 100%; h-full; object-cover;">
                            </div>
                        @else
                            <div style="height: 250px; background: var(--color-blue); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.5rem;">
                                Keiyi Digital 📸
                            </div>
                        @endif

                        <div class="blog-content">
                            <span class="blog-date">{{ $post->created_at->format('d M, Y') }}</span>
                            <h3 class="blog-title">{{ $post->title }}</h3>
                            <p>{{ Str::limit(strip_tags($post->content), 150) }}</p>
                            <a href="{{ route('blog.show', $post->slug) }}" class="read-more">Leer Artículo →</a>
                        </div>
                    </article>
                @empty
                    <div class="text-center" style="grid-column: 1 / -1; padding: 4rem;">
                        <p class="big-text">Próximamente más contenido...</p>
                        <p>Estamos preparando algo genial.</p>
                    </div>
                @endforelse

            </div>
        </div>
    </section>
@endsection