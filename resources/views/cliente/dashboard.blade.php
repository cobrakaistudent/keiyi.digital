<!DOCTYPE html>
<html lang="es">
<head>
    <x-keiyi-head title="Portal de Cliente — Keiyi Digital" />
    <style>
        .client-container { max-width: 720px; margin: 0 auto; padding: 40px 20px; font-family: 'Space Grotesk', sans-serif; }
        .card { border: 3px solid #000; padding: 28px; box-shadow: 6px 6px 0 #000; background: #fff; margin-bottom: 24px; }
        .card h2 { font-size: 20px; font-weight: 800; margin-bottom: 16px; }
        .project-item { padding: 16px 0; border-bottom: 1px solid #eee; }
        .project-item:last-child { border-bottom: none; }
        .project-title { font-weight: 700; font-size: 16px; }
        .project-meta { color: #555; font-size: 13px; margin-top: 4px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 700; }
        .badge-briefing { background: #fef3c7; color: #d97706; }
        .badge-progress { background: #dbeafe; color: #2563eb; }
        .badge-delivered { background: #dcfce7; color: #16a34a; }
    </style>
</head>
<body>
    <x-keiyi-nav />

    <div class="client-container">
        <div style="margin-bottom: 32px;">
            <h1 style="font-size: 28px; font-weight: 800;">Portal de Cliente</h1>
            <p style="color: #555; font-size: 14px;">{{ $user->company_name ?? $user->name }}</p>
        </div>

        <div class="card">
            <h2>Tus proyectos</h2>

            @if($projects->isEmpty())
                <p style="color: #555; text-align: center; padding: 20px;">No tienes proyectos activos. Tu equipo de Keiyi te contactará pronto.</p>
            @else
                @foreach($projects as $project)
                    <div class="project-item">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="project-title">{{ $project->title }}</span>
                            <span class="badge badge-{{ $project->status === 'delivered' ? 'delivered' : ($project->status === 'in_progress' ? 'progress' : 'briefing') }}">
                                {{ $project->status === 'delivered' ? 'Entregado' : ($project->status === 'in_progress' ? 'En progreso' : 'Briefing') }}
                            </span>
                        </div>
                        <p class="project-meta">{{ $project->description }}</p>
                        @if($project->deadline)
                            <p class="project-meta">Deadline: {{ \Carbon\Carbon::parse($project->deadline)->format('d/m/Y') }}</p>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <x-keiyi-footer />
    <script src="{{ asset('script.js') }}"></script>
</body>
</html>
