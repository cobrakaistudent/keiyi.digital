<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} — Keiyi Academy</title>
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

        /* ── COURSE HEADER ── */
        .course-header {
            background: #1a1a1a;
            color: #fff;
            padding: 40px 48px;
            border: 3px solid #000;
            box-shadow: 6px 6px 0 #a3e635;
            margin-bottom: 40px;
        }
        .course-header-emoji { font-size: 48px; margin-bottom: 12px; }
        .course-header-title {
            font-size: 32px;
            font-weight: 800;
            text-transform: uppercase;
            line-height: 1.1;
            margin-bottom: 12px;
        }
        .course-header-title span { color: #a3e635; }
        .course-header-desc { font-size: 14px; color: #aaa; line-height: 1.6; max-width: 700px; }

        /* ── PROGRESS ── */
        .progress-section { margin-bottom: 40px; }
        .progress-bar-wrap {
            background: #e5e7eb;
            border: 3px solid #000;
            height: 16px;
            box-shadow: 3px 3px 0 #000;
        }
        .progress-bar-fill {
            background: #a3e635;
            height: 100%;
            transition: width 0.3s;
        }
        .progress-label {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        /* ── LESSONS LIST ── */
        .lessons-list { list-style: none; margin-bottom: 48px; }
        .lesson-item {
            background: #fff;
            border: 3px solid #000;
            box-shadow: 4px 4px 0 #000;
            padding: 20px 28px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .lesson-item:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0 #000;
        }
        .lesson-item.completed {
            border-color: #a3e635;
            box-shadow: 4px 4px 0 #a3e635;
        }
        .lesson-number {
            background: #1a1a1a;
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #000;
            flex-shrink: 0;
        }
        .lesson-number.done {
            background: #a3e635;
            color: #1a1a1a;
        }
        .lesson-info { flex: 1; }
        .lesson-title-text {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .lesson-title-text a {
            color: #1a1a1a;
            text-decoration: none;
        }
        .lesson-title-text a:hover { color: #a3e635; }
        .lesson-meta { display: flex; align-items: center; gap: 10px; margin-top: 4px; }
        .type-badge {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 2px 10px;
            border: 2px solid #000;
        }
        .type-lecture     { background: #e5e7eb; }
        .type-quiz        { background: #facc15; }
        .type-interactive { background: #60a5fa; color: #fff; }
        .check-done {
            font-size: 18px;
            color: #a3e635;
            font-weight: 800;
            flex-shrink: 0;
        }

        /* ── BACK LINK ── */
        .back-link {
            display: inline-block;
            margin-bottom: 32px;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1a1a1a;
            text-decoration: none;
            border: 2px solid #000;
            padding: 8px 16px;
            box-shadow: 3px 3px 0 #000;
            background: #fff;
            transition: all 0.15s;
        }
        .back-link:hover {
            transform: translate(-2px, -2px);
            box-shadow: 5px 5px 0 #000;
        }

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

        {{-- ALERTAS --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <a href="{{ route('academia.dashboard') }}" class="back-link">&larr; Mi Academia</a>

        {{-- COURSE HEADER --}}
        <div class="course-header">
            <div class="course-header-emoji">{{ $course->emoji ?? '📚' }}</div>
            <h1 class="course-header-title">{{ $course->title }}</h1>
            <p class="course-header-desc">{{ $course->description }}</p>
        </div>

        {{-- PROGRESS --}}
        <div class="progress-section">
            <span class="section-label">Tu Progreso</span>
            <div style="margin-top: 12px;">
                <div class="progress-label">{{ $enrollment->progress_percent }}% completado</div>
                <div class="progress-bar-wrap">
                    <div class="progress-bar-fill" style="width: {{ $enrollment->progress_percent }}%"></div>
                </div>
            </div>
        </div>

        {{-- LESSONS --}}
        <span class="section-label">Lecciones</span>
        <ol class="lessons-list" style="margin-top: 16px;">
            @foreach ($lessons as $index => $lesson)
                @php $isCompleted = in_array($lesson->id, $completedIds); @endphp
                <li class="lesson-item {{ $isCompleted ? 'completed' : '' }}">
                    <div class="lesson-number {{ $isCompleted ? 'done' : '' }}">
                        @if ($isCompleted)
                            ✓
                        @else
                            {{ $index + 1 }}
                        @endif
                    </div>
                    <div class="lesson-info">
                        <div class="lesson-title-text">
                            <a href="{{ route('academia.curso.leccion', [$course->slug, $lesson->slug]) }}">
                                {{ $lesson->title }}
                            </a>
                        </div>
                        <div class="lesson-meta">
                            <span class="type-badge type-{{ $lesson->type }}">{{ $lesson->type }}</span>
                        </div>
                    </div>
                    @if ($isCompleted)
                        <span class="check-done">✓</span>
                    @endif
                </li>
            @endforeach
        </ol>

    </div>

</body>
</html>
