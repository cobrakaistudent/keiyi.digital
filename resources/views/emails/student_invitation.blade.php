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
        .creds-box { background: #1a1a1a; color: #fff; padding: 20px 24px; margin: 24px 0; border: 3px solid #000; }
        .creds-box .label { font-size: 10px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #a3e635; margin-bottom: 10px; }
        .creds-box .field { margin-bottom: 8px; }
        .creds-box .field-label { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 1px; }
        .creds-box .field-value { font-size: 16px; font-weight: 700; color: #fff; font-family: monospace; margin-top: 2px; }
        .btn { display: inline-block; background: #a3e635; color: #1a1a1a; font-weight: 800; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; padding: 14px 32px; border: 3px solid #000; box-shadow: 3px 3px 0 #000; text-decoration: none; margin: 16px 0; }
        .warning { background: #fef3c7; border: 2px solid #d97706; padding: 12px 16px; font-size: 13px; color: #92400e; margin: 16px 0; }
        .footer { background: #1a1a1a; padding: 20px 32px; text-align: center; }
        .footer p { color: #666; font-size: 12px; margin: 0; }
        .footer a { color: #a3e635; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="header">
            <h1>Keiyi Academy</h1>
            <p>Invitacion de curso</p>
        </div>
        <div class="body">
            <p>Hola <strong>{{ $studentName }}</strong>,</p>
            <p><strong>{{ $profesorName }}</strong> te ha inscrito en un curso de Keiyi Academy. Tu cuenta ha sido creada automaticamente.</p>

            <div class="course-box">
                <div class="label">Curso inscrito</div>
                <div class="title">{{ $courseTitle }}</div>
            </div>

            <div class="creds-box">
                <div class="label">Tus credenciales de acceso</div>
                <div class="field">
                    <div class="field-label">Email</div>
                    <div class="field-value">{{ $studentEmail }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Contraseña temporal</div>
                    <div class="field-value">{{ $tempPassword }}</div>
                </div>
            </div>

            <div class="warning">
                Cambia tu contraseña despues de tu primer inicio de sesion desde tu perfil.
            </div>

            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="btn">Entrar a mi curso</a>
            </div>

            <p>Si tienes dudas sobre el curso, contacta directamente a tu profesor(a) <strong>{{ $profesorName }}</strong>.</p>
            <p><strong>El equipo de Keiyi Digital</strong></p>
        </div>
        <div class="footer">
            <p>&copy; 2026 Keiyi Digital &middot; <a href="{{ url('/academia') }}">Acceder al Portal</a></p>
        </div>
    </div>
</body>
</html>
