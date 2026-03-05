<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keiyi Agency | Creatividad Digital</title>
    <meta name="description" content="Marketing Digital que se atreve a ser diferente.">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gloria+Hallelujah&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- CSS Estático Original -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>

    <!-- Capa de Doodles -->
    <div class="doodle-bg">
        <svg class="doodle-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <path d="M10,50 Q25,25 40,50 T70,50 T100,50" fill="none" stroke="black" stroke-width="2" />
        </svg>
        <svg class="doodle-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="40" fill="none" stroke="black" stroke-width="2" stroke-dasharray="5,5" />
        </svg>
    </div>

    <!-- Navegación -->
    <nav class="navbar">
        <div class="container navbar-container">
            <a href="{{ url('/') }}" class="logo">keiyi<span class="dot">.</span></a>
            <div class="nav-right">
                <ul class="nav-links">
                    <li><a href="#value-prop">Filosofía</a></li>
                    <li><a href="#pricing">Servicios</a></li>
                    <li><a href="{{ url('/academy') }}" class="font-bold text-indigo-600">Academy</a></li>
                    <li><a href="{{ url('/3d-world') }}">3D-World</a></li>
                    <li><a href="{{ url('/blog') }}">Blog</a></li>
                    <li><a href="#contact">Contacto</a></li>
                </ul>
                <!-- Lógica de Autenticación de Laravel -->
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-nav">Ir al Panel</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-nav">Identificarse</a>
                    @endauth
                @endif
            </div>
            <div class="hamburger">
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
        </div>
    </nav>

    <!-- Header / Hero -->
    <header id="hero" class="hero" style="background-image: url('{{ asset('v12_5_hero_bg.png') }}');">
        <div class="container hero-container">
            <div class="hero-bubble">
                <div class="hero-content text-center">
                    <div class="hand-note">¡Hola mundo! ✌️</div>
                    <h1 class="hero-title">Marketing que hace <span class="highlight-scribble">POP.</span></h1>
                    <p class="hero-desc">Rompemos el molde aburrido. Creamos experiencias digitales que la gente quiere tocar, ver y compartir.</p>
                    <div class="btn-group centered">
                        <a href="#pricing" class="btn-primary">Ver Planes</a>
                        <a href="#about" class="btn-text">Descubre más ⤵</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Sección Valor -->
    <section id="value-prop" class="section">
        <div class="container">
            <div class="section-intro">
                <h2>La Fórmula <span class="funky-text">Secreta</span></h2>
                <p class="lead">Menos "blah blah", más acción.</p>
            </div>

            <div class="grid-3">
                <div class="card funky-card">
                    <div class="card-icon bg-yellow">⚡</div>
                    <h3>Rápidos</h3>
                    <p>Nos movemos a la velocidad de internet. Sin burocracia, solo envíos.</p>
                </div>
                <div class="card funky-card">
                    <div class="card-icon bg-blue">💎</div>
                    <h3>Reales</h3>
                    <p>Precios claros. Sin letras chiquitas. Lo que ves es lo que hay.</p>
                </div>
                <div class="card funky-card">
                    <div class="card-icon bg-pink">🚀</div>
                    <h3>Flexibles</h3>
                    <p>Escalamos contigo. Desde startups hasta imperios.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="section">
        <div class="container">
            <div class="section-intro text-center">
                <h2>Elige tu Kit</h2>
                <div class="squiggly-line"></div>
            </div>

            <div class="pricing-grid">
                <div class="price-box">
                    <div class="price-header">
                        <h4>Básico</h4>
                        <h3>Despegue</h3>
                    </div>
                    <ul class="check-list">
                        <li>Identidad de Marca</li>
                        <li>Landing Page</li>
                        <li>Setup de Redes</li>
                    </ul>
                    <a href="#contact" class="btn-outline">Elegir</a>
                </div>

                <div class="price-box featured">
                    <span class="pop-tag">Fan Favorite</span>
                    <div class="price-header">
                        <h4>Crecimiento</h4>
                        <h3>Impulso</h3>
                    </div>
                    <ul class="check-list">
                        <li>Community Manager</li>
                        <li>15 Posts/Mes</li>
                        <li>Reporte Mensual</li>
                    </ul>
                    <a href="#contact" class="btn-primary full">¡Lo Quiero!</a>
                </div>

                <div class="price-box">
                    <div class="price-header">
                        <h4>Escala</h4>
                        <h3>Dominio</h3>
                    </div>
                    <ul class="check-list">
                        <li>Estrategia Semanal</li>
                        <li>Web Avanzada</li>
                        <li>Ads Management</li>
                    </ul>
                    <a href="#contact" class="btn-outline">Elegir</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 3D World Teaser -->
    <section id="3d-teaser" class="section">
        <div class="container">
            <div class="section-intro text-center">
                <span class="hand-note">¡Nuevo! ✨</span>
                <h2>3D <span class="highlight-scribble">World</span></h2>
                <p class="lead">Del cerebro a la realidad. Fabricamos tus ideas.</p>
            </div>

            <div class="grid-3">
                <div class="card funky-card">
                    <div class="step-number">01</div>
                    <div class="card-icon bg-yellow">🧠</div>
                    <h3>Imagina</h3>
                    <p>Si puedes soñarlo, existe. Tráenos tu idea loca, boceto o servilleta.</p>
                </div>
                <div class="card funky-card">
                    <div class="step-number">02</div>
                    <div class="card-icon bg-blue">📐</div>
                    <h3>Diseña</h3>
                    <p>Modelado 3D profesional. Convertimos conceptos abstractos en polígonos tangibles.</p>
                </div>
                <div class="card funky-card">
                    <div class="step-number">03</div>
                    <div class="card-icon bg-pink">🖨️</div>
                    <h3>Imprime</h3>
                    <p>Materializamos tu visión. Calidad brutal, materiales resistentes y entrega rápida.</p>
                </div>
            </div>
            
            <div class="btn-group centered">
                <a href="{{ url('/3d-world') }}" class="btn-primary">Entrar al Lab 3D</a>
            </div>
        </div>
    </section>

    <!-- Keiyi Academy Detailed -->
    <section id="academy-teaser" class="section bg-gray-50 border-y-2 border-indigo-100">
        <div class="container">
            <div class="section-intro text-center">
                <span class="hand-note">Aprende con los Pro ✨</span>
                <h2>Keiyi <span class="highlight-scribble">Academy</span></h2>
                <p class="lead">Domina la IA y el Marketing de Élite con nuestros talleres prácticos.</p>
            </div>

            <div class="grid-3 mb-16">
                <div class="card academy-card">
                    <div class="card-icon bg-blue">🧠</div>
                    <span class="tag">Elite</span>
                    <h3>Marketing Elite</h3>
                    <p>Sistemas de venta automatizados, ManyChat, Make y Claude 3.5 en un solo flujo de profit.</p>
                </div>
                <div class="card academy-card">
                    <div class="card-icon bg-pink">🎬</div>
                    <span class="tag">Viral</span>
                    <h3>Contenido Viral</h3>
                    <p>Producción masiva de Reels y TikTok con IA. Domina el algoritmo en 30 minutos.</p>
                </div>
                <div class="card academy-card">
                    <div class="card-icon bg-yellow">⚡</div>
                    <span class="tag">Pro</span>
                    <h3>Productividad IA</h3>
                    <p>Optimiza tu sistema operativo y tu agenda para trabajar solo 4 horas al día.</p>
                </div>
            </div>

            <!-- Academy Membership Box -->
            <div class="academy-membership-box">
                <div class="membership-header">
                    <h3>Pase de Acceso Total</h3>
                    <div class="price">
                        <span class="currency">$</span>
                        <span class="amount">49</span>
                        <span class="period">/ mes</span>
                    </div>
                </div>
                <div class="membership-body">
                    <ul class="check-list-grid">
                        <li><span class="check">✔</span> Los 3 Talleres Completos (15+ Lecciones)</li>
                        <li><span class="check">✔</span> Banco de Prompts Maestro (Actualizado 2026)</li>
                        <li><span class="check">✔</span> Cheat Sheets Descargables por taller</li>
                        <li><span class="check">✔</span> Actualizaciones cada 30 días según tendencias</li>
                        <li><span class="check">✔</span> Scripts de vídeo listos para producir</li>
                        <li><span class="check">✔</span> Acceso a la Comunidad Elite en Telegram</li>
                    </ul>
                </div>
                <div class="membership-footer">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary full">Entrar a tus Clases</a>
                    @else
                        <a href="{{ route('register') }}" class="btn-primary full">Registrarme y Empezar Ahora</a>
                    @endauth
                    <p class="text-xs text-gray-400 mt-4">Sujeto a aprobación por el administrador. Membresía mensual sin permanencia.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About -->
    <section id="about" class="section text-center">
        <div class="container">
            <div class="blob-bg"></div>
            <div class="about-content">
                <h2>¿Quiénes Somos?</h2>
                <p class="big-text">Somos los nerds cool que conectan el caos creativo con resultados de negocio.</p>
                <div class="mission-statement">
                    <strong>Misión:</strong> Democratizar el diseño pro.
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <h3>keiyi.</h3>
            </div>
            <div class="footer-links">
                <a href="#" class="footer-icon icon-insta" aria-label="Instagram">
                    <!-- Instagram SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                    </svg>
                </a>
                <a href="#" class="footer-icon icon-email" aria-label="Email">
                    <!-- Mail SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </a>
                <a href="#" class="footer-icon icon-telegram" aria-label="Telegram">
                    <!-- Telegram SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </a>
                <a href="#" class="footer-icon icon-whatsapp" aria-label="WhatsApp">
                    <!-- WhatsApp SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                        </path>
                    </svg>
                </a>
            </div>
            <div class="footer-copy">
                <p>© 2026 Keiyi Agency & Academy</p>
            </div>
        </div>
    </footer>

    <!-- JS Estático Original -->
    <script src="{{ asset('script.js') }}"></script>
</body>

</html>
