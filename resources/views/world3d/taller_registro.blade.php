<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Taller 3D | Keiyi</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Gloria+Hallelujah&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body style="background-color: var(--color-bg); min-height: 100vh; display: flex; flex-direction: column;">
    
    <nav class="navbar" style="border-bottom: 2px solid black;">
        <div class="container navbar-container">
            <a href="{{ url('/') }}" class="logo">keiyi<span class="dot">.</span></a>
            <a href="{{ url('/3d-world') }}" class="btn-outline" style="border: 2px solid black; padding: 0.5rem 1rem; color: black; text-decoration: none; font-weight: bold; border-radius: 8px; box-shadow: 2px 2px 0 0 black; background: white;">← Galería 3D</a>
        </div>
    </nav>

    <main class="container" style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 4rem 1rem;">
        <div class="funky-card" style="max-width: 500px; width: 100%; padding: 3rem 2rem; background: var(--color-light); border: 2px solid black; border-radius: 12px; box-shadow: 8px 8px 0 0 rgba(0,0,0,1);">
            <div class="text-center" style="margin-bottom: 2rem;">
                <div class="hand-note" style="display: inline-block; transform: rotate(-3deg); margin-bottom: 0.5rem;">Invitación VIP 🎫</div>
                <h1 style="font-size: 2.2rem; margin-bottom: 0.5rem; color: var(--color-blue);">The Print Lab</h1>
                <p style="font-size: 1.1rem; line-height: 1.4;">Únete a nuestra cartera de clientes de impresión 3D High-End. Revisamos cada solicitud a mano.</p>
            </div>

            @if(session('registro_sent'))
                <div style="text-align: center; padding: 2rem 0;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">🚀</div>
                    <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">¡Solicitud Recibida!</h3>
                    <p>Nuestro equipo revisará tu perfil y te enviará el acceso al Print Lab en las próximas 24h.</p>
                    <a href="{{ url('/3d-world') }}" class="btn-outline" style="display: inline-block; margin-top: 2rem; border: 2px solid black; color: black; font-weight: bold; padding: 0.8rem 1.5rem; text-decoration: none; border-radius: 8px; box-shadow: 4px 4px 0 0 black;">Volver a la Galería</a>
                </div>
            @else
                <form action="{{ route('taller.registro.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @csrf
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label for="name" style="font-weight: bold; font-family: 'Space Grotesk', sans-serif;">Nombre completo o Empresa</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required style="padding: 1rem; border: 2px solid black; border-radius: 8px; font-family: 'Space Grotesk', sans-serif; font-size: 1rem; width: 100%; box-sizing: border-box;">
                        @error('name')<span style="color:red;font-size:12px;">{{ $message }}</span>@enderror
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label for="email" style="font-weight: bold; font-family: 'Space Grotesk', sans-serif;">Correo electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required style="padding: 1rem; border: 2px solid black; border-radius: 8px; font-family: 'Space Grotesk', sans-serif; font-size: 1rem; width: 100%; box-sizing: border-box;">
                        @error('email')<span style="color:red;font-size:12px;">{{ $message }}</span>@enderror
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label for="message" style="font-weight: bold; font-family: 'Space Grotesk', sans-serif;">¿Para qué usarás el servicio?</label>
                        <textarea id="message" name="message" rows="4" placeholder="Ej. Prototipos de diseño industrial, props para cosplay, regalos corporativos..." required style="padding: 1rem; border: 2px solid black; border-radius: 8px; font-family: 'Space Grotesk', sans-serif; font-size: 1rem; resize: vertical; width: 100%; box-sizing: border-box;">{{ old('message') }}</textarea>
                        @error('message')<span style="color:red;font-size:12px;">{{ $message }}</span>@enderror
                    </div>

                    <button type="submit" class="btn-primary" style="background: var(--color-orange); color: black; font-size: 1.2rem; margin-top: 1rem; width: 100%; cursor: pointer;">Enviar Solicitud</button>
                </form>
            @endif
        </div>
    </main>

    <footer class="footer" style="padding: 2rem 0; border-top: 2px solid black; text-align: center; background: white;">
        <p style="margin: 0; font-weight: bold; font-family: 'Space Grotesk', sans-serif;">© 2026 Keiyi Agency & Academy</p>
    </footer>
</body>
</html>
