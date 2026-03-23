<!DOCTYPE html>
<html lang="es">
<head>
    <x-keiyi-head title="Verificar Certificado — Keiyi Digital" description="Verifica la autenticidad de un certificado de Keiyi Academy." />
</head>
<body>
    <x-keiyi-nav />

    <section style="min-height: 70vh; display: flex; align-items: center; justify-content: center; padding: 48px 20px;">
        <div style="max-width: 560px; width: 100%; font-family: 'Space Grotesk', sans-serif;">

            <div style="text-align: center; margin-bottom: 32px;">
                <div style="font-size: 48px; margin-bottom: 8px;">🎓</div>
                <h1 style="font-size: 28px; font-weight: 800; margin: 0 0 8px;">Verificar Certificado</h1>
                <p style="color: #555; font-size: 14px;">Ingresa el código del certificado para verificar su autenticidad.</p>
            </div>

            <form method="GET" action="{{ route('verificar') }}" style="display: flex; gap: 8px; margin-bottom: 24px;">
                <input type="text" name="code" value="{{ request('code') }}" placeholder="KY-XXXX-XXXX-XXXX" required
                    style="flex: 1; padding: 14px; border: 3px solid #000; font-family: 'Space Grotesk'; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; text-align: center;">
                <button type="submit"
                    style="background: #000; color: #fff; padding: 14px 24px; border: none; font-family: 'Space Grotesk'; font-size: 14px; font-weight: 700; cursor: pointer; letter-spacing: 1px;">
                    VERIFICAR
                </button>
            </form>

            @if(isset($certificate))
                <div style="border: 3px solid #16a34a; padding: 32px; box-shadow: 6px 6px 0 #16a34a; background: #f0fdf4;">
                    <div style="text-align: center; margin-bottom: 16px;">
                        <span style="font-size: 36px;">✅</span>
                        <h2 style="font-size: 20px; font-weight: 800; margin: 8px 0 0; color: #16a34a;">CERTIFICADO VÁLIDO</h2>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 12px; font-size: 14px;">
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #ddd; padding-bottom: 8px;">
                            <span style="color: #555; font-weight: 600;">Alumno:</span>
                            <span style="font-weight: 700;">{{ $certificate->student_name }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #ddd; padding-bottom: 8px;">
                            <span style="color: #555; font-weight: 600;">Curso:</span>
                            <span style="font-weight: 700;">{{ $certificate->course_title }}</span>
                        </div>
                        @if($certificate->score)
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #ddd; padding-bottom: 8px;">
                            <span style="color: #555; font-weight: 600;">Calificación:</span>
                            <span style="font-weight: 700;">{{ $certificate->score }}%</span>
                        </div>
                        @endif
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #ddd; padding-bottom: 8px;">
                            <span style="color: #555; font-weight: 600;">Fecha de emisión:</span>
                            <span style="font-weight: 700;">{{ $certificate->issued_at->format('d/m/Y') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #555; font-weight: 600;">Código:</span>
                            <span style="font-weight: 700; font-family: monospace; letter-spacing: 1px;">{{ $certificate->code }}</span>
                        </div>
                    </div>
                    <p style="text-align: center; margin: 16px 0 0; font-size: 11px; color: #888;">Emitido por Keiyi Digital — keiyi.digital/academy</p>
                </div>
            @elseif(request('code'))
                <div style="border: 3px solid #dc2626; padding: 24px; box-shadow: 6px 6px 0 #dc2626; background: #fef2f2; text-align: center;">
                    <span style="font-size: 36px;">❌</span>
                    <h2 style="font-size: 20px; font-weight: 800; margin: 8px 0 0; color: #dc2626;">CERTIFICADO NO ENCONTRADO</h2>
                    <p style="color: #555; font-size: 13px; margin-top: 8px;">El código ingresado no corresponde a ningún certificado válido. Verifica que esté escrito correctamente.</p>
                </div>
            @endif
        </div>
    </section>

    <x-keiyi-footer />
    <script src="{{ asset('script.js') }}"></script>
</body>
</html>
