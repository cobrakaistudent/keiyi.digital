<!DOCTYPE html>
<html lang="es">
<head>
    <x-keiyi-head title="Crear cuenta — Keiyi Digital" description="Regístrate en Keiyi Digital para acceder a la Academia y más." />
</head>
<body>
    <x-keiyi-nav />

    <section style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 48px 20px;">
        <div style="max-width: 420px; width: 100%; font-family: 'Space Grotesk', sans-serif;">

            <div style="text-align: center; margin-bottom: 32px;">
                <a href="/" style="font-size: 32px; font-weight: 800; color: #000; text-decoration: none;">keiyi<span style="color: var(--color-orange);">.</span></a>
                <p style="color: #555; margin-top: 8px; font-size: 14px;">Crea tu cuenta para acceder a la Academia</p>
            </div>

            <div style="border: 3px solid #000; padding: 32px; box-shadow: 6px 6px 0 #000; background: #fff;">

                <form method="POST" action="{{ route('register') }}" style="display: flex; flex-direction: column; gap: 14px;">
                    @csrf

                    <div>
                        <label for="name" style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Nombre(s)</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                            style="width: 100%; padding: 12px; border: 2px solid #000; font-family: 'Space Grotesk', sans-serif; font-size: 14px; box-sizing: border-box;">
                        @error('name')
                            <p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label for="apellido_paterno" style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Apellido Paterno</label>
                            <input id="apellido_paterno" type="text" name="apellido_paterno" value="{{ old('apellido_paterno') }}" required
                                style="width: 100%; padding: 12px; border: 2px solid #000; font-family: 'Space Grotesk', sans-serif; font-size: 14px; box-sizing: border-box;">
                            @error('apellido_paterno')
                                <p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="apellido_materno" style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Apellido Materno</label>
                            <input id="apellido_materno" type="text" name="apellido_materno" value="{{ old('apellido_materno') }}"
                                style="width: 100%; padding: 12px; border: 2px solid #000; font-family: 'Space Grotesk', sans-serif; font-size: 14px; box-sizing: border-box;">
                            <p style="font-size: 11px; color: #999; margin-top: 2px;">Opcional</p>
                        </div>
                    </div>

                    <div>
                        <label for="email" style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            style="width: 100%; padding: 12px; border: 2px solid #000; font-family: 'Space Grotesk', sans-serif; font-size: 14px; box-sizing: border-box;">
                        @error('email')
                            <p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label for="password" style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Contraseña</label>
                            <input id="password" type="password" name="password" required
                                style="width: 100%; padding: 12px; border: 2px solid #000; font-family: 'Space Grotesk', sans-serif; font-size: 14px; box-sizing: border-box;">
                            @error('password')
                                <p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Confirmar</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                style="width: 100%; padding: 12px; border: 2px solid #000; font-family: 'Space Grotesk', sans-serif; font-size: 14px; box-sizing: border-box;">
                        </div>
                    </div>

                    <div style="display: flex; align-items: start; gap: 8px; margin-top: 4px;">
                        <input type="checkbox" name="accepts_terms" value="1" required style="width: 16px; height: 16px; margin-top: 2px; accent-color: #000;">
                        <span style="font-size: 12px; color: #555; line-height: 1.4;">
                            He leído y acepto el
                            <a href="{{ route('privacidad') }}" target="_blank" style="color: #000; font-weight: 700; text-decoration: underline;">Aviso de Privacidad</a>
                            y los
                            <a href="{{ route('terminos') }}" target="_blank" style="color: #000; font-weight: 700; text-decoration: underline;">Términos y Condiciones</a>.
                        </span>
                    </div>
                    @error('accepts_terms')
                        <p style="color: #dc2626; font-size: 12px;">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                        style="background: #000; color: #fff; padding: 14px; border: none; font-family: 'Space Grotesk', sans-serif; font-size: 14px; font-weight: 700; letter-spacing: 1px; cursor: pointer; text-transform: uppercase; margin-top: 4px;">
                        Crear cuenta
                    </button>
                </form>

                <div style="margin-top: 16px; text-align: center; font-size: 13px;">
                    <span style="color: #555;">¿Ya tienes cuenta?</span>
                    <a href="{{ route('login') }}" style="color: #000; font-weight: 700; text-decoration: none; border-bottom: 2px solid #000; margin-left: 4px;">Entrar</a>
                </div>
            </div>
        </div>
    </section>

    <x-keiyi-footer />
    <script src="{{ asset('script.js') }}"></script>
</body>
</html>
