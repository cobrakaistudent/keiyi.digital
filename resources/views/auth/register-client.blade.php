<!DOCTYPE html>
<html lang="es">
<head>
    <x-keiyi-head title="Registro Cliente — Keiyi Digital" description="Regístrate como cliente de Keiyi Digital para servicios de marketing y consultoría." />
</head>
<body>
    <x-keiyi-nav />

    <section style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 48px 20px;">
        <div style="max-width: 420px; width: 100%; font-family: 'Space Grotesk', sans-serif;">

            <div style="text-align: center; margin-bottom: 32px;">
                <a href="/" style="font-size: 32px; font-weight: 800; color: #000; text-decoration: none;">keiyi<span style="color: var(--color-orange);">.</span></a>
                <p style="color: #555; margin-top: 8px; font-size: 14px;">Registro de Cliente</p>
            </div>

            <div style="border: 3px solid #000; padding: 32px; box-shadow: 6px 6px 0 #000; background: #fff;">

                <form method="POST" action="{{ route('registro.cliente.store') }}" style="display: flex; flex-direction: column; gap: 14px;">
                    @csrf

                    <div>
                        <label for="name" style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Nombre de contacto</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                            style="width: 100%; padding: 12px; border: 2px solid #000; font-family: 'Space Grotesk', sans-serif; font-size: 14px; box-sizing: border-box;">
                        @error('name') <p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="company_name" style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Empresa</label>
                        <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}" required
                            style="width: 100%; padding: 12px; border: 2px solid #000; font-family: 'Space Grotesk', sans-serif; font-size: 14px; box-sizing: border-box;">
                        @error('company_name') <p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label for="email" style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                style="width: 100%; padding: 12px; border: 2px solid #000; font-family: 'Space Grotesk', sans-serif; font-size: 14px; box-sizing: border-box;">
                            @error('email') <p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="phone" style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Teléfono</label>
                            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                                placeholder="55 1234 5678"
                                style="width: 100%; padding: 12px; border: 2px solid #000; font-family: 'Space Grotesk', sans-serif; font-size: 14px; box-sizing: border-box;">
                            @error('phone') <p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label for="password" style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Contraseña</label>
                            <input id="password" type="password" name="password" required
                                style="width: 100%; padding: 12px; border: 2px solid #000; font-family: 'Space Grotesk', sans-serif; font-size: 14px; box-sizing: border-box;">
                            @error('password') <p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
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
                    @error('accepts_terms') <p style="color: #dc2626; font-size: 12px;">{{ $message }}</p> @enderror

                    <button type="submit"
                        style="background: #000; color: #fff; padding: 14px; border: none; font-family: 'Space Grotesk', sans-serif; font-size: 14px; font-weight: 700; letter-spacing: 1px; cursor: pointer; text-transform: uppercase; margin-top: 4px;">
                        Solicitar acceso
                    </button>
                </form>

                <div style="margin-top: 16px; text-align: center; font-size: 13px;">
                    <a href="{{ route('registro') }}" style="color: #555; text-decoration: underline;">&larr; Elegir otro tipo de cuenta</a>
                </div>
            </div>
        </div>
    </section>

    <x-keiyi-footer />
    <script src="{{ asset('script.js') }}"></script>
</body>
</html>
