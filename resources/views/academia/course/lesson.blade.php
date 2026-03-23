<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $lesson->title }} — {{ $course->title }} — Keiyi Academy</title>
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
        .layout-grid {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 32px;
            align-items: start;
        }
        @media (max-width: 800px) {
            .layout-grid { grid-template-columns: 1fr; }
            .sidebar { order: -1; }
        }

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

        /* ── LESSON HEADER ── */
        .lesson-header {
            background: #1a1a1a;
            color: #fff;
            padding: 32px 40px;
            border: 3px solid #000;
            box-shadow: 6px 6px 0 #a3e635;
            margin-bottom: 32px;
        }
        .lesson-header-title {
            font-size: 28px;
            font-weight: 800;
            text-transform: uppercase;
            line-height: 1.1;
            margin-bottom: 12px;
        }
        .type-badge {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 3px 12px;
            border: 2px solid #000;
            display: inline-block;
        }
        .type-lecture     { background: #e5e7eb; color: #1a1a1a; }
        .type-quiz        { background: #facc15; color: #1a1a1a; }
        .type-interactive { background: #60a5fa; color: #fff; }

        .completed-badge {
            display: inline-block;
            background: #a3e635;
            color: #1a1a1a;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 6px 16px;
            border: 2px solid #000;
            box-shadow: 2px 2px 0 #000;
            margin-top: 12px;
        }

        /* ── VIDEO ── */
        .video-wrap {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border: 3px solid #000;
            box-shadow: 4px 4px 0 #000;
            margin-bottom: 32px;
        }
        .video-wrap iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* ── OUTLINE COLLAPSIBLE ── */
        .outline-toggle {
            background: #facc15;
            border: 3px solid #000;
            box-shadow: 3px 3px 0 #000;
            padding: 14px 24px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            width: 100%;
            text-align: left;
            margin-bottom: 0;
            transition: all 0.15s;
        }
        .outline-toggle:hover { transform: translate(-2px, -2px); box-shadow: 5px 5px 0 #000; }
        .outline-content {
            display: none;
            background: #fff;
            border: 3px solid #000;
            border-top: none;
            padding: 24px;
            margin-bottom: 32px;
            font-size: 14px;
            line-height: 1.8;
        }
        .outline-content.open { display: block; }

        /* ── CONTENT ── */
        .lesson-content {
            background: #fff;
            border: 3px solid #000;
            box-shadow: 4px 4px 0 #000;
            padding: 40px;
            margin-bottom: 32px;
            line-height: 1.8;
            font-size: 15px;
        }
        .lesson-content h2 {
            font-size: 22px;
            font-weight: 800;
            text-transform: uppercase;
            margin: 32px 0 16px;
            padding-bottom: 8px;
            border-bottom: 3px solid #a3e635;
        }
        .lesson-content h3 {
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            margin: 24px 0 12px;
        }
        .lesson-content p { margin-bottom: 16px; }
        .lesson-content ul, .lesson-content ol { margin: 16px 0; padding-left: 24px; }
        .lesson-content li { margin-bottom: 8px; }
        .lesson-content code {
            background: #1a1a1a;
            color: #a3e635;
            padding: 2px 8px;
            font-size: 13px;
            border: 1px solid #333;
        }
        .lesson-content pre {
            background: #1a1a1a;
            color: #a3e635;
            padding: 20px;
            border: 3px solid #000;
            overflow-x: auto;
            margin: 16px 0;
            font-size: 13px;
            line-height: 1.6;
        }
        .lesson-content img {
            max-width: 100%;
            border: 3px solid #000;
            box-shadow: 3px 3px 0 #000;
        }

        /* ── QUIZ ── */
        .quiz-card {
            background: #fff;
            border: 3px solid #000;
            box-shadow: 4px 4px 0 #000;
            padding: 28px;
            margin-bottom: 20px;
        }
        .quiz-question {
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 16px;
            line-height: 1.4;
        }
        .quiz-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            margin-bottom: 8px;
            border: 2px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.15s;
            font-size: 14px;
            font-weight: 600;
        }
        .quiz-option:hover {
            border-color: #a3e635;
            background: #f0fdf4;
        }
        .quiz-option input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #a3e635;
        }

        /* ── INTERACTIVE ── */
        .interactive-card {
            background: #fff;
            border: 3px solid #000;
            box-shadow: 4px 4px 0 #000;
            padding: 28px;
            margin-bottom: 20px;
        }
        .interactive-title {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 16px;
        }
        .matching-row {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }
        .matching-left {
            background: #1a1a1a;
            color: #fff;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 700;
            border: 2px solid #000;
            min-width: 160px;
        }
        .matching-select {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 13px;
            font-weight: 700;
            padding: 8px 12px;
            border: 2px solid #000;
            background: #fff;
            min-width: 160px;
        }
        .checkbox-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            margin-bottom: 8px;
            border: 2px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.15s;
            font-size: 14px;
            font-weight: 600;
        }
        .checkbox-option:hover {
            border-color: #a3e635;
            background: #f0fdf4;
        }
        .checkbox-option input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #a3e635;
        }
        .interactive-result {
            padding: 16px 24px;
            border: 3px solid #000;
            font-weight: 700;
            font-size: 14px;
            margin-top: 16px;
            display: none;
        }
        .result-pass { background: #a3e635; box-shadow: 3px 3px 0 #000; }
        .result-fail { background: #f87171; color: #fff; box-shadow: 3px 3px 0 #000; }

        /* ── CHECKLIST ── */
        .checklist-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            margin-bottom: 6px;
            border: 2px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.15s;
            font-size: 14px;
            font-weight: 600;
        }
        .checklist-item:hover { border-color: #a3e635; background: #f0fdf4; }
        .checklist-item.checked { border-color: #a3e635; background: #ecfccb; }
        .checklist-item input[type="checkbox"] { width: 18px; height: 18px; accent-color: #a3e635; }
        .checklist-counter {
            background: #1a1a1a;
            color: #a3e635;
            font-size: 18px;
            font-weight: 800;
            padding: 16px 24px;
            border: 3px solid #000;
            box-shadow: 3px 3px 0 #a3e635;
            text-align: center;
            margin-top: 16px;
        }
        .checklist-reveal {
            background: #facc15;
            border: 3px solid #000;
            box-shadow: 3px 3px 0 #000;
            padding: 16px 24px;
            margin-top: 12px;
            font-size: 14px;
            font-weight: 700;
            display: none;
        }

        /* ── CALCULATOR ── */
        .calc-input-wrap {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .calc-input {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px;
            font-weight: 600;
            padding: 12px 16px;
            border: 3px solid #000;
            flex: 1;
            min-width: 200px;
        }
        .calc-result-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            font-size: 13px;
        }
        .calc-result-table th {
            background: #1a1a1a;
            color: #fff;
            padding: 10px 16px;
            text-align: left;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 11px;
        }
        .calc-result-table td {
            padding: 10px 16px;
            border-bottom: 2px solid #e5e7eb;
            font-weight: 600;
        }
        .calc-result-table tr:nth-child(even) td { background: #f9fafb; }
        .calc-comparison {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 16px;
        }
        .calc-comparison-box {
            border: 3px solid #000;
            padding: 16px;
        }
        .calc-comparison-box.bad { background: #fef2f2; }
        .calc-comparison-box.good { background: #f0fdf4; }
        .calc-comparison-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        /* ── STACK BUILDER ── */
        .wizard-steps {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
        }
        .wizard-step-dot {
            width: 32px;
            height: 32px;
            border: 3px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 13px;
            background: #e5e7eb;
        }
        .wizard-step-dot.active { background: #a3e635; }
        .wizard-step-dot.done { background: #1a1a1a; color: #a3e635; }
        .wizard-panel { display: none; }
        .wizard-panel.active { display: block; }
        .wizard-select {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px;
            font-weight: 700;
            padding: 12px 16px;
            border: 3px solid #000;
            background: #fff;
            width: 100%;
            margin-bottom: 12px;
        }
        .wizard-chip {
            display: inline-block;
            padding: 6px 14px;
            border: 2px solid #000;
            margin: 4px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
            background: #fff;
        }
        .wizard-chip.selected { background: #a3e635; }
        .wizard-chip:hover { background: #ecfccb; }
        .stack-result {
            background: #1a1a1a;
            color: #fff;
            border: 3px solid #000;
            box-shadow: 6px 6px 0 #a3e635;
            padding: 24px;
            margin-top: 16px;
        }
        .stack-result h3 { color: #a3e635; margin-bottom: 16px; }
        .stack-tool {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #333;
            font-size: 14px;
        }
        .stack-tool:last-child { border-bottom: none; }
        .stack-tool-name { font-weight: 800; }
        .stack-tool-price { color: #a3e635; font-weight: 700; font-size: 12px; }

        /* ── PROMPT LAB ── */
        .prompt-lab-card {
            background: #f9fafb;
            border: 3px solid #000;
            padding: 20px;
            margin-bottom: 16px;
        }
        .prompt-bad {
            background: #fef2f2;
            border: 2px solid #f87171;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 12px;
            font-style: italic;
        }
        .prompt-bad-result {
            background: #fee2e2;
            border: 2px dashed #f87171;
            padding: 12px 16px;
            font-size: 12px;
            color: #666;
            margin-bottom: 16px;
        }
        .prompt-textarea {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px;
            font-weight: 600;
            padding: 16px;
            border: 3px solid #000;
            width: 100%;
            min-height: 100px;
            resize: vertical;
            margin-bottom: 12px;
        }
        .prompt-reveal {
            display: none;
            margin-top: 16px;
        }
        .prompt-improved {
            background: #f0fdf4;
            border: 2px solid #a3e635;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .prompt-good-result {
            background: #ecfccb;
            border: 2px dashed #a3e635;
            padding: 12px 16px;
            font-size: 12px;
            color: #333;
            margin-bottom: 12px;
        }
        .prompt-technique {
            background: #facc15;
            border: 2px solid #000;
            padding: 10px 16px;
            font-size: 12px;
            font-weight: 700;
        }

        /* ── PLAN WIZARD ── */
        .plan-summary {
            background: #1a1a1a;
            color: #fff;
            border: 3px solid #000;
            box-shadow: 6px 6px 0 #a3e635;
            padding: 32px;
            margin-top: 24px;
        }
        .plan-summary h2 { color: #a3e635; border-bottom: 2px solid #a3e635; padding-bottom: 8px; margin-bottom: 16px; }
        .plan-section { margin-bottom: 20px; }
        .plan-section-title { color: #facc15; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; }
        .plan-item { padding: 6px 0; border-bottom: 1px solid #333; font-size: 14px; }
        .plan-timeline {
            display: flex;
            gap: 0;
            margin-top: 16px;
        }
        .plan-phase {
            flex: 1;
            padding: 12px;
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .plan-phase:nth-child(1) { background: #a3e635; color: #000; }
        .plan-phase:nth-child(2) { background: #facc15; color: #000; }
        .plan-phase:nth-child(3) { background: #60a5fa; color: #fff; }

        /* ── BOTONES ── */
        .btn {
            border: 3px solid #000;
            box-shadow: 3px 3px 0 #000;
            padding: 12px 24px;
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

        /* ── PREV/NEXT ── */
        .lesson-nav {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            margin-top: 40px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }
        .lesson-nav a {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1a1a1a;
            text-decoration: none;
            border: 3px solid #000;
            padding: 12px 24px;
            box-shadow: 3px 3px 0 #000;
            background: #fff;
            transition: all 0.15s;
        }
        .lesson-nav a:hover {
            transform: translate(-2px, -2px);
            box-shadow: 5px 5px 0 #000;
        }
        .lesson-nav .placeholder { visibility: hidden; }

        /* ── SIDEBAR ── */
        .sidebar {
            background: #fff;
            border: 3px solid #000;
            box-shadow: 4px 4px 0 #000;
            padding: 0;
            position: sticky;
            top: 24px;
        }
        .sidebar-header {
            background: #1a1a1a;
            color: #facc15;
            padding: 14px 20px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            border-bottom: 1px solid #e5e7eb;
            text-decoration: none;
            color: #1a1a1a;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.15s;
        }
        .sidebar-item:last-child { border-bottom: none; }
        .sidebar-item:hover { background: #f4efeb; }
        .sidebar-item.active {
            background: #a3e635;
            font-weight: 800;
        }
        .sidebar-item.completed-item { color: #888; }
        .sidebar-check {
            font-size: 14px;
            color: #a3e635;
            font-weight: 800;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }
        .sidebar-check.empty { color: #ddd; }
        .sidebar-num {
            font-weight: 800;
            font-size: 11px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
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

        {{-- BREADCRUMB --}}
        <div class="breadcrumb">
            <a href="{{ route('academia.dashboard') }}">Academia</a>
            <span class="sep">/</span>
            <a href="{{ route('academia.curso', $course->slug) }}">{{ $course->title }}</a>
            <span class="sep">/</span>
            <span class="current">{{ $lesson->title }}</span>
        </div>

        <div class="layout-grid">

            {{-- MAIN CONTENT --}}
            <div class="main-content">

                {{-- LESSON HEADER --}}
                <div class="lesson-header">
                    <h1 class="lesson-header-title">{{ $lesson->title }}</h1>
                    <span class="type-badge type-{{ $lesson->type }}">{{ $lesson->type }}</span>
                    @if ($completion)
                        <div class="completed-badge">
                            Completada
                            @if ($completion->score !== null)
                                — {{ $completion->score }}%
                            @endif
                        </div>
                    @endif
                </div>

                {{-- VIDEO --}}
                @if ($lesson->safe_video_url)
                    <div class="video-wrap">
                        <iframe src="{{ $lesson->safe_video_url }}" allowfullscreen></iframe>
                    </div>
                @endif

                {{-- VIDEO OUTLINE --}}
                @if ($lesson->video_outline)
                    <button class="outline-toggle" onclick="toggleOutline()">
                        Outline del Video ▾
                    </button>
                    <div class="outline-content" id="outlineContent">
                        {!! nl2br(e($lesson->video_outline)) !!}
                    </div>
                @endif

                {{-- CONTENT HTML --}}
                @if ($lesson->content_html)
                    <div class="lesson-content">
                        {!! $lesson->content_html !!}
                    </div>
                @endif

                {{-- INTERACTIVE (shown BEFORE quiz for combo lessons) --}}
                @if ($lesson->interactive_data)
                    <span class="section-label">Ejercicios Interactivos</span>
                    <div style="margin-top: 16px;">
                        @foreach ($lesson->interactive_data as $exIndex => $exercise)
                            <div class="interactive-card" id="exercise-{{ $exIndex }}">
                                <div class="interactive-title">{{ $exercise['title'] ?? 'Ejercicio ' . ($exIndex + 1) }}</div>

                                @if (($exercise['type'] ?? '') === 'matching')
                                    {{-- MATCHING EXERCISE --}}
                                    <p style="font-size: 13px; color: #555; margin-bottom: 16px;">{{ $exercise['instructions'] ?? 'Selecciona la opcion correcta para cada elemento.' }}</p>
                                    @foreach ($exercise['pairs'] as $pIndex => $pair)
                                        <div class="matching-row">
                                            <div class="matching-left">{{ $pair['left'] }}</div>
                                            <span style="font-weight: 800;">&rarr;</span>
                                            <select class="matching-select" data-exercise="{{ $exIndex }}" data-pair="{{ $pIndex }}" data-correct="{{ $pair['right'] }}">
                                                <option value="">-- Seleccionar --</option>
                                                @foreach (collect($exercise['pairs'])->pluck('right')->shuffle() as $rightOption)
                                                    <option value="{{ $rightOption }}">{{ $rightOption }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endforeach
                                    <button type="button" class="btn btn-dark" style="margin-top: 12px;" onclick="checkMatching({{ $exIndex }})">Verificar</button>
                                    <div class="interactive-result" id="result-{{ $exIndex }}"></div>

                                @elseif (($exercise['type'] ?? '') === 'multiple_select')
                                    {{-- MULTIPLE SELECT EXERCISE --}}
                                    <p style="font-size: 13px; color: #555; margin-bottom: 16px;">{{ $exercise['instructions'] ?? 'Selecciona todas las opciones correctas.' }}</p>
                                    <p style="font-size: 14px; font-weight: 700; margin-bottom: 12px;">{{ $exercise['scenario'] ?? $exercise['question'] ?? '' }}</p>
                                    @foreach ($exercise['options'] as $msIndex => $msOption)
                                        <label class="checkbox-option">
                                            <input type="checkbox"
                                                   data-exercise="{{ $exIndex }}"
                                                   data-option="{{ $msIndex }}"
                                                   data-correct="{{ in_array($msIndex, $exercise['correct_indices'] ?? []) ? '1' : '0' }}">
                                            {{ $msOption }}
                                        </label>
                                    @endforeach
                                    <button type="button" class="btn btn-dark" style="margin-top: 12px;" onclick="checkMultipleSelect({{ $exIndex }})">Verificar</button>
                                    <div class="interactive-result" id="result-{{ $exIndex }}"></div>

                                @elseif (($exercise['type'] ?? '') === 'checklist')
                                    {{-- CHECKLIST EXERCISE --}}
                                    <p style="font-size: 13px; color: #555; margin-bottom: 16px;">{{ $exercise['instructions'] ?? '' }}</p>
                                    @foreach ($exercise['items'] as $cIndex => $item)
                                        <label class="checklist-item" id="cl-{{ $exIndex }}-{{ $cIndex }}">
                                            <input type="checkbox" onchange="updateChecklist({{ $exIndex }})">
                                            <span>{{ $item }}</span>
                                        </label>
                                    @endforeach
                                    <div class="checklist-counter" id="checklist-counter-{{ $exIndex }}">
                                        0 de {{ count($exercise['items']) }} herramientas de IA identificadas
                                    </div>
                                    @if (!empty($exercise['reveal_text']))
                                        <div class="checklist-reveal" id="checklist-reveal-{{ $exIndex }}">
                                            {{ $exercise['reveal_text'] }}
                                        </div>
                                    @endif

                                @elseif (($exercise['type'] ?? '') === 'calculator')
                                    {{-- CALCULATOR EXERCISE --}}
                                    <p style="font-size: 13px; color: #555; margin-bottom: 16px;">{{ $exercise['instructions'] ?? '' }}</p>
                                    <div class="calc-input-wrap">
                                        <input type="text" class="calc-input" id="calc-input-{{ $exIndex }}" placeholder="{{ $exercise['placeholder'] ?? 'Escribe una frase...' }}">
                                        <button type="button" class="btn btn-dark" onclick="calculateTokens({{ $exIndex }})">Calcular Tokens</button>
                                    </div>
                                    <div id="calc-output-{{ $exIndex }}" style="display: none;">
                                        <div id="calc-result-{{ $exIndex }}"></div>
                                    </div>
                                    @if (!empty($exercise['comparison']))
                                        <div class="calc-comparison" id="calc-comparison-{{ $exIndex }}" style="display: none;">
                                            <div class="calc-comparison-box bad">
                                                <div class="calc-comparison-label" style="color: #f87171;">Prompt ineficiente</div>
                                                <p style="font-size: 13px; font-weight: 600;">{{ $exercise['comparison']['bad']['text'] }}</p>
                                                <p style="font-size: 12px; color: #888; margin-top: 8px;">~{{ $exercise['comparison']['bad']['tokens'] }} tokens = ${{ $exercise['comparison']['bad']['cost'] }}</p>
                                            </div>
                                            <div class="calc-comparison-box good">
                                                <div class="calc-comparison-label" style="color: #16a34a;">Prompt optimizado</div>
                                                <p style="font-size: 13px; font-weight: 600;">{{ $exercise['comparison']['good']['text'] }}</p>
                                                <p style="font-size: 12px; color: #888; margin-top: 8px;">~{{ $exercise['comparison']['good']['tokens'] }} tokens = ${{ $exercise['comparison']['good']['cost'] }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if (!empty($exercise['cost_table']))
                                        <table class="calc-result-table" style="margin-top: 24px;">
                                            <thead>
                                                <tr>
                                                    @foreach (array_keys($exercise['cost_table'][0]) as $header)
                                                        <th>{{ $header }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($exercise['cost_table'] as $row)
                                                    <tr>
                                                        @foreach ($row as $cell)
                                                            <td>{{ $cell }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif

                                @elseif (($exercise['type'] ?? '') === 'stack_builder')
                                    {{-- STACK BUILDER WIZARD --}}
                                    <p style="font-size: 13px; color: #555; margin-bottom: 16px;">{{ $exercise['instructions'] ?? '' }}</p>
                                    <div class="wizard-steps" id="wizard-steps-{{ $exIndex }}">
                                        @foreach ($exercise['steps'] as $sIndex => $step)
                                            <div class="wizard-step-dot {{ $sIndex === 0 ? 'active' : '' }}" id="wdot-{{ $exIndex }}-{{ $sIndex }}">{{ $sIndex + 1 }}</div>
                                        @endforeach
                                    </div>
                                    @foreach ($exercise['steps'] as $sIndex => $step)
                                        <div class="wizard-panel {{ $sIndex === 0 ? 'active' : '' }}" id="wpanel-{{ $exIndex }}-{{ $sIndex }}">
                                            <p style="font-size: 15px; font-weight: 700; margin-bottom: 12px;">{{ $step['label'] }}</p>
                                            @if (($step['input_type'] ?? 'select') === 'select')
                                                <select class="wizard-select" id="winput-{{ $exIndex }}-{{ $sIndex }}" data-step="{{ $sIndex }}">
                                                    <option value="">-- Seleccionar --</option>
                                                    @foreach ($step['options'] as $opt)
                                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                                    @endforeach
                                                </select>
                                            @elseif (($step['input_type'] ?? '') === 'chips')
                                                <div id="winput-{{ $exIndex }}-{{ $sIndex }}" data-step="{{ $sIndex }}">
                                                    @foreach ($step['options'] as $opt)
                                                        <span class="wizard-chip" onclick="toggleChip(this)">{{ $opt }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div style="margin-top: 16px; display: flex; gap: 12px;">
                                                @if ($sIndex > 0)
                                                    <button type="button" class="btn btn-yellow" onclick="wizardPrev({{ $exIndex }}, {{ $sIndex }})">Anterior</button>
                                                @endif
                                                @if ($sIndex < count($exercise['steps']) - 1)
                                                    <button type="button" class="btn btn-dark" onclick="wizardNext({{ $exIndex }}, {{ $sIndex }}, {{ count($exercise['steps']) }})">Siguiente</button>
                                                @else
                                                    <button type="button" class="btn btn-green" onclick="wizardFinish({{ $exIndex }})">Ver mi Stack</button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="stack-result" id="stack-result-{{ $exIndex }}" style="display: none;"></div>
                                    <script>
                                        window['stackData{{ $exIndex }}'] = @json($exercise['recommendations'] ?? []);
                                    </script>

                                @elseif (($exercise['type'] ?? '') === 'prompt_lab')
                                    {{-- PROMPT LAB --}}
                                    <p style="font-size: 13px; color: #555; margin-bottom: 16px;">{{ $exercise['instructions'] ?? '' }}</p>
                                    @foreach ($exercise['prompts'] as $pIndex => $prompt)
                                        <div class="prompt-lab-card" id="plab-{{ $exIndex }}-{{ $pIndex }}">
                                            <p style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #888; margin-bottom: 8px;">Prompt {{ $pIndex + 1 }}</p>
                                            <div class="prompt-bad">"{{ $prompt['bad_prompt'] }}"</div>
                                            <div class="prompt-bad-result">{{ $prompt['bad_result'] }}</div>
                                            <p style="font-size: 13px; font-weight: 700; margin-bottom: 8px;">Reescribe este prompt usando las tecnicas aprendidas:</p>
                                            <textarea class="prompt-textarea" id="plab-input-{{ $exIndex }}-{{ $pIndex }}" placeholder="Escribe tu prompt mejorado..."></textarea>
                                            <button type="button" class="btn btn-dark" onclick="revealPrompt({{ $exIndex }}, {{ $pIndex }})">Enviar y Comparar</button>
                                            <div class="prompt-reveal" id="plab-reveal-{{ $exIndex }}-{{ $pIndex }}">
                                                <p style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #16a34a; margin-bottom: 8px;">Prompt mejorado:</p>
                                                <div class="prompt-improved">"{{ $prompt['good_prompt'] }}"</div>
                                                <p style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #16a34a; margin-bottom: 8px;">Resultado:</p>
                                                <div class="prompt-good-result">{{ $prompt['good_result'] }}</div>
                                                <div class="prompt-technique">Tecnica aplicada: {{ $prompt['technique'] }}</div>
                                            </div>
                                        </div>
                                    @endforeach

                                @elseif (($exercise['type'] ?? '') === 'scenario_prompts')
                                    {{-- SCENARIO PROMPT BUILDER --}}
                                    <p style="font-size: 13px; color: #555; margin-bottom: 16px;">{{ $exercise['instructions'] ?? '' }}</p>
                                    <div style="margin-bottom: 20px;">
                                        @foreach ($exercise['scenarios'] as $scIndex => $scenario)
                                            <label class="quiz-option">
                                                <input type="radio" name="scenario-{{ $exIndex }}" value="{{ $scIndex }}" onchange="selectScenario({{ $exIndex }}, {{ $scIndex }})">
                                                <strong>{{ $scenario['name'] }}:</strong>&nbsp;{{ $scenario['description'] }}
                                            </label>
                                        @endforeach
                                    </div>
                                    <div id="scenario-workspace-{{ $exIndex }}" style="display: none;">
                                        @for ($pi = 0; $pi < 3; $pi++)
                                            <div style="margin-bottom: 16px; padding: 16px; border: 2px solid #e5e7eb;">
                                                <p style="font-size: 13px; font-weight: 800; margin-bottom: 8px;">Prompt {{ $pi + 1 }}:</p>
                                                <textarea class="prompt-textarea" id="scenario-prompt-{{ $exIndex }}-{{ $pi }}" placeholder="Escribe tu prompt profesional..."></textarea>
                                                <p style="font-size: 12px; font-weight: 700; margin-bottom: 8px;">Tecnicas aplicadas:</p>
                                                <div id="scenario-techniques-{{ $exIndex }}-{{ $pi }}">
                                                    @foreach (['Rol + Contexto', 'Formato + Restricciones', 'Chain-of-Thought', 'Few-Shot', 'Mega-prompt'] as $tIndex => $tech)
                                                        <span class="wizard-chip" onclick="toggleChip(this)">{{ $tech }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endfor
                                        <button type="button" class="btn btn-green" onclick="submitScenario({{ $exIndex }})">Ver Comparacion</button>
                                        <div id="scenario-result-{{ $exIndex }}" style="display: none; margin-top: 16px;"></div>
                                    </div>
                                    <script>
                                        window['scenarioData{{ $exIndex }}'] = @json($exercise['scenarios'] ?? []);
                                    </script>

                                @elseif (($exercise['type'] ?? '') === 'plan_wizard')
                                    {{-- PLAN WIZARD (5-step guided form) --}}
                                    <p style="font-size: 13px; color: #555; margin-bottom: 16px;">{{ $exercise['instructions'] ?? '' }}</p>
                                    <div class="wizard-steps" id="plan-steps-{{ $exIndex }}">
                                        @foreach ($exercise['steps'] as $sIndex => $step)
                                            <div class="wizard-step-dot {{ $sIndex === 0 ? 'active' : '' }}" id="pldot-{{ $exIndex }}-{{ $sIndex }}">{{ $sIndex + 1 }}</div>
                                        @endforeach
                                    </div>
                                    @foreach ($exercise['steps'] as $sIndex => $step)
                                        <div class="wizard-panel {{ $sIndex === 0 ? 'active' : '' }}" id="plpanel-{{ $exIndex }}-{{ $sIndex }}">
                                            <p style="font-size: 15px; font-weight: 700; margin-bottom: 6px;">{{ $step['label'] }}</p>
                                            @if (!empty($step['subtitle']))
                                                <p style="font-size: 13px; color: #555; margin-bottom: 12px;">{{ $step['subtitle'] }}</p>
                                            @endif
                                            @if (($step['input_type'] ?? 'select') === 'select')
                                                <select class="wizard-select" id="plinput-{{ $exIndex }}-{{ $sIndex }}">
                                                    <option value="">-- Seleccionar --</option>
                                                    @foreach ($step['options'] as $opt)
                                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                                    @endforeach
                                                </select>
                                            @elseif (($step['input_type'] ?? '') === 'chips')
                                                <div id="plinput-{{ $exIndex }}-{{ $sIndex }}">
                                                    @foreach ($step['options'] as $opt)
                                                        <span class="wizard-chip" onclick="toggleChip(this)">{{ $opt }}</span>
                                                    @endforeach
                                                </div>
                                            @elseif (($step['input_type'] ?? '') === 'multi_select')
                                                <div id="plinput-{{ $exIndex }}-{{ $sIndex }}">
                                                    @foreach ($step['options'] as $opt)
                                                        <label class="checkbox-option">
                                                            <input type="checkbox" value="{{ $opt }}">
                                                            {{ $opt }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div style="margin-top: 16px; display: flex; gap: 12px;">
                                                @if ($sIndex > 0)
                                                    <button type="button" class="btn btn-yellow" onclick="planPrev({{ $exIndex }}, {{ $sIndex }})">Anterior</button>
                                                @endif
                                                @if ($sIndex < count($exercise['steps']) - 1)
                                                    <button type="button" class="btn btn-dark" onclick="planNext({{ $exIndex }}, {{ $sIndex }}, {{ count($exercise['steps']) }})">Siguiente</button>
                                                @else
                                                    <button type="button" class="btn btn-green" onclick="planFinish({{ $exIndex }}, {{ count($exercise['steps']) }})">Generar Mi Plan de IA</button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    <div id="plan-result-{{ $exIndex }}" style="display: none;"></div>
                                    <script>
                                        window['planRecommendations{{ $exIndex }}'] = @json($exercise['recommendations'] ?? []);
                                    </script>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- QUIZ (shown AFTER interactive for combo lessons) --}}
                @if ($lesson->quiz_data)
                    <span class="section-label">Quiz</span>
                    <form method="POST" action="{{ route('academia.curso.quiz', [$course->slug, $lesson->slug]) }}" style="margin-top: 16px;">
                        @csrf
                        @foreach ($lesson->quiz_data as $qIndex => $question)
                            <div class="quiz-card">
                                <div class="quiz-question">{{ $qIndex + 1 }}. {{ $question['question'] }}</div>
                                @foreach ($question['options'] as $oIndex => $option)
                                    <label class="quiz-option">
                                        <input type="radio" name="answers[{{ $qIndex }}]" value="{{ $oIndex }}" required>
                                        {{ $option }}
                                    </label>
                                @endforeach
                            </div>
                        @endforeach
                        <button type="submit" class="btn btn-dark" style="margin-top: 8px;">Enviar Respuestas</button>
                    </form>
                @endif

                {{-- MARK COMPLETE BUTTON --}}
                @if (in_array($lesson->type, ['lecture', 'interactive']) && !$completion)
                    <form method="POST" action="{{ route('academia.curso.complete', [$course->slug, $lesson->slug]) }}" style="margin-top: 24px;">
                        @csrf
                        <button type="submit" class="btn btn-green">Marcar como completada</button>
                    </form>
                @endif

                {{-- PREV / NEXT NAV --}}
                <div class="lesson-nav">
                    @if ($prev)
                        <a href="{{ route('academia.curso.leccion', [$course->slug, $prev->slug]) }}">&larr; {{ $prev->title }}</a>
                    @else
                        <span class="placeholder">&larr; Anterior</span>
                    @endif

                    @if ($next)
                        <a href="{{ route('academia.curso.leccion', [$course->slug, $next->slug]) }}">{{ $next->title }} &rarr;</a>
                    @else
                        <a href="{{ route('academia.curso', $course->slug) }}">Volver al curso &rarr;</a>
                    @endif
                </div>

            </div>

            {{-- SIDEBAR --}}
            <div class="sidebar">
                <div class="sidebar-header">Lecciones</div>
                @foreach ($lessons as $index => $sideLesson)
                    @php
                        $isCurrent = $sideLesson->id === $lesson->id;
                        $isDone = in_array($sideLesson->id, $completedIds);
                    @endphp
                    <a href="{{ route('academia.curso.leccion', [$course->slug, $sideLesson->slug]) }}"
                       class="sidebar-item {{ $isCurrent ? 'active' : '' }} {{ $isDone && !$isCurrent ? 'completed-item' : '' }}">
                        @if ($isDone)
                            <span class="sidebar-check">✓</span>
                        @else
                            <span class="sidebar-num">{{ $index + 1 }}</span>
                        @endif
                        <span>{{ $sideLesson->title }}</span>
                    </a>
                @endforeach
            </div>

        </div>

    </div>

    <script>
        /* ── Outline toggle ── */
        function toggleOutline() {
            var el = document.getElementById('outlineContent');
            el.classList.toggle('open');
        }

        /* ── Matching exercise checker ── */
        function checkMatching(exIndex) {
            var selects = document.querySelectorAll('select[data-exercise="' + exIndex + '"]');
            var correct = 0;
            var total = selects.length;

            selects.forEach(function(sel) {
                var expected = sel.getAttribute('data-correct');
                if (sel.value === expected) {
                    correct++;
                    sel.style.borderColor = '#a3e635';
                    sel.style.background = '#f0fdf4';
                } else {
                    sel.style.borderColor = '#f87171';
                    sel.style.background = '#fef2f2';
                }
            });

            var resultEl = document.getElementById('result-' + exIndex);
            resultEl.style.display = 'block';
            resultEl.textContent = correct + ' de ' + total + ' correctas';
            if (correct === total) {
                resultEl.className = 'interactive-result result-pass';
            } else {
                resultEl.className = 'interactive-result result-fail';
            }
        }

        /* ── Multiple select checker ── */
        function checkMultipleSelect(exIndex) {
            var checkboxes = document.querySelectorAll('input[data-exercise="' + exIndex + '"]');
            var correct = 0;
            var total = 0;

            checkboxes.forEach(function(cb) {
                var isCorrect = cb.getAttribute('data-correct') === '1';
                var isChecked = cb.checked;
                var label = cb.closest('.checkbox-option');

                if (isCorrect) total++;

                if (isChecked && isCorrect) {
                    correct++;
                    label.style.borderColor = '#a3e635';
                    label.style.background = '#f0fdf4';
                } else if (isChecked && !isCorrect) {
                    label.style.borderColor = '#f87171';
                    label.style.background = '#fef2f2';
                } else if (!isChecked && isCorrect) {
                    label.style.borderColor = '#facc15';
                    label.style.background = '#fffbeb';
                } else {
                    label.style.borderColor = '#e5e7eb';
                    label.style.background = '#fff';
                }
            });

            var resultEl = document.getElementById('result-' + exIndex);
            resultEl.style.display = 'block';
            resultEl.textContent = correct + ' de ' + total + ' correctas';
            if (correct === total) {
                resultEl.className = 'interactive-result result-pass';
            } else {
                resultEl.className = 'interactive-result result-fail';
            }
        }

        /* ── Checklist counter ── */
        function updateChecklist(exIndex) {
            var card = document.getElementById('exercise-' + exIndex);
            var checkboxes = card.querySelectorAll('.checklist-item input[type="checkbox"]');
            var checked = 0;
            checkboxes.forEach(function(cb) {
                var label = cb.closest('.checklist-item');
                if (cb.checked) {
                    checked++;
                    label.classList.add('checked');
                } else {
                    label.classList.remove('checked');
                }
            });
            var counter = document.getElementById('checklist-counter-' + exIndex);
            counter.textContent = checked + ' de ' + checkboxes.length + ' herramientas de IA identificadas';
            var reveal = document.getElementById('checklist-reveal-' + exIndex);
            if (reveal && checked >= 3) {
                reveal.style.display = 'block';
            }
        }

        /* ── Token calculator ── */
        function calculateTokens(exIndex) {
            var input = document.getElementById('calc-input-' + exIndex);
            var text = input.value.trim();
            if (!text) return;
            var words = text.split(/\s+/).length;
            var chars = text.length;
            var tokens = Math.ceil(words * 1.3);
            var costInput = (tokens * 0.000003).toFixed(6);
            var costOutput = (tokens * 0.000015).toFixed(6);
            var resultDiv = document.getElementById('calc-result-' + exIndex);
            resultDiv.innerHTML = '<table class="calc-result-table"><thead><tr><th>Metrica</th><th>Valor</th></tr></thead><tbody>' +
                '<tr><td>Palabras</td><td>' + words + '</td></tr>' +
                '<tr><td>Caracteres</td><td>' + chars + '</td></tr>' +
                '<tr><td>Tokens (aprox)</td><td>~' + tokens + '</td></tr>' +
                '<tr><td>Costo input (GPT-4o)</td><td>$' + costInput + '</td></tr>' +
                '<tr><td>Costo output (GPT-4o)</td><td>$' + costOutput + '</td></tr></tbody></table>';
            document.getElementById('calc-output-' + exIndex).style.display = 'block';
            var comp = document.getElementById('calc-comparison-' + exIndex);
            if (comp) comp.style.display = 'grid';
        }

        /* ── Wizard helpers (Stack Builder) ── */
        function toggleChip(el) {
            el.classList.toggle('selected');
        }
        function wizardNext(exIndex, current, total) {
            document.getElementById('wpanel-' + exIndex + '-' + current).classList.remove('active');
            document.getElementById('wpanel-' + exIndex + '-' + (current + 1)).classList.add('active');
            document.getElementById('wdot-' + exIndex + '-' + current).classList.remove('active');
            document.getElementById('wdot-' + exIndex + '-' + current).classList.add('done');
            document.getElementById('wdot-' + exIndex + '-' + (current + 1)).classList.add('active');
        }
        function wizardPrev(exIndex, current) {
            document.getElementById('wpanel-' + exIndex + '-' + current).classList.remove('active');
            document.getElementById('wpanel-' + exIndex + '-' + (current - 1)).classList.add('active');
            document.getElementById('wdot-' + exIndex + '-' + current).classList.remove('active');
            document.getElementById('wdot-' + exIndex + '-' + (current - 1)).classList.remove('done');
            document.getElementById('wdot-' + exIndex + '-' + (current - 1)).classList.add('active');
        }
        function wizardFinish(exIndex) {
            var data = window['stackData' + exIndex] || {};
            var businessEl = document.getElementById('winput-' + exIndex + '-0');
            var budgetEl = document.getElementById('winput-' + exIndex + '-1');
            var business = businessEl.tagName === 'SELECT' ? businessEl.value : '';
            var budget = budgetEl.tagName === 'SELECT' ? budgetEl.value : '';
            var prioritiesEl = document.getElementById('winput-' + exIndex + '-2');
            var selectedPriorities = [];
            if (prioritiesEl) {
                prioritiesEl.querySelectorAll('.wizard-chip.selected').forEach(function(c) {
                    selectedPriorities.push(c.textContent.trim());
                });
            }
            var key = business || Object.keys(data)[0] || 'default';
            var tools = data[key] || data[Object.keys(data)[0]] || [];
            var html = '<h3 style="text-transform: uppercase; letter-spacing: 2px; font-size: 14px;">Tu Stack Recomendado</h3>';
            html += '<p style="font-size: 12px; color: #aaa; margin-bottom: 16px;">' + business + ' · ' + budget + '</p>';
            tools.forEach(function(tool) {
                html += '<div class="stack-tool"><span class="stack-tool-name">' + tool.name + '</span><span class="stack-tool-price">' + tool.price + '</span></div>';
            });
            if (selectedPriorities.length > 0) {
                html += '<p style="font-size: 12px; color: #aaa; margin-top: 16px;">Prioridades: ' + selectedPriorities.join(', ') + '</p>';
            }
            var resultEl = document.getElementById('stack-result-' + exIndex);
            resultEl.innerHTML = html;
            resultEl.style.display = 'block';
        }

        /* ── Prompt Lab reveal ── */
        function revealPrompt(exIndex, pIndex) {
            document.getElementById('plab-reveal-' + exIndex + '-' + pIndex).style.display = 'block';
        }

        /* ── Scenario prompt builder ── */
        function selectScenario(exIndex, scIndex) {
            document.getElementById('scenario-workspace-' + exIndex).style.display = 'block';
        }
        function submitScenario(exIndex) {
            var scenarios = window['scenarioData' + exIndex] || [];
            var selectedRadio = document.querySelector('input[name="scenario-' + exIndex + '"]:checked');
            if (!selectedRadio) return;
            var scIndex = parseInt(selectedRadio.value);
            var scenario = scenarios[scIndex] || {};
            var html = '<div style="background: #1a1a1a; color: #fff; border: 3px solid #000; box-shadow: 4px 4px 0 #a3e635; padding: 24px;">';
            html += '<h3 style="color: #a3e635; text-transform: uppercase; font-size: 14px; letter-spacing: 2px; margin-bottom: 16px;">Ejemplo de prompts profesionales para: ' + scenario.name + '</h3>';
            (scenario.example_prompts || []).forEach(function(ep, i) {
                html += '<div style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #333;">';
                html += '<p style="color: #facc15; font-size: 12px; font-weight: 800; margin-bottom: 6px;">PROMPT ' + (i + 1) + '</p>';
                html += '<p style="font-size: 14px; margin-bottom: 8px;">"' + ep.prompt + '"</p>';
                html += '<p style="font-size: 12px; color: #a3e635;">Tecnica: ' + ep.technique + '</p>';
                html += '</div>';
            });
            html += '</div>';
            var resultEl = document.getElementById('scenario-result-' + exIndex);
            resultEl.innerHTML = html;
            resultEl.style.display = 'block';
        }

        /* ── Plan Wizard ── */
        function planNext(exIndex, current, total) {
            document.getElementById('plpanel-' + exIndex + '-' + current).classList.remove('active');
            document.getElementById('plpanel-' + exIndex + '-' + (current + 1)).classList.add('active');
            document.getElementById('pldot-' + exIndex + '-' + current).classList.remove('active');
            document.getElementById('pldot-' + exIndex + '-' + current).classList.add('done');
            document.getElementById('pldot-' + exIndex + '-' + (current + 1)).classList.add('active');
        }
        function planPrev(exIndex, current) {
            document.getElementById('plpanel-' + exIndex + '-' + current).classList.remove('active');
            document.getElementById('plpanel-' + exIndex + '-' + (current - 1)).classList.add('active');
            document.getElementById('pldot-' + exIndex + '-' + current).classList.remove('active');
            document.getElementById('pldot-' + exIndex + '-' + (current - 1)).classList.remove('done');
            document.getElementById('pldot-' + exIndex + '-' + (current - 1)).classList.add('active');
        }
        function planFinish(exIndex, totalSteps) {
            var selections = [];
            for (var i = 0; i < totalSteps; i++) {
                var el = document.getElementById('plinput-' + exIndex + '-' + i);
                if (!el) continue;
                if (el.tagName === 'SELECT') {
                    selections.push(el.value || '(sin seleccionar)');
                } else {
                    var checked = el.querySelectorAll('input[type="checkbox"]:checked, .wizard-chip.selected');
                    var vals = [];
                    checked.forEach(function(c) { vals.push(c.value || c.textContent.trim()); });
                    selections.push(vals.length > 0 ? vals.join(', ') : '(sin seleccionar)');
                }
            }
            var recs = window['planRecommendations' + exIndex] || {};
            var industry = selections[0] || '';
            var tools = recs[industry] || recs[Object.keys(recs)[0]] || [];
            var timeline = selections[4] || '60 dias';
            var html = '<div class="plan-summary">';
            html += '<h2 style="text-transform: uppercase; font-size: 18px; letter-spacing: 2px;">Mi Plan de IA</h2>';
            html += '<div class="plan-section"><div class="plan-section-title">Industria</div><div class="plan-item">' + selections[0] + '</div></div>';
            html += '<div class="plan-section"><div class="plan-section-title">Perfil</div><div class="plan-item">' + selections[1] + '</div></div>';
            html += '<div class="plan-section"><div class="plan-section-title">Problemas prioritarios</div><div class="plan-item">' + selections[2] + '</div></div>';
            html += '<div class="plan-section"><div class="plan-section-title">Herramientas recomendadas</div>';
            tools.forEach(function(t) {
                html += '<div class="plan-item">' + t.name + ' — ' + t.use + '</div>';
            });
            html += '</div>';
            html += '<div class="plan-section"><div class="plan-section-title">Stack ajustado</div><div class="plan-item">' + selections[3] + '</div></div>';
            html += '<div class="plan-section"><div class="plan-section-title">Roadmap</div>';
            html += '<div class="plan-timeline">';
            html += '<div class="plan-phase">Semana 1-2: Configurar herramientas</div>';
            html += '<div class="plan-phase">Semana 3-4: Primeros workflows</div>';
            html += '<div class="plan-phase">Semana 5+: Optimizar y escalar</div>';
            html += '</div></div>';
            html += '<p style="font-size: 13px; color: #a3e635; margin-top: 20px; font-weight: 700;">Este plan es tuyo. No es generico. Es lo que TU necesitas para TU negocio.</p>';
            html += '</div>';
            var resultEl = document.getElementById('plan-result-' + exIndex);
            resultEl.innerHTML = html;
            resultEl.style.display = 'block';
        }
    </script>

</body>
</html>
