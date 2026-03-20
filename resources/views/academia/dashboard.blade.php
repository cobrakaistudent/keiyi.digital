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

        /* ── ALERTAS ── */
        .alert {
            padding: 16px 24px;
            border: 3px solid #000;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 24px;
        }
        .alert-success { background: #a3e635; box-shadow: 3px 3px 0 #000; }
        .alert-info    { background: #facc15; box-shadow: 3px 3px 0 #000; }
        .alert-error   { background: #f87171; color: #fff; box-shadow: 3px 3px 0 #000; }

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

        /* ── SECCION TITULO ── */
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

        /* ── STATS ── */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
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
        .course-card.enrolled {
            border-color: #a3e635;
            box-shadow: 4px 4px 0 #a3e635;
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
        .tag-prereq     { background: #a3e635; }
        .tag-proximo    { background: #facc15; }
        .tag-inscrito   { background: #60a5fa; color: #fff; }

        .course-emoji { font-size: 40px; line-height: 1; }
        .course-title { font-size: 18px; font-weight: 800; text-transform: uppercase; line-height: 1.2; }
        .course-desc  { font-size: 13px; color: #555; line-height: 1.6; }

        .progress-bar-wrap {
            background: #e5e7eb;
            border: 2px solid #000;
            height: 10px;
        }
        .progress-bar-fill {
            background: #a3e635;
            height: 100%;
        }
        .progress-label { font-size: 11px; font-weight: 700; color: #555; }

        /* ── BOTONES ── */
        .btn-curso {
            margin-top: auto;
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
            width: 100%;
        }
        .btn-dark  { background: #1a1a1a; color: #fff; }
        .btn-green { background: #a3e635; color: #1a1a1a; }
        .btn-curso:hover { transform: translate(-2px, -2px); box-shadow: 5px 5px 0 #000; }
        .btn-disabled {
            background: #e5e7eb;
            color: #aaa;
            cursor: not-allowed;
            box-shadow: 2px 2px 0 #ccc;
            border-color: #ccc;
        }
        .btn-disabled:hover { transform: none; box-shadow: 2px 2px 0 #ccc; }

        /* ── AVISO ── */
        .aviso {
            background: #facc15;
            border: 3px solid #000;
            box-shadow: 4px 4px 0 #000;
            padding: 24px 28px;
        }
        .aviso p { font-size: 13px; font-weight: 700; line-height: 1.7; }

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

        {{-- ALERTAS DE SESION --}}
        @if (session('enrolled'))
            <div class="alert alert-success">
                Inscripcion exitosa en <strong>{{ session('enrolled') }}</strong>. Te enviamos un correo de confirmacion.
            </div>
        @endif
        @if (session('already_enrolled'))
            <div class="alert alert-info">
                Ya estas inscrito en <strong>{{ session('already_enrolled') }}</strong>.
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        {{-- HERO --}}
        <div class="hero">
            <div>
                <p class="hero-label">Portal del Alumno</p>
                <h1 class="hero-title">Hola, <span>{{ explode(' ', Auth::user()->name)[0] }}</span>.</h1>
                <p class="hero-sub">Bienvenido a tu espacio de aprendizaje en Keiyi Digital.</p>
            </div>
            <div class="badge-activo">Acceso Activo</div>
        </div>

        {{-- STATS --}}
        <span class="section-label">Tu Progreso</span>
        <div class="info-grid" style="margin-top: 16px;">
            <div class="info-card" style="background: #a3e635;">
                <div class="info-card-label">Cursos Inscritos</div>
                <div class="info-card-value">{{ $enrollments->count() }}</div>
            </div>
            <div class="info-card" style="background: #facc15;">
                <div class="info-card-label">En Progreso</div>
                <div class="info-card-value">{{ $enrollments->filter(fn($e) => $e->progress_percent > 0 && $e->progress_percent < 100)->count() }}</div>
            </div>
            <div class="info-card" style="background: #fff;">
                <div class="info-card-label">Completados</div>
                <div class="info-card-value">{{ $enrollments->filter(fn($e) => $e->progress_percent >= 100)->count() }}</div>
            </div>
            <div class="info-card" style="background: #1a1a1a; color: #fff; border-color: #a3e635; box-shadow: 3px 3px 0 #a3e635;">
                <div class="info-card-label" style="color: #a3e635;">Miembro desde</div>
                <div class="info-card-value" style="font-size: 16px;">{{ Auth::user()->created_at->format('M Y') }}</div>
            </div>
        </div>

        {{-- CURSOS PUBLICADOS --}}
        <span class="section-label">Talleres Disponibles</span>
        <div class="courses-grid" style="margin-top: 16px;">

            @foreach ($courses as $course)
                @php
                    $enrollment = $enrollments->get($course->slug);
                    $enrolled = !is_null($enrollment);
                @endphp
                <div class="course-card {{ $enrolled ? 'enrolled' : '' }}">

                    @if ($enrolled)
                        <span class="course-tag tag-inscrito">Inscrito</span>
                    @else
                        <span class="course-tag tag-prereq">{{ $course->tag ?? 'Disponible' }}</span>
                    @endif

                    <span class="course-emoji">{{ $course->emoji ?? '📚' }}</span>
                    <div class="course-title">{{ $course->title }}</div>
                    <div class="course-desc">{{ $course->description }}</div>

                    @if ($enrolled)
                        <div>
                            <div class="progress-label" style="margin-bottom: 4px;">
                                {{ $enrollment->progress_percent }}% completado
                            </div>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar-fill" style="width: {{ $enrollment->progress_percent }}%"></div>
                            </div>
                        </div>
                        <a href="{{ route('academia.curso', $course->slug) }}" class="btn-curso btn-green">Ver Curso</a>
                    @else
                        <form method="POST" action="{{ route('academia.enroll', $course->slug) }}">
                            @csrf
                            <button type="submit" class="btn-curso btn-dark">Inscribirme</button>
                        </form>
                    @endif

                </div>
            @endforeach

        </div>

        {{-- CURSOS LEGACY / PROXIMAMENTE --}}
        @if ($legacyCourses->count() > 0)
            <span class="section-label">Proximamente</span>
            <div class="courses-grid" style="margin-top: 16px;">

                @foreach ($legacyCourses as $course)
                    <div class="course-card">
                        <span class="course-tag tag-proximo">Proximamente</span>
                        <span class="course-emoji">{{ $course->emoji ?? '🔒' }}</span>
                        <div class="course-title">{{ $course->title }}</div>
                        <div class="course-desc">{{ $course->description }}</div>
                        <span class="btn-curso btn-disabled">Contenido en preparacion</span>
                    </div>
                @endforeach

            </div>
        @endif

        {{-- AVISO --}}
        <div class="aviso">
            <p>
                <strong>Estamos construyendo el contenido.</strong>
                Al inscribirte registras tu interes y seras el primero en recibir acceso cuando el taller este listo.
                Revisamos tu correo electronico periodicamente para notificaciones.
            </p>
        </div>

    </div>

</body>
</html>
