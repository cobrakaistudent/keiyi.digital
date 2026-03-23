<!DOCTYPE html>
<html lang="es">
<head>
    <x-keiyi-head title="Registro — Keiyi Digital" description="Elige tu tipo de cuenta para registrarte en Keiyi Digital." />
</head>
<body>
    <x-keiyi-nav />

    <section style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 48px 20px;">
        <div style="max-width: 800px; width: 100%; font-family: 'Space Grotesk', sans-serif;">

            <div style="text-align: center; margin-bottom: 40px;">
                <a href="/" style="font-size: 32px; font-weight: 800; color: #000; text-decoration: none;">keiyi<span style="color: var(--color-orange);">.</span></a>
                <h1 style="font-size: 28px; font-weight: 800; margin-top: 12px;">Crea tu cuenta</h1>
                <p style="color: #555; margin-top: 8px; font-size: 15px;">Elige el tipo de cuenta que necesitas</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px;">

                {{-- Alumno --}}
                <a href="{{ route('register') }}" style="text-decoration: none; color: inherit;">
                    <div style="border: 3px solid #000; padding: 28px 24px; box-shadow: 6px 6px 0 #000; background: #fff; transition: transform 0.2s; cursor: pointer;"
                         onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
                        <div style="font-size: 40px; margin-bottom: 12px;">&#x1F393;</div>
                        <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 8px;">Alumno</h3>
                        <p style="font-size: 13px; color: #555; line-height: 1.5;">
                            Toma cursos de marketing digital, IA y negocios. Accede a la academia completa.
                        </p>
                        <div style="margin-top: 16px; font-size: 13px; font-weight: 700; color: var(--color-orange);">
                            Registrarme &rarr;
                        </div>
                    </div>
                </a>

                {{-- Profesor --}}
                <a href="{{ route('registro.profesor') }}" style="text-decoration: none; color: inherit;">
                    <div style="border: 3px solid #000; padding: 28px 24px; box-shadow: 6px 6px 0 #000; background: #fff; transition: transform 0.2s; cursor: pointer;"
                         onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
                        <div style="font-size: 40px; margin-bottom: 12px;">&#x1F4DA;</div>
                        <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 8px;">Profesor</h3>
                        <p style="font-size: 13px; color: #555; line-height: 1.5;">
                            Ofrece cursos grupales a tus alumnos. Inscribe grupos y monitorea su avance.
                        </p>
                        <div style="margin-top: 16px; font-size: 13px; font-weight: 700; color: var(--color-orange);">
                            Registrarme &rarr;
                        </div>
                    </div>
                </a>

                {{-- Cliente --}}
                <a href="{{ route('registro.cliente') }}" style="text-decoration: none; color: inherit;">
                    <div style="border: 3px solid #000; padding: 28px 24px; box-shadow: 6px 6px 0 #000; background: #fff; transition: transform 0.2s; cursor: pointer;"
                         onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
                        <div style="font-size: 40px; margin-bottom: 12px;">&#x1F4BC;</div>
                        <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 8px;">Cliente</h3>
                        <p style="font-size: 13px; color: #555; line-height: 1.5;">
                            Servicios de marketing digital y consultoría. Accede al portal de proyectos.
                        </p>
                        <div style="margin-top: 16px; font-size: 13px; font-weight: 700; color: var(--color-orange);">
                            Registrarme &rarr;
                        </div>
                    </div>
                </a>

            </div>

            <div style="text-align: center; margin-top: 32px; font-size: 14px;">
                <span style="color: #555;">¿Ya tienes cuenta?</span>
                <a href="{{ route('login') }}" style="color: #000; font-weight: 700; text-decoration: none; border-bottom: 2px solid #000; margin-left: 4px;">Entrar</a>
            </div>
        </div>
    </section>

    <x-keiyi-footer />
    <script src="{{ asset('script.js') }}"></script>
</body>
</html>
