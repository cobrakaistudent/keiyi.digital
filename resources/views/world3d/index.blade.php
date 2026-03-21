<!DOCTYPE html>
<html lang="es">

<head>
    <x-keiyi-head title="Keiyi 3D World | Imagina, Diseña, Imprime" description="Servicios de impresión y diseño 3D. Materializamos tus ideas." />
</head>

<body>

    <!-- Doodle Background Elements -->
    <div class="doodle-bg">
        <svg class="doodle-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <rect x="10" y="10" width="80" height="80" fill="none" stroke="black" stroke-width="2" rx="10" />
        </svg>
        <svg class="doodle-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <path d="M50,10 L90,90 L10,90 Z" fill="none" stroke="black" stroke-width="2" stroke-dasharray="5,5" />
        </svg>
    </div>

    <x-keiyi-nav />

    <!-- Header 3D -->
    <header class="hero" style="min-height: 60vh;">
        <div class="container hero-container">
            <div class="hero-bubble">
                <div class="hero-content text-center">
                    <div class="hand-note">Keiyi Lab 🧪</div>
                    <h1 class="hero-title">Tu Realidad, <span class="highlight-scribble">Impresa.</span></h1>
                    <p class="hero-desc">Bienvenido a 3D World. Donde los pixeles se convierten en átomos.</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Sección de Pasos -->
    <section class="section">
        <div class="container">
            <div class="section-intro text-center">
                <h2>El Proceso</h2>
                <p class="lead">De la nada al todo en 3 pasos.</p>
            </div>

            <div class="grid-3">
                <div class="card funky-card">
                    <div class="step-number">01</div>
                    <div class="card-icon bg-yellow" style="width: 70px; height: 70px; font-size: 2rem;">🧠</div>
                    <h3>Imagina</h3>
                    <p>Todo empieza con una idea. Puede ser un dibujo en una servilleta, una foto de referencia o simplemente un "Oye, ¿se puede hacer esto?".</p>
                    <ul class="check-list" style="margin-top: 1rem;">
                        <li>Bocetos rápidos</li>
                        <li>Brainstorming creativo</li>
                        <li>Análisis de viabilidad</li>
                    </ul>
                </div>

                <div class="card funky-card">
                    <div class="step-number">02</div>
                    <div class="card-icon bg-blue" style="width: 70px; height: 70px; font-size: 2rem;">📐</div>
                    <h3>Diseña</h3>
                    <p>Nuestros expertos en CAD toman el control. Esculpimos, modelamos y optimizamos tu objeto en el espacio digital.</p>
                    <ul class="check-list" style="margin-top: 1rem;">
                        <li>Modelado 3D (STL/OBJ)</li>
                        <li>Optimización para impresión</li>
                        <li>Renders previos</li>
                    </ul>
                </div>

                <div class="card funky-card">
                    <div class="step-number">03</div>
                    <div class="card-icon bg-pink" style="width: 70px; height: 70px; font-size: 2rem;">🖨️</div>
                    <h3>Imprime</h3>
                    <p>Las máquinas despiertan. Usamos filamentos de alta calidad (PLA, PETG, TPU) o Resina para traer tu objeto al mundo físico.</p>
                    <ul class="check-list" style="margin-top: 1rem;">
                        <li>Impresión FDM o Resina</li>
                        <li>Post-procesado y limpiezas</li>
                        <li>Envío a domicilio</li>
                    </ul>
                </div>
            </div>

        </div>
    </section>

    <!-- AMS Lite Status Widget -->
    <section class="section" style="padding-top: 0;">
        <div class="container">
            <div class="ams-status-widget funky-card" style="background: var(--color-light); border: 2px solid black; padding: 2rem; border-radius: 12px; box-shadow: 6px 6px 0 0 rgba(0,0,0,1);">
                <div class="text-center" style="margin-bottom: 2rem;">
                    <div class="hand-note" style="display: inline-block; transform: rotate(-3deg); margin-bottom: 0.5rem;">Bambu Lab A1 ✨</div>
                    <h2 style="font-size: 2rem; margin-bottom: 0.5rem;">The Print Lab</h2>
                    <p class="lead" style="margin-bottom: 0;"><strong>En el inyector ahora mismo (Envío 24-48h):</strong></p>
                </div>
                <div class="color-spools" style="display: flex; justify-content: center; gap: 3rem; flex-wrap: wrap;">
                    <div class="spool text-center">
                        <div style="width: 60px; height: 60px; border-radius: 50%; background-color: #000000; border: 3px solid black; box-shadow: 4px 4px 0 0 rgba(0,0,0,1); margin: 0 auto 0.8rem;"></div>
                        <span style="font-size: 1rem; font-weight: bold; font-family: 'Space Grotesk', sans-serif;">Negro</span>
                    </div>
                    <div class="spool text-center">
                        <div style="width: 60px; height: 60px; border-radius: 50%; background-color: #E2E2D5; border: 3px solid black; box-shadow: 4px 4px 0 0 rgba(0,0,0,1); margin: 0 auto 0.8rem;"></div>
                        <span style="font-size: 1rem; font-weight: bold; font-family: 'Space Grotesk', sans-serif;">Hueso</span>
                    </div>
                    <div class="spool text-center">
                        <div style="width: 60px; height: 60px; border-radius: 50%; background-color: #D32F2F; border: 3px solid black; box-shadow: 4px 4px 0 0 rgba(0,0,0,1); margin: 0 auto 0.8rem;"></div>
                        <span style="font-size: 1rem; font-weight: bold; font-family: 'Space Grotesk', sans-serif;">Rojo</span>
                    </div>
                    <div class="spool text-center">
                        <div style="width: 60px; height: 60px; border-radius: 50%; background-color: #1976D2; border: 3px solid black; box-shadow: 4px 4px 0 0 rgba(0,0,0,1); margin: 0 auto 0.8rem;"></div>
                        <span style="font-size: 1rem; font-weight: bold; font-family: 'Space Grotesk', sans-serif;">Azul</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Galería Pública 3D-World -->
    <section class="section" style="background-color: var(--color-bg);">
        <div class="container">
            <div class="section-intro text-center">
                <h2>Catálogo 3D</h2>
                <p class="lead">Piezas curadas. Modelos testeados. Impresión garantizada.</p>
            </div>

            {{-- Flash messages --}}
            @if(session('download_sent'))
                <div style="border: 2px solid #000; background: #e8f5e9; padding: 1rem 1.5rem; margin-bottom: 1.5rem; font-family: 'Space Grotesk', sans-serif; font-weight: 600; border-radius: 8px; box-shadow: 3px 3px 0 #000;">
                    {{ session('download_sent') }}
                </div>
            @endif
            @if(session('order_sent'))
                <div style="border: 2px solid #000; background: #e3f2fd; padding: 1rem 1.5rem; margin-bottom: 1.5rem; font-family: 'Space Grotesk', sans-serif; font-weight: 600; border-radius: 8px; box-shadow: 3px 3px 0 #000;">
                    {{ session('order_sent') }}
                </div>
            @endif
            @if(session('error'))
                <div style="border: 2px solid #000; background: #fce4ec; padding: 1rem 1.5rem; margin-bottom: 1.5rem; font-family: 'Space Grotesk', sans-serif; font-weight: 600; border-radius: 8px; box-shadow: 3px 3px 0 #000;">
                    {{ session('error') }}
                </div>
            @endif

            @if($items->isEmpty())
                <div style="border: 2px dashed #000; padding: 3rem; text-align: center; font-family: 'Space Grotesk', sans-serif; border-radius: 12px;">
                    <p style="font-size: 1.2rem; font-weight: 700; margin: 0 0 8px;">Catálogo en preparación.</p>
                    <p style="color: #555; margin: 0;">Pronto habrá modelos disponibles.</p>
                </div>
            @else
            <div class="grid-3">
                @foreach($items as $item)
                <div class="card funky-card" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.5rem;">
                    {{-- Embed o placeholder --}}
                    <div class="video-container" style="background: black; height: 350px; border-radius: 8px; border: 2px solid black; overflow: hidden; position: relative; box-shadow: inset 0 0 10px rgba(0,0,0,0.5);">
                        @if($item->embed_url)
                            <iframe src="{{ $item->embed_url }}" style="width:100%;height:100%;border:0;" allowfullscreen loading="lazy"></iframe>
                        @else
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white;">
                                <span style="font-size: 3rem;">🖨️</span>
                                <span style="font-family: 'Space Grotesk', sans-serif; font-weight: bold; margin-top: 1rem;">{{ $item->title }}</span>
                            </div>
                        @endif

                        @if($item->material === 'True Color AMS' || str_contains(strtolower($item->material ?? ''), 'ams'))
                        <div style="position: absolute; top: 12px; right: 12px; background: white; border: 2px solid black; border-radius: 20px; padding: 6px 12px; display: flex; align-items: center; gap: 8px; box-shadow: 3px 3px 0 0 black; z-index: 10;">
                            <div style="display: flex;">
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #00FFFF; display: inline-block;"></span>
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #FF00FF; display: inline-block; margin-left: -4px;"></span>
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #FFFF00; display: inline-block; margin-left: -4px;"></span>
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #000000; display: inline-block; margin-left: -4px;"></span>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 800; font-family: 'Space Grotesk', sans-serif; letter-spacing: 0.5px;">TRUE COLOR (AMS)</span>
                        </div>
                        @endif
                    </div>

                    <div>
                        <h3 style="margin: 0 0 1rem 0; font-size: 1.5rem;">{{ $item->title }}</h3>
                        <div style="font-size: 0.95rem; border-top: 2px solid black; padding-top: 1rem; display: flex; flex-direction: column; gap: 0.4rem;">
                            @if($item->material)<p style="margin: 0;"><strong>Material:</strong> {{ $item->material }}</p>@endif
                            @if($item->print_time)<p style="margin: 0;"><strong>Tiempo ref:</strong> {{ $item->print_time }}</p>@endif
                            @if($item->price)
                            <p style="margin: 0; margin-top: 0.5rem; font-size: 1.4rem; font-weight: 800; color: var(--color-orange);">${{ number_format($item->price, 0) }} MXN</p>
                            @endif
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.8rem; margin-top: auto;">
                        {{-- Solicitar impresión --}}
                        @if($item->orderable)
                            @auth
                                <button onclick="document.getElementById('order-modal-{{ $item->id }}').style.display='flex'" class="btn-primary" style="text-align: center; font-size: 1rem; background: var(--color-blue); color: white; cursor: pointer;">Solicitar Impresión</button>
                            @else
                                <a href="{{ url('/login') }}" class="btn-primary" style="text-align: center; font-size: 1rem; background: var(--color-blue); color: white;">Solicitar Impresión</a>
                            @endauth
                        @endif

                        {{-- Descargar STL --}}
                        @if($item->downloadable && $item->file_path)
                            <button onclick="document.getElementById('download-modal-{{ $item->id }}').style.display='flex'" class="btn-outline" style="text-align: center; font-size: 1rem; border: 2px solid black; background: #fff; font-weight: bold; cursor: pointer; box-shadow: 4px 4px 0 0 rgba(0,0,0,1); border-radius: 8px; padding: 0.8rem 1.5rem; color: black;">Descargar Archivo STL</button>
                        @endif
                    </div>
                </div>

                {{-- Modal Descarga --}}
                @if($item->downloadable && $item->file_path)
                <div id="download-modal-{{ $item->id }}" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:100; align-items:center; justify-content:center;">
                    <div style="background:#fff; border:3px solid #000; padding:2rem; max-width:400px; width:90%; box-shadow:8px 8px 0 #000; position:relative;">
                        <button onclick="document.getElementById('download-modal-{{ $item->id }}').style.display='none'" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:1.5rem;cursor:pointer;">×</button>
                        <h3 style="font-family:'Space Grotesk',sans-serif;margin:0 0 8px;">Descargar: {{ $item->title }}</h3>
                        <p style="font-family:'Space Grotesk',sans-serif;font-size:14px;color:#555;margin:0 0 1.5rem;">Ingresa tu email y te enviamos el link de descarga. Válido 24h, un solo uso.</p>
                        <form action="{{ route('world3d.request_download', $item->id) }}" method="POST" style="display:flex;flex-direction:column;gap:1rem;">
                            @csrf
                            <input type="email" name="email" placeholder="tu@email.com" required style="padding:0.8rem;border:2px solid #000;font-family:'Space Grotesk',sans-serif;font-size:1rem;width:100%;box-sizing:border-box;">
                            <button type="submit" style="background:#000;color:#fff;padding:0.8rem;border:2px solid #000;font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:1rem;cursor:pointer;">Enviar Link</button>
                        </form>
                    </div>
                </div>
                @endif

                {{-- Modal Orden --}}
                @if($item->orderable)
                @auth
                <div id="order-modal-{{ $item->id }}" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:100; align-items:center; justify-content:center;">
                    <div style="background:#fff; border:3px solid #000; padding:2rem; max-width:440px; width:90%; box-shadow:8px 8px 0 #000; position:relative;">
                        <button onclick="document.getElementById('order-modal-{{ $item->id }}').style.display='none'" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:1.5rem;cursor:pointer;">×</button>
                        <h3 style="font-family:'Space Grotesk',sans-serif;margin:0 0 1.5rem;">Solicitar: {{ $item->title }}</h3>
                        <form action="{{ route('world3d.order', $item->id) }}" method="POST" style="display:flex;flex-direction:column;gap:1rem;">
                            @csrf
                            <select name="material" required style="padding:0.8rem;border:2px solid #000;font-family:'Space Grotesk',sans-serif;">
                                <option value="PLA">PLA (Biodegradable)</option>
                                <option value="PETG">PETG (Alta Resistencia)</option>
                                <option value="TPU">TPU (Flexible)</option>
                            </select>
                            <select name="color" required style="padding:0.8rem;border:2px solid #000;font-family:'Space Grotesk',sans-serif;">
                                <option value="Negro">Negro</option><option value="Café">Café</option>
                                <option value="Hueso">Hueso</option><option value="Azul">Azul</option>
                                <option value="Rojo">Rojo</option><option value="Amarillo">Amarillo</option>
                                <option value="Rosa">Rosa</option><option value="Verde Militar">Verde Militar</option>
                                <option value="Transparente">Transparente</option><option value="True Color AMS">True Color AMS</option>
                            </select>
                            <input type="number" name="quantity" value="1" min="1" required style="padding:0.8rem;border:2px solid #000;font-family:'Space Grotesk',sans-serif;" placeholder="Cantidad">
                            <textarea name="notes" rows="2" style="padding:0.8rem;border:2px solid #000;font-family:'Space Grotesk',sans-serif;" placeholder="Notas para el operario (opcional)"></textarea>
                            <button type="submit" style="background:#000;color:#fff;padding:0.8rem;border:2px solid #000;font-family:'Space Grotesk',sans-serif;font-weight:700;cursor:pointer;">Enviar Solicitud</button>
                        </form>
                    </div>
                </div>
                @endauth
                @endif
                @endforeach
            </div>
            @endif

            <div class="text-center" style="margin-top: 4rem; padding: 2rem; background: white; border: 2px dotted black; border-radius: 12px;">
                <h3 style="font-size: 1.8rem; margin-bottom: 1rem;">¿Tienes tu propio diseño?</h3>
                <p style="font-size: 1.1rem; max-width: 600px; margin: 0 auto 1.5rem;">Sube tu archivo modelo (STL, OBJ, 3MF) directamente a nuestro Taller privado y cotizamos la fabricación al instante.</p>
                <a href="{{ url('/taller/registro') }}" class="btn-primary" style="background: var(--color-orange); color: black;">Acceder al Taller</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <x-keiyi-footer />

    <script src="{{ asset('script.js') }}"></script>
</body>
</html>
