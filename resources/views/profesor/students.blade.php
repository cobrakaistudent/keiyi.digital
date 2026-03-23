<!DOCTYPE html>
<html lang="es">
<head>
    <x-keiyi-head title="Gestionar Alumnos — Keiyi Digital" />
    <style>
        .prof-container { max-width: 720px; margin: 0 auto; padding: 40px 20px; font-family: 'Space Grotesk', sans-serif; }
        .card { border: 3px solid #000; padding: 28px; box-shadow: 6px 6px 0 #000; background: #fff; margin-bottom: 24px; }
        .card h2 { font-size: 20px; font-weight: 800; margin-bottom: 16px; }
        .form-group { margin-bottom: 14px; }
        .form-group label { font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px; }
        .form-group select, .form-group input { width: 100%; padding: 12px; border: 2px solid #000; font-family: 'Space Grotesk', sans-serif; font-size: 14px; box-sizing: border-box; background: #fff; }
        .student-row { display: grid; grid-template-columns: 1fr 1fr 40px; gap: 8px; align-items: end; margin-bottom: 8px; }
        .student-row input { padding: 10px; border: 2px solid #000; font-family: 'Space Grotesk', sans-serif; font-size: 14px; box-sizing: border-box; }
        .btn-remove { background: none; border: 2px solid #dc2626; color: #dc2626; width: 36px; height: 42px; cursor: pointer; font-size: 18px; font-weight: 700; }
        .btn-add { background: none; border: 2px dashed #000; padding: 10px; width: 100%; cursor: pointer; font-family: 'Space Grotesk', sans-serif; font-size: 13px; font-weight: 700; margin-bottom: 16px; }
        .btn-add:hover { background: #f3f4f6; }
        .btn-primary { background: #000; color: #fff; padding: 14px; border: none; font-family: 'Space Grotesk', sans-serif; font-size: 14px; font-weight: 700; letter-spacing: 1px; cursor: pointer; text-transform: uppercase; width: 100%; }
        .existing-student { display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; border-bottom: 1px solid #eee; }
        .existing-student:last-child { border-bottom: none; }
        .quota-bar { display: flex; justify-content: space-between; padding: 8px 0; font-size: 13px; color: #555; }
        .alert { padding: 12px 16px; margin-bottom: 16px; font-size: 14px; font-weight: 600; border: 2px solid; }
        .alert-success { background: #dcfce7; border-color: #16a34a; color: #16a34a; }
        .alert-error { background: #fef2f2; border-color: #dc2626; color: #dc2626; }
    </style>
</head>
<body>
    <x-keiyi-nav />

    <div class="prof-container">

        <div style="margin-bottom: 24px;">
            <a href="{{ route('profesor.dashboard') }}" style="color: #555; text-decoration: none; font-size: 14px;">&larr; Volver al panel</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Cuota --}}
        <div class="card">
            <div class="quota-bar">
                <span>Alumnos: <strong>{{ $students->count() }} / {{ $user->student_limit }}</strong></span>
                <span>Disponibles: <strong>{{ $user->remainingStudentSlots() }}</strong></span>
            </div>
            <div style="height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                <div style="height: 100%; border-radius: 4px; background: {{ $students->count() >= $user->student_limit ? '#dc2626' : '#f59e0b' }}; width: {{ $user->student_limit > 0 ? round(($students->count() / $user->student_limit) * 100) : 0 }}%;"></div>
            </div>
        </div>

        {{-- Agregar alumnos --}}
        @if($user->canEnrollMoreStudents())
        <div class="card">
            <h2>Inscribir alumnos</h2>

            <form method="POST" action="{{ route('profesor.students.add') }}" id="add-students-form">
                @csrf

                <div class="form-group">
                    <label for="course_slug">Curso</label>
                    <select name="course_slug" id="course_slug" required>
                        <option value="">Selecciona un curso...</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->slug }}">{{ $course->emoji }} {{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <label style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 8px;">Alumnos (nombre + correo)</label>

                <div id="students-list">
                    <div class="student-row">
                        <input type="text" name="students[0][name]" placeholder="Nombre completo" required>
                        <input type="email" name="students[0][email]" placeholder="correo@ejemplo.com" required>
                        <button type="button" class="btn-remove" onclick="this.parentElement.remove()" title="Quitar">&times;</button>
                    </div>
                </div>

                <button type="button" class="btn-add" id="btn-add-student">+ Agregar otro alumno</button>

                <button type="submit" class="btn-primary">Inscribir alumnos</button>
            </form>
        </div>
        @else
        <div class="card" style="text-align: center;">
            <p style="font-weight: 700;">Has alcanzado tu límite de {{ $user->student_limit }} alumnos</p>
            <p style="color: #555; font-size: 14px; margin-top: 4px;">Contacta a soporte para ampliar tu plan.</p>
        </div>
        @endif

        {{-- Lista de alumnos actuales --}}
        @if($students->isNotEmpty())
        <div class="card">
            <h2>Tus alumnos ({{ $students->count() }})</h2>
            @foreach($students as $student)
                <div class="existing-student">
                    <div>
                        <strong>{{ $student->name }}</strong>
                        <span style="color: #555; font-size: 13px; margin-left: 8px;">{{ $student->email }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

    </div>

    <x-keiyi-footer />
    <script src="{{ asset('script.js') }}"></script>
    <script>
        (function() {
            let studentIndex = 1;
            const btnAdd = document.getElementById('btn-add-student');
            const list = document.getElementById('students-list');
            if (btnAdd && list) {
                btnAdd.addEventListener('click', function() {
                    const row = document.createElement('div');
                    row.className = 'student-row';
                    const nameInput = document.createElement('input');
                    nameInput.type = 'text';
                    nameInput.name = 'students[' + studentIndex + '][name]';
                    nameInput.placeholder = 'Nombre completo';
                    nameInput.required = true;
                    const emailInput = document.createElement('input');
                    emailInput.type = 'email';
                    emailInput.name = 'students[' + studentIndex + '][email]';
                    emailInput.placeholder = 'correo@ejemplo.com';
                    emailInput.required = true;
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn-remove';
                    removeBtn.title = 'Quitar';
                    removeBtn.textContent = '\u00D7';
                    removeBtn.addEventListener('click', function() { row.remove(); });
                    row.appendChild(nameInput);
                    row.appendChild(emailInput);
                    row.appendChild(removeBtn);
                    list.appendChild(row);
                    studentIndex++;
                });
            }
        })();
    </script>
</body>
</html>
