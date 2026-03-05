@extends('layouts.app')

@section('title', 'Keiyi 3D World | Imagina, Diseña, Imprime')
@section('description', 'Servicios de impresión y diseño 3D. Materializamos tus ideas.')

@section('content')
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

    <section class="section">
        <div class="container">
            <div class="section-intro text-center">
                <h2>El Proceso</h2>
                <p class="lead">De la nada al todo en 3 pasos.</p>
            </div>

            <div class="grid-3">
                <!-- Step 1 -->
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

                <!-- Step 2 -->
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

                <!-- Step 3 -->
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

            <div class="text-center" style="margin-top: 4rem;">
                <p class="big-text">¿Listo para crear algo único?</p>
                <a href="#contact" class="btn-primary">Iniciar Proyecto 3D</a>
            </div>
        </div>
    </section>
@endsection