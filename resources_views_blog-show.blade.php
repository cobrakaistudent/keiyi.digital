@extends('layouts.app')

@section('title', $post->title . ' | Keiyi Blog')

@section('content')
    <article class="section">
        <div class="container" style="max-width: 800px;">
            
            <header class="text-center" style="margin-bottom: 3rem;">
                <span class="blog-date">{{ $post->created_at->format('d M, Y') }}</span>
                <h1 class="hero-title" style="font-size: 3rem;">{{ $post->title }}</h1>
                <div class="squiggly-line"></div>
            </header>

            @if($post->image)
                <div class="blog-card" style="margin-bottom: 3rem; box-shadow: 10px 10px 0 var(--color-blue);">
                    <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" style="width: 100%; height: auto; display: block;">
                </div>
            @endif

            <div class="blog-full-content" style="font-size: 1.2rem; line-height: 1.8; color: var(--color-navy);">
                {!! nl2br(e($post->content)) !!}
            </div>

            <div style="margin-top: 5rem; padding-top: 2rem; border-top: 2px dashed var(--color-navy);">
                <a href="{{ route('blog') }}" class="read-more" style="font-size: 1.1rem;">← Volver al Blog</a>
            </div>

        </div>
    </article>
@endsection