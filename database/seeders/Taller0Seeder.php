<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class Taller0Seeder extends Seeder
{
    public function run(): void
    {
        // Update or create the Taller 0 course
        $course = Course::updateOrCreate(
            ['slug' => 'taller-0'],
            [
                'title' => 'Taller 0: IA Origins & Motor Agentico',
                'slug' => 'taller-0',
                'description' => 'El complemento practico del Intro a la IA. 10 lecciones hands-on: simuladores, ejercicios interactivos y tu propio Plan de IA.',
                'emoji' => '🔧',
                'tag' => 'Taller Practico',
                'is_published' => true,
                'sort_order' => 1,
            ]
        );

        // Delete existing lessons for this course (idempotent re-run)
        Lesson::where('course_id', $course->id)->delete();

        // ═══════════════════════════════════════════════════
        // LECCION 1: Mision: Conoce a tu Aliado Digital
        // ═══════════════════════════════════════════════════
        Lesson::create([
            'course_id' => $course->id,
            'title' => 'Mision: Conoce a tu Aliado Digital',
            'slug' => 'conoce-aliado-digital',
            'type' => 'interactive',
            'sort_order' => 1,
            'is_published' => true,
            'pass_threshold' => 70,
            'video_outline' => "Video (5 min)\n0:00 — \"¿En que nivel estas?\" — Intro provocativa\n0:45 — Auditoria visual: herramientas IA que ya usas\n1:30 — Los 3 niveles de madurez en IA\n2:30 — Ejemplos LATAM por nivel\n3:45 — \"Imagina si las usaras CON INTENCION\"\n4:30 — Cierre + avance leccion 2",
            'content_html' => '
<h2>Ya usas IA — solo que no lo sabes</h2>
<p>Antes de aprender a usar IA "de verdad", necesitas darte cuenta de algo: <strong>ya eres usuario de IA</strong>. Cada vez que Google te autocompleta una busqueda, Spotify te recomienda una cancion, o Gmail filtra tu spam — estas usando inteligencia artificial.</p>
<p>La diferencia entre un usuario pasivo y un estratega digital no es la tecnologia — es la <strong>intencion</strong>.</p>

<h2>Los 3 niveles de madurez en IA</h2>
<h3>Nivel 1: Usuario Pasivo</h3>
<p>Usas IA sin saberlo. Netflix te recomienda series, Waze te redirige por trafico, tu banco detecta fraude automaticamente. No tomas decisiones conscientes sobre IA.</p>

<h3>Nivel 2: Usuario Activo</h3>
<p>Usas ChatGPT o Claude para tareas especificas: redactar emails, resumir textos, generar ideas. Sabes que existe la IA y la usas como herramienta puntual.</p>

<h3>Nivel 3: Estratega Digital</h3>
<p>Diseñas workflows completos con IA integrada. Automatizas procesos, conectas herramientas, mides resultados. La IA no es una herramienta mas — es parte de tu operacion.</p>

<p><strong>El objetivo de este taller:</strong> llevarte del nivel donde estes al siguiente. Si eres nivel 1, terminaras como nivel 2 solido. Si ya eres nivel 2, tendras las bases para ser estratega.</p>
',
            'interactive_data' => [
                [
                    'type' => 'checklist',
                    'title' => 'Auditoria IA de tu Dia',
                    'instructions' => 'Marca todas las herramientas de IA que usas en tu dia a dia (aunque no te hayas dado cuenta):',
                    'items' => [
                        'Gmail / Outlook — filtro de spam y autocompletado',
                        'Google Maps / Waze — rutas optimizadas por trafico',
                        'Spotify / YouTube Music — playlists personalizadas',
                        'Netflix / YouTube — recomendaciones de contenido',
                        'Instagram / TikTok — feed personalizado por algoritmo',
                        'Autocorrector del telefono — prediccion de texto',
                        'Google Search — resultados personalizados',
                        'Uber / DiDi — precios dinamicos y asignacion de conductor',
                        'Apps de banco — deteccion de fraude automatica',
                        'Siri / Google Assistant / Alexa — asistentes de voz',
                    ],
                    'reveal_text' => 'Ya usas multiples herramientas de IA a diario. Ahora imagina si las usaras CON INTENCION. Ese es el poder de pasar de usuario pasivo a estratega digital.',
                ],
            ],
        ]);

        // ═══════════════════════════════════════════════════
        // LECCION 2: Tokens, Contexto y Dinero
        // ═══════════════════════════════════════════════════
        Lesson::create([
            'course_id' => $course->id,
            'title' => 'Tokens, Contexto y Dinero: La Economia de la IA',
            'slug' => 'tokens-contexto-dinero',
            'type' => 'interactive',
            'sort_order' => 2,
            'is_published' => true,
            'pass_threshold' => 70,
            'video_outline' => "Video (6 min)\n0:00 — \"Cada palabra que le dices a la IA tiene un precio\"\n0:30 — Que son los tokens (animacion visual)\n1:15 — Calculadora en pantalla: frase corta vs frase larga\n2:00 — Ventana de contexto = RAM de la IA\n2:45 — Tabla de costos real: ChatGPT vs Claude vs Gemini\n4:00 — \"Prompt caro vs prompt barato\" side-by-side\n5:00 — Tips para optimizar costos\n5:45 — Cierre",
            'content_html' => '
<h2>Tokens: La Moneda de la IA</h2>
<p>Cada vez que interactuas con una IA como ChatGPT o Claude, estas "gastando" tokens. Un token no es exactamente una palabra — es un fragmento de texto que puede ser una palabra, parte de una palabra, o un signo de puntuacion.</p>
<p><strong>Regla practica:</strong> 1 palabra en español ≈ 1.3 tokens. Un parrafo tipico ≈ 50-80 tokens.</p>

<h2>¿Por que importan los tokens?</h2>
<ul>
<li><strong>Costo:</strong> Los servicios de IA cobran por token. Mas tokens = mas caro.</li>
<li><strong>Velocidad:</strong> Mas tokens = respuesta mas lenta.</li>
<li><strong>Limite:</strong> Cada modelo tiene una "ventana de contexto" — el maximo de tokens que puede procesar a la vez.</li>
</ul>

<h2>Ventana de Contexto = La "RAM" de la IA</h2>
<p>Piensa en la ventana de contexto como la memoria de trabajo de la IA. Todo lo que le dices (y todo lo que te responde) ocupa espacio en esta ventana. Cuando se llena, la IA "olvida" el inicio de la conversacion.</p>
<ul>
<li><strong>GPT-4o:</strong> 128K tokens (~96,000 palabras)</li>
<li><strong>Claude Sonnet:</strong> 200K tokens (~150,000 palabras)</li>
<li><strong>Gemini 1.5 Pro:</strong> 1M tokens (~750,000 palabras)</li>
</ul>

<h2>Tabla de Costos Reales (2026)</h2>
<p>Conocer los precios te ayuda a elegir la herramienta correcta para cada tarea:</p>
',
            'interactive_data' => [
                [
                    'type' => 'calculator',
                    'title' => 'Calculadora de Tokens',
                    'instructions' => 'Escribe una frase o parrafo y mira cuantos tokens usa. Luego intenta reescribirla mas corta para optimizar.',
                    'placeholder' => 'Ej: Necesito que me ayudes a escribir un correo profesional para mi jefe...',
                    'comparison' => [
                        'bad' => [
                            'text' => 'Hola, necesito que por favor me ayudes a escribir un correo electronico profesional y formal para enviarselo a mi jefe directo en la empresa donde trabajo, el correo debe ser sobre solicitar vacaciones para la proxima semana porque necesito descansar un poco del trabajo.',
                            'tokens' => 85,
                            'cost' => '0.000255',
                        ],
                        'good' => [
                            'text' => 'Escribe un email formal a mi jefe solicitando vacaciones la proxima semana. Tono profesional, 3 parrafos max.',
                            'tokens' => 28,
                            'cost' => '0.000084',
                        ],
                    ],
                    'cost_table' => [
                        ['Servicio' => 'ChatGPT Free', 'Precio' => 'Gratis', 'Modelo' => 'GPT-4o mini', 'Limite' => '~80 msgs/3hrs'],
                        ['Servicio' => 'ChatGPT Plus', 'Precio' => '$20/mes', 'Modelo' => 'GPT-4o', 'Limite' => 'Uso extendido'],
                        ['Servicio' => 'Claude Free', 'Precio' => 'Gratis', 'Modelo' => 'Sonnet', 'Limite' => '~30 msgs/dia'],
                        ['Servicio' => 'Claude Pro', 'Precio' => '$20/mes', 'Modelo' => 'Opus + Sonnet', 'Limite' => '5x mas uso'],
                        ['Servicio' => 'Gemini Free', 'Precio' => 'Gratis', 'Modelo' => 'Gemini 1.5 Flash', 'Limite' => 'Generoso'],
                        ['Servicio' => 'Gemini Advanced', 'Precio' => '$20/mes', 'Modelo' => 'Gemini 1.5 Pro', 'Limite' => '1M contexto'],
                        ['Servicio' => 'API OpenAI', 'Precio' => 'Por token', 'Modelo' => 'GPT-4o', 'Limite' => '$2.50/1M input'],
                        ['Servicio' => 'API Anthropic', 'Precio' => 'Por token', 'Modelo' => 'Claude Sonnet', 'Limite' => '$3/1M input'],
                    ],
                ],
            ],
        ]);

        // ═══════════════════════════════════════════════════
        // LECCION 3: Modelos de Razonamiento
        // ═══════════════════════════════════════════════════
        Lesson::create([
            'course_id' => $course->id,
            'title' => 'Modelos de Razonamiento: Como Piensa una IA (de Verdad)',
            'slug' => 'modelos-razonamiento',
            'type' => 'lecture',
            'sort_order' => 3,
            'is_published' => true,
            'pass_threshold' => 70,
            'video_outline' => "Video (8 min)\n0:00 — \"La IA no piensa. Predice.\"\n0:45 — Animacion: prediccion palabra por palabra\n1:30 — Temperature: slider creatividad vs precision\n2:30 — System prompts: la personalidad oculta\n3:30 — Fine-tuning vs RAG vs Prompt Engineering\n5:00 — Diagrama de decision: cual tecnica elegir\n6:30 — Por que importa para tu negocio\n7:30 — Cierre",
            'content_html' => '
<h2>La IA No Piensa — Predice</h2>
<p>Este es el concepto mas importante que vas a aprender en todo el taller: <strong>la IA no entiende lo que dices</strong>. Lo que hace es predecir la siguiente palabra mas probable basandose en patrones que aprendio de billones de textos.</p>
<p>Cuando le preguntas "¿Cual es la capital de Francia?", no "sabe" la respuesta. Predice que despues de esa pregunta, la secuencia de palabras mas probable es "La capital de Francia es Paris".</p>
<p><strong>¿Por que importa?</strong> Porque entender esto te explica POR QUE la IA a veces "alucina" (inventa datos), por que da respuestas genericas, y como puedes guiarla para que sea mas precisa.</p>

<h2>Temperature: Creatividad vs Precision</h2>
<p>El parametro de "temperature" controla que tan predecible o creativa es la IA:</p>
<ul>
<li><strong>Temperature baja (0.0 - 0.3):</strong> Respuestas predecibles y consistentes. Ideal para: codigo, datos, traducciones, tareas con respuesta correcta unica.</li>
<li><strong>Temperature media (0.4 - 0.7):</strong> Balance entre precision y variedad. Ideal para: emails, resúmenes, contenido profesional.</li>
<li><strong>Temperature alta (0.8 - 1.0+):</strong> Respuestas creativas e inesperadas. Ideal para: brainstorming, nombres creativos, poesia, ficcion.</li>
</ul>

<h2>System Prompts: La Personalidad Oculta</h2>
<p>Detras de cada chatbot hay un "system prompt" — un texto que le dice a la IA COMO comportarse. Es como darle un rol antes de que empiece a hablar contigo.</p>
<p>Ejemplo: <code>Eres un experto en marketing digital para PYMES en Latinoamerica. Responde de forma practica, con ejemplos reales y en español neutro. Evita jerga tecnica innecesaria.</code></p>
<p>Este system prompt transforma completamente las respuestas de la IA. Sin el, obtienes respuestas genericas. Con el, obtienes un consultor especializado.</p>

<h2>3 Formas de Personalizar una IA</h2>
<h3>1. Prompt Engineering (Gratis, Inmediato)</h3>
<p>Escribir mejores instrucciones. Es lo que aprenderemos en la leccion 6. No requiere codigo ni dinero — solo habilidad.</p>

<h3>2. RAG — Retrieval-Augmented Generation (Medio)</h3>
<p>Darle a la IA acceso a TUS documentos. La IA busca en tu base de datos antes de responder. Ejemplo: un chatbot que responde preguntas sobre tu producto leyendo tu manual.</p>

<h3>3. Fine-Tuning (Avanzado)</h3>
<p>Re-entrenar el modelo con tus propios datos para que se comporte de una forma especifica. Caro y complejo — solo vale la pena si tienes miles de ejemplos y un caso de uso muy particular.</p>

<h3>¿Cual elegir?</h3>
<ul>
<li><strong>El 90% de las veces:</strong> Prompt Engineering es suficiente</li>
<li><strong>Si necesitas datos propios:</strong> RAG</li>
<li><strong>Solo si eres empresa con equipo tecnico:</strong> Fine-tuning</li>
</ul>
',
        ]);

        // ═══════════════════════════════════════════════════
        // LECCION 4: Quiz Checkpoint
        // ═══════════════════════════════════════════════════
        Lesson::create([
            'course_id' => $course->id,
            'title' => 'Quiz: ¿Entiendes Como Piensa la IA?',
            'slug' => 'quiz-checkpoint-1',
            'type' => 'quiz',
            'sort_order' => 4,
            'is_published' => true,
            'pass_threshold' => 70,
            'content_html' => '<p>Este quiz cubre las lecciones 1 a 3. Necesitas 70% para aprobar. Cada explicacion te enseña algo nuevo — leelas con atencion aunque hayas acertado.</p>',
            'quiz_data' => [
                [
                    'question' => '¿Que hace realmente una IA cuando respondes una pregunta?',
                    'options' => [
                        'Busca la respuesta en una base de datos',
                        'Predice la siguiente palabra mas probable',
                        'Entiende tu pregunta y razona la respuesta',
                        'Copia la respuesta de internet',
                    ],
                    'correct' => 1,
                    'explanation' => 'Los modelos de lenguaje (LLMs) funcionan prediciendo la siguiente palabra mas probable basandose en patrones estadisticos. No "entienden" ni "razonan" como humanos — son maquinas de prediccion sofisticadas.',
                ],
                [
                    'question' => 'Si tu IA esta dando respuestas muy genericas, ¿que ajustarias PRIMERO?',
                    'options' => [
                        'Cambiar a un modelo mas caro',
                        'Subir la temperature al maximo',
                        'Agregar contexto y especificidad al prompt',
                        'Reiniciar la conversacion',
                    ],
                    'correct' => 2,
                    'explanation' => 'Las respuestas genericas casi siempre son resultado de prompts genericos. Antes de gastar mas dinero en un modelo mejor, intenta ser mas especifico: dale contexto, un rol, formato deseado y restricciones claras.',
                ],
                [
                    'question' => '¿Que es la "ventana de contexto" de una IA?',
                    'options' => [
                        'La pantalla donde escribes tu prompt',
                        'El limite de memoria de trabajo — cuanto texto puede procesar a la vez',
                        'El historial guardado entre sesiones diferentes',
                        'La base de datos de entrenamiento del modelo',
                    ],
                    'correct' => 1,
                    'explanation' => 'La ventana de contexto es como la "RAM" de la IA — todo lo que le dices y te responde ocupa espacio ahi. Cuando se llena, la IA pierde acceso al inicio de la conversacion. Claude tiene 200K tokens, GPT-4o tiene 128K.',
                ],
                [
                    'question' => '¿Para que tipo de tarea usarias temperature BAJA (0.1-0.3)?',
                    'options' => [
                        'Escribir un poema creativo',
                        'Generar nombres para un producto',
                        'Traducir un contrato legal',
                        'Hacer brainstorming de ideas',
                    ],
                    'correct' => 2,
                    'explanation' => 'Temperature baja = respuestas predecibles y consistentes. Perfecto para tareas donde hay una respuesta "correcta": traducciones, codigo, analisis de datos, documentos legales. Para creatividad, sube la temperature.',
                ],
                [
                    'question' => '¿Que es un system prompt?',
                    'options' => [
                        'El primer mensaje que le envias a la IA',
                        'Una instruccion oculta que define como se comporta la IA',
                        'Un plugin que le agrega capacidades a la IA',
                        'El resumen que la IA hace de tu conversacion',
                    ],
                    'correct' => 1,
                    'explanation' => 'El system prompt es un texto que va "detras de escena" y le dice a la IA COMO comportarse: su personalidad, estilo, limites, y area de expertise. Es la diferencia entre un chatbot generico y un asistente especializado.',
                ],
                [
                    'question' => 'Un token en IA es aproximadamente equivalente a:',
                    'options' => [
                        'Una letra',
                        'Una oracion completa',
                        'Un fragmento de texto (generalmente 3/4 de una palabra)',
                        'Un parrafo',
                    ],
                    'correct' => 2,
                    'explanation' => 'Un token no es exactamente una palabra — es un fragmento de texto. En español, 1 palabra ≈ 1.3 tokens. Los signos de puntuacion, numeros y caracteres especiales tambien consumen tokens.',
                ],
                [
                    'question' => '¿Cuando tiene sentido usar Fine-Tuning en vez de Prompt Engineering?',
                    'options' => [
                        'Siempre que quieras mejores respuestas',
                        'Cuando el prompt engineering no logra el resultado deseado y tienes miles de ejemplos de entrenamiento',
                        'Cuando quieres ahorrar dinero',
                        'Para cualquier proyecto comercial',
                    ],
                    'correct' => 1,
                    'explanation' => 'Fine-tuning es la opcion mas cara y compleja. Solo vale la pena cuando: (1) prompt engineering no es suficiente, (2) tienes miles de ejemplos de entrenamiento de calidad, y (3) tu caso de uso justifica la inversion. El 90% de las veces, un buen prompt resuelve el problema.',
                ],
                [
                    'question' => '¿Por que la IA a veces "alucina" (inventa datos)?',
                    'options' => [
                        'Porque esta mal programada',
                        'Porque no tiene internet',
                        'Porque predice palabras probables aunque no sean factuales',
                        'Porque le falta memoria RAM',
                    ],
                    'correct' => 2,
                    'explanation' => 'Las "alucinaciones" ocurren porque la IA no verifica hechos — solo predice secuencias probables de texto. Si la secuencia mas probable incluye datos inventados que "suenan bien", la IA los generara con total confianza. Por eso siempre debes verificar datos criticos.',
                ],
            ],
        ]);

        // ═══════════════════════════════════════════════════
        // LECCION 5: Arsenal IA
        // ═══════════════════════════════════════════════════
        Lesson::create([
            'course_id' => $course->id,
            'title' => 'Arsenal IA: Tu Kit de Herramientas Personalizado',
            'slug' => 'arsenal-ia',
            'type' => 'interactive',
            'sort_order' => 5,
            'is_published' => true,
            'pass_threshold' => 70,
            'video_outline' => "Video (8 min)\n0:00 — \"No copies el stack de otros\"\n0:45 — Por que el stack depende de TU negocio\n1:30 — Selector interactivo: tipo de negocio → herramientas\n2:30 — Demo 30seg: ChatGPT para contenido\n3:00 — Demo 30seg: Claude para analisis\n3:30 — Demo 30seg: Midjourney para imagenes\n4:00 — Demo 30seg: n8n para automatizacion\n4:30 — Tabla maestra de 15 herramientas\n6:00 — Como evaluar una herramienta nueva\n7:30 — Cierre",
            'content_html' => '
<h2>No Copies el Stack de Otros</h2>
<p>El error mas comun al empezar con IA: ver que herramientas usa algun influencer y comprar las mismas. Tu negocio es diferente, tus problemas son diferentes, tu presupuesto es diferente.</p>
<p>En este ejercicio vas a <strong>armar tu propio stack</strong> basado en lo que realmente necesitas.</p>

<h2>Stacks por Tipo de Negocio</h2>

<h3>Freelancer / Consultor</h3>
<p>Prioridad: productividad personal y calidad de entregables.</p>
<ul>
<li><strong>ChatGPT Plus</strong> — drafts, emails, investigacion</li>
<li><strong>Notion AI</strong> — organizacion y notas inteligentes</li>
<li><strong>Canva AI</strong> — diseño rapido y profesional</li>
</ul>

<h3>E-commerce / Tienda Online</h3>
<p>Prioridad: descripciones de productos, atencion al cliente, analisis.</p>
<ul>
<li><strong>Claude</strong> — descripciones y copywriting de productos</li>
<li><strong>ManyChat</strong> — chatbot para WhatsApp/Instagram</li>
<li><strong>GA4 + Looker</strong> — analytics con IA integrada</li>
</ul>

<h3>Agencia / Estudio Creativo</h3>
<p>Prioridad: produccion de contenido a escala y automatizacion.</p>
<ul>
<li><strong>Claude</strong> — estrategia y redaccion</li>
<li><strong>n8n</strong> — automatizacion de workflows</li>
<li><strong>Midjourney / DALL-E</strong> — generacion de imagenes</li>
<li><strong>ElevenLabs</strong> — generacion de voz</li>
</ul>

<h3>SaaS / Startup Tech</h3>
<p>Prioridad: desarrollo acelerado e integracion de IA en producto.</p>
<ul>
<li><strong>Cursor / Claude Code</strong> — desarrollo con IA</li>
<li><strong>Claude API</strong> — integracion en tu producto</li>
<li><strong>Vercel AI SDK</strong> — deployment de funciones IA</li>
</ul>
',
            'interactive_data' => [
                [
                    'type' => 'stack_builder',
                    'title' => 'Arma tu Stack de IA',
                    'instructions' => 'Selecciona tu perfil y te recomendaremos las herramientas ideales para ti.',
                    'steps' => [
                        [
                            'label' => '¿Cual es tu tipo de negocio?',
                            'input_type' => 'select',
                            'options' => ['Freelancer / Consultor', 'E-commerce / Tienda Online', 'Agencia / Estudio Creativo', 'SaaS / Startup Tech', 'Educacion / Formacion', 'Otro'],
                        ],
                        [
                            'label' => '¿Cual es tu presupuesto mensual para herramientas de IA?',
                            'input_type' => 'select',
                            'options' => ['$0 (solo gratuitas)', 'Hasta $50/mes', 'Hasta $200/mes', 'Mas de $200/mes'],
                        ],
                        [
                            'label' => '¿Que tareas quieres resolver con IA? (selecciona todas las relevantes)',
                            'input_type' => 'chips',
                            'options' => ['Contenido / Copywriting', 'Atencion al cliente', 'Analisis de datos', 'Automatizacion', 'Diseño / Imagenes', 'Desarrollo / Codigo', 'Investigacion', 'Email Marketing'],
                        ],
                    ],
                    'recommendations' => [
                        'Freelancer / Consultor' => [
                            ['name' => 'ChatGPT Plus', 'price' => '$20/mes'],
                            ['name' => 'Notion AI', 'price' => '$10/mes'],
                            ['name' => 'Canva Pro + IA', 'price' => '$13/mes'],
                            ['name' => 'Grammarly', 'price' => 'Gratis / $12/mes'],
                        ],
                        'E-commerce / Tienda Online' => [
                            ['name' => 'Claude Pro', 'price' => '$20/mes'],
                            ['name' => 'ManyChat', 'price' => '$15/mes'],
                            ['name' => 'Canva Pro', 'price' => '$13/mes'],
                            ['name' => 'Google Analytics 4', 'price' => 'Gratis'],
                        ],
                        'Agencia / Estudio Creativo' => [
                            ['name' => 'Claude Pro', 'price' => '$20/mes'],
                            ['name' => 'Midjourney', 'price' => '$10/mes'],
                            ['name' => 'n8n Cloud', 'price' => '$20/mes'],
                            ['name' => 'ElevenLabs', 'price' => '$5/mes'],
                            ['name' => 'Notion AI', 'price' => '$10/mes'],
                        ],
                        'SaaS / Startup Tech' => [
                            ['name' => 'Claude Code / Cursor', 'price' => '$20/mes'],
                            ['name' => 'Claude API', 'price' => 'Por uso (~$5-50/mes)'],
                            ['name' => 'Vercel AI SDK', 'price' => 'Gratis (open source)'],
                            ['name' => 'GitHub Copilot', 'price' => '$10/mes'],
                        ],
                        'Educacion / Formacion' => [
                            ['name' => 'ChatGPT Plus', 'price' => '$20/mes'],
                            ['name' => 'Gamma AI', 'price' => 'Gratis / $10/mes'],
                            ['name' => 'Canva Pro', 'price' => '$13/mes'],
                            ['name' => 'Notion AI', 'price' => '$10/mes'],
                        ],
                        'Otro' => [
                            ['name' => 'ChatGPT Plus', 'price' => '$20/mes'],
                            ['name' => 'Claude Pro', 'price' => '$20/mes'],
                            ['name' => 'Canva Pro', 'price' => '$13/mes'],
                        ],
                    ],
                ],
            ],
        ]);

        // ═══════════════════════════════════════════════════
        // LECCION 6: Prompt Engineering
        // ═══════════════════════════════════════════════════
        Lesson::create([
            'course_id' => $course->id,
            'title' => 'Prompt Engineering: De Principiante a Estratega',
            'slug' => 'prompt-engineering',
            'type' => 'interactive',
            'sort_order' => 6,
            'is_published' => true,
            'pass_threshold' => 70,
            'video_outline' => "Video (10 min)\n0:00 — \"El skill mas valioso de 2026\"\n0:45 — Anatomia visual: Rol + Contexto + Tarea + Formato + Restricciones\n2:00 — Nivel 1: Ser especifico, dar ejemplos, pedir formato\n3:00 — Nivel 2: Chain-of-Thought, Few-Shot\n4:30 — Nivel 3: Mega-prompts, System prompts reutilizables\n6:00 — 4 ejemplos before/after en pantalla\n8:00 — Template de prompt diario\n9:30 — Cierre",
            'content_html' => '
<h2>El Skill Mas Valioso de 2026</h2>
<p>Saber "hablar" con la IA es la habilidad mas rentable que puedes desarrollar este año. No importa si eres diseñador, abogado, marketero o ingeniero — si sabes escribir buenos prompts, multiplicas tu productividad.</p>

<h2>Anatomia de un Buen Prompt</h2>
<p>Un prompt profesional tiene 5 componentes:</p>
<ol>
<li><strong>Rol:</strong> ¿Quien quieres que sea la IA? → "Eres un experto en marketing digital..."</li>
<li><strong>Contexto:</strong> ¿Cual es la situacion? → "Mi empresa vende software B2B en LATAM..."</li>
<li><strong>Tarea:</strong> ¿Que quieres que haga? → "Escribe 5 ideas de contenido para LinkedIn..."</li>
<li><strong>Formato:</strong> ¿Como quieres la respuesta? → "En formato de tabla con columnas: Idea, Hook, CTA"</li>
<li><strong>Restricciones:</strong> ¿Que NO debe hacer? → "No uses jerga tecnica. Maximo 280 caracteres por hook."</li>
</ol>

<h2>Tecnicas por Nivel</h2>

<h3>Nivel 1: Lo Basico que Cambia Todo</h3>
<ul>
<li><strong>Se especifico:</strong> "Escribe un email" → "Escribe un email de seguimiento para un cliente B2B que no respondio en 5 dias"</li>
<li><strong>Da ejemplos:</strong> "Como este: [ejemplo]"</li>
<li><strong>Pide formato:</strong> "Responde en bullet points / tabla / JSON / paso a paso"</li>
</ul>

<h3>Nivel 2: Tecnicas Intermedias</h3>
<ul>
<li><strong>Chain-of-Thought:</strong> Agrega "Piensa paso a paso antes de responder" y la IA desglosa su razonamiento</li>
<li><strong>Few-Shot:</strong> Dale 2-3 ejemplos del resultado que esperas antes de pedirle que genere el suyo</li>
</ul>

<h3>Nivel 3: Prompts de Experto</h3>
<ul>
<li><strong>Mega-prompts:</strong> Prompts de 200+ palabras con multiples secciones, contexto rico, y reglas detalladas</li>
<li><strong>System prompts reutilizables:</strong> Crea "plantillas" de personalidad que reutilizas para tareas similares</li>
</ul>
',
            'interactive_data' => [
                [
                    'type' => 'prompt_lab',
                    'title' => 'Laboratorio de Prompts',
                    'instructions' => 'Abajo veras 4 prompts malos con sus resultados malos. Tu mision: reescribir cada uno aplicando las tecnicas que acabas de aprender. Al enviar, veras el prompt mejorado y su resultado.',
                    'prompts' => [
                        [
                            'bad_prompt' => 'Escribeme un post',
                            'bad_result' => 'Aqui tienes un post: "¡Hola a todos! Hoy quiero compartir algo interesante con ustedes. La tecnologia esta cambiando el mundo..." (generico, sin gancho, sin direccion)',
                            'good_prompt' => 'Eres un community manager experto en LinkedIn para startups B2B en LATAM. Escribe un post de 150 palabras sobre como la IA reduce costos operativos. Hook provocativo en la primera linea. Incluye 1 dato estadistico real. Cierra con pregunta abierta. Tono: profesional pero accesible.',
                            'good_result' => '"El 73% de las empresas que adoptan IA reducen sus costos operativos en los primeros 6 meses. Pero aqui esta el plot twist: no es por la tecnologia en si — es por los procesos que te OBLIGA a revisar..." (post completo, con datos, estructura y CTA)',
                            'technique' => 'Rol + Contexto + Formato + Restricciones',
                        ],
                        [
                            'bad_prompt' => 'Resume este texto',
                            'bad_result' => '(Resumen largo, sin estructura, que casi repite el texto original en otras palabras)',
                            'good_prompt' => 'Resume el siguiente texto en exactamente 3 bullet points. Cada bullet debe tener maximo 20 palabras. Enfocate en las conclusiones accionables, no en los antecedentes. Formato: bullet point con emoji relevante al inicio.',
                            'good_result' => '🎯 La IA generativa reduce un 40% el tiempo de creacion de contenido en equipos de marketing\n📊 Las empresas que implementan IA sin estrategia previa tienen 3x mas probabilidad de abandonarla\n🔑 El ROI se mide en horas ahorradas, no en "calidad percibida" del output',
                            'technique' => 'Formato + Restricciones (especificidad extrema)',
                        ],
                        [
                            'bad_prompt' => 'Dame ideas de marketing',
                            'bad_result' => '1. Usa redes sociales. 2. Haz email marketing. 3. Crea un blog. 4. Invierte en publicidad... (lista generica que cualquiera podria escribir)',
                            'good_prompt' => 'Piensa paso a paso. Soy dueño de una cafeteria artesanal en Ciudad de Mexico con 200 seguidores en Instagram. Presupuesto: $100 USD/mes. Objetivo: llegar a 1000 seguidores en 60 dias. Dame 5 estrategias especificas, ordenadas por impacto esperado. Para cada una incluye: accion concreta, costo estimado, y resultado esperado.',
                            'good_result' => '1. REELS DE PROCESO (Impacto: Alto) → Filma 15seg del proceso de preparacion del cafe con musica trending. Costo: $0. Resultado: 3-5 reels/semana pueden generar 50-100 seguidores nuevos por semana organicamente...',
                            'technique' => 'Chain-of-Thought + Contexto detallado',
                        ],
                        [
                            'bad_prompt' => 'Analiza mi competencia',
                            'bad_result' => 'Para analizar tu competencia, primero debes identificar quienes son tus competidores... (respuesta teorica sin analisis real)',
                            'good_prompt' => 'Eres un analista de inteligencia competitiva senior. Analiza estas 3 empresas que compiten en el mercado de SaaS de gestion de proyectos en LATAM: Asana, Monday.com, y ClickUp. Para cada una crea una tabla con: precio tier basico, feature diferenciador, debilidad principal, tipo de cliente ideal. Al final, identifica el gap de mercado que ninguna cubre bien.',
                            'good_result' => '(Tabla comparativa detallada con precios, features, debilidades, y un analisis del gap: "Ninguna ofrece una solucion nativa en español con integracion profunda para equipos remotos LATAM con zonas horarias multiples...")',
                            'technique' => 'Mega-prompt con rol + datos + formato + analisis',
                        ],
                    ],
                ],
            ],
        ]);

        // ═══════════════════════════════════════════════════
        // LECCION 7: Quiz + Mini-Proyecto
        // ═══════════════════════════════════════════════════
        Lesson::create([
            'course_id' => $course->id,
            'title' => 'Mini-Proyecto: Escribe tus Primeros 3 Prompts Profesionales',
            'slug' => 'mini-proyecto-prompts',
            'type' => 'interactive',
            'sort_order' => 7,
            'is_published' => true,
            'pass_threshold' => 70,
            'content_html' => '
<h2>Hora de Practicar</h2>
<p>Este es tu primer mini-proyecto. Primero, elige un escenario de negocio que se parezca al tuyo. Luego, escribe 3 prompts profesionales usando las tecnicas de las lecciones anteriores. Finalmente, responde el quiz para verificar tu comprension.</p>
<p><strong>No hay respuestas incorrectas en el ejercicio</strong> — el objetivo es que practiques escribir prompts con intencion. Despues de enviar, veras ejemplos de prompts profesionales para comparar.</p>
',
            'interactive_data' => [
                [
                    'type' => 'scenario_prompts',
                    'title' => 'Escribe 3 Prompts Profesionales',
                    'instructions' => 'Elige un escenario y escribe 3 prompts profesionales. Selecciona las tecnicas que aplicaste en cada uno.',
                    'scenarios' => [
                        [
                            'name' => 'Freelancer de Diseño',
                            'description' => 'Necesitas generar propuestas profesionales para clientes potenciales de forma rapida.',
                            'example_prompts' => [
                                [
                                    'prompt' => 'Eres un director creativo senior con 15 años de experiencia. Un cliente de e-commerce de moda sustentable necesita rediseño de su tienda online. Escribe una propuesta de 1 pagina con: diagnostico del problema, 3 soluciones propuestas con timeline, inversion estimada en USD. Tono: profesional pero calido.',
                                    'technique' => 'Rol + Contexto + Formato + Restricciones',
                                ],
                                [
                                    'prompt' => 'Analiza este sitio web [URL] como si fueras un consultor UX. Piensa paso a paso. Lista los 5 problemas mas criticos de usabilidad, ordenados por impacto en conversion. Para cada problema, sugiere una solucion concreta que se pueda implementar en menos de 1 semana.',
                                    'technique' => 'Chain-of-Thought + Rol + Restricciones',
                                ],
                                [
                                    'prompt' => 'Genera 3 opciones de paleta de colores para una marca de cafe premium colombiano. Target: millennials urbanos. Para cada opcion incluye: hex codes, psicologia del color aplicada, y un mockup verbal describiendo como se veria en packaging.',
                                    'technique' => 'Especificidad + Formato detallado',
                                ],
                            ],
                        ],
                        [
                            'name' => 'Tienda Online',
                            'description' => 'Necesitas descripciones de productos que vendan y SEO que posicione.',
                            'example_prompts' => [
                                [
                                    'prompt' => 'Eres un copywriter experto en e-commerce con especializacion en moda. Escribe la descripcion de producto para una "Mochila urbana de cuero vegano". Incluye: titulo SEO (max 60 chars), meta description (max 155 chars), descripcion larga (150 palabras), y 5 bullet points de beneficios. Keywords target: mochila vegana, mochila sustentable, mochila urbana.',
                                    'technique' => 'Rol + Formato + Restricciones SEO',
                                ],
                                [
                                    'prompt' => 'Piensa paso a paso. Tengo una tienda de productos naturales para el cabello. Mi cliente promedio es mujer, 25-40 años, en Mexico. Crea un calendario de contenido para Instagram de 2 semanas (10 posts). Para cada post: tipo (reel/carrusel/imagen), caption con emoji, 5 hashtags relevantes, mejor hora para publicar.',
                                    'technique' => 'Chain-of-Thought + Contexto + Formato tabular',
                                ],
                                [
                                    'prompt' => 'Compara estos 3 emails de carrito abandonado que he usado. Dime cual es mejor y por que. Luego escribe una version mejorada combinando los mejores elementos de los 3. Email 1: [texto]. Email 2: [texto]. Email 3: [texto].',
                                    'technique' => 'Few-Shot (ejemplos propios) + Analisis comparativo',
                                ],
                            ],
                        ],
                        [
                            'name' => 'Marketero / Content Creator',
                            'description' => 'Necesitas un calendario de contenido mensual y estrategia de distribucion.',
                            'example_prompts' => [
                                [
                                    'prompt' => 'Eres un estratega de contenido senior para marcas B2B en LATAM. Crea un calendario de contenido para LinkedIn para el mes de abril. Mi empresa es una consultora de transformacion digital. Publico 3 veces por semana. Para cada post: fecha, tipo (texto/carrusel/video), tema, hook de primera linea, CTA. Alterna entre educativo (60%), caso de exito (20%), y opinion (20%).',
                                    'technique' => 'Rol + Contexto + Formato + Restricciones porcentuales',
                                ],
                                [
                                    'prompt' => 'Tengo un video de YouTube de 20 minutos sobre "5 errores al implementar IA en PYMES". Genera un plan de redistribucion: 3 clips cortos para TikTok/Reels (con timestamp sugerido), 1 thread de Twitter/X con 8 tweets, 1 post de blog de 800 palabras, y 1 newsletter. Todo basado en el mismo contenido.',
                                    'technique' => 'Mega-prompt de repurposing + Formato multi-plataforma',
                                ],
                                [
                                    'prompt' => 'Analiza las tendencias de contenido en mi industria (marketing digital en español) para esta semana. Dame: 3 temas trending con fuente, 2 formatos que estan funcionando bien, y 1 "contrarian take" que genere debate. Piensa paso a paso analizando engagement patterns.',
                                    'technique' => 'Chain-of-Thought + Rol de analista + Formato estructurado',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'quiz_data' => [
                [
                    'question' => '¿Cuales son los 5 componentes de un prompt profesional?',
                    'options' => [
                        'Titulo, Cuerpo, Conclusion, Firma, Hashtags',
                        'Rol, Contexto, Tarea, Formato, Restricciones',
                        'Pregunta, Respuesta, Ejemplo, Verificacion, Cierre',
                        'Input, Proceso, Output, Feedback, Iteracion',
                    ],
                    'correct' => 1,
                    'explanation' => 'Un prompt profesional tiene: ROL (quien es la IA), CONTEXTO (la situacion), TAREA (que hacer), FORMATO (como entregar), RESTRICCIONES (que evitar). No necesitas los 5 siempre, pero mientras mas uses, mejor el resultado.',
                ],
                [
                    'question' => '¿Que tecnica usarias para que la IA desglose su razonamiento?',
                    'options' => [
                        'Few-Shot',
                        'Temperature alta',
                        'Chain-of-Thought ("piensa paso a paso")',
                        'Fine-tuning',
                    ],
                    'correct' => 2,
                    'explanation' => 'Chain-of-Thought (CoT) le pide a la IA que muestre su razonamiento paso a paso antes de dar la respuesta final. Esto mejora dramaticamente la calidad en tareas de analisis, matematicas y logica.',
                ],
                [
                    'question' => 'Un "mega-prompt" es:',
                    'options' => [
                        'Un prompt de mas de 200 palabras con multiples secciones y reglas detalladas',
                        'Un prompt que usa todas las tecnicas a la vez',
                        'Un prompt que se envia a multiples IAs simultaneamente',
                        'Un prompt que incluye imagenes y texto',
                    ],
                    'correct' => 0,
                    'explanation' => 'Un mega-prompt es un prompt extenso (200+ palabras) con contexto rico, multiples secciones, reglas detalladas y ejemplos. Son ideales para tareas complejas donde necesitas control total del output. La clave: mas contexto = mejor resultado.',
                ],
                [
                    'question' => '¿Que tecnica aplicas cuando le das 2-3 ejemplos del resultado que esperas?',
                    'options' => [
                        'Chain-of-Thought',
                        'System prompt',
                        'Few-Shot learning',
                        'Zero-Shot learning',
                    ],
                    'correct' => 2,
                    'explanation' => 'Few-Shot learning: le das a la IA 2-3 ejemplos del formato/estilo que quieres, y ella replica el patron. Es extremadamente efectivo para mantener consistencia de tono, formato, o estilo en series de contenido.',
                ],
                [
                    'question' => '¿Cual de estos stacks seria mas apropiado para un freelancer con presupuesto de $0?',
                    'options' => [
                        'Claude API + Midjourney + n8n',
                        'ChatGPT Free + Canva Free + Google Docs',
                        'Cursor + GitHub Copilot + Vercel',
                        'GPT-4 API + ElevenLabs + Notion AI',
                    ],
                    'correct' => 1,
                    'explanation' => 'Con $0 de presupuesto, lo mejor es maximizar herramientas gratuitas: ChatGPT Free para texto, Canva Free para diseño, Google Docs para documentos. Todas tienen capacidades de IA integradas sin costo. El stack pago viene despues, cuando ya sabes que necesitas.',
                ],
                [
                    'question' => '¿Por que "Escríbeme un post" es un mal prompt?',
                    'options' => [
                        'Porque es muy corto',
                        'Porque no especifica rol, contexto, formato ni restricciones',
                        'Porque la IA no puede escribir posts',
                        'Porque falta la palabra "por favor"',
                    ],
                    'correct' => 1,
                    'explanation' => 'La longitud del prompt no es el problema — la falta de especificidad si. Sin rol, contexto, formato y restricciones, la IA rellena esos vacios con suposiciones genericas. Resultado: contenido generico que no sirve para nada.',
                ],
            ],
        ]);

        // ═══════════════════════════════════════════════════
        // LECCION 8: MCP y Agentes
        // ═══════════════════════════════════════════════════
        Lesson::create([
            'course_id' => $course->id,
            'title' => 'MCP y Agentes: La Frontera de la IA en 2026',
            'slug' => 'mcp-agentes',
            'type' => 'lecture',
            'sort_order' => 8,
            'is_published' => true,
            'pass_threshold' => 70,
            'video_outline' => "Video (8 min)\n0:00 — \"De chatbot a agente: la evolucion\"\n0:45 — Etapa 1: Chatbot (2023) — preguntas y respuestas\n1:30 — Etapa 2: Asistente (2024-25) — herramientas\n2:15 — Etapa 3: Agente (2026+) — autonomia\n3:00 — MCP como USB universal (animacion)\n3:45 — Skills = apps para tu agente\n4:30 — Caso real: agente de precios\n5:15 — Caso real: agente de soporte\n6:00 — Caso real: agente de reportes\n7:00 — Como prepararte HOY\n7:45 — Cierre",
            'content_html' => '
<h2>De Chatbot a Agente: La Evolucion en 3 Etapas</h2>

<h3>Etapa 1: Chatbot (2023)</h3>
<p>Le preguntas algo → te responde con texto. Es una conversacion de ida y vuelta. No puede hacer nada fuera del chat: no busca informacion actualizada, no ejecuta acciones, no se conecta a tus herramientas.</p>
<p><strong>Ejemplo:</strong> "¿Cual es la capital de Francia?" → "Paris." Fin.</p>

<h3>Etapa 2: Asistente con Herramientas (2024-2025)</h3>
<p>La IA puede buscar en internet, leer archivos, generar imagenes, ejecutar codigo. Ya no solo habla — hace cosas. Pero necesita que TU le digas que hacer paso a paso.</p>
<p><strong>Ejemplo:</strong> "Busca las ultimas noticias sobre IA en LATAM y resumelas" → (busca en web, lee resultados, genera resumen).</p>

<h3>Etapa 3: Agente Autonomo (2026+)</h3>
<p>La IA planifica, ejecuta, evalua resultados, y ajusta su estrategia — con minima intervencion humana. No necesitas decirle COMO hacerlo, solo QUE quieres lograr.</p>
<p><strong>Ejemplo:</strong> "Monitorea los precios de mi competencia y avisame si alguien baja mas del 10%" → (el agente configura el monitoreo, revisa diariamente, analiza cambios, y te manda un mensaje solo cuando detecta algo relevante).</p>

<h2>MCP: El USB Universal de la IA</h2>
<p>MCP (Model Context Protocol) es un estandar abierto creado por Anthropic que resuelve un problema enorme:</p>

<h3>Sin MCP (El caos actual)</h3>
<p>Cada IA necesita su propia integracion para cada herramienta. Quieres que Claude lea tu Google Drive? Necesitas una integracion especifica. ¿Que tambien use Slack? Otra integracion. ¿Trello? Otra mas. Multiplicado por cada IA que uses.</p>

<h3>Con MCP (El futuro)</h3>
<p>Un protocolo universal — como USB para la IA. Cualquier herramienta que "hable MCP" se conecta automaticamente a cualquier IA compatible. Escribes una integracion, funciona en todas partes.</p>

<p><strong>Ejemplo real:</strong> Claude con MCP puede leer tus documentos de Notion, buscar archivos en Google Drive, actualizar tareas en Trello, y enviar mensajes en Slack — todo desde una sola conversacion, sin salir del chat.</p>

<h2>Skills = Apps para tu Agente</h2>
<p>Piensa en los skills como "apps" que le instalas a tu agente. Cada skill le da una capacidad nueva:</p>
<ul>
<li><strong>Skill de Email:</strong> Puede leer, redactar y enviar emails</li>
<li><strong>Skill de Analytics:</strong> Puede consultar Google Analytics y generar reportes</li>
<li><strong>Skill de CRM:</strong> Puede buscar y actualizar registros de clientes</li>
<li><strong>Skill de Codigo:</strong> Puede escribir, ejecutar y debuggear codigo</li>
</ul>

<h2>Casos Reales Funcionando HOY</h2>
<h3>1. Agente Monitor de Precios</h3>
<p>Una tienda online configuro un agente que revisa los precios de sus 3 principales competidores cada 6 horas. Cuando detecta un cambio mayor al 5%, genera un reporte con recomendacion de accion y lo envia por Slack al equipo de pricing.</p>

<h3>2. Agente de Soporte Level 1</h3>
<p>Una SaaS LATAM implemento un agente que responde automaticamente el 60% de los tickets de soporte (los que son preguntas frecuentes o problemas comunes). Los tickets complejos se escalan a humanos con un resumen del contexto.</p>

<h3>3. Agente de Reportes Semanales</h3>
<p>Un equipo de marketing tiene un agente que cada lunes a las 8am genera un reporte con: metricas de la semana anterior, comparacion vs la semana anterior, y 3 recomendaciones accionables. Lo envia automaticamente por email al director.</p>

<h2>¿Como Prepararte HOY?</h2>
<ol>
<li><strong>Domina Prompt Engineering</strong> — Si no sabes hablarle bien a la IA, un agente no te va a servir de nada</li>
<li><strong>Identifica tareas repetitivas</strong> — Todo lo que haces igual cada semana es candidato a automatizacion</li>
<li><strong>Empieza con asistentes</strong> — Antes de saltar a agentes autonomos, usa herramientas como ChatGPT con plugins o Claude con MCP</li>
<li><strong>Aprende lo basico de automatizacion</strong> — Herramientas como n8n o Make (Integromat) son la antesala de los agentes</li>
</ol>
',
        ]);

        // ═══════════════════════════════════════════════════
        // LECCION 9: Tu Plan de IA
        // ═══════════════════════════════════════════════════
        Lesson::create([
            'course_id' => $course->id,
            'title' => 'Tu Plan de IA: De la Teoria a la Accion',
            'slug' => 'plan-de-ia',
            'type' => 'interactive',
            'sort_order' => 9,
            'is_published' => true,
            'pass_threshold' => 70,
            'content_html' => '
<h2>El Ejercicio Mas Importante del Taller</h2>
<p>Todo lo que aprendiste en las 8 lecciones anteriores converge aqui. Vas a construir <strong>tu propio Plan de IA</strong> — no uno generico, sino uno basado en tu industria, tus problemas reales y tu presupuesto.</p>
<p>Al completar los 5 pasos, tendras un documento accionable que puedes empezar a ejecutar mañana.</p>
<p><strong>Este plan es tuyo. No es generico. Es lo que TU necesitas para TU negocio.</strong></p>
',
            'interactive_data' => [
                [
                    'type' => 'plan_wizard',
                    'title' => 'Construye Tu Plan de IA en 5 Pasos',
                    'instructions' => 'Completa cada paso para generar tu plan personalizado de implementacion de IA.',
                    'steps' => [
                        [
                            'label' => 'Paso 1: ¿En que industria operas?',
                            'subtitle' => 'Selecciona la que mas se acerque a tu negocio.',
                            'input_type' => 'select',
                            'options' => [
                                'Marketing / Publicidad / Agencia',
                                'E-commerce / Retail',
                                'Educacion / Formacion',
                                'Tecnologia / Software / SaaS',
                                'Salud / Bienestar',
                                'Servicios Profesionales (Legal, Contable, Consultoria)',
                                'Gastronomia / Alimentos',
                                'Manufactura / Produccion',
                                'Inmobiliaria / Bienes Raices',
                                'Otro',
                            ],
                        ],
                        [
                            'label' => 'Paso 2: ¿Cual es el tamaño de tu operacion?',
                            'subtitle' => 'Esto determina la escala de herramientas recomendadas.',
                            'input_type' => 'select',
                            'options' => [
                                'Solo yo (freelancer / solopreneur)',
                                'Equipo pequeño (2-5 personas)',
                                'Empresa mediana (6-20 personas)',
                                'Empresa grande (20+ personas)',
                            ],
                        ],
                        [
                            'label' => 'Paso 3: ¿Cuales son tus 3 problemas mas grandes?',
                            'subtitle' => 'Selecciona los que mas te duelen en tu dia a dia.',
                            'input_type' => 'multi_select',
                            'options' => [
                                'Crear contenido toma demasiado tiempo',
                                'No tengo tiempo para analizar datos / metricas',
                                'La atencion al cliente es lenta o inconsistente',
                                'Las tareas administrativas me quitan tiempo productivo',
                                'No puedo escalar sin contratar mas gente',
                                'Mis propuestas / cotizaciones toman mucho tiempo',
                                'No se que herramientas de IA usar',
                                'Mis competidores estan usando IA y yo no',
                                'No puedo personalizar la experiencia de mis clientes',
                                'La investigacion de mercado es manual y lenta',
                            ],
                        ],
                        [
                            'label' => 'Paso 4: ¿Que herramientas te interesan mas?',
                            'subtitle' => 'Puedes ajustar despues. Selecciona las que te llamen la atencion.',
                            'input_type' => 'chips',
                            'options' => ['ChatGPT', 'Claude', 'Gemini', 'Midjourney', 'Canva AI', 'Notion AI', 'n8n', 'ManyChat', 'Cursor', 'ElevenLabs', 'Gamma AI', 'Perplexity'],
                        ],
                        [
                            'label' => 'Paso 5: ¿En cuanto tiempo quieres ver resultados?',
                            'subtitle' => 'Sé realista — los mejores resultados vienen con consistencia.',
                            'input_type' => 'select',
                            'options' => ['30 dias (inicio rapido)', '60 dias (implementacion solida)', '90 dias (transformacion completa)'],
                        ],
                    ],
                    'recommendations' => [
                        'Marketing / Publicidad / Agencia' => [
                            ['name' => 'Claude Pro', 'use' => 'Estrategia y redaccion de contenido'],
                            ['name' => 'Midjourney', 'use' => 'Generacion de imagenes para campañas'],
                            ['name' => 'n8n', 'use' => 'Automatizacion de workflows repetitivos'],
                            ['name' => 'Notion AI', 'use' => 'Gestion de proyectos y conocimiento'],
                        ],
                        'E-commerce / Retail' => [
                            ['name' => 'Claude', 'use' => 'Descripciones de productos y copywriting'],
                            ['name' => 'ManyChat', 'use' => 'Chatbot para WhatsApp e Instagram'],
                            ['name' => 'Canva AI', 'use' => 'Diseño de creatividades y banners'],
                            ['name' => 'Perplexity', 'use' => 'Investigacion de mercado y competencia'],
                        ],
                        'Educacion / Formacion' => [
                            ['name' => 'ChatGPT Plus', 'use' => 'Creacion de material didactico'],
                            ['name' => 'Gamma AI', 'use' => 'Presentaciones y cursos visuales'],
                            ['name' => 'Canva AI', 'use' => 'Infografias y material visual'],
                            ['name' => 'Notion AI', 'use' => 'Organizacion de curricula y notas'],
                        ],
                        'Tecnologia / Software / SaaS' => [
                            ['name' => 'Claude Code / Cursor', 'use' => 'Desarrollo asistido por IA'],
                            ['name' => 'Claude API', 'use' => 'Integracion de IA en tu producto'],
                            ['name' => 'GitHub Copilot', 'use' => 'Autocompletado de codigo'],
                            ['name' => 'n8n', 'use' => 'Automatizacion de pipelines'],
                        ],
                        'Salud / Bienestar' => [
                            ['name' => 'ChatGPT Plus', 'use' => 'Contenido educativo para pacientes'],
                            ['name' => 'Canva AI', 'use' => 'Material informativo visual'],
                            ['name' => 'Notion AI', 'use' => 'Organizacion de protocolos y guias'],
                        ],
                        'Servicios Profesionales (Legal, Contable, Consultoria)' => [
                            ['name' => 'Claude Pro', 'use' => 'Analisis de documentos y redaccion'],
                            ['name' => 'Perplexity', 'use' => 'Investigacion rapida con fuentes'],
                            ['name' => 'Notion AI', 'use' => 'Gestion de conocimiento interno'],
                        ],
                        'Gastronomia / Alimentos' => [
                            ['name' => 'ChatGPT', 'use' => 'Menu engineering y contenido social'],
                            ['name' => 'Canva AI', 'use' => 'Diseño de menus y promociones'],
                            ['name' => 'ManyChat', 'use' => 'Pedidos y reservas por WhatsApp'],
                        ],
                        'Manufactura / Produccion' => [
                            ['name' => 'Claude', 'use' => 'Analisis de procesos y documentacion'],
                            ['name' => 'n8n', 'use' => 'Automatizacion de reportes'],
                            ['name' => 'Perplexity', 'use' => 'Investigacion de proveedores y materiales'],
                        ],
                        'Inmobiliaria / Bienes Raices' => [
                            ['name' => 'ChatGPT Plus', 'use' => 'Descripciones de propiedades y seguimiento'],
                            ['name' => 'Canva AI', 'use' => 'Flyers y presentaciones de propiedades'],
                            ['name' => 'ManyChat', 'use' => 'Calificacion automatica de leads'],
                        ],
                        'Otro' => [
                            ['name' => 'ChatGPT Plus', 'use' => 'Asistente general de productividad'],
                            ['name' => 'Claude Pro', 'use' => 'Analisis y redaccion avanzada'],
                            ['name' => 'Canva AI', 'use' => 'Diseño visual rapido'],
                        ],
                    ],
                ],
            ],
        ]);

        // ═══════════════════════════════════════════════════
        // LECCION 10: Cierre + Quiz Final
        // ═══════════════════════════════════════════════════
        Lesson::create([
            'course_id' => $course->id,
            'title' => 'Cierre: Tu Primer Paso Empieza Hoy',
            'slug' => 'cierre-quiz-final',
            'type' => 'quiz',
            'sort_order' => 10,
            'is_published' => true,
            'pass_threshold' => 70,
            'video_outline' => "Video (6 min)\n0:00 — Montaje rapido de todo el taller (recap visual)\n0:45 — Que aprendiste en 10 lecciones\n1:30 — Error 1: Comprar herramientas antes de definir el problema\n2:15 — Error 2: Esperar magia sin invertir en prompts\n3:00 — Error 3: No medir resultados\n3:45 — IA Responsable: checklist de 5 puntos\n4:45 — \"Tu Plan de IA ya esta listo — ejecutalo\"\n5:15 — CTA: Taller 1 → Ecosistema completo de IA\n5:45 — Cierre",
            'content_html' => '
<h2>Lo Que Aprendiste en Este Taller</h2>
<p>En 10 lecciones pasaste de entender los fundamentos a tener un plan real de implementacion:</p>
<ol>
<li><strong>Leccion 1-3:</strong> Como funciona la IA (de verdad), tokens, costos, y modelos de razonamiento</li>
<li><strong>Leccion 4:</strong> Verificaste tu comprension con el primer quiz</li>
<li><strong>Leccion 5-6:</strong> Armaste tu stack personalizado y aprendiste prompt engineering</li>
<li><strong>Leccion 7:</strong> Escribiste tus primeros prompts profesionales</li>
<li><strong>Leccion 8:</strong> Descubriste MCP, agentes y el futuro de la IA</li>
<li><strong>Leccion 9:</strong> Construiste TU Plan de IA personalizado</li>
</ol>

<h2>Los 3 Errores Mas Comunes al Implementar IA</h2>

<h3>Error 1: Comprar herramientas antes de definir el problema</h3>
<p>"Necesito Midjourney" → ¿Para que? Si no puedes responder esa pregunta en una oracion, no lo necesitas todavia. Primero define el problema, luego busca la herramienta.</p>

<h3>Error 2: Esperar resultados magicos sin invertir tiempo en prompts</h3>
<p>"La IA no sirve, me da respuestas genericas" → El problema no es la IA — es el prompt. Un prompt de 10 palabras produce resultados de 10 palabras. Invierte 5 minutos en escribir un buen prompt y los resultados cambian dramaticamente.</p>

<h3>Error 3: No medir resultados</h3>
<p>Si no mides, no sabes si la IA te esta ayudando o solo entreteniendote. Define metricas claras: horas ahorradas, conversion mejorada, costos reducidos, velocidad de entrega.</p>

<h2>IA Responsable: Tu Checklist</h2>
<ul>
<li>No compartas datos personales de clientes con IAs publicas sin consentimiento</li>
<li>Siempre verifica datos criticos (numeros, fechas, citas, estadisticas)</li>
<li>No publiques contenido generado por IA sin revision humana</li>
<li>Se transparente: si usas IA para crear contenido, no finjas que es 100% humano cuando importa</li>
<li>No uses IA para manipular, engañar o crear desinformacion</li>
</ul>

<h2>Tu Siguiente Paso</h2>
<p>Completaste el Taller 0. Ahora tienes la base teorica Y practica. El siguiente paso: <strong>Taller 1</strong>, donde mapeas el ecosistema completo de IA para tu industria y construyes tu primer workflow automatizado.</p>
',
            'quiz_data' => [
                [
                    'question' => '¿Cual es el error mas comun al empezar a usar IA en un negocio?',
                    'options' => [
                        'No tener presupuesto suficiente',
                        'Comprar herramientas antes de definir el problema que quieres resolver',
                        'No tener equipo tecnico',
                        'Usar la IA gratuita en vez de la de pago',
                    ],
                    'correct' => 1,
                    'explanation' => 'El error mas caro no es falta de dinero — es falta de direccion. Muchos compran suscripciones de herramientas sin saber que problema van a resolver. Resultado: herramientas que no se usan y dinero desperdiciado. Siempre: problema primero, herramienta despues.',
                ],
                [
                    'question' => 'MCP (Model Context Protocol) se puede comparar con:',
                    'options' => [
                        'Un lenguaje de programacion nuevo',
                        'USB: un conector universal para que la IA se conecte a cualquier herramienta',
                        'Un modelo de IA mas potente',
                        'Una red social para IAs',
                    ],
                    'correct' => 1,
                    'explanation' => 'MCP es como USB para la IA: un protocolo estandar que permite que cualquier IA se conecte a cualquier herramienta compatible, sin necesidad de integraciones personalizadas. Un conector para todo.',
                ],
                [
                    'question' => '¿Cual es la diferencia principal entre un "asistente" y un "agente" de IA?',
                    'options' => [
                        'El agente es mas caro',
                        'El asistente necesita instrucciones paso a paso; el agente planifica y ejecuta autonomamente',
                        'El agente solo funciona con MCP',
                        'No hay diferencia real, son terminos de marketing',
                    ],
                    'correct' => 1,
                    'explanation' => 'Un asistente ejecuta lo que le pides paso a paso. Un agente recibe un OBJETIVO y decide por si mismo como lograrlo: planifica, ejecuta, evalua resultados, y ajusta. La autonomia es la diferencia clave.',
                ],
                [
                    'question' => 'Si quieres que la IA verifique datos antes de responder, ¿que tecnica de personalizacion usarias?',
                    'options' => [
                        'Fine-tuning',
                        'Temperature baja',
                        'RAG (Retrieval-Augmented Generation)',
                        'System prompt',
                    ],
                    'correct' => 2,
                    'explanation' => 'RAG permite que la IA busque en TUS documentos o bases de datos antes de responder. En vez de "adivinar" o alucinar, la IA consulta fuentes reales y basa su respuesta en datos verificados.',
                ],
                [
                    'question' => '¿Que elemento de un prompt tiene MAS impacto en la calidad de la respuesta?',
                    'options' => [
                        'Usar "por favor" y "gracias"',
                        'El contexto especifico sobre tu situacion',
                        'La longitud del prompt',
                        'Escribir en ingles en vez de español',
                    ],
                    'correct' => 1,
                    'explanation' => 'El contexto es el rey. Darle a la IA informacion especifica sobre tu situacion, tu audiencia, tus restricciones, y tus objetivos es lo que mas impacta la calidad. Un prompt corto pero con buen contexto supera a un prompt largo pero vago.',
                ],
                [
                    'question' => 'Un agente de IA que monitorea precios de competidores es un ejemplo de:',
                    'options' => [
                        'Chatbot basico',
                        'Asistente con herramientas',
                        'Agente autonomo',
                        'Fine-tuning',
                    ],
                    'correct' => 2,
                    'explanation' => 'Un agente autonomo: recibe un objetivo (monitorear precios), decide cuando y como ejecutar (revisar cada X horas), evalua resultados (¿hubo cambio significativo?), y actua (enviar alerta). Todo sin intervencion humana constante.',
                ],
                [
                    'question' => '¿Cual de estas NO es una buena practica de IA responsable?',
                    'options' => [
                        'Verificar datos criticos antes de publicarlos',
                        'Ser transparente sobre el uso de IA en tu contenido',
                        'Compartir datos personales de clientes con ChatGPT para analisis',
                        'Revisar contenido generado por IA antes de publicarlo',
                    ],
                    'correct' => 2,
                    'explanation' => 'Compartir datos personales de clientes con IAs publicas (ChatGPT, Claude free, etc.) sin consentimiento explicito puede violar leyes de privacidad y la confianza de tus clientes. Si necesitas analizar datos sensibles, usa versiones empresariales con garantias de privacidad.',
                ],
                [
                    'question' => 'Despues de completar este taller, ¿cual deberia ser tu PRIMER paso?',
                    'options' => [
                        'Comprar todas las herramientas de IA disponibles',
                        'Esperar a que la IA mejore mas antes de usarla',
                        'Ejecutar el Plan de IA que construiste en la leccion 9',
                        'Aprender a programar para crear tu propia IA',
                    ],
                    'correct' => 2,
                    'explanation' => 'Tu Plan de IA ya esta listo — no necesitas mas teoria, mas herramientas, ni mas espera. El siguiente paso es EJECUTAR: configura la primera herramienta de tu stack, escribe tus primeros prompts profesionales, y empieza a medir resultados. La accion supera a la preparacion infinita.',
                ],
            ],
        ]);
    }
}
