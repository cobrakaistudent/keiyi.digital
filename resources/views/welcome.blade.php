<!DOCTYPE html>
<html lang="es">

<head>
    <x-keiyi-head title="Keiyi Digital | Marketing + IA para LATAM" description="Marketing Digital que se atreve a ser diferente. Agencia + Academia con inteligencia artificial." />
</head>

<body>

    <!-- Capa de Doodles -->
    <div class="doodle-bg">
        <svg class="doodle-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <path d="M10,50 Q25,25 40,50 T70,50 T100,50" fill="none" stroke="black" stroke-width="2" />
        </svg>
        <svg class="doodle-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="40" fill="none" stroke="black" stroke-width="2" stroke-dasharray="5,5" />
        </svg>
    </div>

    <x-keiyi-nav />

    <!-- Header / Hero -->
    <header id="hero" class="hero" style="background-image: url('{{ asset('v12_5_hero_bg.png') }}');">
        <div class="container hero-container">
            <div class="hero-bubble">
                <div class="hero-content text-center">
                    <div class="hand-note">¡Hola mundo! ✌️</div>
                    <h1 class="hero-title">Marketing que hace <span class="highlight-scribble">POP.</span></h1>
                    <p class="hero-desc">Rompemos el molde aburrido. Creamos experiencias digitales que la gente quiere tocar, ver y compartir.</p>
                    <div class="btn-group centered">
                        <a href="#pricing" class="btn-primary">Ver Planes</a>
                        <a href="#about" class="btn-text">Descubre más ⤵</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Sección Valor -->
    <section id="value-prop" class="section">
        <div class="container">
            <div class="section-intro">
                <h2>La Fórmula <span class="funky-text">Secreta</span></h2>
                <p class="lead">Menos "blah blah", más acción.</p>
            </div>

            <div class="grid-3">
                <div class="card funky-card">
                    <div class="card-icon bg-yellow">⚡</div>
                    <h3>Rápidos</h3>
                    <p>Nos movemos a la velocidad de internet. Sin burocracia, solo envíos.</p>
                </div>
                <div class="card funky-card">
                    <div class="card-icon bg-blue">💎</div>
                    <h3>Reales</h3>
                    <p>Precios claros. Sin letras chiquitas. Lo que ves es lo que hay.</p>
                </div>
                <div class="card funky-card">
                    <div class="card-icon bg-pink">🚀</div>
                    <h3>Flexibles</h3>
                    <p>Escalamos contigo. Desde startups hasta imperios.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="section">
        <div class="container">
            <div class="section-intro text-center">
                <h2>Elige tu Kit</h2>
                <div class="squiggly-line"></div>
            </div>

            <div class="pricing-grid">
                <div class="price-box">
                    <div class="price-header">
                        <h4>Básico</h4>
                        <h3>Despegue</h3>
                    </div>
                    <ul class="check-list">
                        <li>Identidad de Marca</li>
                        <li>Landing Page</li>
                        <li>Setup de Redes</li>
                    </ul>
                    <a href="#contact" class="btn-outline">Elegir</a>
                </div>

                <div class="price-box featured">
                    <span class="pop-tag">Fan Favorite</span>
                    <div class="price-header">
                        <h4>Crecimiento</h4>
                        <h3>Impulso</h3>
                    </div>
                    <ul class="check-list">
                        <li>Community Manager</li>
                        <li>15 Posts/Mes</li>
                        <li>Reporte Mensual</li>
                    </ul>
                    <a href="#contact" class="btn-primary full">¡Lo Quiero!</a>
                </div>

                <div class="price-box">
                    <div class="price-header">
                        <h4>Escala</h4>
                        <h3>Dominio</h3>
                    </div>
                    <ul class="check-list">
                        <li>Estrategia Semanal</li>
                        <li>Web Avanzada</li>
                        <li>Ads Management</li>
                    </ul>
                    <a href="#contact" class="btn-outline">Elegir</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 3D World Teaser -->
    <section id="3d-teaser" class="section">
        <div class="container">
            <div class="section-intro text-center">
                <span class="hand-note">¡Nuevo! ✨</span>
                <h2>3D <span class="highlight-scribble">World</span></h2>
                <p class="lead">Del cerebro a la realidad. Fabricamos tus ideas.</p>
            </div>

            <div class="grid-3">
                <div class="card funky-card">
                    <div class="step-number">01</div>
                    <div class="card-icon bg-yellow">🧠</div>
                    <h3>Imagina</h3>
                    <p>Si puedes soñarlo, existe. Tráenos tu idea loca, boceto o servilleta.</p>
                </div>
                <div class="card funky-card">
                    <div class="step-number">02</div>
                    <div class="card-icon bg-blue">📐</div>
                    <h3>Diseña</h3>
                    <p>Modelado 3D profesional. Convertimos conceptos abstractos en polígonos tangibles.</p>
                </div>
                <div class="card funky-card">
                    <div class="step-number">03</div>
                    <div class="card-icon bg-pink">🖨️</div>
                    <h3>Imprime</h3>
                    <p>Materializamos tu visión. Calidad brutal, materiales resistentes y entrega rápida.</p>
                </div>
            </div>
            
            <div class="btn-group centered">
                <a href="{{ url('/3d-world') }}" class="btn-primary">Entrar al Lab 3D</a>
            </div>
        </div>
    </section>

    <!-- Keiyi Academy Detailed -->
    <section id="academy-teaser" class="section bg-gray-50 border-y-2 border-indigo-100">
        <div class="container">
            <div class="section-intro text-center">
                <span class="hand-note">Aprende con los Pro ✨</span>
                <h2>Keiyi <span class="highlight-scribble">Academy</span></h2>
                <p class="lead">Domina la IA y el Marketing de Élite con nuestros talleres prácticos.</p>
            </div>

            <div class="grid-3 mb-16">
                <div class="card academy-card">
                    <div class="card-icon bg-blue">🧠</div>
                    <span class="tag">Elite</span>
                    <h3>Marketing Elite</h3>
                    <p>Sistemas de venta automatizados, ManyChat, Make y Claude 3.5 en un solo flujo de profit.</p>
                </div>
                <div class="card academy-card">
                    <div class="card-icon bg-pink">🎬</div>
                    <span class="tag">Viral</span>
                    <h3>Contenido Viral</h3>
                    <p>Producción masiva de Reels y TikTok con IA. Domina el algoritmo en 30 minutos.</p>
                </div>
                <div class="card academy-card">
                    <div class="card-icon bg-yellow">⚡</div>
                    <span class="tag">Pro</span>
                    <h3>Productividad IA</h3>
                    <p>Optimiza tu sistema operativo y tu agenda para trabajar solo 4 horas al día.</p>
                </div>
            </div>

            <!-- Academy Membership Box -->
            <div class="academy-membership-box">
                <div class="membership-header">
                    <h3>Pase de Acceso Total</h3>
                    <div class="price">
                        <span class="currency">$</span>
                        <span class="amount">49</span>
                        <span class="period">/ mes</span>
                    </div>
                </div>
                <div class="membership-body">
                    <ul class="check-list-grid">
                        <li><span class="check">✔</span> Los 3 Talleres Completos (15+ Lecciones)</li>
                        <li><span class="check">✔</span> Banco de Prompts Maestro (Actualizado 2026)</li>
                        <li><span class="check">✔</span> Cheat Sheets Descargables por taller</li>
                        <li><span class="check">✔</span> Actualizaciones cada 30 días según tendencias</li>
                        <li><span class="check">✔</span> Scripts de vídeo listos para producir</li>
                        <li><span class="check">✔</span> Acceso a la Comunidad Elite en Telegram</li>
                    </ul>
                </div>
                <div class="membership-footer">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary full">Entrar a tus Clases</a>
                    @else
                        <a href="{{ route('register') }}" class="btn-primary full">Registrarme y Empezar Ahora</a>
                    @endauth
                    <p class="text-xs text-gray-400 mt-4">Sujeto a aprobación por el administrador. Membresía mensual sin permanencia.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About -->
    <section id="about" class="section text-center">
        <div class="container">
            <div class="blob-bg"></div>
            <div class="about-content">
                <h2>¿Quiénes Somos?</h2>
                <p class="big-text">Somos los nerds cool que conectan el caos creativo con resultados de negocio.</p>
                <div class="mission-statement">
                    <strong>Misión:</strong> Democratizar el diseño pro.
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Contacto / Cotización -->
    <section id="contact" class="section" style="background: #1a1a1a; color: #fff;">
        <div class="container">
            <div class="section-intro text-center">
                <h2 style="color: #fff;">¿Hablamos? <span class="highlight-scribble">Escríbenos.</span></h2>
                <p class="lead" style="color: #aaa;">Cuéntanos tu proyecto. Respondemos en menos de 24 horas.</p>
            </div>

            @if (session('contact_sent'))
                <div style="background: #a3e635; color: #1a1a1a; border: 3px solid #000; box-shadow: 4px 4px 0 #000; padding: 20px 24px; font-weight: 700; font-size: 15px; max-width: 600px; margin: 0 auto 32px; text-align: center;">
                    ¡Mensaje recibido! Te contactaremos pronto.
                </div>
            @endif

            <form method="POST" action="{{ route('contacto.store') }}" style="max-width: 600px; margin: 0 auto; display: flex; flex-direction: column; gap: 16px;">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <input type="text" name="name" placeholder="Tu nombre *" required
                            value="{{ old('name') }}"
                            style="width: 100%; padding: 14px 16px; border: 3px solid #fff; background: transparent; color: #fff; font-family: inherit; font-size: 14px; font-weight: 600; outline: none;">
                        @error('name')<p style="color: #f87171; font-size: 12px; margin-top: 4px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <input type="email" name="email" placeholder="Tu correo *" required
                            value="{{ old('email') }}"
                            style="width: 100%; padding: 14px 16px; border: 3px solid #fff; background: transparent; color: #fff; font-family: inherit; font-size: 14px; font-weight: 600; outline: none;">
                        @error('email')<p style="color: #f87171; font-size: 12px; margin-top: 4px;">{{ $message }}</p>@enderror
                    </div>
                </div>

                <select name="service"
                    style="width: 100%; padding: 14px 16px; border: 3px solid #fff; background: #1a1a1a; color: #fff; font-family: inherit; font-size: 14px; font-weight: 600; outline: none; appearance: none;">
                    <option value="">¿Qué necesitas? (opcional)</option>
                    <option value="marketing" {{ old('service') === 'marketing' ? 'selected' : '' }}>Marketing Digital</option>
                    <option value="branding"  {{ old('service') === 'branding'  ? 'selected' : '' }}>Identidad de Marca</option>
                    <option value="academia"  {{ old('service') === 'academia'  ? 'selected' : '' }}>Academia / Cursos</option>
                    <option value="3d"        {{ old('service') === '3d'        ? 'selected' : '' }}>Impresión 3D</option>
                    <option value="otro"      {{ old('service') === 'otro'      ? 'selected' : '' }}>Otro</option>
                </select>

                <textarea name="message" placeholder="Cuéntanos tu proyecto *" required rows="5"
                    style="width: 100%; padding: 14px 16px; border: 3px solid #fff; background: transparent; color: #fff; font-family: inherit; font-size: 14px; font-weight: 600; outline: none; resize: vertical;">{{ old('message') }}</textarea>
                @error('message')<p style="color: #f87171; font-size: 12px; margin-top: -12px;">{{ $message }}</p>@enderror

                <button type="submit" id="contact-submit"
                    style="background: #a3e635; color: #1a1a1a; border: 3px solid #a3e635; padding: 16px; font-family: inherit; font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; box-shadow: 4px 4px 0 #000; transition: all 0.15s;">
                    Enviar Mensaje
                </button>
                <script>
                    document.currentScript.closest('form').addEventListener('submit', function() {
                        var btn = document.getElementById('contact-submit');
                        btn.disabled = true;
                        btn.textContent = 'Enviando...';
                    });
                </script>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <x-keiyi-footer />

    <!-- JS Estático Original -->
    <script src="{{ asset('script.js') }}"></script>
</body>

</html>
