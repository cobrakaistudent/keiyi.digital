<!DOCTYPE html>
<html lang="es">
<head>
    <x-keiyi-head title="Entrar — Keiyi Digital" description="Inicia sesión en tu cuenta de Keiyi Digital." />
</head>
<body>
    <x-keiyi-nav />

    <section style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 48px 20px;">
        <div style="max-width: 420px; width: 100%; font-family: 'Space Grotesk', sans-serif;">

            <div style="text-align: center; margin-bottom: 32px;">
                <a href="/" style="font-size: 32px; font-weight: 800; color: #000; text-decoration: none;">keiyi<span style="color: var(--color-orange);">.</span></a>
                <p style="color: #555; margin-top: 8px; font-size: 14px;">Entra a tu cuenta</p>
            </div>

            <div style="border: 3px solid #000; padding: 32px; box-shadow: 6px 6px 0 #000; background: #fff;">

                @if (session('status'))
                    <div style="background: #dcfce7; border: 2px solid #16a34a; padding: 10px 16px; margin-bottom: 20px; font-size: 13px; font-weight: 600; border-radius: 4px;">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" style="display: flex; flex-direction: column; gap: 16px;">
                    @csrf

                    <div>
                        <label for="email" style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            style="width: 100%; padding: 12px; border: 2px solid #000; font-family: 'Space Grotesk', sans-serif; font-size: 14px; box-sizing: border-box;">
                        @error('email')
                            <p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" style="font-size: 13px; font-weight: 700; display: block; margin-bottom: 4px;">Contraseña</label>
                        <input id="password" type="password" name="password" required
                            style="width: 100%; padding: 12px; border: 2px solid #000; font-family: 'Space Grotesk', sans-serif; font-size: 14px; box-sizing: border-box;">
                        @error('password')
                            <p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <input id="remember" type="checkbox" name="remember" style="width: 16px; height: 16px; accent-color: #000;">
                        <label for="remember" style="font-size: 13px; color: #555;">Recordarme</label>
                    </div>

                    <button type="submit"
                        style="background: #000; color: #fff; padding: 14px; border: none; font-family: 'Space Grotesk', sans-serif; font-size: 14px; font-weight: 700; letter-spacing: 1px; cursor: pointer; text-transform: uppercase;">
                        Entrar
                    </button>
                </form>

                <div style="margin-top: 20px; display: flex; justify-content: space-between; font-size: 13px;">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="color: #555; text-decoration: underline;">¿Olvidaste tu contraseña?</a>
                    @endif
                    <a href="{{ route('register') }}" style="color: #000; font-weight: 700; text-decoration: none; border-bottom: 2px solid #000;">Crear cuenta</a>
                </div>
            </div>
        </div>
    </section>

    <x-keiyi-footer />
    <script src="{{ asset('script.js') }}"></script>
</body>
</html>
