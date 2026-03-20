<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class Taller1Seeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('slug', 'taller-1')->first();

        if (!$course) {
            $course = Course::create([
                'title' => 'Taller 1: Notion Mastery',
                'slug' => 'taller-1',
                'description' => 'Convierte Notion en el sistema operativo de tu negocio. Desde cero hasta dashboards automatizados, bases de datos relacionales y workflows que se actualizan solos.',
                'emoji' => '📓',
                'tag' => 'Productividad',
                'is_published' => true,
                'sort_order' => 3,
            ]);
        } else {
            $course->update([
                'description' => 'Convierte Notion en el sistema operativo de tu negocio. Desde cero hasta dashboards automatizados, bases de datos relacionales y workflows que se actualizan solos.',
                'emoji' => '📓',
                'tag' => 'Productividad',
                'is_published' => true,
            ]);
        }

        // Delete existing lessons to reseed
        Lesson::where('course_id', $course->id)->delete();

        $lessons = [
            [
                'title' => '¿Qué es Notion y por qué tu negocio lo necesita?',
                'slug' => 'que-es-notion',
                'type' => 'lecture',
                'sort_order' => 1,
                'content_html' => '
<h2>Notion no es una app de notas</h2>
<p>Muchos piensan que Notion es "otro Google Docs" o "un cuaderno digital". La realidad es muy diferente: <strong>Notion es un sistema operativo para tu negocio</strong>. Piensa en él como un espacio donde puedes construir desde cero cualquier herramienta que tu equipo necesite — sin escribir una sola línea de código.</p>

<h2>¿Qué puedes construir con Notion?</h2>
<ul>
    <li><strong>Wiki de empresa</strong> — Un lugar donde todo el conocimiento de tu negocio vive organizado. Procesos, guías, contactos, todo en un solo lugar.</li>
    <li><strong>CRM básico</strong> — Una base de datos de tus clientes con seguimiento de cada interacción, sin pagar por HubSpot.</li>
    <li><strong>Tablero de proyectos</strong> — Como Trello o Asana, pero integrado con todo lo demás de tu negocio.</li>
    <li><strong>Base de conocimiento</strong> — Tutoriales, FAQs y documentación para tu equipo o tus clientes.</li>
    <li><strong>Dashboard de métricas</strong> — Vistas consolidadas con filtros, gráficos y resúmenes automáticos.</li>
</ul>

<h2>¿Cuánto cuesta?</h2>
<p>Notion tiene un plan gratuito que es suficiente para empezar. El plan Plus (ideal para equipos pequeños) cuesta $10 USD/mes por persona. Comparado con lo que cuesta un Asana + Confluence + Trello por separado, es una fracción del precio.</p>

<h2>Lo que vamos a construir en este taller</h2>
<p>Al terminar este taller vas a tener un <strong>workspace funcional</strong> con:</p>
<ol>
    <li>Un sistema de gestión de proyectos con vistas Kanban y calendario</li>
    <li>Un CRM básico para rastrear clientes y leads</li>
    <li>Una wiki de procesos para tu equipo</li>
    <li>Un dashboard que se actualiza solo con la información de tus bases de datos</li>
</ol>
<p>No necesitas experiencia previa. Si sabes usar Excel o Google Sheets, ya tienes el 80% de lo que necesitas.</p>
',
            ],
            [
                'title' => 'Tu primer workspace: páginas, bloques y navegación',
                'slug' => 'primer-workspace',
                'type' => 'lecture',
                'sort_order' => 2,
                'content_html' => '
<h2>La anatomía de Notion</h2>
<p>Notion se organiza en tres niveles. Una vez que los entiendas, todo lo demás tiene sentido:</p>

<h3>1. Workspace (tu espacio de trabajo)</h3>
<p>Es el contenedor principal. Piensa en él como tu oficina virtual. Aquí vive todo lo de tu negocio. Puedes tener UN workspace para todo o separar por proyecto — recomendamos uno solo para empezar.</p>

<h3>2. Páginas</h3>
<p>Las páginas son como carpetas inteligentes. Una página puede contener texto, imágenes, tablas, otras páginas adentro (sub-páginas), y bases de datos. No hay límite de profundidad.</p>
<p><strong>Tip:</strong> Estructura tu sidebar con máximo 5-7 páginas principales. Si tienes más, probablemente necesitas sub-páginas.</p>

<h3>3. Bloques</h3>
<p>Todo dentro de una página es un bloque. Un párrafo es un bloque. Una imagen es un bloque. Una tabla es un bloque. Puedes mover bloques arrastrándolos, convertir un bloque de texto en un heading, o transformar una lista en un tablero Kanban.</p>

<h2>Estructura recomendada para un negocio</h2>
<p>Aquí una estructura inicial que funciona para el 90% de los negocios pequeños:</p>
<ul>
    <li>📋 <strong>Proyectos</strong> — Base de datos con todos tus proyectos activos</li>
    <li>👥 <strong>Clientes</strong> — CRM con datos de cada cliente</li>
    <li>📖 <strong>Wiki</strong> — Procesos documentados de tu equipo</li>
    <li>📅 <strong>Calendario</strong> — Vista de calendario de entregas y deadlines</li>
    <li>📊 <strong>Dashboard</strong> — Vista consolidada con métricas clave</li>
</ul>

<h2>Ejercicio práctico</h2>
<p>Abre Notion (notion.so), crea una cuenta gratuita, y construye esta estructura de 5 páginas en tu sidebar. No las llenes todavía — solo crea las páginas vacías con un emoji y el nombre. En la siguiente lección las vamos a convertir en bases de datos.</p>
',
            ],
            [
                'title' => 'Bases de datos: el superpoder de Notion',
                'slug' => 'bases-de-datos',
                'type' => 'lecture',
                'sort_order' => 3,
                'content_html' => '
<h2>¿Qué es una base de datos en Notion?</h2>
<p>Si has usado Excel o Google Sheets, ya entiendes el concepto. Una base de datos en Notion es una tabla con filas (registros) y columnas (propiedades). La diferencia es que <strong>cada fila es también una página</strong> donde puedes escribir notas, adjuntar archivos y agregar contenido.</p>

<p>Imagina una hoja de Excel donde cada fila se puede abrir como un documento completo. Eso es una base de datos de Notion.</p>

<h2>Tipos de propiedades (columnas)</h2>
<p>Cada columna tiene un tipo. Los más útiles para empezar:</p>
<ul>
    <li><strong>Text</strong> — Texto libre (nombre del cliente, notas)</li>
    <li><strong>Select</strong> — Lista desplegable de opciones (estado: Activo, Pausa, Cerrado)</li>
    <li><strong>Multi-select</strong> — Varias opciones a la vez (tags: SEO, Redes, Diseño)</li>
    <li><strong>Date</strong> — Fechas con calendario (deadline, fecha de inicio)</li>
    <li><strong>Number</strong> — Números con formato (presupuesto, horas)</li>
    <li><strong>Checkbox</strong> — Sí/No (completado, pagado)</li>
    <li><strong>URL</strong> — Links clickeables (sitio web del cliente)</li>
    <li><strong>Person</strong> — Asignar a un miembro del equipo</li>
    <li><strong>Relation</strong> — Conectar con otra base de datos (cliente ↔ proyecto)</li>
    <li><strong>Formula</strong> — Cálculos automáticos (como fórmulas de Excel)</li>
</ul>

<h2>Las 6 vistas de una base de datos</h2>
<p>La magia de Notion es que una misma base de datos puede verse de 6 formas diferentes, sin duplicar datos:</p>
<ol>
    <li><strong>Tabla</strong> — Como Excel, filas y columnas</li>
    <li><strong>Board (Kanban)</strong> — Tarjetas agrupadas por estado, como Trello</li>
    <li><strong>Timeline</strong> — Línea de tiempo, como un Gantt chart</li>
    <li><strong>Calendar</strong> — Vista de calendario mensual</li>
    <li><strong>Gallery</strong> — Tarjetas con imagen destacada</li>
    <li><strong>List</strong> — Lista simple con títulos</li>
</ol>

<h2>Ejercicio práctico</h2>
<p>Convierte tu página "Proyectos" en una base de datos tipo tabla. Agrega estas columnas: Nombre (título), Estado (select: Briefing, En Progreso, Entregado), Cliente (text), Deadline (date), Prioridad (select: Alta, Media, Baja). Agrega 3 proyectos de ejemplo.</p>
',
            ],
            [
                'title' => 'Quiz: Fundamentos de Notion',
                'slug' => 'quiz-fundamentos',
                'type' => 'quiz',
                'sort_order' => 4,
                'pass_threshold' => 70,
                'quiz_data' => json_encode([
                    [
                        'question' => '¿Cuál es la unidad básica de contenido en Notion?',
                        'options' => ['Archivo', 'Bloque', 'Carpeta', 'Documento'],
                        'correct' => 1,
                        'explanation' => 'En Notion, todo es un bloque. Un párrafo, una imagen, una tabla — cada elemento es un bloque que puedes mover y transformar.'
                    ],
                    [
                        'question' => '¿Cuántas vistas diferentes puede tener una misma base de datos?',
                        'options' => ['2', '4', '6', 'Ilimitadas'],
                        'correct' => 2,
                        'explanation' => 'Notion ofrece 6 tipos de vista: Tabla, Board (Kanban), Timeline, Calendar, Gallery y List. Puedes crear múltiples vistas de cada tipo.'
                    ],
                    [
                        'question' => '¿Qué tipo de propiedad usarías para conectar tu base de datos de Clientes con la de Proyectos?',
                        'options' => ['Text', 'URL', 'Relation', 'Formula'],
                        'correct' => 2,
                        'explanation' => 'La propiedad Relation permite conectar dos bases de datos entre sí. Así cada proyecto puede estar vinculado a un cliente específico.'
                    ],
                    [
                        'question' => '¿Qué diferencia a una base de datos de Notion de una hoja de Excel?',
                        'options' => [
                            'Notion no puede hacer cálculos',
                            'Cada fila en Notion es también una página con contenido',
                            'Excel tiene más columnas disponibles',
                            'No hay diferencia real'
                        ],
                        'correct' => 1,
                        'explanation' => 'La ventaja clave de Notion es que cada fila (registro) se puede abrir como una página completa donde puedes agregar notas, archivos, sub-tareas y contenido estructurado.'
                    ],
                    [
                        'question' => '¿Cuántas páginas principales recomienda este curso tener en el sidebar?',
                        'options' => ['2-3', '5-7', '10-15', 'Sin límite'],
                        'correct' => 1,
                        'explanation' => 'Recomendamos 5-7 páginas principales en el sidebar. Si necesitas más, probablemente deberías usar sub-páginas para mantener la organización.'
                    ],
                ]),
                'content_html' => '<p>Evalúa tu comprensión de los fundamentos de Notion antes de avanzar a la construcción de tu CRM.</p>',
            ],
            [
                'title' => 'Construye tu CRM en Notion (paso a paso)',
                'slug' => 'crm-notion',
                'type' => 'lecture',
                'sort_order' => 5,
                'content_html' => '
<h2>¿Qué es un CRM y por qué necesitas uno?</h2>
<p>Un CRM (Customer Relationship Management) es un sistema para gestionar tus relaciones con clientes. En español: una herramienta que te ayuda a recordar quién es cada cliente, qué te ha comprado, cuándo fue la última vez que hablaron, y qué necesita.</p>
<p>Sin un CRM, dependes de tu memoria, de notas sueltas, y de WhatsApp. Con un CRM, todo está en un solo lugar.</p>

<h2>El CRM que vamos a construir</h2>
<p>No necesitas pagar por HubSpot o Salesforce para empezar. Un CRM en Notion es gratis y puedes personalizarlo exactamente como lo necesitas. Vamos a construir uno con:</p>

<h3>Base de datos: Contactos</h3>
<p>Propiedades recomendadas:</p>
<ul>
    <li><strong>Nombre</strong> (Título) — Nombre completo del contacto</li>
    <li><strong>Empresa</strong> (Text) — Donde trabaja</li>
    <li><strong>Email</strong> (Email) — Correo principal</li>
    <li><strong>Teléfono</strong> (Phone) — Para seguimiento directo</li>
    <li><strong>Estado</strong> (Select) — Lead / Cliente Activo / Pausa / Cerrado</li>
    <li><strong>Origen</strong> (Select) — Referido / Redes / Web / Evento</li>
    <li><strong>Valor estimado</strong> (Number, USD) — Ticket promedio</li>
    <li><strong>Último contacto</strong> (Date) — Cuándo hablaste por última vez</li>
    <li><strong>Notas</strong> (Text) — Contexto libre, lo que necesites recordar</li>
    <li><strong>Proyectos</strong> (Relation → Proyectos) — Conecta con la base de proyectos</li>
</ul>

<h3>Vistas que vas a crear</h3>
<ol>
    <li><strong>Vista Tabla</strong> — "Todos los contactos" — para búsqueda y edición rápida</li>
    <li><strong>Vista Board</strong> — "Pipeline" — agrupado por Estado (Lead → Activo → Pausa → Cerrado)</li>
    <li><strong>Vista Calendar</strong> — "Seguimiento" — filtrado por "Último contacto" para ver a quién le debes una llamada</li>
</ol>

<h2>Ejercicio práctico</h2>
<p>Crea la base de datos de Contactos con todas las propiedades listadas. Agrega 5 contactos reales de tu negocio (o inventados si apenas estás empezando). Crea las 3 vistas: Tabla, Board por Estado, y Calendar por Último contacto.</p>
',
            ],
            [
                'title' => 'Relaciones y Rollups: conecta todo',
                'slug' => 'relaciones-rollups',
                'type' => 'lecture',
                'sort_order' => 6,
                'content_html' => '
<h2>El verdadero poder: bases de datos conectadas</h2>
<p>Hasta ahora tienes bases de datos independientes (Proyectos, Contactos). Pero la magia ocurre cuando las <strong>conectas entre sí</strong>. Eso se llama una Relación (Relation).</p>

<h3>¿Qué es una Relación?</h3>
<p>Imagina que tienes un cliente llamado "María García" en tu base de Contactos, y un proyecto "Rediseño Web" en tu base de Proyectos. Con una Relación, puedes vincularlos: María García ↔ Rediseño Web. Ahora, cuando abras el perfil de María, ves sus proyectos. Cuando abras el proyecto, ves que es de María.</p>

<h3>¿Qué es un Rollup?</h3>
<p>Un Rollup es como un "resumen automático" de datos que vienen de una relación. Ejemplo: si María tiene 3 proyectos con valores $500, $1,200 y $800 — puedes crear un Rollup en Contactos que automáticamente sume y muestre "$2,500 total". Sin fórmulas manuales.</p>

<h2>Paso a paso: conectar Contactos ↔ Proyectos</h2>
<ol>
    <li>Abre tu base de Contactos</li>
    <li>Agrega una propiedad nueva → tipo Relation</li>
    <li>Selecciona tu base de Proyectos como destino</li>
    <li>Activa "Show on Proyectos" para que la relación sea bidireccional</li>
    <li>Ahora puedes vincular cada contacto con sus proyectos</li>
</ol>

<h2>Rollup práctico: valor total por cliente</h2>
<ol>
    <li>Asegúrate que tu base de Proyectos tenga una columna "Presupuesto" (Number)</li>
    <li>En Contactos, agrega una propiedad tipo Rollup</li>
    <li>Relación: Proyectos</li>
    <li>Propiedad: Presupuesto</li>
    <li>Cálculo: Sum</li>
</ol>
<p>Ahora cada contacto muestra automáticamente el total de presupuesto de todos sus proyectos. Sin tocar nada manualmente.</p>

<h2>Ejercicio</h2>
<p>Conecta tus bases de Contactos y Proyectos con una Relación bidireccional. Crea un Rollup en Contactos que sume el presupuesto de proyectos asociados. Verifica que cuando agregas un nuevo proyecto y lo vinculas a un contacto, el total se actualiza solo.</p>
',
            ],
            [
                'title' => 'Templates y automatización en Notion',
                'slug' => 'templates-automatizacion',
                'type' => 'lecture',
                'sort_order' => 7,
                'content_html' => '
<h2>Templates de base de datos: crea una vez, usa siempre</h2>
<p>Cada vez que creas un nuevo proyecto o un nuevo cliente, probablemente repites los mismos pasos: agregar las mismas secciones, los mismos checklists, la misma estructura. Un Template te permite definir esa estructura una vez y aplicarla automáticamente a cada nuevo registro.</p>

<h3>Cómo crear un template</h3>
<ol>
    <li>Abre tu base de datos (por ejemplo, Proyectos)</li>
    <li>Haz clic en la flecha junto al botón "New" (o el menú ···)</li>
    <li>Selecciona "New template"</li>
    <li>Construye la estructura que quieres que tenga cada nuevo proyecto</li>
    <li>Guarda</li>
</ol>

<h3>Ejemplo: Template de Proyecto</h3>
<p>Un buen template de proyecto para una agencia podría tener:</p>
<ul>
    <li><strong>Briefing</strong> — Sección con preguntas clave (objetivo, público, deadline, presupuesto)</li>
    <li><strong>Checklist de entregables</strong> — Lista de lo que hay que entregar con checkboxes</li>
    <li><strong>Notas de reuniones</strong> — Espacio para documentar cada call con el cliente</li>
    <li><strong>Archivos</strong> — Zona para adjuntar diseños, documentos, referencias</li>
    <li><strong>Retrospectiva</strong> — Al cerrar el proyecto: qué salió bien, qué mejorar</li>
</ul>

<h2>Automatizaciones nativas</h2>
<p>Notion incluye automatizaciones básicas (sin herramientas externas):</p>
<ul>
    <li><strong>Cambio de estado automático</strong> — Cuando una propiedad cambia, actualiza otra</li>
    <li><strong>Notificaciones</strong> — Recibe alertas cuando un registro cambia</li>
    <li><strong>Botones</strong> — Crea botones que ejecutan acciones (crear página, cambiar propiedad, agregar a base de datos)</li>
</ul>

<h2>Nivel avanzado: Notion + Make.com</h2>
<p>Si quieres ir más allá, puedes conectar Notion con Make.com (que vimos en el blog) para crear automatizaciones más potentes:</p>
<ul>
    <li>Nuevo lead en tu formulario → se crea automáticamente en tu CRM de Notion</li>
    <li>Proyecto cambia a "Entregado" → envía email al cliente automáticamente</li>
    <li>Deadline se acerca → notificación en Slack</li>
</ul>
<p>Esto lo cubrimos a fondo en el Taller de Automatización (próximamente).</p>
',
            ],
            [
                'title' => 'Dashboard: tu centro de control',
                'slug' => 'dashboard',
                'type' => 'lecture',
                'sort_order' => 8,
                'content_html' => '
<h2>¿Qué es un dashboard en Notion?</h2>
<p>Un dashboard es una página que consolida la información más importante de tu negocio en un solo lugar. En vez de ir base por base buscando datos, abres tu dashboard y ves todo de un vistazo.</p>

<h2>Cómo construirlo</h2>
<p>Un dashboard en Notion se construye con <strong>vistas enlazadas</strong> (linked views). Son ventanas a tus bases de datos existentes, con filtros específicos.</p>

<h3>Dashboard recomendado para una agencia</h3>
<p>Crea una página llamada "📊 Dashboard" con estas secciones en 2 columnas:</p>

<p><strong>Columna izquierda:</strong></p>
<ul>
    <li><strong>Proyectos activos</strong> — Vista Board de Proyectos, filtrado por Estado ≠ Entregado</li>
    <li><strong>Deadlines esta semana</strong> — Vista List de Proyectos, filtrado por Deadline = esta semana, ordenado por fecha</li>
</ul>

<p><strong>Columna derecha:</strong></p>
<ul>
    <li><strong>Leads por atender</strong> — Vista List de Contactos, filtrado por Estado = Lead, ordenado por fecha de creación</li>
    <li><strong>Seguimiento pendiente</strong> — Vista List de Contactos, filtrado por Último contacto > 7 días</li>
</ul>

<h2>Tips para un buen dashboard</h2>
<ul>
    <li><strong>Menos es más</strong> — Máximo 4-6 secciones. Si pones todo, no ves nada.</li>
    <li><strong>Usa filtros agresivos</strong> — Solo muestra lo que requiere acción ahora.</li>
    <li><strong>Ponlo como página de inicio</strong> — En Notion Settings, puedes configurar que tu workspace abra directamente en el dashboard.</li>
</ul>

<h2>Ejercicio final</h2>
<p>Construye tu dashboard con las 4 secciones descritas. Usa "Create linked view of database" para agregar vistas filtradas de tus bases de Proyectos y Contactos. Configúralo como tu página de inicio.</p>
',
            ],
            [
                'title' => 'Quiz final: Notion Mastery',
                'slug' => 'quiz-final',
                'type' => 'quiz',
                'sort_order' => 9,
                'pass_threshold' => 70,
                'quiz_data' => json_encode([
                    [
                        'question' => '¿Qué propiedad usarías para que Notion calcule automáticamente el total de presupuesto de los proyectos de un cliente?',
                        'options' => ['Formula', 'Relation', 'Rollup', 'Number'],
                        'correct' => 2,
                        'explanation' => 'Un Rollup toma datos de una Relación y aplica un cálculo (sum, average, count, etc.) automáticamente.'
                    ],
                    [
                        'question' => '¿Cuál es la ventaja principal de un Template de base de datos?',
                        'options' => [
                            'Hace las páginas más bonitas',
                            'Define una estructura reutilizable para cada nuevo registro',
                            'Permite compartir con otros usuarios',
                            'Agrega más columnas a la base de datos'
                        ],
                        'correct' => 1,
                        'explanation' => 'Los templates definen la estructura interna que tendrá cada nuevo registro. Creas la estructura una vez y se aplica automáticamente.'
                    ],
                    [
                        'question' => '¿Cuántas secciones recomienda este curso para un dashboard efectivo?',
                        'options' => ['2-3', '4-6', '10-12', 'Sin límite'],
                        'correct' => 1,
                        'explanation' => 'Un buen dashboard tiene máximo 4-6 secciones con filtros agresivos. Si pones todo, no ves nada útil.'
                    ],
                    [
                        'question' => 'Un cliente te pregunta: "¿Cómo hago para que cuando un lead llene mi formulario web, aparezca automáticamente en mi CRM de Notion?" ¿Qué le recomiendas?',
                        'options' => [
                            'Copiar y pegar manualmente',
                            'Usar la integración de Notion con Make.com',
                            'Exportar a CSV cada día',
                            'No es posible con Notion'
                        ],
                        'correct' => 1,
                        'explanation' => 'Notion + Make.com permite crear automatizaciones donde un formulario web crea automáticamente un registro en tu base de datos de Notion.'
                    ],
                    [
                        'question' => '¿Qué tipo de vista usarías para ver tus proyectos agrupados por estado (Briefing → En Progreso → Entregado)?',
                        'options' => ['Table', 'Board (Kanban)', 'Calendar', 'Gallery'],
                        'correct' => 1,
                        'explanation' => 'La vista Board (Kanban) agrupa los registros en columnas según una propiedad Select. Es perfecta para visualizar pipelines y flujos de trabajo.'
                    ],
                ]),
                'content_html' => '<p>Demuestra lo que aprendiste. Necesitas 70% para aprobar.</p>',
            ],
            [
                'title' => 'Cierre: tu workspace está listo',
                'slug' => 'cierre',
                'type' => 'lecture',
                'sort_order' => 10,
                'content_html' => '
<h2>Lo que construiste en este taller</h2>
<p>Si seguiste los ejercicios, ahora tienes un workspace de Notion funcional con:</p>
<ul>
    <li>✅ Una base de datos de Proyectos con vistas de tabla, kanban y calendario</li>
    <li>✅ Un CRM de Contactos con pipeline de leads y vista de seguimiento</li>
    <li>✅ Relaciones bidireccionales entre clientes y proyectos</li>
    <li>✅ Rollups que calculan métricas automáticamente</li>
    <li>✅ Templates para crear nuevos proyectos con estructura predefinida</li>
    <li>✅ Un dashboard que te muestra todo lo importante en un solo lugar</li>
</ul>

<h2>Siguientes pasos</h2>
<p>Este taller te dio los fundamentos. Para seguir mejorando tu workspace:</p>
<ol>
    <li><strong>Usa Notion diariamente durante 2 semanas</strong> — La herramienta se adapta a ti. Mueve columnas, cambia vistas, ajusta filtros según lo que realmente necesitas.</li>
    <li><strong>Agrega tu equipo</strong> — Invita a tus colaboradores y asígnales tareas directamente desde Notion.</li>
    <li><strong>Explora la API de Notion</strong> — Si quieres integraciones más avanzadas, Notion tiene una API gratuita que se conecta con prácticamente cualquier herramienta.</li>
</ol>

<h2>¿Qué sigue en la Academia Keiyi?</h2>
<p>En los próximos talleres vamos a construir sobre esta base:</p>
<ul>
    <li><strong>Taller 2: Viral Contenido</strong> — Cómo crear contenido que se comparte solo, usando IA para escalar tu producción.</li>
    <li><strong>Marketing Elite</strong> — Estrategia completa de marketing digital con GEO, pauta inteligente y análisis de datos.</li>
</ul>

<p><strong>Felicidades por completar el Taller 1.</strong> Tu negocio ahora tiene un sistema operativo digital. Úsalo.</p>
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
