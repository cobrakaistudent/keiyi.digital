<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class MarketingEliteSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('slug', 'marketing-elite')->first();

        if (!$course) {
            $course = Course::create([
                'title' => 'Marketing Elite',
                'slug' => 'marketing-elite',
                'description' => 'El programa completo de marketing digital para LATAM. GEO, pauta inteligente, análisis financiero (LTV/CAC), automatización con IA, y liderazgo estratégico. Diseñado para superar lo que enseñan Harvard y Wharton — con ejecución práctica.',
                'emoji' => '🏆',
                'tag' => 'Marketing Avanzado',
                'is_published' => true,
                'sort_order' => 5,
            ]);
        } else {
            $course->update([
                'description' => 'El programa completo de marketing digital para LATAM. GEO, pauta inteligente, análisis financiero (LTV/CAC), automatización con IA, y liderazgo estratégico. Diseñado para superar lo que enseñan Harvard y Wharton — con ejecución práctica.',
                'emoji' => '🏆',
                'tag' => 'Marketing Avanzado',
                'is_published' => true,
            ]);
        }

        Lesson::where('course_id', $course->id)->delete();

        $lessons = [
            [
                'title' => 'El nuevo panorama: por qué el marketing tradicional ya no funciona',
                'slug' => 'nuevo-panorama',
                'type' => 'lecture',
                'sort_order' => 1,
                'content_html' => '
<h2>El mundo cambió. Tu marketing debe cambiar también.</h2>
<p>El CTR orgánico (la cantidad de personas que hacen clic en un resultado de búsqueda de Google) ha caído drásticamente. ¿La razón? Los motores de búsqueda con IA (como Perplexity, Google SGE y ChatGPT con browsing) están respondiendo las preguntas directamente — sin que el usuario necesite visitar tu sitio web.</p>
<p>Esto se llama <strong>"Zero-Click Search"</strong>: el usuario busca, obtiene la respuesta ahí mismo, y nunca hace clic en ningún resultado. Para tu negocio, esto significa que la estrategia de "escribir blogs y esperar tráfico orgánico" está muriendo.</p>

<h2>Las 3 fuerzas que están transformando el marketing</h2>

<h3>1. IA Generativa como primer punto de contacto</h3>
<p>Millones de personas ya le preguntan a ChatGPT "¿cuál es la mejor agencia de marketing en México?" o "¿qué herramienta uso para automatizar mis redes?". Si tu marca no aparece en esas respuestas, estás invisible para una audiencia creciente.</p>

<h3>2. La atención se fragmentó</h3>
<p>Tu cliente potencial ve contenido en TikTok, Instagram, LinkedIn, YouTube, newsletters, podcasts y WhatsApp. Ya no basta con estar en una plataforma. Necesitas una estrategia que funcione en múltiples puntos de contacto — sin multiplicar tu equipo por 5.</p>

<h3>3. Los datos de terceros están desapareciendo</h3>
<p>Las cookies de terceros (esos datos que permitían rastrear usuarios entre sitios) están siendo eliminadas. Esto significa que tu capacidad de hacer retargeting (mostrar anuncios a personas que ya visitaron tu sitio) se está reduciendo. La solución: <strong>datos propios (first-party data)</strong> recopilados con el consentimiento del usuario.</p>

<h2>¿Qué vamos a aprender en este curso?</h2>
<p>Marketing Elite no es otro curso de "cómo usar Instagram". Es un programa de estrategia completa que cubre:</p>
<ol>
    <li><strong>GEO</strong> — Cómo posicionar tu marca en los motores de IA</li>
    <li><strong>Pauta inteligente</strong> — Publicidad pagada con tracking limpio y ROI medible</li>
    <li><strong>Finanzas de marketing</strong> — LTV, CAC, y las métricas que determinan si tu negocio es rentable</li>
    <li><strong>Automatización con IA</strong> — Flujos que trabajan mientras duermes</li>
    <li><strong>Liderazgo estratégico</strong> — Cómo tomar decisiones de marketing como un director, no como un ejecutor</li>
</ol>
<p>Este curso asume que ya tienes los fundamentos (si no, haz primero el Taller 0: IA Origins y el Taller 2: Viral Contenido).</p>
',
            ],
            [
                'title' => 'GEO: Generative Engine Optimization',
                'slug' => 'geo-optimization',
                'type' => 'lecture',
                'sort_order' => 2,
                'content_html' => '
<h2>¿Qué es GEO?</h2>
<p><strong>GEO (Generative Engine Optimization)</strong> es el equivalente del SEO pero para motores de búsqueda con IA. Mientras el SEO tradicional se enfoca en aparecer en los 10 resultados azules de Google, el GEO se enfoca en que tu marca sea <strong>mencionada y recomendada en las respuestas generadas por IA</strong>.</p>

<p>Piensa en la diferencia así: el SEO te pone en la lista de opciones. El GEO te pone en la <em>respuesta</em>.</p>

<h2>¿Cómo decide la IA qué marcas recomendar?</h2>
<p>Los modelos de IA como ChatGPT, Perplexity y Gemini no buscan en Google. Buscan en su entrenamiento y en fuentes que consideran confiables. Los factores que influyen:</p>
<ul>
    <li><strong>Autoridad del contenido</strong> — ¿Tu sitio tiene artículos profundos con datos originales? Los modelos citan fuentes que aportan información única, no que repiten lo que todos dicen.</li>
    <li><strong>Estructura clara</strong> — Los artículos con headers, listas y definiciones claras son más fáciles de citar para la IA.</li>
    <li><strong>Menciones en fuentes confiables</strong> — Si tu marca aparece en artículos de medios reconocidos, foros especializados, o directorios de industria, la IA la "conoce".</li>
    <li><strong>Datos propios</strong> — Estadísticas, estudios de caso y benchmarks originales que nadie más tiene.</li>
</ul>

<h2>Estrategia GEO en 5 pasos</h2>
<ol>
    <li><strong>Auditoría de visibilidad actual</strong> — Abre ChatGPT y Perplexity. Busca preguntas que tus clientes harían sobre tu categoría. ¿Apareces? ¿Aparecen tus competidores?</li>
    <li><strong>Contenido citeable</strong> — Crea artículos que respondan preguntas específicas con datos originales. No "5 tips de marketing" sino "Estudio: cuánto invierte una PYME en LATAM en publicidad digital en 2026".</li>
    <li><strong>Definiciones claras</strong> — Si tu marca puede ser la fuente de una definición ("¿Qué es el marketing de influencia?"), la IA te citará como referencia.</li>
    <li><strong>Presencia en directorios y foros</strong> — Asegúrate de que tu marca aparezca en los lugares donde la IA busca: directorios de industria, perfiles de Crunchbase/LinkedIn, artículos en medios.</li>
    <li><strong>Monitoreo continuo</strong> — Revisa mensualmente si tu visibilidad en respuestas de IA mejora. Ajusta tu contenido según lo que la IA cita y lo que ignora.</li>
</ol>

<h2>GEO vs SEO: no es uno u otro</h2>
<p>El GEO no reemplaza al SEO. Lo complementa. El SEO sigue siendo importante para tráfico directo a tu sitio. El GEO asegura que tu marca exista en el nuevo canal de descubrimiento: las respuestas de IA.</p>

<h2>Ejercicio</h2>
<p>Haz la auditoría del paso 1: abre ChatGPT y Perplexity, busca 5 preguntas que tus clientes harían. Documenta: ¿qué marcas aparecen? ¿Apareces tú? ¿Qué tipo de contenido están citando? Escribe tus hallazgos — los usaremos en las siguientes lecciones.</p>
',
            ],
            [
                'title' => 'LTV y CAC: las métricas que deciden si tu negocio sobrevive',
                'slug' => 'ltv-cac',
                'type' => 'lecture',
                'sort_order' => 3,
                'content_html' => '
<h2>Dos números que todo dueño de negocio debe saber</h2>
<p>Si solo pudieras conocer dos métricas de tu negocio, deberían ser estas:</p>

<h3>CAC — Costo de Adquisición de Cliente</h3>
<p><strong>¿Cuánto te cuesta conseguir un nuevo cliente?</strong> Se calcula dividiendo todo lo que gastas en marketing y ventas entre el número de clientes nuevos que conseguiste en ese período.</p>
<p>Fórmula: <strong>CAC = Inversión en marketing y ventas ÷ Clientes nuevos</strong></p>
<p>Ejemplo: Si gastaste $5,000 USD en publicidad y ventas el mes pasado y conseguiste 10 clientes nuevos, tu CAC es $500.</p>

<h3>LTV — Lifetime Value (Valor del Cliente a lo Largo de su Vida)</h3>
<p><strong>¿Cuánto dinero te deja un cliente durante toda su relación contigo?</strong> No solo la primera compra — todo lo que te compra mientras sigue siendo tu cliente.</p>
<p>Fórmula simplificada: <strong>LTV = Ticket promedio × Frecuencia de compra × Tiempo promedio como cliente</strong></p>
<p>Ejemplo: Si un cliente te paga $200/mes y se queda en promedio 18 meses, su LTV es $3,600.</p>

<h2>La regla de oro: LTV debe ser al menos 3x tu CAC</h2>
<p>Si tu LTV es $3,600 y tu CAC es $500, tu ratio es 7.2x — excelente. Puedes invertir más en adquisición.</p>
<p>Si tu LTV es $600 y tu CAC es $500, tu ratio es 1.2x — estás en problemas. Casi no ganas dinero por cada cliente nuevo.</p>

<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr style="border-bottom:2px solid #000;"><th style="text-align:left;padding:8px;">Ratio LTV:CAC</th><th style="text-align:left;padding:8px;">Significado</th></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;">Menor a 1:1</td><td style="padding:8px;color:#dc2626;"><strong>Pierdes dinero</strong> por cada cliente. Urgente revisar.</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;">1:1 a 3:1</td><td style="padding:8px;color:#d97706;"><strong>Zona peligrosa.</strong> Apenas cubres costos.</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;">3:1 a 5:1</td><td style="padding:8px;color:#16a34a;"><strong>Saludable.</strong> Puedes crecer con confianza.</td></tr>
<tr><td style="padding:8px;">Mayor a 5:1</td><td style="padding:8px;color:#2563eb;"><strong>Excelente.</strong> Considera invertir más agresivamente en adquisición.</td></tr>
</table>

<h2>Cómo mejorar cada métrica</h2>
<p><strong>Para bajar tu CAC:</strong></p>
<ul>
    <li>Mejora tu contenido orgánico (GEO + SEO) — clientes que llegan sin publicidad pagada</li>
    <li>Optimiza tus campañas de pauta — mejor segmentación, mejores ads</li>
    <li>Pide referidos a tus clientes actuales — el canal más barato que existe</li>
</ul>
<p><strong>Para subir tu LTV:</strong></p>
<ul>
    <li>Mejora la retención — un cliente que se queda 24 meses en vez de 12 duplica tu LTV</li>
    <li>Haz upselling — ofrece servicios adicionales a tus clientes existentes</li>
    <li>Mejora la experiencia — clientes satisfechos compran más y se quedan más</li>
</ul>

<h2>Ejercicio</h2>
<p>Calcula tu CAC y LTV reales. Si no tienes datos exactos, estima con lo que sabes. Luego calcula tu ratio LTV:CAC. ¿En qué zona estás? Escribe 2 acciones concretas que puedes tomar para mejorar cada métrica.</p>
',
            ],
            [
                'title' => 'Quiz: Estrategia y métricas',
                'slug' => 'quiz-estrategia',
                'type' => 'quiz',
                'sort_order' => 4,
                'pass_threshold' => 70,
                'quiz_data' => json_encode([
                    [
                        'question' => '¿Qué es GEO (Generative Engine Optimization)?',
                        'options' => [
                            'Una herramienta para crear contenido con IA',
                            'Optimizar tu marca para aparecer en las respuestas de motores de búsqueda con IA',
                            'Un tipo de publicidad pagada en Google',
                            'Una métrica de rendimiento de redes sociales'
                        ],
                        'correct' => 1,
                        'explanation' => 'GEO es la estrategia de optimizar tu contenido y presencia digital para que los motores de búsqueda con IA (ChatGPT, Perplexity, Gemini) mencionen y recomienden tu marca en sus respuestas.'
                    ],
                    [
                        'question' => 'Tu negocio gastó $3,000 en marketing el mes pasado y consiguió 6 clientes nuevos. ¿Cuál es tu CAC?',
                        'options' => ['$500', '$3,000', '$18,000', '$250'],
                        'correct' => 0,
                        'explanation' => 'CAC = Inversión ÷ Clientes nuevos = $3,000 ÷ 6 = $500 por cliente.'
                    ],
                    [
                        'question' => '¿Cuál es el ratio LTV:CAC mínimo saludable?',
                        'options' => ['1:1', '2:1', '3:1', '10:1'],
                        'correct' => 2,
                        'explanation' => 'El ratio mínimo saludable es 3:1 — tu cliente debe dejarte al menos 3 veces lo que te costó adquirirlo. Menos de eso y difícilmente cubres costos operativos.'
                    ],
                    [
                        'question' => '¿Qué es una "Zero-Click Search"?',
                        'options' => [
                            'Una búsqueda que no genera resultados',
                            'Una búsqueda donde el usuario obtiene la respuesta sin hacer clic en ningún resultado',
                            'Un tipo de publicidad que no requiere clics',
                            'Una métrica de engagement en redes sociales'
                        ],
                        'correct' => 1,
                        'explanation' => 'En una Zero-Click Search, el motor de búsqueda responde directamente con IA, y el usuario nunca visita ningún sitio web. Esto reduce el tráfico orgánico a los sitios.'
                    ],
                    [
                        'question' => '¿Cuál de estas estrategias ayuda a BAJAR tu CAC?',
                        'options' => [
                            'Subir los precios de tus servicios',
                            'Pedir referidos a clientes actuales',
                            'Contratar más vendedores',
                            'Publicar menos contenido'
                        ],
                        'correct' => 1,
                        'explanation' => 'Los referidos son el canal de adquisición más barato porque aprovechas la confianza que tus clientes actuales ya tienen con sus contactos. No requieren inversión en publicidad.'
                    ],
                ]),
                'content_html' => '<p>Evalúa tu comprensión de GEO, LTV, CAC y el nuevo panorama de marketing antes de avanzar a la ejecución.</p>',
            ],
            [
                'title' => 'Ingeniería de pauta: publicidad pagada inteligente',
                'slug' => 'ingenieria-pauta',
                'type' => 'lecture',
                'sort_order' => 5,
                'content_html' => '
<h2>La pauta no es "gastar dinero en anuncios"</h2>
<p>La mayoría de los negocios en LATAM hacen publicidad pagada de la peor forma: boost de posts en Instagram sin estrategia, sin tracking, y sin idea de si están ganando o perdiendo dinero. Eso no es pauta — es tirar dinero.</p>
<p><strong>Ingeniería de pauta</strong> es tratar la publicidad pagada como un sistema medible: entran X dólares, salen Y clientes, con un costo que conoces y puedes optimizar.</p>

<h2>El embudo de pauta: TOFU → MOFU → BOFU</h2>
<p>Toda campaña de publicidad efectiva tiene 3 niveles. Piensa en un embudo (ancho arriba, estrecho abajo):</p>

<h3>TOFU — Top of Funnel (parte alta: conocimiento)</h3>
<p>Objetivo: que la mayor cantidad de personas CONOZCA tu marca. Aquí no vendes — informas, educas o entretienes.</p>
<ul>
    <li><strong>Formato:</strong> Videos cortos, carruseles educativos, artículos</li>
    <li><strong>Métrica clave:</strong> Alcance y CPM (Costo por Mil impresiones — cuánto pagas para que 1,000 personas vean tu anuncio)</li>
    <li><strong>Presupuesto:</strong> 40% de tu inversión en pauta</li>
</ul>

<h3>MOFU — Middle of Funnel (parte media: consideración)</h3>
<p>Objetivo: que las personas que ya te conocen empiecen a CONSIDERAR comprarte. Aquí muestras valor y generas confianza.</p>
<ul>
    <li><strong>Formato:</strong> Testimonios, casos de estudio, webinars, demos</li>
    <li><strong>Métrica clave:</strong> Engagement rate y CPC (Costo por Clic — cuánto pagas por cada visita a tu sitio)</li>
    <li><strong>Presupuesto:</strong> 30% de tu inversión</li>
</ul>

<h3>BOFU — Bottom of Funnel (parte baja: conversión)</h3>
<p>Objetivo: que las personas que ya te consideran COMPREN. Aquí haces la oferta directa.</p>
<ul>
    <li><strong>Formato:</strong> Ofertas directas, pruebas gratuitas, landing pages</li>
    <li><strong>Métrica clave:</strong> CPA (Costo por Adquisición — cuánto pagas por cada cliente) y ROAS (Return on Ad Spend — cuánto ganas por cada dólar invertido en publicidad)</li>
    <li><strong>Presupuesto:</strong> 30% de tu inversión</li>
</ul>

<h2>Server-Side Tracking: por qué importa</h2>
<p>Cuando alguien hace clic en tu anuncio, esa información se envía a Meta o Google para que sepan qué funciona. El problema: los bloqueadores de publicidad y las restricciones de cookies eliminan esos datos. Resultado: tus campañas parecen funcionar peor de lo que realmente funcionan.</p>
<p><strong>Server-Side Tracking</strong> envía los datos directamente desde tu servidor (no desde el navegador del usuario), esquivando los bloqueadores. Es más preciso y te permite optimizar mejor tus campañas.</p>

<h2>Ejercicio</h2>
<p>Diseña tu embudo de pauta en papel: TOFU, MOFU, BOFU. Para cada nivel, define: qué tipo de contenido mostrarías, a qué audiencia, y qué métrica medirías. Calcula cuánto presupuesto asignarías a cada nivel con un presupuesto hipotético de $1,000 USD mensuales.</p>
',
            ],
            [
                'title' => 'Automatización con IA: flujos que trabajan solos',
                'slug' => 'automatizacion-ia',
                'type' => 'lecture',
                'sort_order' => 6,
                'content_html' => '
<h2>La automatización es el multiplicador de fuerza</h2>
<p>Si tú o tu equipo pasan tiempo haciendo tareas repetitivas (enviar emails de seguimiento, actualizar hojas de cálculo, publicar en redes, generar reportes), están trabajando como máquinas. Y las máquinas hacen ese trabajo mejor.</p>
<p>La automatización de marketing con IA no es ciencia ficción — es conectar las herramientas que ya usas para que se hablen entre sí y ejecuten tareas sin intervención humana.</p>

<h2>Los 5 flujos de automatización más valiosos</h2>

<h3>1. Lead scoring automático</h3>
<p>Cuando alguien llena un formulario en tu sitio, la IA evalúa su perfil y le asigna una puntuación. ¿Tiene presupuesto? ¿Es tu cliente ideal? ¿Cuán urgente es su necesidad? Los leads con score alto van directo a ventas. Los demás entran a una secuencia de nurturing (emails educativos que los "calientan" gradualmente).</p>

<h3>2. Secuencias de email personalizadas</h3>
<p>En vez de enviar el mismo email a toda tu lista, la IA segmenta y personaliza: un lead que visitó tu página de precios recibe un email diferente al que leyó tu blog. Todo automático.</p>

<h3>3. Reportes automáticos para clientes</h3>
<p>Si manejas clientes de marketing, los reportes mensuales consumen horas. Con automatización: los datos de Meta Ads + Google Ads se consolidan automáticamente en un template de reporte que se envía al cliente cada lunes.</p>

<h3>4. Publicación y reciclaje de contenido</h3>
<p>Publicas un video en YouTube → se corta automáticamente en 3 clips para Reels → se genera un carrusel con los puntos clave → se programa en LinkedIn. Un solo contenido se multiplica en 5 formatos sin trabajo manual.</p>

<h3>5. Respuestas inteligentes a consultas</h3>
<p>Un chatbot con IA en tu sitio web responde preguntas frecuentes, califica leads, y agenda reuniones — 24/7, incluyendo fines de semana y noches, cuando tú no estás disponible.</p>

<h2>Herramientas para empezar</h2>
<ul>
    <li><strong>Make.com</strong> — La herramienta más versátil para conectar apps (la vimos en el Taller 1). Plan gratuito suficiente para empezar.</li>
    <li><strong>Zapier</strong> — Alternativa a Make.com, más simple pero menos flexible.</li>
    <li><strong>n8n</strong> — Open source (gratis), para los que quieren control total.</li>
    <li><strong>ChatGPT / Claude API</strong> — Para agregar inteligencia a tus flujos (análisis de texto, generación de respuestas, clasificación).</li>
</ul>

<h2>Ejercicio</h2>
<p>Identifica las 3 tareas más repetitivas en tu negocio esta semana. Para cada una, escribe: qué herramientas están involucradas, qué datos se mueven, y cómo se vería el flujo automatizado. No necesitas implementarlo todavía — primero diseña el flujo en papel.</p>
',
            ],
            [
                'title' => 'Propuesta de valor: cómo diferenciarte con IA',
                'slug' => 'propuesta-valor-ia',
                'type' => 'lecture',
                'sort_order' => 7,
                'content_html' => '
<h2>Tu propuesta de valor es tu ventaja competitiva</h2>
<p>La <strong>propuesta de valor</strong> es la respuesta a una pregunta simple: <em>"¿Por qué debería elegirte a ti y no a tu competencia?"</em> Si no puedes responder esto en una oración clara, tu marketing no va a funcionar — sin importar cuánto gastes en publicidad.</p>

<h2>El framework de propuesta de valor</h2>
<p>Una buena propuesta de valor tiene 3 componentes:</p>
<ol>
    <li><strong>Para quién</strong> — Tu cliente ideal, descrito con precisión</li>
    <li><strong>Qué problema resuelves</strong> — El dolor específico que eliminas</li>
    <li><strong>Cómo lo resuelves de forma única</strong> — Lo que tú haces diferente que nadie más ofrece</li>
</ol>

<p><strong>Ejemplo genérico (malo):</strong> "Somos una agencia de marketing digital que ayuda a empresas a crecer."</p>
<p><strong>Ejemplo específico (bueno):</strong> "Ayudamos a PYMEs de e-commerce en LATAM a triplicar sus ventas en 6 meses usando automatización con IA — sin que necesiten contratar equipo de marketing."</p>

<h2>Cómo usar IA para crear tu propuesta de valor</h2>
<p>La IA puede ayudarte a iterar rápidamente sobre tu propuesta de valor:</p>

<p><strong>Prompt para ChatGPT/Claude:</strong></p>
<p><em>"Soy [tu negocio]. Mi cliente ideal es [descripción]. El principal problema que resuelvo es [problema]. Lo que me diferencia es [diferenciador]. Genera 5 variaciones de propuesta de valor, cada una en máximo 2 oraciones. Tono directo, sin jerga corporativa."</em></p>

<p>La IA te da 5 opciones en 30 segundos. Tú eliges la mejor, la ajustas, y la pruebas con clientes reales.</p>

<h2>Dónde debe vivir tu propuesta de valor</h2>
<ul>
    <li><strong>Tu sitio web</strong> — En el header, arriba del fold (lo primero que se ve sin hacer scroll)</li>
    <li><strong>Tu bio de redes sociales</strong> — En la primera línea</li>
    <li><strong>Tus anuncios</strong> — Como copy principal</li>
    <li><strong>Tu pitch de ventas</strong> — Los primeros 30 segundos</li>
    <li><strong>Tus respuestas de IA</strong> — Si haces GEO bien, la IA cita tu propuesta de valor cuando alguien pregunta por soluciones en tu categoría</li>
</ul>

<h2>Ejercicio</h2>
<p>Usa el prompt de arriba con ChatGPT o Claude. Genera 5 variaciones de tu propuesta de valor. Elige las 2 mejores y envíalas a 3 clientes actuales o contactos de confianza. Pregúntales: "Si leyeras esto, ¿te queda claro qué hago y por qué me elegirías?" Ajusta según su feedback.</p>
',
            ],
            [
                'title' => 'Liderazgo IA: gobernanza, riesgos y decisiones C-Level',
                'slug' => 'liderazgo-ia',
                'type' => 'lecture',
                'sort_order' => 8,
                'content_html' => '
<h2>La IA no es solo una herramienta técnica — es una decisión de negocio</h2>
<p>Hasta ahora hemos hablado de la IA como herramienta: para crear contenido, automatizar tareas, optimizar campañas. Pero si diriges un negocio, necesitas pensar en un nivel más alto: <strong>¿cómo gobiernas el uso de IA en tu organización?</strong></p>

<h2>Los 4 riesgos que todo líder debe conocer</h2>

<h3>1. Sesgo algorítmico</h3>
<p>Los modelos de IA reflejan los datos con los que fueron entrenados. Si esos datos tienen sesgos (de género, raza, región), tus decisiones de marketing heredan esos sesgos. Ejemplo: un modelo de IA que decide a quién mostrar tus anuncios podría excluir involuntariamente a ciertos grupos demográficos.</p>
<p><strong>Qué hacer:</strong> Revisa regularmente a quién llegan tus campañas automatizadas. Si ciertos segmentos están subrepresentados, ajusta manualmente.</p>

<h3>2. Dependencia de proveedores</h3>
<p>Si todo tu marketing depende de una sola IA (ChatGPT, por ejemplo), un cambio de precios, una caída del servicio, o un cambio en los términos de uso puede paralizar tu operación.</p>
<p><strong>Qué hacer:</strong> Diversifica. Usa al menos 2 herramientas de IA. Ten procesos documentados que puedan funcionar sin IA como respaldo.</p>

<h3>3. Calidad del contenido generado</h3>
<p>La IA puede generar contenido rápido pero genérico. Si todo tu marketing es generado por IA sin revisión humana, tu marca pierde autenticidad y se pierde entre miles de marcas que hacen lo mismo.</p>
<p><strong>Qué hacer:</strong> La IA genera el borrador. Un humano revisa, personaliza y aprueba. Nunca publiques contenido de IA sin revisión.</p>

<h3>4. Privacidad y datos de clientes</h3>
<p>Al usar herramientas de IA, estás enviando datos a servidores externos. Si esos datos incluyen información de tus clientes (nombres, emails, historial de compras), podrías estar violando regulaciones de privacidad.</p>
<p><strong>Qué hacer:</strong> Nunca envíes datos personales de clientes a herramientas de IA sin anonimizarlos primero. Lee los términos de uso de cada herramienta.</p>

<h2>El framework de decisión C-Level para IA</h2>
<p>Antes de implementar cualquier herramienta de IA en tu negocio, pasa por estas 4 preguntas:</p>
<ol>
    <li><strong>¿Qué problema resuelve?</strong> — Si no puedes articular el problema claramente, no necesitas la herramienta.</li>
    <li><strong>¿Cuál es el costo total?</strong> — No solo la suscripción: incluye tiempo de implementación, curva de aprendizaje, y mantenimiento.</li>
    <li><strong>¿Qué pasa si falla?</strong> — ¿Tienes un plan B si la herramienta deja de funcionar o cambia de precio?</li>
    <li><strong>¿Quién es responsable?</strong> — Alguien en tu equipo debe ser el "dueño" de cada herramienta de IA. Si es de todos, es de nadie.</li>
</ol>

<h2>Ejercicio</h2>
<p>Lista todas las herramientas de IA que usas actualmente en tu negocio. Para cada una, responde las 4 preguntas del framework. ¿Hay alguna que no pase las 4 preguntas? Probablemente es una herramienta que estás pagando sin necesitar.</p>
',
            ],
            [
                'title' => 'Quiz final: Marketing Elite',
                'slug' => 'quiz-final',
                'type' => 'quiz',
                'sort_order' => 9,
                'pass_threshold' => 70,
                'quiz_data' => json_encode([
                    [
                        'question' => 'Un cliente tiene un LTV de $1,800 y un CAC de $900. ¿En qué zona está su negocio?',
                        'options' => ['Excelente (>5:1)', 'Saludable (3:1 a 5:1)', 'Zona peligrosa (1:1 a 3:1)', 'Pierde dinero (<1:1)'],
                        'correct' => 2,
                        'explanation' => 'LTV $1,800 ÷ CAC $900 = ratio 2:1. Esto está en la zona peligrosa (1:1 a 3:1). Necesita bajar su CAC o aumentar su LTV para llegar al mínimo saludable de 3:1.'
                    ],
                    [
                        'question' => '¿Qué nivel del embudo de pauta es responsable de la CONVERSIÓN (que la gente compre)?',
                        'options' => ['TOFU (Top of Funnel)', 'MOFU (Middle of Funnel)', 'BOFU (Bottom of Funnel)', 'Todos por igual'],
                        'correct' => 2,
                        'explanation' => 'BOFU (Bottom of Funnel) es el nivel de conversión. TOFU genera conocimiento, MOFU genera consideración, y BOFU cierra la venta.'
                    ],
                    [
                        'question' => '¿Qué ventaja tiene el Server-Side Tracking sobre el tracking tradicional?',
                        'options' => [
                            'Es más barato',
                            'No necesita código',
                            'Los datos se envían desde tu servidor, esquivando bloqueadores de publicidad',
                            'Funciona solo con Google Ads'
                        ],
                        'correct' => 2,
                        'explanation' => 'El Server-Side Tracking envía datos directamente desde tu servidor (no desde el navegador del usuario), por lo que los bloqueadores de publicidad y las restricciones de cookies no lo afectan. Esto da datos más precisos para optimizar campañas.'
                    ],
                    [
                        'question' => '¿Cuál de estas es una buena práctica de gobernanza de IA según este curso?',
                        'options' => [
                            'Publicar contenido de IA sin revisión para ahorrar tiempo',
                            'Depender de una sola herramienta de IA para todo',
                            'Nunca enviar datos personales de clientes a herramientas de IA sin anonimizarlos',
                            'Dejar que todos en el equipo usen cualquier herramienta sin control'
                        ],
                        'correct' => 2,
                        'explanation' => 'Enviar datos de clientes a herramientas de IA sin anonimizar puede violar regulaciones de privacidad. Siempre anonimiza o elimina datos personales antes de procesarlos con IA externa.'
                    ],
                    [
                        'question' => '¿Qué diferencia al GEO del SEO tradicional?',
                        'options' => [
                            'El GEO solo funciona en Google',
                            'El SEO te pone en la lista de resultados; el GEO te pone en la respuesta de la IA',
                            'El GEO reemplaza completamente al SEO',
                            'No hay diferencia real'
                        ],
                        'correct' => 1,
                        'explanation' => 'El SEO busca que aparezcas en los resultados de búsqueda. El GEO busca que la IA te mencione directamente en su respuesta. Son complementarios, no sustitutos.'
                    ],
                ]),
                'content_html' => '<p>Examen final del programa Marketing Elite. Necesitas 70% para completar el curso.</p>',
            ],
            [
                'title' => 'Cierre: tu plan estratégico de marketing',
                'slug' => 'cierre',
                'type' => 'lecture',
                'sort_order' => 10,
                'content_html' => '
<h2>Lo que aprendiste en Marketing Elite</h2>
<ul>
    <li>✅ El nuevo panorama: Zero-Click Search, fragmentación de atención, muerte de cookies</li>
    <li>✅ GEO: cómo posicionar tu marca en las respuestas de IA</li>
    <li>✅ LTV y CAC: las métricas financieras que deciden si tu negocio es rentable</li>
    <li>✅ Ingeniería de pauta: embudo TOFU/MOFU/BOFU con tracking preciso</li>
    <li>✅ Automatización con IA: los 5 flujos más valiosos</li>
    <li>✅ Propuesta de valor: cómo diferenciarte usando IA como acelerador</li>
    <li>✅ Liderazgo IA: gobernanza, riesgos y decisiones estratégicas</li>
</ul>

<h2>Tu plan de 30 días</h2>
<ol>
    <li><strong>Semana 1:</strong> Calcula tu LTV y CAC reales. Haz la auditoría GEO (¿apareces en respuestas de IA?). Define tu propuesta de valor con el framework.</li>
    <li><strong>Semana 2:</strong> Diseña tu embudo de pauta (TOFU/MOFU/BOFU) con presupuesto asignado a cada nivel. Identifica las 3 tareas más automatizables.</li>
    <li><strong>Semana 3:</strong> Implementa tu primera automatización con Make.com o la herramienta que prefieras. Lanza tu primera campaña de pauta estructurada.</li>
    <li><strong>Semana 4:</strong> Mide resultados. ¿Tu CAC bajó? ¿Tu contenido GEO está siendo citado? Ajusta y optimiza.</li>
</ol>

<h2>La diferencia entre saber y ejecutar</h2>
<p>El 95% de las personas que toman cursos de marketing no implementan nada. Leen, asienten, y siguen haciendo lo mismo de siempre. Los que ganan son los que ejecutan — aunque sea imperfectamente.</p>
<p>No necesitas implementar todo a la vez. Elige UNA cosa de este curso e impleméntala esta semana. Una sola. Después la siguiente. El progreso acumulado es lo que construye negocios exitosos.</p>

<h2>Felicidades</h2>
<p>Completaste Marketing Elite. Ahora tienes las herramientas estratégicas que la mayoría de los marketers en LATAM no conocen. La ventaja es tuya. Úsala.</p>

<p><strong>¿Necesitas acompañamiento personalizado?</strong> Nuestro equipo de estrategia digital en Keiyi puede ayudarte a implementar todo lo que aprendiste aquí, adaptado a tu negocio específico. Hablemos.</p>
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
