<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Arial', sans-serif; background: #f4efeb; margin: 0; padding: 40px 20px; }
        .wrap { max-width: 560px; margin: 0 auto; background: #fff; border: 3px solid #000; box-shadow: 6px 6px 0 #a3e635; }
        .header { background: #1a1a1a; padding: 32px; text-align: center; }
        .header h1 { color: #a3e635; font-size: 28px; margin: 0; letter-spacing: 2px; text-transform: uppercase; }
        .header p { color: #aaa; font-size: 12px; margin: 8px 0 0; text-transform: uppercase; letter-spacing: 1px; }
        .body { padding: 40px 32px; }
        .body p { color: #333; font-size: 15px; line-height: 1.7; margin: 0 0 16px; }
        .course-box { background: #f4efeb; border: 3px solid #000; box-shadow: 3px 3px 0 #a3e635; padding: 20px 24px; margin: 24px 0; }
        .course-box .label { font-size: 10px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #888; margin-bottom: 6px; }
        .course-box .title { font-size: 18px; font-weight: 800; color: #1a1a1a; text-transform: uppercase; }
        .badge { display: inline-block; background: #a3e635; color: #1a1a1a; font-weight: 800; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; padding: 6px 16px; border: 2px solid #000; margin: 16px 0; }
        .footer { background: #1a1a1a; padding: 20px 32px; text-align: center; }
        .footer p { color: #666; font-size: 12px; margin: 0; }
        .footer a { color: #a3e635; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="header">
            <h1>Keiyi Academy</h1>
            <p>Portal de Alumnos</p>
        </div>
        <div class="body">
            <p>Hola <strong>{{ $studentName }}</strong>,</p>
            <p>Tu inscripcion ha sido registrada exitosamente. En cuanto el contenido este disponible, seras el primero en saberlo.</p>

            <div class="course-box">
                <div class="label">Curso inscrito</div>
                <div class="title">{{ $courseTitle }}</div>
            </div>

            <div class="badge">Inscripcion Confirmada</div>

            <p>Mientras tanto, tu acceso al portal sigue activo. Puedes revisar el estado de tus cursos en cualquier momento.</p>
            <p>Cualquier pregunta, respondemos rapido. Bienvenido al ecosistema Keiyi.</p>
            <p><strong>El equipo de Keiyi Digital</strong></p>
        </div>
        <div class="footer">
            <p>© 2026 Keiyi Digital · <a href="{{ url('/academia') }}">Acceder al Portal</a></p>
        </div>
    </div>
</body>
</html>
