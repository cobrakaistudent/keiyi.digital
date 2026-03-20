<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keiyi Agency | Blog de Atracción</title>
    <meta name="description" content="Tendencias escalables y crecimiento acelerado con IA.">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gloria+Hallelujah&family=Space+Grotesk:wght@400;500;700;900&display=swap" rel="stylesheet">
    <!-- CSS Estático Original de Keiyi -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    
    <style>
        /* Animación Temblor Controlado solicitada por el CEO */
        @keyframes shake-wiggle {
            0%, 100% { transform: rotate(-1deg); }
            50% { transform: rotate(1.5deg) translateY(-5px); }
        }
        .shake-hover:hover {
            animation: shake-wiggle 0.3s ease-in-out 2;
        }
        .keyword-tag {
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            padding: 4px 10px;
            border: 2px solid var(--color-navy);
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 15px;
            box-shadow: 2px 2px 0 var(--color-navy);
        }
    </style>
</head>

<body>
    <!-- Capa de Doodles (Opcional, misma vibra que welcome) -->
    <div class="doodle-bg">
        <svg class="doodle-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <path d="M10,50 Q25,25 40,50 T70,50 T100,50" fill="none" stroke="black" stroke-width="2" />
        </svg>
    </div>

    <!-- Navegación Original de Keiyi -->
    <nav class="navbar" style="border-bottom: 4px solid var(--color-navy); background: white; position: sticky; top: 0; z-index: 100; padding: 1rem 0;">
        <div class="container navbar-container">
            <a href="{{ url('/') }}" class="logo">keiyi<span class="dot">.</span></a>
            <div class="nav-right">
                <ul class="nav-links">
                    <li><a href="{{ url('/#value-prop') }}">Filosofía</a></li>
                    <li><a href="{{ url('/#pricing') }}">Servicios</a></li>
                    <li><a href="{{ url('/academy') }}">Academy</a></li>
                    <li><a href="{{ url('/3d-world') }}">3D-World</a></li>
                    <li><a href="{{ url('/blog-proposal') }}" style="color: var(--color-orange); text-decoration: underline wavy;">Blog</a></li>
                    <li><a href="{{ url('/#contact') }}">Contacto</a></li>
                </ul>
                @auth
                    <a href="{{ route('academia.dashboard') }}" class="btn-nav">Mi Academia</a>
                @else
                    <a href="{{ route('login') }}" class="btn-nav">Identificarse</a>
                @endauth
            </div>
            <div class="hamburger">
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
        </div>
    </nav>

    <!-- Header del Blog -->
    <section class="section" style="padding-bottom: 2rem;">
        <div class="container">
            <div class="section-intro text-center">
                <span class="hand-note">Inteligencia Pura ✨</span>
                <h2>Keiyi Brain <span class="highlight-scribble">Blog</span></h2>
                <p class="lead">Tendencias escalables y crecimiento acelerado con Inteligencia Artificial.</p>
            </div>
        </div>
    </section>

    <!-- Content / Articles & Sidebar -->
    <section class="section" style="padding-top: 0; background-color: var(--color-bg);">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr; gap: 3rem; @media(min-width: 992px) { grid-template-columns: 2fr 1fr; }">
                
                <!-- Columna Izquierda: Los Artículos (El 70% del ancho en Desktop) -->
                <div>
                    <!-- Añadimos un flex-col para que las cards se apilen en vez de usar el grid-3 completo -->
                    <div style="display: flex; flex-direction: column; gap: 3rem;">
                        
                        <!-- Tarjeta 1: IA & Ventas -->
                        <div class="card funky-card shake-hover" style="border-top-color: var(--color-blue); border-top-width: 6px;">
                            <div class="keyword-tag" style="background-color: var(--color-blue);">🏷️ IA & Ventas</div>
                            <h3>El engaño de ChatGPT y por qué necesitas Agentes Locales.</h3>
                            <p>Dejar que OpenAI controle la data de tu negocio es un riesgo 2026. Montar clústeres locales en Mac te da la paz, la seguridad y el cero-costo mensual para operar automatizaciones sin límites.</p>
                            <div style="margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; border-top: 2px dashed var(--color-navy); padding-top: 1rem;">
                                <span style="font-family: var(--font-hand); color: var(--color-navy); font-size: 0.9rem;">06 Mar 2026</span>
                                <a href="#" class="btn-text" style="margin-left:0;">Leer Más ⤵</a>
                            </div>
                        </div>

                        <!-- Tarjeta 2: Content Marketing -->
                        <div class="card funky-card shake-hover" style="border-top-color: var(--color-pink); border-top-width: 6px;">
                            <div class="keyword-tag" style="background-color: var(--color-pink);">🏷️ Crecimiento Viral</div>
                            <h3>Vibe Coding: La técnica secreta contra el scroll infinito.</h3>
                            <p>Si tu post parece genérico, nadie lo leerá. Aplicar metodologías de diseño asimétrico o "nichebending" con IA te diferenciará inmediatamente de los competidores aburridos.</p>
                            <div style="margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; border-top: 2px dashed var(--color-navy); padding-top: 1rem;">
                                <span style="font-family: var(--font-hand); color: var(--color-navy); font-size: 0.9rem;">05 Mar 2026</span>
                                <a href="#" class="btn-text" style="margin-left:0;">Leer Más ⤵</a>
                            </div>
                        </div>

                        <!-- Tarjeta 3: Breaking News -->
                        <div class="card funky-card shake-hover" style="border-top-color: var(--color-orange); border-top-width: 6px;">
                            <div class="keyword-tag" style="background-color: var(--color-orange);">🚨 Breaking News</div>
                            <h3>El fin de los Prompt Engineers tradicionales está aquí.</h3>
                            <p>Los modelos de razonamiento profundo escriben prompt mejor que tú. La verdadera habilidad ahora es el "Agent Orchestration" y el Master en flujos de Make.com.</p>
                            <div style="margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; border-top: 2px dashed var(--color-navy); padding-top: 1rem;">
                                <span style="font-family: var(--font-hand); color: var(--color-navy); font-size: 0.9rem;">04 Mar 2026</span>
                                <a href="#" class="btn-text" style="margin-left:0;">Leer Más ⤵</a>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Columna Derecha: Sidebar Radial (El Widget Agéntico de Gemini) -->
                <div>
                    <div class="card funky-card" style="position: sticky; top: 120px; background-color: white; border: 4px solid var(--color-navy); padding: 2rem;">
                        <!-- Insignia del Radar -->
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                            <div style="width: 15px; height: 15px; background-color: var(--color-orange); border-radius: 50%; animation: pulse-radar 2s infinite; border: 2px solid var(--color-navy);"></div>
                            <h3 style="margin: 0; font-size: 1.3rem; text-transform: uppercase;">Radar de Inteligencia</h3>
                        </div>
                        
                        <p style="font-family: var(--font-hand); color: var(--color-navy); font-size: 1rem; margin-bottom: 20px; line-height: 1.3;">
                            Curaduría diaria por <span style="color: var(--color-blue); font-weight: bold;">DeepScout</span>. Validado por el CEO.
                        </p>

                        <!-- Elementos del Radar -->
                        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 15px;">
                            
                            <!-- Ítem 1 -->
                            <li style="border-left: 4px solid var(--color-pink); padding-left: 15px;">
                                <span style="display: block; font-size: 0.8rem; font-weight: 800; color: #666;">HACE 12 HORAS</span>
                                <strong style="display: block; font-size: 1.1rem; line-height: 1.2; margin-bottom: 5px;">Saturación de Modelos de Lenguaje</strong>
                                <span style="font-size: 0.95rem; color: #444;">No es una "estafa", pero depender solo de LLMs sin conectar herramientas externas (APIs) limita el ROI dramáticamente. El foco migra hacia la Acción.</span>
                            </li>

                            <!-- Ítem 2 -->
                            <li style="border-left: 4px solid var(--color-yellow); padding-left: 15px;">
                                <span style="display: block; font-size: 0.8rem; font-weight: 800; color: #666;">HACE 24 HORAS</span>
                                <strong style="display: block; font-size: 1.1rem; line-height: 1.2; margin-bottom: 5px;">Caída en el Alcance Orgánico de Instagram</strong>
                                <span style="font-size: 0.95rem; color: #444;">El algoritmo prioriza formatos largos sobre micro-reels. Necesitas sistemas de retención, no solo ganchos de 3 segundos.</span>
                            </li>

                        </ul>

                        <!-- Estilo de Pulso para el Radar -->
                        <style>
                            @keyframes pulse-radar {
                                0% { box-shadow: 0 0 0 0 rgba(255, 127, 80, 0.7); }
                                70% { box-shadow: 0 0 0 10px rgba(255, 127, 80, 0); }
                                100% { box-shadow: 0 0 0 0 rgba(255, 127, 80, 0); }
                            }
                            /* Pequeño hack para grid responsive en el div sin crear clases nuevas */
                            @media (min-width: 992px) {
                                .container > div {
                                    grid-template-columns: 2fr 1fr !important;
                                }
                            }
                        </style>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer Original de Keiyi -->
    <footer class="footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <h3>keiyi.</h3>
            </div>
            <div class="footer-links">
                <a href="#" class="footer-icon icon-insta" aria-label="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
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

