<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados: {{ $lesson->title }} — Keiyi Academy</title>
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
        .wrap { max-width: 900px; margin: 0 auto; padding: 48px 24px; }

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

        /* ── BREADCRUMB ── */
        .breadcrumb {
            display: flex;
            gap: 8px;
            align-items: center;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        .breadcrumb a { color: #888; text-decoration: none; }
        .breadcrumb a:hover { color: #1a1a1a; }
        .breadcrumb .sep { color: #ccc; }
        .breadcrumb .current { color: #1a1a1a; }

        /* ── SCORE BANNER ── */
        .score-banner {
            padding: 40px 48px;
            border: 3px solid #000;
            margin-bottom: 32px;
            text-align: center;
        }
        .score-banner.passed {
            background: #a3e635;
            box-shadow: 6px 6px 0 #000;
        }
        .score-banner.failed {
            background: #f87171;
            color: #fff;
            box-shadow: 6px 6px 0 #000;
        }
        .score-label {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .score-banner.passed .score-label { color: #1a1a1a; }
        .score-banner.failed .score-label { color: rgba(255,255,255,0.8); }
        .score-value {
            font-size: 64px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 8px;
        }
        .score-detail {
            font-size: 16px;
            font-weight: 700;
        }
        .score-verdict {
            font-size: 24px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 16px;
            padding: 8px 24px;
            border: 3px solid #000;
            display: inline-block;
        }
        .score-banner.passed .score-verdict { background: #1a1a1a; color: #a3e635; }
        .score-banner.failed .score-verdict { background: #1a1a1a; color: #f87171; }

        /* ── QUESTION RESULTS ── */
        .question-card {
            background: #fff;
            border: 3px solid #000;
            box-shadow: 4px 4px 0 #000;
            padding: 28px;
            margin-bottom: 20px;
        }
        .question-card.correct {
            border-color: #a3e635;
            box-shadow: 4px 4px 0 #a3e635;
        }
        .question-card.wrong {
            border-color: #f87171;
            box-shadow: 4px 4px 0 #f87171;
        }
        .question-text {
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 16px;
            line-height: 1.4;
        }
        .question-status {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 3px 12px;
            border: 2px solid #000;
            display: inline-block;
            margin-bottom: 16px;
        }
        .status-correct { background: #a3e635; }
        .status-wrong   { background: #f87171; color: #fff; }

        .option-row {
            padding: 10px 16px;
            margin-bottom: 6px;
            border: 2px solid #e5e7eb;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .option-row.is-correct {
            border-color: #a3e635;
            background: #f0fdf4;
        }
        .option-row.is-wrong {
            border-color: #f87171;
            background: #fef2f2;
        }
        .option-icon { font-size: 16px; flex-shrink: 0; }

        .explanation {
            margin-top: 16px;
            padding: 14px 20px;
            background: #fffbeb;
            border: 2px solid #facc15;
            font-size: 13px;
            line-height: 1.6;
        }
        .explanation strong {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            display: block;
            margin-bottom: 4px;
            color: #92400e;
        }

        /* ── BOTONES ── */
        .actions {
            display: flex;
            gap: 16px;
            margin-top: 32px;
            flex-wrap: wrap;
        }
        .btn {
            border: 3px solid #000;
            box-shadow: 3px 3px 0 #000;
            padding: 14px 28px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            transition: all 0.15s;
        }
        .btn:hover { transform: translate(-2px, -2px); box-shadow: 5px 5px 0 #000; }
        .btn-dark  { background: #1a1a1a; color: #fff; }
        .btn-green { background: #a3e635; color: #1a1a1a; }
        .btn-yellow { background: #facc15; color: #1a1a1a; }

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

        {{-- BREADCRUMB --}}
        <div class="breadcrumb">
            <a href="{{ route('academia.dashboard') }}">Academia</a>
            <span class="sep">/</span>
            <a href="{{ route('academia.curso', $course->slug) }}">{{ $course->title }}</a>
            <span class="sep">/</span>
            <a href="{{ route('academia.curso.leccion', [$course->slug, $lesson->slug]) }}">{{ $lesson->title }}</a>
            <span class="sep">/</span>
            <span class="current">Resultados</span>
        </div>

        {{-- SCORE BANNER --}}
        <div class="score-banner {{ $passed ? 'passed' : 'failed' }}">
            <div class="score-label">Tu Puntuacion</div>
            <div class="score-value">{{ $score }}%</div>
            <div class="score-detail">{{ $correct }} de {{ $total }} respuestas correctas</div>
            <div class="score-verdict">
                @if ($passed)
                    Aprobado
                @else
                    No aprobado
                @endif
            </div>
        </div>

        {{-- QUESTION DETAILS --}}
        <span class="section-label">Detalle de Respuestas</span>
        <div style="margin-top: 16px;">
            @foreach ($results as $rIndex => $result)
                <div class="question-card {{ $result['is_correct'] ? 'correct' : 'wrong' }}">
                    <span class="question-status {{ $result['is_correct'] ? 'status-correct' : 'status-wrong' }}">
                        {{ $result['is_correct'] ? 'Correcta' : 'Incorrecta' }}
                    </span>
                    <div class="question-text">{{ $rIndex + 1 }}. {{ $result['question'] }}</div>

                    @foreach ($result['options'] as $oIndex => $option)
                        @php
                            $isCorrectOption = $oIndex === ($result['correct'] ?? $result['correct_index'] ?? -1);
                            $isUserAnswer = $oIndex === $result['user_answer'];
                            $rowClass = '';
                            $icon = '';
                            if ($isCorrectOption) {
                                $rowClass = 'is-correct';
                                $icon = '✓';
                            } elseif ($isUserAnswer && !$isCorrectOption) {
                                $rowClass = 'is-wrong';
                                $icon = '✗';
                            }
                        @endphp
                        <div class="option-row {{ $rowClass }}">
                            @if ($icon)
                                <span class="option-icon">{{ $icon }}</span>
                            @endif
                            {{ $option }}
                        </div>
                    @endforeach

                    @if (!empty($result['explanation']))
                        <div class="explanation">
                            <strong>Explicacion</strong>
                            {{ $result['explanation'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- ACTIONS --}}
        <div class="actions">
            @if ($passed)
                @if (isset($next))
                    <a href="{{ route('academia.curso.leccion', [$course->slug, $next->slug]) }}" class="btn btn-green">Continuar &rarr;</a>
                @else
                    <a href="{{ route('academia.curso', $course->slug) }}" class="btn btn-green">Volver al Curso</a>
                @endif
            @else
                <a href="{{ route('academia.curso.leccion', [$course->slug, $lesson->slug]) }}" class="btn btn-yellow">Intentar de nuevo</a>
            @endif
            <a href="{{ route('academia.curso', $course->slug) }}" class="btn btn-dark">Ver Curso</a>
        </div>

    </div>

</body>
</html>
