<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Keiyi Agency | Creatividad Digital')</title>
    <meta name="description" content="@yield('description', 'Marketing Digital que se atreve a ser diferente.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gloria+Hallelujah&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Laravel Asset Helper -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body>

    <!-- Doodle Background Elements -->
    <div class="doodle-bg">
        <svg class="doodle-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <path d="M10,50 Q25,25 40,50 T70,50 T100,50" fill="none" stroke="black" stroke-width="2" />
        </svg>
        <svg class="doodle-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="40" fill="none" stroke="black" stroke-width="2" stroke-dasharray="5,5" />
        </svg>
    </div>

    <nav class="navbar">
        <div class="container navbar-container">
            <a href="{{ route('home') }}" class="logo">keiyi<span class="dot">.</span></a>
            <div class="nav-right">
                <ul class="nav-links">
                    <li><a href="{{ route('home') }}#value-prop">Filosofía</a></li>
                    <li><a href="{{ route('home') }}#pricing">Servicios</a></li>
                    <li><a href="{{ route('3d-world') }}" style="{{ Request::routeIs('3d-world') ? 'text-decoration: underline; text-decoration-color: var(--color-orange);' : '' }}">3D-World</a></li>
                    <li><a href="{{ route('blog') }}" style="{{ Request::routeIs('blog') ? 'text-decoration: underline; text-decoration-color: var(--color-orange);' : '' }}">Blog</a></li>
                    <li><a href="#contact">Contacto</a></li>
                </ul>
                <a href="#contact" class="btn-nav">Hablemos</a>
            </div>
            <div class="hamburger">
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
        </div>
    </nav>

    <!-- Content Injection Point -->
    <main>
        @yield('content')
    </main>

    <footer id="contact" class="footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <h3>keiyi.</h3>
            </div>
            <div class="footer-links">
                <a href="#" class="footer-icon icon-insta" aria-label="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                </a>
                <a href="#" class="footer-icon icon-email" aria-label="Email">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                </a>
                <a href="#" class="footer-icon icon-telegram" aria-label="Telegram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </a>
                <a href="#" class="footer-icon icon-whatsapp" aria-label="WhatsApp">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                </a>
            </div>
            <div class="footer-copy">
                <p>© 2025 Keiyi Agency</p>
            </div>
        </div>
    </footer>

    <script src="{{ asset('script.js') }}"></script>
</body>
</html>