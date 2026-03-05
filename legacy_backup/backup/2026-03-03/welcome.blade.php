@extends('layouts.public')

@section('content')
    <header id="hero" class="hero">
        <div class="container hero-container">
            <!-- The Content Balloon -->
            <div class="hero-bubble">
                <div class="hero-content text-center">
                    <div class="hand-note">¡Hola mundo! ✌️</div>
                    <h1 class="hero-title">Marketing que hace <span class="highlight-scribble">POP.</span></h1>
                    <p class="hero-desc">Rompemos el molde aburrido. Creamos experiencias digitales que la gente quiere
                        tocar, ver y compartir.</p>
                    <div class="btn-group centered">
                        <a href="#pricing" class="btn-primary">Ver Planes</a>
                        <a href="#about" class="btn-text">Descubre más ⤵</a>
                    </div>
                </div>
            </div>
            <!-- Image is now background, no img tag here -->
        </div>
    </header>

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

    <!-- NEW SECTION: 3D World Teaser -->
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
                <a href="{{ route('3d-world') }}" class="btn-primary">Entrar al Lab 3D</a>
            </div>
        </div>
    </section>

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
@endsection