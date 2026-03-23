<!DOCTYPE html>
<html lang="es">
<head>
    <x-keiyi-head title="Panel de Profesor — Keiyi Digital" />
    <style>
        .prof-container { max-width: 960px; margin: 0 auto; padding: 40px 20px; font-family: 'Space Grotesk', sans-serif; }
        .prof-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; flex-wrap: wrap; gap: 16px; }
        .prof-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 32px; }
        .stat-card { border: 3px solid #000; padding: 20px; box-shadow: 4px 4px 0 #000; background: #fff; }
        .stat-card h4 { font-size: 13px; color: #555; margin-bottom: 4px; }
        .stat-card .value { font-size: 28px; font-weight: 800; }
        .prof-table { width: 100%; border-collapse: collapse; border: 3px solid #000; box-shadow: 4px 4px 0 #000; background: #fff; }
        .prof-table th { background: #000; color: #fff; padding: 12px 16px; text-align: left; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }
        .prof-table td { padding: 12px 16px; border-bottom: 1px solid #eee; font-size: 14px; }
        .prof-table tr:hover td { background: #f9f9f9; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 700; }
        .badge-green { background: #dcfce7; color: #16a34a; }
        .badge-yellow { background: #fef3c7; color: #d97706; }
        .badge-gray { background: #f3f4f6; color: #6b7280; }
        .btn-primary { background: #000; color: #fff; padding: 12px 24px; border: none; font-family: 'Space Grotesk', sans-serif; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-block; font-size: 14px; }
        .btn-primary:hover { background: #333; }
        .progress-bar { height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 4px; background: #f59e0b; }
    </style>
</head>
<body>
    <x-keiyi-nav />

    <div class="prof-container">
        <div class="prof-header">
            <div>
                <h1 style="font-size: 28px; font-weight: 800;">Panel de Profesor</h1>
                <p style="color: #555; font-size: 14px;">{{ $user->fullName() }} {{ $user->company_name ? '— '.$user->company_name : '' }}</p>
            </div>
            <a href="{{ route('profesor.students') }}" class="btn-primary">Gestionar Alumnos</a>
        </div>

        <div class="prof-stats">
            <div class="stat-card">
                <h4>Alumnos inscritos</h4>
                <div class="value">{{ $students->count() }}</div>
            </div>
            <div class="stat-card">
                <h4>Límite</h4>
                <div class="value">{{ $user->student_limit }}</div>
            </div>
            <div class="stat-card">
                <h4>Espacios disponibles</h4>
                <div class="value">{{ $user->remainingStudentSlots() }}</div>
            </div>
            <div class="stat-card">
                <h4>Cursos disponibles</h4>
                <div class="value">{{ $courses->count() }}</div>
            </div>
        </div>

        {{-- Tutorial: solo se muestra cuando no hay alumnos --}}
        @if($students->isEmpty())
            <div style="border: 3px solid #000; box-shadow: 6px 6px 0 #a3e635; background: #fff; margin-bottom: 32px; overflow: hidden;">
                <div style="background: #1a1a1a; padding: 20px 28px;">
                    <h2 style="color: #a3e635; font-size: 18px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px;">Bienvenido a tu panel de profesor</h2>
                    <p style="color: #aaa; font-size: 13px; margin: 6px 0 0;">Sigue estos pasos para empezar con tus cursos grupales</p>
                </div>
                <div style="padding: 28px;">
                    <div style="display: flex; gap: 16px; align-items: flex-start; margin-bottom: 24px;">
                        <div style="min-width: 36px; height: 36px; background: #000; color: #a3e635; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px;">1</div>
                        <div>
                            <h3 style="font-size: 15px; font-weight: 800; margin: 0 0 4px;">Agrega a tus alumnos</h3>
                            <p style="font-size: 13px; color: #555; margin: 0; line-height: 1.5;">
                                Haz clic en <strong>"Gestionar Alumnos"</strong> arriba. Selecciona un curso, y agrega a tus alumnos escribiendo su <strong>nombre y correo</strong>. Puedes agregar varios a la vez.
                            </p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 16px; align-items: flex-start; margin-bottom: 24px;">
                        <div style="min-width: 36px; height: 36px; background: #000; color: #a3e635; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px;">2</div>
                        <div>
                            <h3 style="font-size: 15px; font-weight: 800; margin: 0 0 4px;">Ellos reciben un correo</h3>
                            <p style="font-size: 13px; color: #555; margin: 0; line-height: 1.5;">
                                Cada alumno nuevo recibe un correo automatico con sus <strong>credenciales de acceso</strong> (email + contraseña temporal) y un enlace directo para entrar al curso.
                            </p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 16px; align-items: flex-start; margin-bottom: 24px;">
                        <div style="min-width: 36px; height: 36px; background: #000; color: #a3e635; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px;">3</div>
                        <div>
                            <h3 style="font-size: 15px; font-weight: 800; margin: 0 0 4px;">Monitorea su avance</h3>
                            <p style="font-size: 13px; color: #555; margin: 0; line-height: 1.5;">
                                Regresa a este panel para ver el <strong>progreso de cada alumno</strong> por curso. Veras badges de color con su porcentaje de avance: gris (sin comenzar), amarillo (en progreso) y verde (completado).
                            </p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 16px; align-items: flex-start; padding-top: 16px; border-top: 2px dashed #eee;">
                        <div style="min-width: 36px; height: 36px; background: #f3f4f6; color: #555; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px;">?</div>
                        <div>
                            <h3 style="font-size: 15px; font-weight: 800; margin: 0 0 4px;">Tu plan actual</h3>
                            <p style="font-size: 13px; color: #555; margin: 0; line-height: 1.5;">
                                Tienes espacio para <strong>{{ $user->student_limit }} alumnos</strong>. Si necesitas mas, contactanos en <a href="mailto:hola@keiyi.digital" style="color: #000; font-weight: 700;">hola@keiyi.digital</a> para ampliar tu plan.
                            </p>
                        </div>
                    </div>
                </div>
                <div style="padding: 0 28px 28px; text-align: center;">
                    <a href="{{ route('profesor.students') }}" class="btn-primary" style="padding: 16px 40px; font-size: 15px;">Agregar mis primeros alumnos</a>
                </div>
            </div>
        @else
            <h2 style="font-size: 20px; font-weight: 800; margin-bottom: 16px;">Avance de tus alumnos</h2>
            <table class="prof-table">
                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>Email</th>
                        @foreach($courses as $course)
                            <th style="text-align: center;">{{ $course->emoji }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        @php $progress = $studentProgress[$student->id] ?? ['enrollments' => collect()]; @endphp
                        <tr>
                            <td style="font-weight: 600;">{{ $student->name }}</td>
                            <td style="color: #555; font-size: 13px;">{{ $student->email }}</td>
                            @foreach($courses as $course)
                                @php $enrollment = $progress['enrollments']->get($course->slug); @endphp
                                <td style="text-align: center;">
                                    @if($enrollment)
                                        @if($enrollment->progress_percent >= 100)
                                            <span class="badge badge-green">100%</span>
                                        @elseif($enrollment->progress_percent > 0)
                                            <span class="badge badge-yellow">{{ $enrollment->progress_percent }}%</span>
                                        @else
                                            <span class="badge badge-gray">0%</span>
                                        @endif
                                    @else
                                        <span style="color: #ccc;">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <x-keiyi-footer />
    <script src="{{ asset('script.js') }}"></script>
</body>
</html>
