<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class Taller2Seeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('slug', 'taller-2')->first();

        if (!$course) {
            $course = Course::create([
                'title' => 'Taller 2: Viral Contenido',
                'slug' => 'taller-2',
                'description' => 'Aprende a crear contenido que se comparte solo. Desde la psicología de la viralidad hasta producción con IA y CapCut — todo lo que necesitas para escalar tu presencia digital.',
                'emoji' => '🔥',
                'tag' => 'Contenido',
                'is_published' => true,
                'sort_order' => 4,
            ]);
        } else {
            $course->update([
                'description' => 'Aprende a crear contenido que se comparte solo. Desde la psicología de la viralidad hasta producción con IA y CapCut — todo lo que necesitas para escalar tu presencia digital.',
                'emoji' => '🔥',
                'tag' => 'Contenido',
                'is_published' => true,
            ]);
        }

        Lesson::where('course_id', $course->id)->delete();

        $lessons = [
            [
                'title' => '¿Por qué algunas cosas se vuelven virales y otras no?',
                'slug' => 'psicologia-viralidad',
                'type' => 'lecture',
                'sort_order' => 1,
                'content_html' => '
<h2>La viralidad no es suerte</h2>
<p>La mayoría de la gente cree que el contenido viral es cuestión de suerte — que "se dio" porque el algoritmo lo favoreció. La realidad es diferente: <strong>la viralidad tiene patrones predecibles</strong>. No puedes garantizar que algo se vuelva viral, pero sí puedes aumentar dramáticamente las probabilidades.</p>

<h2>Los 6 principios de la viralidad (según la investigación)</h2>
<p>Jonah Berger, profesor de Wharton, estudió durante años por qué ciertas cosas se comparten más que otras. Su modelo STEPPS identifica 6 detonadores:</p>

<ol>
    <li><strong>Social Currency (Moneda social)</strong> — Compartimos cosas que nos hacen ver bien. Si tu contenido hace que quien lo comparte parezca inteligente, informado o gracioso, lo van a compartir.</li>
    <li><strong>Triggers (Detonadores)</strong> — Tu contenido se conecta con algo que la gente ve o experimenta frecuentemente. Kit Kat se asoció con el café: cada vez que alguien toma café, piensa en Kit Kat. ¿Con qué momento del día se puede asociar tu marca?</li>
    <li><strong>Emotion (Emoción)</strong> — El contenido que genera emociones fuertes (asombro, risa, indignación, inspiración) se comparte más. Lo que NO funciona: contenido que genera tristeza pasiva o que es simplemente "interesante".</li>
    <li><strong>Public (Visibilidad)</strong> — Si la gente puede ver que otros ya usan tu producto o comparten tu contenido, es más probable que lo hagan también. Los logos visibles, los stickers, las plantillas con tu marca — todo cuenta.</li>
    <li><strong>Practical Value (Valor práctico)</strong> — Los tips útiles se comparten mucho. "5 atajos de Excel que te ahorran 2 horas por semana" funciona porque la persona que lo comparte está ayudando a alguien.</li>
    <li><strong>Stories (Historias)</strong> — Las personas no comparten datos, comparten historias. Tu marca debe ser parte de una narrativa, no un anuncio.</li>
</ol>

<h2>Lo que esto significa para tu contenido</h2>
<p>Antes de crear cualquier pieza de contenido, pregúntate: <strong>¿cuál de estos 6 principios estoy activando?</strong> Si la respuesta es "ninguno", tu contenido probablemente no se va a compartir. Si activas 2 o 3, tienes una pieza con potencial real.</p>

<h2>Ejercicio</h2>
<p>Abre tu feed de Instagram o TikTok. Busca 3 posts que hayas compartido o guardado recientemente. Para cada uno, identifica cuáles de los 6 principios STEPPS están activos. Escríbelo. Este ejercicio te va a cambiar la forma en que ves el contenido.</p>
',
            ],
            [
                'title' => 'Formatos que funcionan: carruseles, reels y hooks',
                'slug' => 'formatos-que-funcionan',
                'type' => 'lecture',
                'sort_order' => 2,
                'content_html' => '
<h2>No todos los formatos son iguales</h2>
<p>Cada plataforma tiene formatos que el algoritmo favorece. No se trata de estar en todas las plataformas — se trata de <strong>dominar 1-2 formatos en 1-2 plataformas</strong> antes de expandir.</p>

<h2>Los 4 formatos con mayor alcance orgánico en 2026</h2>

<h3>1. Reels / TikTok (video corto: 15-90 segundos)</h3>
<p>El formato rey del alcance orgánico. Instagram y TikTok priorizan videos cortos sobre cualquier otro tipo de contenido.</p>
<ul>
    <li><strong>Ideal para:</strong> Tutoriales rápidos, antes/después, storytelling, tendencias</li>
    <li><strong>La regla de oro:</strong> Los primeros 2 segundos deciden todo. Si no enganchas ahí, la gente hace scroll.</li>
    <li><strong>Duración óptima:</strong> 30-60 segundos para tutoriales, 15-30 para tendencias</li>
</ul>

<h3>2. Carruseles (Instagram, LinkedIn)</h3>
<p>Un carrusel es una serie de imágenes que la persona desliza. Es el formato con mayor tasa de guardado (saves), que es la métrica que más pesa en el algoritmo de Instagram.</p>
<ul>
    <li><strong>Ideal para:</strong> Listas, paso a paso, comparativas, mini-cursos</li>
    <li><strong>La regla de oro:</strong> La primera slide es tu hook. La última es tu CTA (llamada a la acción).</li>
    <li><strong>Cantidad de slides:</strong> 7-10 es el punto óptimo</li>
</ul>

<h3>3. Threads / Posts largos (Twitter/X, LinkedIn)</h3>
<p>Los hilos (threads) funcionan porque generan engagement sostenido. Cada tweet del hilo es una oportunidad de interacción.</p>
<ul>
    <li><strong>Ideal para:</strong> Análisis, opiniones con datos, frameworks, lecciones aprendidas</li>
    <li><strong>La regla de oro:</strong> El primer tweet debe funcionar solo — si no lo leerían sin el hilo, el hilo no va a funcionar</li>
</ul>

<h3>4. YouTube Shorts + video largo (YouTube)</h3>
<p>YouTube es el único lugar donde tu contenido sigue generando vistas meses después de publicarlo. Los Shorts (videos verticales de hasta 60 segundos) alimentan tu canal y atraen nuevos suscriptores que luego ven tu contenido largo.</p>
<ul>
    <li><strong>Estrategia:</strong> Shorts como anzuelo → video largo como profundidad</li>
</ul>

<h2>¿En cuál empezar?</h2>
<p>Si estás empezando desde cero, elige UNO:</p>
<ul>
    <li><strong>Si vendes a consumidores (B2C):</strong> Reels en Instagram o TikTok</li>
    <li><strong>Si vendes a empresas (B2B):</strong> Carruseles en LinkedIn</li>
    <li><strong>Si tu contenido es educativo/evergreen:</strong> YouTube</li>
</ul>

<h2>Ejercicio</h2>
<p>Elige tu formato y plataforma principal. Busca 5 cuentas en tu nicho que estén creciendo. Analiza: ¿qué formato usan más? ¿Qué tipo de hook usan? ¿Cuántas veces publican por semana? Anota los patrones.</p>
',
            ],
            [
                'title' => 'El hook: los primeros 3 segundos que lo deciden todo',
                'slug' => 'el-hook',
                'type' => 'lecture',
                'sort_order' => 3,
                'content_html' => '
<h2>Si no enganchas en 3 segundos, perdiste</h2>
<p>Las plataformas miden una métrica clave: <strong>retention rate</strong> (tasa de retención). Es el porcentaje de personas que siguen viendo tu video después de los primeros segundos. Si tu retención cae en los primeros 3 segundos, el algoritmo deja de mostrar tu contenido. Así de simple.</p>

<p>El hook (gancho) es la frase, imagen o acción que abre tu contenido. Es lo más importante que vas a aprender en este taller.</p>

<h2>Los 7 tipos de hook que funcionan</h2>

<ol>
    <li><strong>La pregunta directa</strong><br>
    "¿Sabías que el 80% de los negocios pierden clientes por esto?"<br>
    Funciona porque activa la curiosidad inmediata.</li>

    <li><strong>La afirmación polémica</strong><br>
    "Publicar todos los días en redes es una pérdida de tiempo."<br>
    Funciona porque genera disonancia — la gente quiere ver si estás equivocado.</li>

    <li><strong>El resultado primero</strong><br>
    "Pasé de 500 a 50,000 seguidores en 3 meses. Esto es lo que hice."<br>
    Funciona porque promete un resultado concreto antes de pedir atención.</li>

    <li><strong>El "no hagas esto"</strong><br>
    "Si estás haciendo esto en tu marketing, para inmediatamente."<br>
    Funciona porque activa el miedo a estar cometiendo un error.</li>

    <li><strong>El tutorial instantáneo</strong><br>
    "Truco de CapCut que nadie te enseñó:" (y lo muestras directamente)<br>
    Funciona porque el valor es inmediato — no hay introducción.</li>

    <li><strong>La lista rápida</strong><br>
    "3 herramientas gratuitas que reemplazaron mi equipo de marketing:"<br>
    Funciona porque la gente ama las listas cortas y accionables.</li>

    <li><strong>El contexto personal</strong><br>
    "Después de 5 años manejando redes para clientes, esto es lo que nadie te dice:"<br>
    Funciona porque establece autoridad antes de dar el contenido.</li>
</ol>

<h2>Hook visual vs. hook verbal</h2>
<p>En video, el hook tiene DOS componentes:</p>
<ul>
    <li><strong>Visual:</strong> Lo que se ve en los primeros 2 segundos (movimiento, texto en pantalla, algo inesperado)</li>
    <li><strong>Verbal:</strong> Lo que se dice o se lee (la frase que engancha)</li>
</ul>
<p>Los mejores videos activan ambos al mismo tiempo. Texto grande en pantalla + voz diciendo algo diferente pero complementario = retención alta.</p>

<h2>Ejercicio</h2>
<p>Escribe 5 hooks para tu negocio, uno de cada tipo (pregunta, polémica, resultado, "no hagas esto", tutorial). No los publiques todavía — en la siguiente lección vamos a convertirlos en contenido real.</p>
',
            ],
            [
                'title' => 'Quiz: Fundamentos de contenido viral',
                'slug' => 'quiz-fundamentos',
                'type' => 'quiz',
                'sort_order' => 4,
                'pass_threshold' => 70,
                'quiz_data' => json_encode([
                    [
                        'question' => 'Según el modelo STEPPS, ¿cuál de estos principios explica por qué la gente comparte tips útiles?',
                        'options' => ['Social Currency', 'Triggers', 'Practical Value', 'Public'],
                        'correct' => 2,
                        'explanation' => 'Practical Value (valor práctico) es el principio que explica por qué los tips, trucos y tutoriales se comparten tanto — la persona que comparte siente que está ayudando a alguien.'
                    ],
                    [
                        'question' => '¿Cuántos segundos tienes para enganchar a tu audiencia en un video corto?',
                        'options' => ['10 segundos', '5 segundos', '3 segundos', '1 segundo'],
                        'correct' => 2,
                        'explanation' => 'Los primeros 3 segundos son críticos. Las plataformas miden la tasa de retención y si la gente se va en los primeros segundos, el algoritmo deja de mostrar tu contenido.'
                    ],
                    [
                        'question' => '¿Cuál es el formato con mayor tasa de guardado (saves) en Instagram?',
                        'options' => ['Reels', 'Carruseles', 'Stories', 'Fotos individuales'],
                        'correct' => 1,
                        'explanation' => 'Los carruseles tienen la mayor tasa de guardado porque la gente los guarda como referencia (listas, paso a paso, tips). Los saves pesan mucho en el algoritmo de Instagram.'
                    ],
                    [
                        'question' => 'Si vendes servicios a otras empresas (B2B), ¿cuál plataforma y formato recomienda este curso para empezar?',
                        'options' => ['TikTok + Reels', 'LinkedIn + Carruseles', 'YouTube + Shorts', 'Twitter + Threads'],
                        'correct' => 1,
                        'explanation' => 'Para B2B, LinkedIn con carruseles es el punto de partida recomendado. Los tomadores de decisión empresariales están en LinkedIn y los carruseles educativos funcionan muy bien ahí.'
                    ],
                    [
                        'question' => '¿Qué tipo de emoción genera MÁS compartidos según la investigación?',
                        'options' => ['Tristeza pasiva', 'Asombro e inspiración', 'Aburrimiento informado', 'Nostalgia'],
                        'correct' => 1,
                        'explanation' => 'Las emociones de alta activación (asombro, risa, indignación, inspiración) generan más compartidos. La tristeza pasiva y el contenido "simplemente interesante" rara vez se comparte.'
                    ],
                ]),
                'content_html' => '<p>Demuestra que entiendes los fundamentos antes de pasar a la producción de contenido.</p>',
            ],
            [
                'title' => 'Producción de video con CapCut (desde cero)',
                'slug' => 'capcut-desde-cero',
                'type' => 'lecture',
                'sort_order' => 5,
                'content_html' => '
<h2>¿Qué es CapCut?</h2>
<p>CapCut es un editor de video gratuito hecho por ByteDance (la empresa detrás de TikTok). Es la herramienta que usan la mayoría de los creadores de contenido en Latinoamérica porque es gratis, fácil de usar, y tiene funciones que en otras apps cuestan dinero.</p>
<p>Existe en versión móvil (iOS/Android) y escritorio (Mac/Windows). Para producción seria, la versión de escritorio es mejor.</p>

<h2>Lo que necesitas para empezar</h2>
<ul>
    <li><strong>Un teléfono con buena cámara</strong> — No necesitas equipo profesional. Un iPhone o Android de gama media es suficiente.</li>
    <li><strong>Luz natural</strong> — Graba frente a una ventana. La luz natural es mejor que cualquier aro de luz barato.</li>
    <li><strong>CapCut Desktop</strong> — Descárgalo gratis desde capcut.com</li>
    <li><strong>Audífonos con micrófono</strong> — Los que vienen con tu teléfono son suficientes para audio decente.</li>
</ul>

<h2>Flujo de trabajo para un Reel / TikTok</h2>
<ol>
    <li><strong>Graba en vertical</strong> (9:16) — Esto es lo más importante. El contenido horizontal no funciona en Reels/TikTok.</li>
    <li><strong>Graba 3-5 tomas</strong> de la misma escena — Siempre tendrás una buena. No te preocupes por los errores, se editan.</li>
    <li><strong>Importa a CapCut</strong> — Arrastra tus clips a la línea de tiempo</li>
    <li><strong>Corta los silencios</strong> — Esta es la diferencia entre un video amateur y uno profesional. Elimina toda pausa, "ehh", "umm" y momento muerto.</li>
    <li><strong>Agrega texto en pantalla</strong> — Las palabras clave de lo que dices. El 80% de las personas ve videos sin sonido.</li>
    <li><strong>Pon música de fondo</strong> — CapCut tiene una biblioteca de música libre de derechos.</li>
    <li><strong>Exporta y publica</strong></li>
</ol>

<h2>Funciones de CapCut que debes conocer</h2>
<ul>
    <li><strong>Auto-captions</strong> — Genera subtítulos automáticamente a partir de tu voz. Funciona en español.</li>
    <li><strong>Keyframes</strong> — Permite hacer zoom dinámico (zoom lento hacia tu cara mientras hablas).</li>
    <li><strong>Templates</strong> — Plantillas prediseñadas que puedes llenar con tu contenido.</li>
    <li><strong>Remove background</strong> — Elimina el fondo de tu video automáticamente.</li>
    <li><strong>Speed ramping</strong> — Cambios de velocidad suaves para transiciones profesionales.</li>
</ul>

<h2>Ejercicio</h2>
<p>Graba un video de 30 segundos con tu teléfono explicando qué hace tu negocio. Usa uno de los hooks que escribiste en la lección anterior. Importa a CapCut, corta los silencios, agrega texto en pantalla y subtítulos automáticos. No lo publiques todavía — guárdalo.</p>
',
            ],
            [
                'title' => 'IA para contenido: texto, imagen y guiones',
                'slug' => 'ia-para-contenido',
                'type' => 'lecture',
                'sort_order' => 6,
                'content_html' => '
<h2>La IA no reemplaza tu creatividad — la multiplica</h2>
<p>La inteligencia artificial (IA) te permite producir contenido a una velocidad que antes era imposible. Pero hay una trampa: <strong>si usas IA para generar contenido genérico, vas a sonar como todos los demás</strong>. La clave es usar IA como herramienta de velocidad, no como sustituto de tu voz.</p>

<h2>3 formas prácticas de usar IA para contenido</h2>

<h3>1. Generación de ideas y guiones</h3>
<p>En vez de sentarte frente a una pantalla en blanco, usa ChatGPT o Claude para generar opciones:</p>
<ul>
    <li><strong>Prompt ejemplo:</strong> "Dame 10 ideas de Reels para una agencia de marketing digital en Latinoamérica. Cada idea debe tener: hook (frase de apertura), concepto del video, y CTA final. Formato de lista."</li>
    <li><strong>Lo que haces tú:</strong> Eliges las 2-3 mejores ideas, las adaptas a tu voz y tu contexto, y las grabas.</li>
</ul>
<p>La IA genera el 60% del trabajo (ideas, estructura). Tú pones el 40% que importa (tu perspectiva, tu experiencia, tu estilo).</p>

<h3>2. Textos para carruseles y captions</h3>
<p>Un carrusel necesita texto conciso, directo, slide por slide. La IA es excelente para esto:</p>
<ul>
    <li><strong>Prompt ejemplo:</strong> "Escribe un carrusel de 8 slides para Instagram sobre [tema]. Slide 1 es el hook. Slides 2-7 son los puntos principales (una idea por slide, máximo 3 líneas). Slide 8 es el CTA. Tono directo, sin frases corporativas."</li>
    <li><strong>Lo que haces tú:</strong> Revisas, ajustas el tono, verificas que los datos sean correctos, y diseñas en Canva.</li>
</ul>

<h3>3. Imágenes con IA</h3>
<p>Herramientas como Canva (con su IA integrada), DALL-E, o Midjourney pueden crear imágenes para tus posts:</p>
<ul>
    <li><strong>Thumbnails</strong> para YouTube</li>
    <li><strong>Fondos</strong> para tus carruseles</li>
    <li><strong>Mockups</strong> de productos</li>
    <li><strong>Ilustraciones</strong> para conceptos abstractos</li>
</ul>
<p><strong>Advertencia:</strong> Usa imágenes de IA como complemento, no como sustituto de fotos reales de tu equipo y tu producto. La autenticidad gana.</p>

<h2>El flujo de producción con IA</h2>
<ol>
    <li><strong>Investiga</strong> — ¿Qué temas interesan a tu audiencia? (usa tus datos de redes, Google Trends, o herramientas como AnswerThePublic)</li>
    <li><strong>Genera con IA</strong> — Pide ideas, guiones y textos a ChatGPT o Claude</li>
    <li><strong>Edita y personaliza</strong> — Adapta a tu voz, agrega tu experiencia, verifica datos</li>
    <li><strong>Produce</strong> — Graba, diseña, edita con CapCut o Canva</li>
    <li><strong>Publica y mide</strong> — Analiza qué funcionó, ajusta para el siguiente</li>
</ol>

<h2>Ejercicio</h2>
<p>Abre ChatGPT o Claude. Usa el prompt de carrusel que te dimos arriba, pero sobre un tema relevante para TU negocio. Revisa el resultado, ajústalo a tu voz, y diseña las slides en Canva (plan gratuito). Guárdalo como borrador.</p>
',
            ],
            [
                'title' => 'El calendario de contenido: publica con sistema',
                'slug' => 'calendario-contenido',
                'type' => 'lecture',
                'sort_order' => 7,
                'content_html' => '
<h2>Publicar sin sistema es como cocinar sin receta</h2>
<p>La consistencia mata al talento sin disciplina. No importa qué tan bueno sea tu contenido si publicas una vez al mes. Los creadores que crecen tienen UNA cosa en común: <strong>un sistema de publicación predecible</strong>.</p>

<h2>¿Cuántas veces publicar?</h2>
<p>Depende de tu formato y plataforma, pero aquí las recomendaciones mínimas:</p>
<ul>
    <li><strong>Instagram Reels:</strong> 3-5 por semana</li>
    <li><strong>TikTok:</strong> 1-3 por día (sí, por día — el volumen importa más aquí)</li>
    <li><strong>LinkedIn:</strong> 3-4 por semana</li>
    <li><strong>YouTube:</strong> 1 video largo por semana + 2-3 Shorts</li>
</ul>
<p>Si estás empezando: <strong>3 publicaciones por semana es el mínimo viable</strong> para que el algoritmo te tome en serio.</p>

<h2>El método Batch: produce todo en un día</h2>
<p>La forma más eficiente de mantener consistencia es el batching (producción en lote):</p>
<ol>
    <li><strong>Lunes: Planificación</strong> — Define los temas de la semana (usa tus ideas generadas con IA)</li>
    <li><strong>Martes: Grabación</strong> — Graba todos los videos de la semana en una sola sesión (2-3 horas)</li>
    <li><strong>Miércoles: Edición</strong> — Edita todo en CapCut. Con práctica, un Reel toma 15-20 minutos de editar.</li>
    <li><strong>Jueves-Domingo: Publicación</strong> — Programa y publica según tu calendario</li>
</ol>
<p>Con este sistema, inviertes 1 día fuerte de producción y el resto de la semana solo publicas y respondes comentarios.</p>

<h2>Tu calendario en Notion</h2>
<p>Si hiciste el Taller 1, ya tienes Notion configurado. Crea una base de datos llamada "Calendario de Contenido" con:</p>
<ul>
    <li><strong>Título</strong> — Nombre del post</li>
    <li><strong>Plataforma</strong> (Multi-select) — Instagram, TikTok, LinkedIn, YouTube</li>
    <li><strong>Formato</strong> (Select) — Reel, Carrusel, Post, Short, Video largo</li>
    <li><strong>Estado</strong> (Select) — Idea, En Producción, Listo, Publicado</li>
    <li><strong>Fecha de publicación</strong> (Date)</li>
    <li><strong>Hook</strong> (Text) — La frase de apertura</li>
    <li><strong>Métricas</strong> (Number) — Views, likes, saves (llénalos después de publicar)</li>
</ul>
<p>Vista Board agrupada por Estado = tu pipeline de contenido visual.</p>

<h2>Ejercicio</h2>
<p>Crea tu calendario de contenido (en Notion o donde prefieras). Planifica 6 publicaciones para las próximas 2 semanas: define tema, hook, formato y fecha. Graba al menos 2 esta semana usando el método batch.</p>
',
            ],
            [
                'title' => 'Nichebending: diferénciate del contenido genérico',
                'slug' => 'nichebending',
                'type' => 'lecture',
                'sort_order' => 8,
                'content_html' => '
<h2>¿Qué es Nichebending?</h2>
<p>Nichebending es una estrategia de diferenciación de contenido que consiste en <strong>mezclar tu nicho principal con un tema inesperado</strong> para crear algo que nadie más está haciendo.</p>

<p>La idea es simple: si 1,000 cuentas hablan de marketing digital de la misma forma, tu contenido se pierde en el ruido. Pero si hablas de marketing digital usando analogías de cocina, o de videojuegos, o de psicología criminal — de repente eres el único haciendo eso.</p>

<h2>Cómo funciona</h2>
<p>La fórmula es: <strong>Tu expertise + Tema inesperado = Contenido único</strong></p>

<p>Ejemplos reales:</p>
<ul>
    <li>"Marketing explicado con memes de The Office" — Nicho marketing + cultura pop</li>
    <li>"Finanzas personales pero como si fuera un videojuego RPG" — Finanzas + gaming</li>
    <li>"Lecciones de liderazgo desde la cocina de un restaurante" — Management + gastronomía</li>
    <li>"Psicología del consumidor explicada con casos criminales" — Marketing + true crime</li>
</ul>

<h2>Por qué funciona</h2>
<ol>
    <li><strong>Rompe patrones</strong> — La gente está acostumbrada a ver el mismo contenido. Algo inesperado activa la atención.</li>
    <li><strong>Es memorable</strong> — "El que explica SEO con recetas de cocina" es más fácil de recordar que "otro consultor de SEO".</li>
    <li><strong>Genera conversación</strong> — Lo inesperado se comenta y se comparte más.</li>
    <li><strong>Es difícil de copiar</strong> — Tu combinación única de intereses es tu ventaja competitiva.</li>
</ol>

<h2>Cómo encontrar tu ángulo de nichebending</h2>
<ol>
    <li>Escribe tu área de expertise (ej: marketing digital para LATAM)</li>
    <li>Lista 5 intereses personales que NO tienen nada que ver (ej: fútbol, anime, cocina, historia, viajes)</li>
    <li>Para cada combinación, pregúntate: "¿Puedo explicar [concepto de mi negocio] usando [interés personal] como metáfora?"</li>
    <li>Elige la combinación más natural y pruébala con 3-5 posts</li>
</ol>

<h2>Ejercicio</h2>
<p>Aplica la fórmula: lista tu expertise + 5 intereses personales. Genera 3 ideas de contenido que combinen ambos. Ejemplo: "El embudo de ventas explicado como una receta de tacos al pastor". Escribe el hook para cada idea.</p>
',
            ],
            [
                'title' => 'Quiz final: Viral Contenido',
                'slug' => 'quiz-final',
                'type' => 'quiz',
                'sort_order' => 9,
                'pass_threshold' => 70,
                'quiz_data' => json_encode([
                    [
                        'question' => '¿Cuál es la regla de oro para la primera slide de un carrusel en Instagram?',
                        'options' => [
                            'Debe tener el logo de tu marca',
                            'Debe ser tu hook — la frase que engancha',
                            'Debe explicar de qué trata el carrusel',
                            'Debe tener una foto profesional'
                        ],
                        'correct' => 1,
                        'explanation' => 'La primera slide es tu hook. Si no engancha ahí, nadie desliza. La última slide es para el CTA.'
                    ],
                    [
                        'question' => '¿Qué es el método Batch para producción de contenido?',
                        'options' => [
                            'Publicar todo el contenido al mismo tiempo',
                            'Producir todo el contenido de la semana en un solo día de grabación',
                            'Usar IA para generar todo automáticamente',
                            'Copiar el contenido de la competencia'
                        ],
                        'correct' => 1,
                        'explanation' => 'El batching consiste en concentrar la producción en un día (planificar, grabar, editar) y el resto de la semana solo publicas y respondes. Es la forma más eficiente de mantener consistencia.'
                    ],
                    [
                        'question' => '¿Cuál es la fórmula del Nichebending?',
                        'options' => [
                            'Más contenido + más plataformas = más alcance',
                            'Tu expertise + Tema inesperado = Contenido único',
                            'Copiar tendencias + agregar tu logo = diferenciación',
                            'IA + automatización = producción infinita'
                        ],
                        'correct' => 1,
                        'explanation' => 'Nichebending combina tu área de expertise con un interés o tema completamente diferente para crear contenido que nadie más está haciendo.'
                    ],
                    [
                        'question' => 'Según este curso, ¿cuál es el rol correcto de la IA en la producción de contenido?',
                        'options' => [
                            'Generar todo automáticamente y publicar sin revisar',
                            'No usarla — el contenido debe ser 100% humano',
                            'Usarla para velocidad (ideas, estructura), tú pones la voz y experiencia',
                            'Solo para imágenes, no para texto'
                        ],
                        'correct' => 2,
                        'explanation' => 'La IA genera el 60% del trabajo (ideas, estructura, borradores). Tú pones el 40% que importa: tu perspectiva, tu experiencia, tu estilo. Nunca publiques contenido de IA sin revisarlo y adaptarlo.'
                    ],
                    [
                        'question' => '¿Cuántas publicaciones por semana recomienda este curso como mínimo viable?',
                        'options' => ['1', '3', '7', '14'],
                        'correct' => 1,
                        'explanation' => '3 publicaciones por semana es el mínimo viable para que los algoritmos de las plataformas te tomen en serio y empiecen a distribuir tu contenido.'
                    ],
                ]),
                'content_html' => '<p>Último quiz del taller. Necesitas 70% para aprobar.</p>',
            ],
            [
                'title' => 'Cierre: tu sistema de contenido está armado',
                'slug' => 'cierre',
                'type' => 'lecture',
                'sort_order' => 10,
                'content_html' => '
<h2>Lo que aprendiste en este taller</h2>
<ul>
    <li>✅ La psicología detrás de la viralidad (modelo STEPPS)</li>
    <li>✅ Qué formatos funcionan mejor en cada plataforma</li>
    <li>✅ Cómo escribir hooks que retienen la atención en 3 segundos</li>
    <li>✅ Producción de video con CapCut desde cero</li>
    <li>✅ Cómo usar IA para multiplicar tu producción (sin perder tu voz)</li>
    <li>✅ Un calendario de contenido con sistema de batching</li>
    <li>✅ Nichebending como estrategia de diferenciación</li>
</ul>

<h2>Tu plan de acción para las próximas 2 semanas</h2>
<ol>
    <li><strong>Semana 1:</strong> Publica 3 piezas de contenido usando los hooks y formatos que aprendiste. No busques perfección — busca consistencia.</li>
    <li><strong>Semana 2:</strong> Analiza qué funcionó mejor (más saves, más shares, más comentarios). Duplica lo que funcionó, descarta lo que no.</li>
</ol>

<h2>La regla del 10-3-1</h2>
<p>De cada 10 piezas de contenido que publiques, probablemente 3 van a tener buen alcance y 1 va a ser un hit. Eso es normal. Los creadores exitosos no son los que hacen contenido perfecto — son los que publican consistentemente hasta que encuentran lo que resuena.</p>

<h2>¿Qué sigue en la Academia?</h2>
<ul>
    <li><strong>Marketing Elite</strong> — Estrategia completa: GEO, pauta inteligente, análisis de datos, LTV/CAC.</li>
    <li><strong>Próximos talleres</strong> — Automatización con Make.com, CRM avanzado, y más.</li>
</ul>

<p><strong>Felicidades por completar el Taller 2.</strong> Ahora tienes un sistema de producción de contenido. Úsalo consistentemente y los resultados llegan.</p>
',
            ],
        ];

        foreach ($lessons as $lessonData) {
            Lesson::create(array_merge($lessonData, [
                'course_id' => $course->id,
                'is_published' => true,
            ]));
        }
    }
}
