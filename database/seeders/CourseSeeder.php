<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // ───────────────────────────────────────────────
        // 1. CURSO PRINCIPAL — Introducción a la IA
        // ───────────────────────────────────────────────
        $introIA = Course::create([
            'title'        => 'Introducción a la Inteligencia Artificial',
            'slug'         => 'intro-ia',
            'description'  => 'Tu primer paso en el mundo de la IA: desde los fundamentos hasta herramientas que puedes usar hoy en tu negocio.',
            'emoji'        => '🧠',
            'tag'          => 'Curso Fundamental',
            'is_published' => true,
            'sort_order'   => 0,
        ]);

        // — Lesson 1 ————————————————————————————————————
        Lesson::create([
            'course_id'     => $introIA->id,
            'title'         => '¿Qué es la Inteligencia Artificial?',
            'slug'          => 'que-es-ia',
            'type'          => 'lecture',
            'sort_order'    => 1,
            'is_published'  => true,
            'pass_threshold'=> 70,
            'video_outline' => "Video (5-7 min)\n0:00 — Intro: \"La IA no es magia ni ciencia ficción\"\n0:45 — Mitos vs Realidad (3 mitos comunes)\n1:30 — Definición simple: software que aprende de datos\n2:15 — Mini-historia: de Turing a ChatGPT (línea de tiempo visual)\n3:30 — Narrow AI vs General AI: ¿dónde estamos hoy?\n4:30 — 5 ejemplos prácticos que ya usas (Netflix, Gmail, Spotify, Maps, Waze)\n5:45 — Resumen y pregunta para reflexión\n6:30 — Cierre + avance de la siguiente lección",
            'content_html'  => <<<'HTML'
<h2>¿Qué es la Inteligencia Artificial?</h2>

<p>Si escuchas "Inteligencia Artificial" y piensas en robots que dominan el mundo, no te preocupes — no eres el único. Pero la realidad es mucho más interesante (y menos aterradora) de lo que Hollywood quiere que creas.</p>

<h3>Mitos vs. Realidad</h3>
<ul>
    <li><strong>Mito:</strong> "La IA piensa como un humano." <br><strong>Realidad:</strong> La IA actual no tiene conciencia ni emociones. Es software que detecta patrones en datos y genera resultados basados en probabilidades.</li>
    <li><strong>Mito:</strong> "La IA va a reemplazar todos los empleos mañana." <br><strong>Realidad:</strong> La IA transforma trabajos, no los elimina de golpe. Quien aprenda a usarla tendrá ventaja competitiva.</li>
    <li><strong>Mito:</strong> "Necesitas ser programador para usar IA." <br><strong>Realidad:</strong> Herramientas como ChatGPT, Claude o Gemini se usan con lenguaje natural — escribes lo que necesitas, como si hablaras con un asistente.</li>
</ul>

<h3>Definición Simple</h3>
<p>La Inteligencia Artificial es <strong>software que aprende de datos</strong>. En vez de programar cada regla a mano ("si X, entonces Y"), le das ejemplos y el sistema descubre los patrones por sí mismo. Eso es todo.</p>

<h3>Ultra-breve Historia: De Turing a ChatGPT</h3>
<ol>
    <li><strong>1950 — Alan Turing</strong> publica "Computing Machinery and Intelligence" y propone el famoso Test de Turing: ¿puede una máquina hacerse pasar por humano en una conversación?</li>
    <li><strong>1956 — Conferencia de Dartmouth:</strong> se acuña oficialmente el término "Inteligencia Artificial".</li>
    <li><strong>1997 — Deep Blue (IBM)</strong> vence al campeón mundial de ajedrez Garry Kasparov.</li>
    <li><strong>2012 — Deep Learning despega:</strong> las redes neuronales profundas empiezan a ganar competencias de visión por computadora.</li>
    <li><strong>2017 — El paper "Attention Is All You Need"</strong> introduce la arquitectura Transformer, la base de todos los modelos de lenguaje actuales.</li>
    <li><strong>2022 — ChatGPT</strong> se lanza al público y alcanza 100 millones de usuarios en 2 meses. La IA generativa llega a todos.</li>
    <li><strong>2024-2026 — Agentes y MCP:</strong> la IA pasa de responder preguntas a ejecutar tareas completas de forma autónoma.</li>
</ol>

<h3>Narrow AI vs. General AI</h3>
<p>Existen dos categorías fundamentales:</p>
<ul>
    <li><strong>Narrow AI (IA Estrecha):</strong> Diseñada para una tarea específica. Es lo que existe HOY. Ejemplos: el algoritmo de recomendaciones de Netflix, el filtro de spam de Gmail, el asistente de voz de tu teléfono.</li>
    <li><strong>General AI (AGI — IA General):</strong> Una inteligencia que podría hacer cualquier tarea intelectual que un humano hace. <strong>Esto NO existe todavía.</strong> Es un objetivo de investigación, no un producto que puedas comprar.</li>
</ul>
<p>Toda la tecnología que vas a aprender en este curso es <strong>Narrow AI</strong> — extremadamente poderosa dentro de su dominio, pero no "inteligente" en el sentido humano.</p>

<h3>5 Ejemplos que Ya Usas (Sin Darte Cuenta)</h3>
<table>
    <thead>
        <tr><th>Servicio</th><th>IA que usa</th><th>Qué hace</th></tr>
    </thead>
    <tbody>
        <tr><td><strong>Netflix</strong></td><td>Recomendación</td><td>Analiza millones de perfiles para sugerirte la serie que probablemente verás completa.</td></tr>
        <tr><td><strong>Gmail</strong></td><td>Filtro de spam + respuestas sugeridas</td><td>Clasifica correos automáticamente y te propone respuestas cortas.</td></tr>
        <tr><td><strong>Spotify</strong></td><td>Discover Weekly</td><td>Crea una playlist semanal personalizada analizando tus hábitos y los de usuarios similares.</td></tr>
        <tr><td><strong>Google Maps</strong></td><td>Predicción de tráfico</td><td>Combina datos GPS de millones de teléfonos en tiempo real para estimar tiempos de llegada.</td></tr>
        <tr><td><strong>Waze</strong></td><td>Enrutamiento dinámico</td><td>Recalcula tu ruta segundo a segundo según condiciones de tráfico, accidentes y reportes de usuarios.</td></tr>
    </tbody>
</table>

<h3>Reflexión</h3>
<p>La IA no es algo del futuro — ya está en tu bolsillo, en tu bandeja de entrada y en tu pantalla de inicio. La diferencia entre quienes la aprovechan y quienes no es simplemente <strong>saber que existe y aprender a usarla con intención</strong>.</p>

<p>En la siguiente lección vamos a meternos al concepto más importante para entender cómo "piensa" una IA: los <strong>tokens</strong>.</p>
HTML,
        ]);

        // — Lesson 2 ————————————————————————————————————
        Lesson::create([
            'course_id'     => $introIA->id,
            'title'         => '¿Qué es un Token y Cómo Piensa una IA?',
            'slug'          => 'tokens-y-como-piensa-ia',
            'type'          => 'lecture',
            'sort_order'    => 2,
            'is_published'  => true,
            'pass_threshold'=> 70,
            'video_outline' => "Video (6-8 min)\n0:00 — Intro: \"Si no entiendes los tokens, no entiendes el costo ni el poder de la IA\"\n0:30 — ¿Qué es un token? (definición visual)\n1:15 — Demostración: cómo se tokeniza una frase en español\n2:00 — ¿Por qué importan? Costo, velocidad y límites\n3:00 — Ventana de contexto: la \"memoria de trabajo\" de la IA\n4:30 — ¿Cómo predice la IA? Intuición probabilística (sin fórmulas)\n5:30 — Demo en vivo: la IA completando texto paso a paso\n6:30 — Analogía final: la IA como un chef con recetario probabilístico\n7:15 — Resumen y cierre",
            'content_html'  => <<<'HTML'
<h2>¿Qué es un Token y Cómo Piensa una IA?</h2>

<p>Antes de usar cualquier herramienta de IA con confianza, necesitas entender un concepto fundamental: los <strong>tokens</strong>. Son la moneda interna de toda IA de lenguaje — y entenderlos te da control sobre costos, resultados y estrategia.</p>

<h3>¿Qué es un Token?</h3>
<p>Un token es un <strong>fragmento de texto</strong> que la IA procesa como una unidad. No es exactamente una palabra ni una letra — está en un punto intermedio.</p>

<p>Piénsalo así: cuando lees, tu cerebro agrupa letras en palabras. La IA hace algo parecido, pero agrupa texto en "pedazos" que le resultan estadísticamente útiles.</p>

<h3>Ejemplos Visuales</h3>
<p>Así es como un tokenizador típico divide texto en español:</p>
<table>
    <thead>
        <tr><th>Texto original</th><th>Tokens</th><th>Cantidad</th></tr>
    </thead>
    <tbody>
        <tr><td>"Hola mundo"</td><td>["Hola", " mundo"]</td><td>2 tokens</td></tr>
        <tr><td>"Inteligencia Artificial"</td><td>["Int", "elig", "encia", " Artificial"]</td><td>4 tokens</td></tr>
        <tr><td>"¿Cómo estás?"</td><td>["¿", "Cómo", " est", "ás", "?"]</td><td>5 tokens</td></tr>
        <tr><td>"marketing@gmail.com"</td><td>["marketing", "@", "gmail", ".", "com"]</td><td>5 tokens</td></tr>
    </tbody>
</table>
<p><strong>Regla general:</strong> en español, 1 token equivale aproximadamente a 3/4 de una palabra. Un texto de 1,000 palabras tiene cerca de 1,300 tokens.</p>

<h3>¿Por Qué Importan los Tokens?</h3>
<ul>
    <li><strong>Costo:</strong> Los servicios de IA como la API de OpenAI o Anthropic cobran por token. Más tokens = más dinero. Un prompt largo y desordenado cuesta más que uno conciso.</li>
    <li><strong>Velocidad:</strong> Más tokens de entrada = más tiempo de procesamiento. Si tu prompt es innecesariamente largo, la respuesta tarda más.</li>
    <li><strong>Límite de contexto:</strong> Cada modelo tiene un número máximo de tokens que puede manejar en una sola conversación (entrada + salida combinados). Si te pasas del límite, la IA literalmente "olvida" el inicio de la conversación.</li>
</ul>

<h3>La Ventana de Contexto: La Memoria de Trabajo de la IA</h3>
<p>Imagina que la IA tiene un escritorio de trabajo con espacio limitado. Todo lo que le escribes (tu prompt) y todo lo que ella responde ocupa espacio en ese escritorio. A eso le llamamos <strong>ventana de contexto</strong>.</p>

<table>
    <thead>
        <tr><th>Modelo</th><th>Ventana de contexto</th><th>Equivalencia aproximada</th></tr>
    </thead>
    <tbody>
        <tr><td>GPT-4o</td><td>128K tokens</td><td>~96,000 palabras (~200 páginas)</td></tr>
        <tr><td>Claude Opus 4</td><td>200K tokens</td><td>~150,000 palabras (~300 páginas)</td></tr>
        <tr><td>Gemini 2.5 Pro</td><td>1M tokens</td><td>~750,000 palabras (varios libros)</td></tr>
    </tbody>
</table>

<h3>¿Cómo "Piensa" la IA? Predicción Probabilística</h3>
<p>La IA de lenguaje no "entiende" lo que escribes. Lo que hace es algo elegantemente simple:</p>
<ol>
    <li>Lee todos los tokens de tu mensaje.</li>
    <li>Calcula cuál es el <strong>siguiente token más probable</strong> basándose en los patrones que aprendió durante su entrenamiento.</li>
    <li>Genera ese token.</li>
    <li>Repite desde el paso 1, ahora incluyendo el nuevo token como contexto.</li>
</ol>

<p><strong>Analogía:</strong> Imagina un chef que tiene un recetario gigantesco donde cada receta dice: "después de agregar X, lo más probable es que el siguiente ingrediente sea Y". El chef no "entiende" de cocina — pero como su recetario se basó en millones de recetas reales, los resultados son sorprendentemente buenos.</p>

<p>Esto explica por qué a veces la IA "alucina" (inventa datos falsos con total confianza): no está buscando la verdad, está buscando la <strong>continuación más probable</strong>. Y a veces lo más probable no es lo más verdadero.</p>

<h3>Implicaciones Prácticas para tu Negocio</h3>
<ul>
    <li><strong>Prompts claros y concisos</strong> no solo dan mejores respuestas — también cuestan menos y son más rápidos.</li>
    <li><strong>Conversaciones muy largas</strong> degradan la calidad porque la IA pierde de vista el contexto inicial. A veces es mejor empezar una nueva conversación.</li>
    <li><strong>Saber que la IA predice en vez de "saber"</strong> te protege de confiar ciegamente en sus respuestas. Siempre verifica datos críticos.</li>
</ul>

<p>En la siguiente lección vamos a explorar los <strong>diferentes tipos de IA</strong> que existen y cuál necesita tu negocio.</p>
HTML,
        ]);

        // — Lesson 3 ————————————————————————————————————
        Lesson::create([
            'course_id'     => $introIA->id,
            'title'         => 'Tipos de IA: ¿Cuál Necesita tu Negocio?',
            'slug'          => 'tipos-de-ia',
            'type'          => 'lecture',
            'sort_order'    => 3,
            'is_published'  => true,
            'pass_threshold'=> 70,
            'video_outline' => "Video (8-10 min)\n0:00 — Intro: \"No toda la IA es igual — y elegir mal te cuesta tiempo y dinero\"\n0:30 — Machine Learning: qué es + ejemplo de negocio\n1:30 — Deep Learning: cuándo lo necesitas\n2:30 — NLP (Procesamiento de Lenguaje Natural): el tipo más relevante para marketers\n3:30 — IA Generativa: texto, imagen, audio, video\n4:45 — Computer Vision: ojos digitales para tu operación\n5:30 — Tabla comparativa en pantalla\n6:30 — Adopción regional: dónde estamos en LATAM vs USA\n7:30 — ¿Cómo elegir? Framework de 3 preguntas\n8:30 — Resumen y cierre",
            'content_html'  => <<<'HTML'
<h2>Tipos de IA: ¿Cuál Necesita tu Negocio?</h2>

<p>Decir "voy a usar IA en mi negocio" es como decir "voy a usar transporte". ¿Un avión? ¿Una bicicleta? ¿Un camión de carga? Cada tipo de IA resuelve problemas diferentes. Aquí te explico los cinco principales para que sepas cuál necesitas.</p>

<h3>1. Machine Learning (ML) — Aprendizaje Automático</h3>
<p><strong>Qué es:</strong> Algoritmos que encuentran patrones en datos históricos y hacen predicciones. No necesitan ser programados regla por regla — "aprenden" de los datos.</p>
<p><strong>Ejemplo de negocio:</strong> Un e-commerce que predice qué productos va a necesitar reabastecer la próxima semana basándose en ventas históricas, temporadas y tendencias.</p>
<p><strong>Necesitas ML si:</strong> Tienes datos históricos y quieres predecir comportamientos futuros (churn de clientes, demanda de producto, scoring de leads).</p>

<h3>2. Deep Learning — Aprendizaje Profundo</h3>
<p><strong>Qué es:</strong> Una rama avanzada de ML que usa redes neuronales con muchas capas (de ahí lo de "profundo"). Es lo que hace posible el reconocimiento de voz, la traducción automática y los asistentes virtuales.</p>
<p><strong>Ejemplo de negocio:</strong> Un call center que transcribe y analiza automáticamente todas las llamadas para detectar quejas recurrentes y oportunidades de mejora.</p>
<p><strong>Necesitas Deep Learning si:</strong> Trabajas con datos complejos no estructurados (audio, video, imágenes, texto libre) y necesitas procesarlos a escala.</p>

<h3>3. NLP — Procesamiento de Lenguaje Natural</h3>
<p><strong>Qué es:</strong> La rama de la IA especializada en entender, interpretar y generar texto en lenguaje humano. ChatGPT, Claude y Gemini son todos modelos de NLP.</p>
<p><strong>Ejemplo de negocio:</strong> Un chatbot que atiende consultas de clientes en WhatsApp 24/7, entiende preguntas en español coloquial y responde con precisión — sin scripts rígidos.</p>
<p><strong>Necesitas NLP si:</strong> Tu negocio genera o consume mucho texto (emails, redes sociales, documentos, soporte al cliente).</p>

<h3>4. IA Generativa</h3>
<p><strong>Qué es:</strong> Modelos que crean contenido nuevo (texto, imágenes, audio, video, código) a partir de instrucciones en lenguaje natural. Es la revolución de 2022-2026.</p>
<p><strong>Subcategorías:</strong></p>
<ul>
    <li><strong>Texto:</strong> ChatGPT, Claude, Gemini — redacción, resúmenes, estrategia.</li>
    <li><strong>Imagen:</strong> Midjourney, DALL-E, Stable Diffusion — diseño, branding, contenido visual.</li>
    <li><strong>Audio:</strong> ElevenLabs, Suno — voces sintéticas, podcasts, jingles.</li>
    <li><strong>Video:</strong> Runway, Sora — clips de marketing, prototipos visuales.</li>
</ul>
<p><strong>Ejemplo de negocio:</strong> Una agencia que produce 30 piezas de contenido a la semana para redes sociales usando IA generativa — algo que antes requería un equipo de 5 personas.</p>

<h3>5. Computer Vision — Visión por Computadora</h3>
<p><strong>Qué es:</strong> IA que "ve" e interpreta imágenes y video. Puede detectar objetos, leer texto en fotos, clasificar productos y más.</p>
<p><strong>Ejemplo de negocio:</strong> Un restaurante que usa una cámara con IA para contar automáticamente el inventario de ingredientes en la bodega y generar órdenes de compra.</p>
<p><strong>Necesitas Computer Vision si:</strong> Tu operación involucra inspección visual, conteo, clasificación de objetos físicos o monitoreo.</p>

<h3>Tabla Comparativa</h3>
<table>
    <thead>
        <tr><th>Tipo de IA</th><th>Mejor para</th><th>Ejemplo LATAM</th><th>Dificultad de implementar</th></tr>
    </thead>
    <tbody>
        <tr><td><strong>Machine Learning</strong></td><td>Predicciones con datos históricos</td><td>Rappi prediciendo demanda por zona</td><td>Media-Alta</td></tr>
        <tr><td><strong>Deep Learning</strong></td><td>Datos no estructurados a escala</td><td>Nubank analizando riesgo crediticio</td><td>Alta</td></tr>
        <tr><td><strong>NLP</strong></td><td>Texto, conversaciones, documentos</td><td>Chatbot de Mercado Libre</td><td>Baja-Media</td></tr>
        <tr><td><strong>IA Generativa</strong></td><td>Creación de contenido</td><td>Agencias digitales produciendo contenido a escala</td><td>Baja</td></tr>
        <tr><td><strong>Computer Vision</strong></td><td>Inspección visual, conteo, clasificación</td><td>Control de calidad en manufactura</td><td>Media</td></tr>
    </tbody>
</table>

<h3>Adopción Regional</h3>
<p><strong>Estados Unidos:</strong> Lidera en inversión y adopción empresarial. El 72% de las empresas Fortune 500 ya usan alguna forma de IA en sus operaciones (2025).</p>
<p><strong>Latinoamérica:</strong> La adopción va en aceleración. Brasil y México lideran la región. Las pymes están adoptando principalmente IA generativa (por su bajo costo de entrada) y NLP (chatbots de atención). La oportunidad para quien se adelante es enorme.</p>
<p><strong>Ciencia y academia:</strong> Deep Learning y ML dominan la investigación. Pero los avances en IA generativa están democratizando el acceso — ya no necesitas un doctorado para usar estas herramientas.</p>

<h3>¿Cómo Elegir? 3 Preguntas Clave</h3>
<ol>
    <li><strong>¿Qué problema específico quieres resolver?</strong> No busques "usar IA" — busca resolver un dolor concreto.</li>
    <li><strong>¿Qué tipo de datos tienes?</strong> Texto → NLP/Generativa. Números históricos → ML. Imágenes → Computer Vision.</li>
    <li><strong>¿Cuál es tu presupuesto y nivel técnico?</strong> Si eres emprendedor sin equipo técnico, empieza por IA Generativa y NLP — son las más accesibles.</li>
</ol>

<p>En la siguiente lección vas a poner a prueba lo que aprendiste con un quiz rápido.</p>
HTML,
        ]);

        // — Lesson 4 (Quiz) ——————————————————————————————
        Lesson::create([
            'course_id'     => $introIA->id,
            'title'         => 'Quiz: ¿Cuánto Sabes de IA?',
            'slug'          => 'quiz-fundamentos',
            'type'          => 'quiz',
            'sort_order'    => 4,
            'is_published'  => true,
            'pass_threshold'=> 70,
            'content_html'  => '<h2>Quiz: ¿Cuánto Sabes de IA?</h2><p>Pon a prueba lo que aprendiste en las lecciones 1 a 3. Necesitas al menos un <strong>70%</strong> para aprobar. Si no lo logras a la primera, puedes volver a intentarlo — el aprendizaje real se construye con repetición.</p>',
            'quiz_data'     => [
                [
                    'question'    => '¿Cuál es la definición más precisa de Inteligencia Artificial en el contexto actual?',
                    'options'     => [
                        'Un robot con conciencia propia que puede tomar decisiones éticas',
                        'Software que aprende patrones a partir de datos y genera resultados basados en probabilidades',
                        'Un programa que sigue reglas escritas por un programador paso a paso',
                        'Una supercomputadora que almacena todo el conocimiento de Internet',
                    ],
                    'correct'     => 1,
                    'explanation' => 'La IA actual es software que detecta patrones en datos y genera resultados probabilísticos. No tiene conciencia ni sigue reglas rígidas — aprende de ejemplos.',
                ],
                [
                    'question'    => '¿Qué tipo de IA es la que existe HOY en productos comerciales como ChatGPT, Netflix o Gmail?',
                    'options'     => [
                        'Inteligencia General Artificial (AGI)',
                        'Superinteligencia Artificial',
                        'Narrow AI (IA Estrecha)',
                        'IA Cuántica',
                    ],
                    'correct'     => 2,
                    'explanation' => 'Toda la IA comercial actual es Narrow AI: diseñada para tareas específicas. La AGI (IA General) sigue siendo un objetivo de investigación, no un producto disponible.',
                ],
                [
                    'question'    => '¿Qué es un token en el contexto de la IA de lenguaje?',
                    'options'     => [
                        'Una contraseña de seguridad para acceder al modelo',
                        'Un fragmento de texto que la IA procesa como una unidad',
                        'Un tipo de criptomoneda usada para pagar servicios de IA',
                        'El nombre técnico de cada respuesta que da la IA',
                    ],
                    'correct'     => 1,
                    'explanation' => 'Un token es un fragmento de texto (puede ser una palabra, parte de una palabra o un signo de puntuación) que la IA procesa como unidad básica.',
                ],
                [
                    'question'    => '¿Por qué es importante la ventana de contexto de un modelo de IA?',
                    'options'     => [
                        'Define el color y estilo visual de la interfaz',
                        'Determina cuántos usuarios pueden usar el modelo al mismo tiempo',
                        'Limita cuánta información (tokens) la IA puede considerar en una sola conversación',
                        'Controla la velocidad de Internet necesaria para usar la IA',
                    ],
                    'correct'     => 2,
                    'explanation' => 'La ventana de contexto es el límite de tokens (entrada + salida) que el modelo puede manejar en una conversación. Si se excede, la IA pierde de vista el inicio del diálogo.',
                ],
                [
                    'question'    => '¿Cuál tipo de IA es el más accesible para un emprendedor sin equipo técnico?',
                    'options'     => [
                        'Deep Learning',
                        'Machine Learning clásico',
                        'IA Generativa',
                        'Computer Vision industrial',
                    ],
                    'correct'     => 2,
                    'explanation' => 'La IA Generativa (ChatGPT, Claude, Midjourney) es la más accesible: se usa con lenguaje natural, no requiere programación y tiene costos de entrada bajos.',
                ],
                [
                    'question'    => '¿Por qué la IA a veces "alucina" e inventa datos falsos?',
                    'options'     => [
                        'Porque tiene acceso a Internet en tiempo real y los datos cambian',
                        'Porque busca la continuación más probable del texto, no la más verdadera',
                        'Porque fue programada para ser creativa, no precisa',
                        'Porque su base de datos tiene errores que nadie ha corregido',
                    ],
                    'correct'     => 1,
                    'explanation' => 'La IA genera el siguiente token más probable según sus patrones de entrenamiento. "Probable" no siempre significa "verdadero" — por eso hay que verificar datos críticos.',
                ],
                [
                    'question'    => '¿Qué tipo de IA usarías para analizar automáticamente las opiniones de tus clientes en redes sociales?',
                    'options'     => [
                        'Computer Vision',
                        'NLP (Procesamiento de Lenguaje Natural)',
                        'Machine Learning de series temporales',
                        'IA Robótica',
                    ],
                    'correct'     => 1,
                    'explanation' => 'El NLP es la rama de la IA especializada en entender, interpretar y analizar texto en lenguaje humano — ideal para analizar comentarios, reseñas y conversaciones.',
                ],
                [
                    'question'    => '¿Cuál fue el paper de 2017 que introdujo la arquitectura base de todos los modelos de lenguaje actuales?',
                    'options'     => [
                        '"Computing Machinery and Intelligence" de Turing',
                        '"ImageNet Classification with Deep Convolutional Neural Networks"',
                        '"Attention Is All You Need"',
                        '"Playing Atari with Deep Reinforcement Learning"',
                    ],
                    'correct'     => 2,
                    'explanation' => '"Attention Is All You Need" (2017) introdujo la arquitectura Transformer, que es la base de GPT, Claude, Gemini y prácticamente todos los modelos de lenguaje actuales.',
                ],
            ],
        ]);

        // — Lesson 5 ————————————————————————————————————
        Lesson::create([
            'course_id'     => $introIA->id,
            'title'         => 'Herramientas de IA que Puedes Usar Hoy',
            'slug'          => 'herramientas-ia-hoy',
            'type'          => 'lecture',
            'sort_order'    => 5,
            'is_published'  => true,
            'pass_threshold'=> 70,
            'video_outline' => "Video (10-12 min)\n0:00 — Intro: \"Estas son las herramientas que estoy usando en mi negocio HOY\"\n0:45 — Categoría 1: Asistentes de texto (ChatGPT, Claude, Gemini)\n2:00 — Categoría 2: Generación de imagen (Midjourney, DALL-E)\n3:15 — Categoría 3: Audio y voz (ElevenLabs)\n4:00 — Categoría 4: Automatización (n8n, Zapier/Make, ManyChat)\n5:30 — Categoría 5: Analítica (GA4, Hotjar)\n6:30 — Tabla comparativa en pantalla: herramienta, precio, caso de uso, dificultad\n8:00 — Mi stack recomendado para empezar con $0\n9:00 — Mi stack recomendado para escalar ($50-200/mes)\n10:00 — Error más común: comprar herramientas antes de definir el problema\n11:00 — Resumen y cierre",
            'content_html'  => <<<'HTML'
<h2>Herramientas de IA que Puedes Usar Hoy</h2>

<p>Hay cientos de herramientas de IA en el mercado. La mayoría no las necesitas. Aquí te presento las que realmente importan en 2026, organizadas por categoría, con precios reales y el caso de uso donde brillan.</p>

<h3>Asistentes de Texto (Los "Cerebros")</h3>

<p><strong>ChatGPT (OpenAI)</strong></p>
<ul>
    <li><strong>Qué hace:</strong> Redacción, análisis, código, investigación, conversación general.</li>
    <li><strong>Ideal para:</strong> Tareas generales de texto. Es el más popular y tiene el ecosistema de plugins más amplio.</li>
    <li><strong>Precio:</strong> Gratis (GPT-4o mini) / $20 USD/mes (Plus) / $200 USD/mes (Pro).</li>
</ul>

<p><strong>Claude (Anthropic)</strong></p>
<ul>
    <li><strong>Qué hace:</strong> Análisis de documentos largos, razonamiento complejo, redacción de alta calidad, código.</li>
    <li><strong>Ideal para:</strong> Trabajos que requieren contexto extenso (contratos, reportes largos, libros). Ventana de contexto de hasta 200K tokens.</li>
    <li><strong>Precio:</strong> Gratis (limitado) / $20 USD/mes (Pro).</li>
</ul>

<p><strong>Gemini (Google)</strong></p>
<ul>
    <li><strong>Qué hace:</strong> Búsqueda, análisis, integración con Google Workspace (Docs, Sheets, Gmail).</li>
    <li><strong>Ideal para:</strong> Si tu negocio vive en Google Workspace. Ventana de contexto de hasta 1 millón de tokens.</li>
    <li><strong>Precio:</strong> Gratis (básico) / incluido en Google One AI Premium ($20 USD/mes).</li>
</ul>

<h3>Generación de Imagen</h3>

<p><strong>Midjourney</strong></p>
<ul>
    <li><strong>Qué hace:</strong> Genera imágenes de calidad artística profesional a partir de descripciones de texto.</li>
    <li><strong>Ideal para:</strong> Contenido visual para redes, branding, mockups de productos, presentaciones.</li>
    <li><strong>Precio:</strong> Desde $10 USD/mes.</li>
</ul>

<p><strong>DALL-E (OpenAI)</strong></p>
<ul>
    <li><strong>Qué hace:</strong> Generación y edición de imágenes, integrado en ChatGPT.</li>
    <li><strong>Ideal para:</strong> Generación rápida sin salir de ChatGPT. Menos artístico que Midjourney, pero más práctico.</li>
    <li><strong>Precio:</strong> Incluido en ChatGPT Plus.</li>
</ul>

<h3>Audio y Voz</h3>

<p><strong>ElevenLabs</strong></p>
<ul>
    <li><strong>Qué hace:</strong> Genera voces sintéticas ultra-realistas. Puede clonar tu voz y producir audio en múltiples idiomas.</li>
    <li><strong>Ideal para:</strong> Podcasts, doblaje de videos, IVR (grabaciones telefónicas), contenido en audio.</li>
    <li><strong>Precio:</strong> Gratis (limitado) / desde $5 USD/mes.</li>
</ul>

<h3>Automatización</h3>

<p><strong>n8n</strong></p>
<ul>
    <li><strong>Qué hace:</strong> Plataforma de automatización open-source. Conecta cualquier servicio con cualquier otro mediante flujos visuales.</li>
    <li><strong>Ideal para:</strong> Automatizaciones complejas con IA. Es la opción más potente y flexible, especialmente si quieres control total.</li>
    <li><strong>Precio:</strong> Gratis (self-hosted) / desde $20 EUR/mes (cloud).</li>
</ul>

<p><strong>Zapier / Make</strong></p>
<ul>
    <li><strong>Qué hace:</strong> Conectan apps entre sí sin código. "Cuando pase X en App A, haz Y en App B."</li>
    <li><strong>Ideal para:</strong> Automatizaciones simples a moderadas. Make tiene mejor relación precio-capacidad.</li>
    <li><strong>Precio:</strong> Zapier: gratis (limitado) / desde $20 USD/mes. Make: gratis / desde $9 USD/mes.</li>
</ul>

<p><strong>ManyChat</strong></p>
<ul>
    <li><strong>Qué hace:</strong> Chatbots para Instagram, WhatsApp y Messenger. Automatiza conversaciones de venta y soporte.</li>
    <li><strong>Ideal para:</strong> Negocios que generan leads o ventas por redes sociales y mensajería.</li>
    <li><strong>Precio:</strong> Gratis (hasta 1,000 contactos) / desde $15 USD/mes.</li>
</ul>

<h3>Analítica</h3>

<p><strong>Google Analytics 4 (GA4)</strong></p>
<ul>
    <li><strong>Qué hace:</strong> Mide el comportamiento de usuarios en tu sitio web. Predice conversiones y churn con ML integrado.</li>
    <li><strong>Ideal para:</strong> Todo negocio con presencia web. Es gratuito y obligatorio.</li>
    <li><strong>Precio:</strong> Gratis.</li>
</ul>

<p><strong>Hotjar</strong></p>
<ul>
    <li><strong>Qué hace:</strong> Mapas de calor, grabaciones de sesiones, encuestas in-situ. Muestra exactamente cómo los usuarios interactúan con tu sitio.</li>
    <li><strong>Ideal para:</strong> Optimización de landing pages, diagnóstico de problemas de conversión.</li>
    <li><strong>Precio:</strong> Gratis (básico) / desde $40 USD/mes.</li>
</ul>

<h3>Tabla Comparativa Completa</h3>
<table>
    <thead>
        <tr><th>Herramienta</th><th>Precio base</th><th>Mejor para</th><th>Dificultad</th></tr>
    </thead>
    <tbody>
        <tr><td>ChatGPT</td><td>Gratis / $20/mes</td><td>Texto general, plugins, investigación</td><td>Baja</td></tr>
        <tr><td>Claude</td><td>Gratis / $20/mes</td><td>Documentos largos, razonamiento, código</td><td>Baja</td></tr>
        <tr><td>Gemini</td><td>Gratis / $20/mes</td><td>Google Workspace, contexto masivo</td><td>Baja</td></tr>
        <tr><td>Midjourney</td><td>$10/mes</td><td>Imágenes artísticas y de marca</td><td>Media</td></tr>
        <tr><td>DALL-E</td><td>Incluido en ChatGPT+</td><td>Imágenes rápidas sin cambiar de app</td><td>Baja</td></tr>
        <tr><td>ElevenLabs</td><td>Gratis / $5/mes</td><td>Voz sintética, podcasts, doblaje</td><td>Baja</td></tr>
        <tr><td>n8n</td><td>Gratis / $20/mes</td><td>Automatizaciones complejas con IA</td><td>Media-Alta</td></tr>
        <tr><td>Zapier / Make</td><td>Gratis / $9-20/mes</td><td>Automatizaciones simples entre apps</td><td>Baja-Media</td></tr>
        <tr><td>ManyChat</td><td>Gratis / $15/mes</td><td>Chatbots en redes y WhatsApp</td><td>Baja</td></tr>
        <tr><td>GA4</td><td>Gratis</td><td>Analítica web con predicciones ML</td><td>Media</td></tr>
        <tr><td>Hotjar</td><td>Gratis / $40/mes</td><td>Mapas de calor, grabaciones UX</td><td>Baja</td></tr>
    </tbody>
</table>

<h3>Recomendación: Por Dónde Empezar</h3>
<p><strong>Con $0/mes:</strong> ChatGPT (gratis) + Make (gratis) + GA4 (gratis). Ya tienes un cerebro de IA, automatización básica y analítica.</p>
<p><strong>Con $50-200/mes:</strong> Claude Pro + Midjourney + ManyChat + n8n Cloud. Tienes todo para producir contenido, automatizar ventas y analizar resultados a nivel profesional.</p>

<p><strong>El error más común:</strong> Comprar herramientas antes de definir el problema. Primero identifica qué proceso de tu negocio consume más tiempo o dinero, y después busca la herramienta que lo resuelve.</p>
HTML,
        ]);

        // — Lesson 6 ————————————————————————————————————
        Lesson::create([
            'course_id'     => $introIA->id,
            'title'         => '¿Qué es un MCP, Skills y Agentes de IA?',
            'slug'          => 'mcp-skills-agentes',
            'type'          => 'lecture',
            'sort_order'    => 6,
            'is_published'  => true,
            'pass_threshold'=> 70,
            'video_outline' => "Video (8-10 min)\n0:00 — Intro: \"Esto es lo que separa a un usuario casual de un power user de IA\"\n0:45 — De prompts a sistemas: la evolución en 3 niveles\n1:30 — ¿Qué es un agente de IA? Diferencia con un chatbot\n2:30 — Demo conceptual: chatbot vs agente resolviendo un problema real\n3:30 — ¿Qué es un MCP (Model Context Protocol)? — el \"USB universal\" de la IA\n5:00 — Analogía visual: mundo sin MCP vs mundo con MCP\n6:00 — ¿Qué son los Skills? — habilidades modulares para agentes\n7:00 — Ejemplos reales para marketers y emprendedores\n8:00 — El futuro cercano: agentes que trabajan mientras duermes\n9:00 — Resumen y cierre",
            'content_html'  => <<<'HTML'
<h2>¿Qué es un MCP, Skills y Agentes de IA?</h2>

<p>Hasta ahora hemos hablado de IA como algo a lo que le haces preguntas y te responde. Eso está bien para empezar, pero es solo el primer nivel. En esta lección entramos al territorio que realmente transforma negocios: <strong>agentes de IA, MCP y Skills</strong>.</p>

<h3>La Evolución en 3 Niveles</h3>
<ol>
    <li><strong>Nivel 1 — Prompts:</strong> Le escribes algo a la IA, te responde. Es útil, pero requiere tu intervención constante.</li>
    <li><strong>Nivel 2 — Automatizaciones:</strong> Conectas la IA a flujos automáticos (Zapier, Make, n8n). "Cuando llegue un email de cliente, la IA clasifica y responde." Ya no necesitas estar presente para cada interacción.</li>
    <li><strong>Nivel 3 — Agentes:</strong> La IA tiene un objetivo, herramientas y autonomía para decidir cómo lograrlo. Es como tener un empleado digital que piensa, planifica y ejecuta.</li>
</ol>

<h3>Chatbot vs. Agente: ¿Cuál es la Diferencia?</h3>
<table>
    <thead>
        <tr><th>Característica</th><th>Chatbot tradicional</th><th>Agente de IA</th></tr>
    </thead>
    <tbody>
        <tr><td>Interacción</td><td>Espera tu pregunta para responder</td><td>Puede iniciar acciones por sí mismo</td></tr>
        <tr><td>Herramientas</td><td>Solo genera texto</td><td>Puede buscar en la web, leer archivos, ejecutar código, enviar emails</td></tr>
        <tr><td>Memoria</td><td>Limitada a la conversación actual</td><td>Puede acceder a bases de datos, documentos y sistemas externos</td></tr>
        <tr><td>Planificación</td><td>Responde paso a paso</td><td>Descompone un objetivo en subtareas y las ejecuta en secuencia</td></tr>
        <tr><td>Ejemplo</td><td>"Escríbeme un post para Instagram"</td><td>"Investiga las tendencias de esta semana, redacta 5 posts, programa su publicación y genera las imágenes"</td></tr>
    </tbody>
</table>

<h3>¿Qué es MCP? (Model Context Protocol)</h3>
<p>MCP es un estándar creado por Anthropic (los creadores de Claude) que resuelve un problema enorme: <strong>¿cómo conectas a la IA con el mundo exterior de forma segura y estandarizada?</strong></p>

<p><strong>La analogía del USB:</strong></p>
<p>Antes de que existiera el USB, cada dispositivo tenía su propio conector. Impresoras, cámaras, teclados — todos diferentes. Era un caos. El USB creó un estándar universal: un solo conector para todo.</p>

<p>MCP hace lo mismo para la IA:</p>
<ul>
    <li><strong>Sin MCP:</strong> Cada herramienta de IA necesita una integración personalizada con cada servicio externo. Quieres que Claude lea tu Google Drive? Integración custom. Quieres que lea tu base de datos? Otra integración. Cada combinación es un proyecto.</li>
    <li><strong>Con MCP:</strong> Cualquier servicio que implemente el estándar MCP se conecta automáticamente con cualquier IA que lo soporte. Un "enchufe universal" para la inteligencia artificial.</li>
</ul>

<h3>¿Qué son los Skills?</h3>
<p>Un Skill es una <strong>habilidad modular</strong> que le das a un agente de IA. Piensa en ellos como "superpoderes" que puedes activar o desactivar según la tarea.</p>

<p>Ejemplos de Skills:</p>
<ul>
    <li><strong>Skill de búsqueda web:</strong> El agente puede buscar información actualizada en Internet.</li>
    <li><strong>Skill de lectura de archivos:</strong> El agente puede abrir y analizar PDFs, hojas de cálculo, documentos.</li>
    <li><strong>Skill de código:</strong> El agente puede escribir y ejecutar código Python para analizar datos.</li>
    <li><strong>Skill de email:</strong> El agente puede redactar y enviar correos electrónicos.</li>
    <li><strong>Skill de base de datos:</strong> El agente puede consultar y actualizar registros en tu CRM o ERP.</li>
</ul>

<p>La combinación de <strong>Agente + MCP + Skills</strong> es lo que crea sistemas de IA verdaderamente poderosos. El agente tiene el "cerebro", MCP es el "sistema nervioso" que lo conecta al mundo, y los Skills son las "manos" con las que ejecuta tareas.</p>

<h3>Ejemplos Reales para tu Negocio</h3>
<ul>
    <li><strong>Agente de investigación de mercado:</strong> Le das un nicho o producto. El agente busca en Reddit, Twitter y foros especializados. Analiza sentimiento y tendencias. Te entrega un reporte ejecutivo con oportunidades detectadas — todo sin que toques un botón.</li>
    <li><strong>Agente de contenido:</strong> Monitorea tendencias de tu industria, redacta borradores de blog posts, los adapta para diferentes plataformas (LinkedIn, Instagram, email) y los deja en cola para tu aprobación.</li>
    <li><strong>Agente de soporte al cliente:</strong> Lee tu base de conocimiento via MCP, responde consultas frecuentes automáticamente, escala los casos complejos a un humano con un resumen del contexto.</li>
</ul>

<h3>El Futuro Cercano</h3>
<p>Estamos en el punto donde los agentes pasan de ser una novedad tecnológica a una herramienta de productividad cotidiana. En 2026, tener un agente de IA trabajando para tu negocio es como tener un sitio web en 2010 — los que se adelantan ganan ventaja masiva.</p>

<p>En la siguiente lección vas a diseñar tu propio caso de uso con un ejercicio práctico.</p>
HTML,
        ]);

        // — Lesson 7 (Interactive) ——————————————————————
        Lesson::create([
            'course_id'     => $introIA->id,
            'title'         => 'Ejercicio: Diseña tu Primer Caso de Uso',
            'slug'          => 'ejercicio-caso-de-uso',
            'type'          => 'interactive',
            'sort_order'    => 7,
            'is_published'  => true,
            'pass_threshold'=> 70,
            'content_html'  => '<h2>Ejercicio: Diseña tu Primer Caso de Uso</h2><p>Es hora de aplicar lo que aprendiste. Completa los dos ejercicios a continuación para conectar los conceptos con situaciones reales de negocio. No hay respuestas incorrectas en la reflexión — pero sí en el matching, así que piensa bien antes de conectar cada par.</p>',
            'interactive_data' => [
                [
                    'type'  => 'matching',
                    'title' => 'Conecta la necesidad con el tipo de IA',
                    'instructions' => 'Arrastra cada necesidad de negocio y conéctala con el tipo de IA que mejor la resuelve.',
                    'pairs' => [
                        ['left' => 'Quiero generar posts para redes sociales automáticamente', 'right' => 'IA Generativa'],
                        ['left' => 'Necesito analizar 500 reseñas de clientes para detectar quejas comunes', 'right' => 'NLP'],
                        ['left' => 'Quiero predecir qué productos se van a agotar la próxima semana', 'right' => 'Machine Learning'],
                        ['left' => 'Necesito un sistema que clasifique fotos de productos defectuosos', 'right' => 'Computer Vision'],
                        ['left' => 'Quiero un asistente que investigue, planifique y ejecute tareas de marketing sin supervisión', 'right' => 'Agente de IA'],
                    ],
                ],
                [
                    'type'           => 'multiple_select',
                    'title'          => 'Elige las herramientas correctas para el escenario',
                    'scenario'       => 'Tienes una tienda en línea de ropa. Quieres: (1) generar descripciones de productos automáticamente, (2) responder preguntas de clientes por WhatsApp 24/7, y (3) analizar qué páginas de tu sitio convierten mejor. ¿Cuáles herramientas usarías?',
                    'options'        => [
                        'ChatGPT o Claude para generar descripciones de productos',
                        'Midjourney para crear imágenes de cada producto',
                        'ManyChat para automatizar respuestas en WhatsApp',
                        'ElevenLabs para crear un podcast de moda',
                        'GA4 + Hotjar para analizar conversiones en tu sitio',
                        'n8n para conectar todo sin necesidad de un desarrollador',
                    ],
                    'correct_indices' => [0, 2, 4],
                ],
            ],
        ]);

        // — Lesson 8 ————————————————————————————————————
        Lesson::create([
            'course_id'     => $introIA->id,
            'title'         => 'IA Responsable y El Futuro de tu Negocio',
            'slug'          => 'ia-responsable-futuro',
            'type'          => 'lecture',
            'sort_order'    => 8,
            'is_published'  => true,
            'pass_threshold'=> 70,
            'video_outline' => "Video (7-8 min)\n0:00 — Intro: \"El poder de la IA viene con responsabilidad real\"\n0:30 — Sesgo en la IA: qué es y por qué importa para tu negocio\n1:30 — Privacidad: qué NUNCA debes poner en ChatGPT (o cualquier IA)\n2:30 — Regulación en LATAM: Brasil (LGPD), México, Colombia\n3:30 — El futuro cercano 2026-2028: qué viene\n5:00 — 3 pasos para empezar HOY con IA en tu negocio\n6:00 — Quiz final rápido (5 preguntas)\n7:00 — Cierre del curso + CTA: siguiente curso y comunidad",
            'content_html'  => <<<'HTML'
<h2>IA Responsable y El Futuro de tu Negocio</h2>

<p>Llegar a la última lección de este curso significa que ya tienes una base sólida sobre qué es la IA, cómo funciona, qué tipos existen y qué herramientas usar. Ahora toca hablar de algo que muchos cursos omiten: <strong>la responsabilidad</strong> y cómo preparar tu negocio para lo que viene.</p>

<h3>Sesgo en la IA: Un Problema Real</h3>
<p>La IA aprende de datos creados por humanos. Si esos datos tienen sesgos (raciales, de género, culturales), la IA los reproduce y amplifica.</p>

<p><strong>Ejemplos reales:</strong></p>
<ul>
    <li>Sistemas de contratación que descartaban automáticamente CVs de mujeres porque los datos históricos mostraban mayoría de hombres contratados.</li>
    <li>Modelos de crédito que asignaban mayor riesgo a personas de ciertas zonas geográficas, perpetuando desigualdades.</li>
    <li>Generadores de imágenes que asociaban profesiones de alto estatus solo con personas de piel clara.</li>
</ul>

<p><strong>Qué hacer en tu negocio:</strong></p>
<ul>
    <li>Revisa siempre los resultados de la IA antes de publicarlos o usarlos para tomar decisiones.</li>
    <li>Si usas IA para filtrar candidatos, evaluar clientes o segmentar audiencias, audita los resultados por posibles sesgos.</li>
    <li>Diversifica tus datos de entrada: entre más variados sean, menos sesgados serán los resultados.</li>
</ul>

<h3>Privacidad: Qué NO Debes Poner en ChatGPT (ni en Ninguna IA)</h3>
<p>Cuando escribes algo en una IA, esos datos pueden ser usados para entrenar futuros modelos (a menos que uses la API o desactives el entrenamiento). Regla de oro:</p>

<p><strong>NUNCA introduzcas:</strong></p>
<ul>
    <li>Datos personales de clientes (nombres completos + emails + teléfonos + datos financieros).</li>
    <li>Contraseñas, tokens de API o claves de acceso.</li>
    <li>Información médica o legal confidencial de terceros.</li>
    <li>Secretos comerciales, fórmulas propietarias o estrategias confidenciales detalladas.</li>
    <li>Datos financieros internos de tu empresa (estados financieros, nóminas, proyecciones no públicas).</li>
</ul>

<p><strong>Alternativas seguras:</strong></p>
<ul>
    <li>Usa datos anonimizados o ficticios para probar prompts.</li>
    <li>Activa la opción "No usar mis datos para entrenamiento" en la configuración de cada herramienta.</li>
    <li>Para datos sensibles, usa las APIs (que tienen políticas de privacidad más estrictas) en vez de la interfaz web.</li>
</ul>

<h3>Regulación en Latinoamérica</h3>
<p>La regulación de IA en LATAM está en desarrollo activo. Lo que necesitas saber:</p>

<table>
    <thead>
        <tr><th>País</th><th>Marco legal</th><th>Estado</th></tr>
    </thead>
    <tbody>
        <tr><td><strong>Brasil</strong></td><td>LGPD (Lei Geral de Proteção de Dados) + Marco Legal de IA en tramitación</td><td>LGPD vigente desde 2020. Proyecto de ley de IA avanzado en el Senado.</td></tr>
        <tr><td><strong>México</strong></td><td>LFPDPPP + iniciativas de regulación de IA</td><td>Ley de protección de datos vigente. Regulación específica de IA en discusión.</td></tr>
        <tr><td><strong>Colombia</strong></td><td>Ley 1581 + Marco Ético de IA del MinTIC</td><td>Protección de datos vigente. Guías éticas de IA publicadas, sin ley vinculante aún.</td></tr>
    </tbody>
</table>

<p><strong>Consejo práctico:</strong> No esperes a que la ley te obligue. Implementa buenas prácticas de privacidad y transparencia desde ahora. Las empresas que lo hacen proactivamente ganan confianza del mercado.</p>

<h3>El Futuro Cercano: 2026-2028</h3>
<ul>
    <li><strong>Agentes autónomos se vuelven mainstream.</strong> Así como hoy es normal usar un chatbot, en 2027-2028 será normal tener agentes de IA que ejecutan tareas completas en tu negocio sin supervisión paso a paso.</li>
    <li><strong>MCP y estándares abiertos se consolidan.</strong> Conectar IA con tus herramientas será tan fácil como instalar una app en tu teléfono.</li>
    <li><strong>La IA multimodal será la norma.</strong> Un solo modelo que entiende y genera texto, imagen, audio y video. Ya no habrá "herramientas de texto" separadas de "herramientas de imagen".</li>
    <li><strong>El costo de la IA seguirá bajando.</strong> Lo que hoy cuesta $20/mes probablemente cueste $5 o menos en 2028. La barrera de entrada desaparece progresivamente.</li>
    <li><strong>Quien no use IA competirá con una mano atada.</strong> No es alarmismo — es la misma dinámica que vivimos con Internet en los 2000 y con móviles en los 2010.</li>
</ul>

<h3>3 Pasos para Empezar HOY</h3>
<ol>
    <li><strong>Identifica tu dolor más grande.</strong> ¿Qué tarea te consume más tiempo, más dinero o más energía? Esa es tu primera candidata para IA.</li>
    <li><strong>Elige UNA herramienta y úsala 30 días.</strong> No intentes dominar 10 herramientas a la vez. Empieza con ChatGPT o Claude para texto, ManyChat si tu negocio es social, o Make/n8n si quieres automatizar procesos.</li>
    <li><strong>Mide y ajusta.</strong> Después de 30 días, pregúntate: ¿ahorré tiempo? ¿Mejoró la calidad? ¿Reduje costos? Si sí, escala. Si no, ajusta el enfoque o prueba otra herramienta.</li>
</ol>

<h3>Cierre del Curso</h3>
<p>Completaste <strong>Introducción a la Inteligencia Artificial</strong>. Ahora tienes los fundamentos que el 90% de los emprendedores y marketers en LATAM todavía no tienen. Eso es una ventaja real.</p>

<p>Pero los fundamentos son solo el principio. El siguiente paso es pasar de "entender" a "implementar". Explora los siguientes cursos de la Academia Keiyi para dominar herramientas específicas y construir sistemas de IA que trabajen para tu negocio mientras duermes.</p>

<p><strong>Responde el quiz final a continuación para obtener tu certificado de finalización.</strong></p>
HTML,
            'quiz_data' => [
                [
                    'question'    => '¿Qué es el sesgo en la IA y por qué es peligroso?',
                    'options'     => [
                        'Es un error de programación que hace que la IA sea más lenta',
                        'Es cuando la IA reproduce y amplifica prejuicios presentes en sus datos de entrenamiento',
                        'Es una técnica para hacer que la IA sea más precisa en sus respuestas',
                        'Es el nombre del proceso de entrenamiento de modelos de lenguaje',
                    ],
                    'correct'     => 1,
                    'explanation' => 'El sesgo ocurre cuando la IA aprende patrones discriminatorios de datos históricos creados por humanos y los reproduce en sus resultados, amplificando desigualdades existentes.',
                ],
                [
                    'question'    => '¿Cuál de estos datos NUNCA deberías introducir en ChatGPT o Claude?',
                    'options'     => [
                        'Una idea para un post de Instagram sobre tu producto',
                        'Las contraseñas de acceso a tu banca en línea y datos financieros de clientes',
                        'Un borrador de email de marketing para que lo mejore',
                        'Una lista de temas para un blog de tu negocio',
                    ],
                    'correct'     => 1,
                    'explanation' => 'Nunca introduzcas datos sensibles como contraseñas, datos financieros de clientes o información personal identificable. Usa datos anonimizados o ficticios para probar prompts.',
                ],
                [
                    'question'    => '¿Qué es MCP (Model Context Protocol)?',
                    'options'     => [
                        'Un lenguaje de programación para crear chatbots',
                        'Una certificación profesional en IA',
                        'Un estándar que permite a la IA conectarse con servicios externos de forma segura y universal',
                        'El nombre del sistema operativo que usan los modelos de IA',
                    ],
                    'correct'     => 2,
                    'explanation' => 'MCP es un estándar creado por Anthropic que funciona como un "USB universal" para la IA — permite que cualquier modelo se conecte con cualquier servicio externo de forma estandarizada y segura.',
                ],
                [
                    'question'    => '¿Cuál es la diferencia principal entre un chatbot y un agente de IA?',
                    'options'     => [
                        'El chatbot es más inteligente porque está conectado a Internet',
                        'El agente puede planificar, usar herramientas y ejecutar tareas de forma autónoma; el chatbot solo responde preguntas',
                        'No hay diferencia — son nombres distintos para la misma tecnología',
                        'El chatbot funciona con IA y el agente funciona con reglas programadas',
                    ],
                    'correct'     => 1,
                    'explanation' => 'Un agente de IA tiene la capacidad de descomponer un objetivo en subtareas, usar herramientas (buscar web, leer archivos, ejecutar código) y actuar de forma autónoma. Un chatbot solo responde cuando le preguntas.',
                ],
                [
                    'question'    => '¿Cuál es el primer paso recomendado para implementar IA en tu negocio?',
                    'options'     => [
                        'Comprar todas las herramientas de IA disponibles para tener la mayor cobertura',
                        'Esperar a que la regulación esté completa antes de hacer cualquier cosa',
                        'Identificar tu dolor o tarea que más tiempo/dinero consume y buscar la herramienta que lo resuelve',
                        'Contratar un equipo de ingenieros de Machine Learning',
                    ],
                    'correct'     => 2,
                    'explanation' => 'Siempre empieza por el problema, no por la herramienta. Identifica qué tarea te consume más recursos, elige UNA herramienta para resolverla, úsala 30 días y mide resultados.',
                ],
            ],
        ]);

        // ───────────────────────────────────────────────
        // 2. CURSOS LEGACY (no publicados)
        // ───────────────────────────────────────────────

        Course::create([
            'title'        => 'Taller 0: IA Origins',
            'slug'         => 'taller-0',
            'description'  => 'De zero a Power User: Historia, modelos y prompts de élite.',
            'emoji'        => '🚀',
            'tag'          => 'Inicial',
            'is_published' => false,
            'sort_order'   => 10,
        ]);

        Course::create([
            'title'        => 'Taller 1: Notion Mastery',
            'slug'         => 'taller-1',
            'description'  => 'Domina Wikis, Proyectos y Agentes de IA en tu nuevo Segundo Cerebro.',
            'emoji'        => '📓',
            'tag'          => 'Intermedio',
            'is_published' => false,
            'sort_order'   => 11,
        ]);

        Course::create([
            'title'        => 'Taller 2: Viral Contenido',
            'slug'         => 'taller-2',
            'description'  => 'Producción masiva de Reels y TikTok con IA a alta velocidad.',
            'emoji'        => '🎬',
            'tag'          => 'Intermedio',
            'is_published' => false,
            'sort_order'   => 12,
        ]);

        Course::create([
            'title'        => 'Marketing Elite',
            'slug'         => 'marketing-elite',
            'description'  => 'Sistemas de venta automatizados con IA, ManyChat y Claude 3.5.',
            'emoji'        => '💰',
            'tag'          => 'Elite',
            'is_published' => false,
            'sort_order'   => 13,
        ]);
    }
}
