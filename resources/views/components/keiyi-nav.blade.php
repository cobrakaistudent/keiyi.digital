{{-- Navbar reutilizable para todas las páginas públicas de Keiyi Digital --}}
@props(['transparent' => false])

<nav class="navbar" @if($transparent) style="background:transparent;border:none;position:absolute;width:100%;z-index:100;" @endif>
    <div class="container navbar-container">
        <a href="{{ url('/') }}" class="logo">keiyi<span class="dot">.</span></a>
        <div class="nav-right">
            <ul class="nav-links">
                <li><a href="{{ url('/') }}#value-prop" @class(['active' => request()->is('/')])>Filosofía</a></li>
                <li><a href="{{ url('/academy') }}" @class(['active' => request()->is('academy')])>Academy</a></li>
                <li><a href="{{ url('/blog') }}" @class(['active' => request()->is('blog*')])>Blog</a></li>
                <li><a href="{{ url('/3d-world') }}" @class(['active' => request()->is('3d-world*')])>3D World</a></li>
                <li><a href="{{ url('/') }}#contact">Contacto</a></li>
            </ul>
            @auth
                <a href="{{ route('academia.dashboard') }}" class="btn-nav">Mi Academia</a>
            @else
                <a href="{{ route('login') }}" class="btn-nav">Identificarse</a>
            @endauth
        </div>
        <div class="hamburger" onclick="this.classList.toggle('open');document.querySelector('.nav-right').classList.toggle('nav-open');">
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
    </div>
</nav>
