<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Academia — Keiyi Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Space Grotesk', sans-serif;
            background: #f4efeb;
            color: #1a1a1a;
            min-height: 100vh;
        }

        /* ── NAV ── */
        nav {
            background: #1a1a1a;
            padding: 0 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 64px;
            border-bottom: 3px solid #a3e635;
        }
        .nav-logo {
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
        }
        .nav-logo span { color: #a3e635; }
        .nav-links { display: flex; align-items: center; gap: 24px; }
        .nav-links a {
            color: #aaa;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: color 0.15s;
        }
        .nav-links a:hover { color: #fff; }
        .nav-user {
            background: #a3e635;
            color: #1a1a1a;
            font-weight: 800;
            font-size: 12px;
            text-transform: uppercase;
            padding: 6px 16px;
            border: 2px solid #000;
            box-shadow: 2px 2px 0 #000;
            letter-spacing: 1px;
        }

        /* ── LAYOUT ── */
        .wrap { max-width: 1100px; margin: 0 auto; padding: 48px 24px; }

        /* ── HERO BIENVENIDA ── */
        .hero {
            background: #1a1a1a;
            color: #fff;
            padding: 40px 48px;
            border: 3px solid #000;
            box-shadow: 6px 6px 0 #a3e635;
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }
        .hero-label {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 3px;
            color: #a3e635;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .hero-title {
            font-size: 36px;
            font-weight: 800;
            text-transform: uppercase;
            line-height: 1.1;
        }
        .hero-title span { color: #a3e635; }
        .hero-sub { font-size: 14px; color: #aaa; margin-top: 8px; }
        .badge-activo {
            background: #a3e635;
            color: #1a1a1a;
            font-weight: 800;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 20px;
            border: 3px solid #000;
            box-shadow: 3px 3px 0 #000;
            white-space: nowrap;
        }

        /* ── SECCIÓN TÍTULO ── */
        .section-label {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            background: #1a1a1a;
            color: #facc15;
            display: inline-block;
            padding: 4px 14px;
            margin-bottom: 20px;
        }

        /* ── GRID CURSOS ── */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 48px;
        }

        .course-card {
            background: #fff;
            border: 3px solid #000;
            box-shadow: 4px 4px 0 #000;
            padding: 28px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .course-card:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0 #000;
        }
        .course-tag {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 3px 10px;
            border: 2px solid #000;
            display: inline-block;
            width: fit-content;
        }
        .tag-proximamente { background: #facc15; }
        .tag-nuevo { background: #a3e635; }
        .tag-progreso { background: #60a5fa; color: #fff; }

        .course-emoji { font-size: 40px; line-height: 1; }
        .course-title { font-size: 18px; font-weight: 800; text-transform: uppercase; line-height: 1.2; }
        .course-desc { font-size: 13px; color: #555; line-height: 1.6; }

        .progress-bar-wrap {
            background: #e5e7eb;
            border: 2px solid #000;
            height: 10px;
        }
        .progress-bar-fill {
            background: #a3e635;
            height: 100%;
            transition: width 0.5s;
        }
        .progress-label { font-size: 11px; font-weight: 700; color: #555; }

        .btn-curso {
            margin-top: auto;
            background: #1a1a1a;
            color: #fff;
            border: 2px solid #000;
            box-shadow: 3px 3px 0 #000;
            padding: 10px 20px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: block;
            transition: all 0.15s;
        }
        .btn-curso:hover { transform: translate(-2px, -2px); box-shadow: 5px 5px 0 #000; }
        .btn-curso.disabled {
            background: #e5e7eb;
            color: #aaa;
            cursor: not-allowed;
            box-shadow: 2px 2px 0 #ccc;
            border-color: #ccc;
        }
        .btn-curso.disabled:hover { transform: none; box-shadow: 2px 2px 0 #ccc; }

        /* ── TARJETA INFO ── */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 48px;
        }
        .info-card {
            border: 3px solid #000;
            padding: 20px 24px;
            box-shadow: 3px 3px 0 #000;
        }
        .info-card-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #888;
            margin-bottom: 6px;
        }
        .info-card-value { font-size: 22px; font-weight: 800; }

        /* ── LOGOUT ── */
        .logout-form { display: inline; }
        .btn-logout {
            background: transparent;
            border: none;
            color: #aaa;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: color 0.15s;
        }
        .btn-logout:hover { color: #fff; }
    </style>
</head>
<body>

    <nav>
        <a href="{{ url('/') }}" class="nav-logo">Keiyi <span>Academy</span></a>
        <div class="nav-links">
            <a href="{{ route('academia.dashboard') }}">Mi Academia</a>
            <span class="nav-user">{{ Auth::user()->name }}</span>
            <form class="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">Salir</button>
            </form>
        </div>
    </nav>

    <div class="wrap">

        {{-- HERO --}}
        <div class="hero">
            <div>
                <p class="hero-label">Portal del Alumno</p>
                <h1 class="hero-title">Hola, <span>{{ explode(' ', Auth::user()->name)[0] }}</span>.</h1>
                <p class="hero-sub">Bienvenido a tu espacio de aprendizaje en Keiyi Digital.</p>
            </div>
            <div class="badge-activo">✓ Acceso Activo</div>
        </div>

        {{-- STATS RÁPIDOS --}}
        <span class="section-label">Tu Progreso</span>
        <div class="info-grid" style="margin-top: 16px;">
            <div class="info-card" style="background: #a3e635;">
                <div class="info-card-label">Cursos Inscritos</div>
                <div class="info-card-value">{{ $enrollments->count() }}</div>
            </div>
            <div class="info-card" style="background: #facc15;">
                <div class="info-card-label">En Progreso</div>
                <div class="info-card-value">{{ $enrollments->where('progress_percent', '>', 0)->where('progress_percent', '<', 100)->count() }}</div>
            </div>
            <div class="info-card" style="background: #fff;">
                <div class="info-card-label">Completados</div>
                <div class="info-card-value">{{ $enrollments->where('progress_percent', 100)->count() }}</div>
            </div>
            <div class="info-card" style="background: #1a1a1a; color: #fff; border-color: #a3e635; box-shadow: 3px 3px 0 #a3e635;">
                <div class="info-card-label" style="color: #a3e635;">Miembro desde</div>
                <div class="info-card-value" style="font-size: 16px;">{{ Auth::user()->created_at->format('M Y') }}</div>
            </div>
        </div>

        {{-- CURSOS --}}
        <span class="section-label">Talleres Disponibles</span>
        <div class="courses-grid" style="margin-top: 16px;">

            {{-- Taller 0 --}}
            <div class="course-card">
                <span class="course-tag tag-nuevo">Pre-requisito</span>
                <span class="course-emoji">🤖</span>
                <div class="course-title">Taller 0: IA Origins & Motor Agéntico</div>
                <div class="course-desc">Desmitifica la IA: Tokens, Modelos de Razonamiento, MCP y Arquitectura Agéntica. 3 días intensivos.</div>
                <div>
                    <div class="progress-label" style="margin-bottom: 4px;">0% completado</div>
                    <div class="progress-bar-wrap"><div class="progress-bar-fill" style="width: 0%"></div></div>
                </div>
                <a href="#" class="btn-curso disabled">Próximamente</a>
            </div>

            {{-- Taller 1 --}}
            <div class="course-card">
                <span class="course-tag tag-proximamente">Próximamente</span>
                <span class="course-emoji">🗺️</span>
                <div class="course-title">Taller 1: El Mapa de la IA (Ecosistema)</div>
                <div class="course-desc">Navega el ecosistema completo de herramientas de IA para marketing y agencias digitales en 2026.</div>
                <a href="#" class="btn-curso disabled">Próximamente</a>
            </div>

            {{-- Taller 2 --}}
            <div class="course-card">
                <span class="course-tag tag-proximamente">Próximamente</span>
                <span class="course-emoji">⚡</span>
                <div class="course-title">Taller 2: Prompt Engineering Masterclass</div>
                <div class="course-desc">De prompts básicos a ingeniería de contexto avanzada. Técnicas de Chain-of-Thought, RAG y control de salida.</div>
                <a href="#" class="btn-curso disabled">Próximamente</a>
            </div>

            {{-- Taller Marketing Elite --}}
            <div class="course-card">
                <span class="course-tag tag-proximamente">Próximamente</span>
                <span class="course-emoji">🚀</span>
                <div class="course-title">Marketing Elite 2026</div>
                <div class="course-desc">GEO, Performance, LTV/CAC, automatización con IA y casos reales Latam. El curso que Harvard no tiene.</div>
                <a href="#" class="btn-curso disabled">Próximamente</a>
            </div>

        </div>

        {{-- AVISO --}}
        <div style="background: #facc15; border: 3px solid #000; box-shadow: 4px 4px 0 #000; padding: 24px 28px;">
            <p style="font-size: 13px; font-weight: 700; line-height: 1.7;">
                <strong>🔔 Estamos construyendo el contenido.</strong>
                Te notificaremos por correo en cuanto el primer taller esté disponible.
                Mientras tanto, tu acceso está activo y listo.
            </p>
        </div>

    </div>

</body>
</html>
