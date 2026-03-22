<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class FinanzasBasicasSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::updateOrCreate(
            ['slug' => 'finanzas-basicas'],
            [
                'title' => 'Finanzas para tu Negocio',
                'slug' => 'finanzas-basicas',
                'description' => 'Todo lo que necesitas saber para manejar el dinero de tu negocio sin ser contador. SAT, facturación, separar gastos, calcular si tu negocio es rentable, y las herramientas para llevar el control desde el día uno.',
                'emoji' => '💰',
                'tag' => 'Finanzas',
                'is_published' => true,
                'sort_order' => 6,
            ]
        );

        Lesson::where('course_id', $course->id)->delete();

        $lessons = [
            [
                'title' => '¿Por qué la mayoría de los negocios no saben si ganan o pierden dinero?',
                'slug' => 'por-que-importa',
                'type' => 'lecture',
                'sort_order' => 1,
                'content_html' => '
<h2>El problema que nadie te dice</h2>
<p>Según datos del INEGI, el 33% de los negocios en México muere antes de cumplir 5 años. La razón principal no es falta de clientes — es <strong>falta de control financiero</strong>. El dueño no sabe cuánto gasta, cuánto gana, ni si su negocio es realmente rentable o solo está moviendo dinero.</p>

<p>Esto pasa porque nadie te enseña finanzas de negocio en la escuela. Aprendes a sumar y restar, pero no a separar tus gastos personales de los del negocio, ni a calcular si lo que cobras cubre tus costos reales.</p>

<h2>Lo que vas a aprender en este curso</h2>
<p>Este curso no es para contadores. Es para ti, el emprendedor que quiere entender:</p>
<ol>
    <li><strong>¿Estoy ganando o perdiendo dinero?</strong> — Cómo saberlo con certeza, no con corazonadas</li>
    <li><strong>¿Cuánto debo cobrar?</strong> — Para que tus precios cubran todos tus costos y te dejen ganancia</li>
    <li><strong>¿Qué onda con el SAT?</strong> — Qué régimen fiscal te conviene, cómo facturar, qué puedes deducir</li>
    <li><strong>¿Cómo llevo el control?</strong> — Herramientas simples para no perder de vista tu dinero</li>
</ol>

<h2>Requisitos</h2>
<p>Ninguno. Si sabes usar una hoja de cálculo (Excel o Google Sheets), ya tienes todo lo que necesitas. Si no, te enseñamos en el camino.</p>

<h2>Para quién es este curso</h2>
<ul>
    <li>Emprendedores que están empezando y no saben cómo manejar el dinero de su negocio</li>
    <li>Freelancers que cobran pero no saben si realmente les conviene</li>
    <li>Dueños de negocios pequeños que "llevan las cuentas en la cabeza"</li>
    <li>Cualquiera que le tenga miedo al SAT y quiera entender lo básico</li>
</ul>
',
            ],
            [
                'title' => 'Separa tu dinero: personal vs negocio',
                'slug' => 'separar-dinero',
                'type' => 'lecture',
                'sort_order' => 2,
                'content_html' => '
<h2>El error #1 de todo emprendedor</h2>
<p>Mezclar el dinero personal con el del negocio. "Es que todo sale de la misma cuenta." Ese es el camino directo a no saber si tu negocio funciona o si estás subsidiándolo con tu salario, tus ahorros, o peor — con deuda.</p>

<h2>Cómo separarlo (paso a paso)</h2>

<h3>1. Abre una cuenta bancaria solo para el negocio</h3>
<p>No necesita ser una cuenta empresarial cara. Una cuenta de débito personal separada funciona al principio. Lo importante es que <strong>todo ingreso del negocio entre ahí</strong> y <strong>todo gasto del negocio salga de ahí</strong>.</p>
<p>Opciones en México:</p>
<ul>
    <li><strong>Nu México</strong> — Sin comisiones, app fácil, se abre en 5 minutos</li>
    <li><strong>Mercado Pago</strong> — Si vendes en MercadoLibre, ya tienes una</li>
    <li><strong>Banco tradicional</strong> — BBVA, Banorte, cualquiera con cuenta digital sin comisión</li>
</ul>

<h3>2. Págate un "sueldo"</h3>
<p>Cada quincena o cada mes, transfiere una cantidad fija de la cuenta del negocio a tu cuenta personal. Eso es tu sueldo. Lo que queda en la cuenta del negocio es del negocio — para reinvertir, para pagar gastos operativos, para impuestos.</p>
<p><strong>¿Cuánto pagarme?</strong> Empieza con el 30-40% de lo que factures. Ajusta según tus gastos fijos personales y los del negocio. Lo importante es que sea una cantidad fija, no "lo que sobre".</p>

<h3>3. Registra TODO</h3>
<p>Cada peso que entra y sale de la cuenta del negocio debe quedar registrado. No mañana, no "cuando tenga tiempo" — en el momento. Las apps bancarias facilitan esto, pero necesitas un registro propio (hoja de cálculo o app) para categorizar.</p>

<h2>Las 5 categorías básicas de gastos</h2>
<p>Todo gasto de tu negocio cae en una de estas:</p>
<ol>
    <li><strong>Costo directo</strong> — Lo que gastas para producir lo que vendes (material, insumos, filamento)</li>
    <li><strong>Operación</strong> — Lo que necesitas para que el negocio funcione (internet, luz, hosting, software)</li>
    <li><strong>Marketing</strong> — Lo que inviertes para conseguir clientes (publicidad, redes, contenido)</li>
    <li><strong>Impuestos</strong> — Lo que le pagas al SAT (ISR, IVA)</li>
    <li><strong>Tu sueldo</strong> — Lo que te pagas a ti mismo</li>
</ol>

<h2>Ejercicio</h2>
<p>Abre tu estado de cuenta del último mes. Clasifica cada gasto en una de las 5 categorías. ¿Cuánto fue para el negocio y cuánto fue personal? Si están mezclados, ese es tu punto de partida para separar.</p>
',
            ],
            [
                'title' => 'El SAT sin miedo: régimen fiscal para emprendedores',
                'slug' => 'sat-sin-miedo',
                'type' => 'lecture',
                'sort_order' => 3,
                'content_html' => '
<h2>¿Por qué le tenemos miedo al SAT?</h2>
<p>Porque nadie nos explica cómo funciona. El SAT (Servicio de Administración Tributaria) es la autoridad fiscal de México — el que cobra los impuestos. No es un enemigo. Es un sistema con reglas, y si las entiendes, puedes jugar a tu favor.</p>

<h2>Lo básico que necesitas saber</h2>

<h3>RFC (Registro Federal de Contribuyentes)</h3>
<p>Es tu identidad fiscal. Como tu CURP pero para impuestos. Si no tienes RFC con actividad económica, no puedes facturar legalmente. <strong>Se tramita gratis en sat.gob.mx.</strong></p>

<h3>¿Qué régimen fiscal me conviene?</h3>
<p>Para un emprendedor que empieza, hay dos opciones principales:</p>

<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr style="border-bottom:2px solid #000;"><th style="text-align:left;padding:12px;">Concepto</th><th style="text-align:left;padding:12px;">RESICO</th><th style="text-align:left;padding:12px;">Actividad Empresarial</th></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:12px;"><strong>Para quién</strong></td><td style="padding:12px;">Negocios con ingresos menores a $3.5 millones/año</td><td style="padding:12px;">Sin límite de ingresos</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:12px;"><strong>Tasa de ISR</strong></td><td style="padding:12px;">1% a 2.5% sobre ingresos</td><td style="padding:12px;">Hasta 35% sobre utilidad (ganancia)</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:12px;"><strong>¿Puedo deducir gastos?</strong></td><td style="padding:12px;">No</td><td style="padding:12px;">Sí (equipo, software, internet, etc.)</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:12px;"><strong>Complejidad</strong></td><td style="padding:12px;">Muy simple</td><td style="padding:12px;">Necesitas contador</td></tr>
<tr><td style="padding:12px;"><strong>Ideal para</strong></td><td style="padding:12px;">Empezar. Ingresos bajos-medios.</td><td style="padding:12px;">Cuando crezcas y tengas gastos fuertes que deducir.</td></tr>
</table>

<p><strong>Recomendación:</strong> Si facturas menos de $3.5 millones al año (y la mayoría de emprendedores que empiezan están MUY lejos de eso), <strong>RESICO es tu mejor opción</strong>. Pagas entre 1% y 2.5% de impuestos. Es ridículamente bajo comparado con otros países.</p>

<h3>IVA (Impuesto al Valor Agregado)</h3>
<p>El IVA es 16% en México. Si vendes un servicio de $1,000, cobras $1,160 ($1,000 + $160 de IVA). Esos $160 no son tuyos — se los debes al SAT. Apártalos.</p>
<p><strong>Tip crucial:</strong> Cada vez que cobres, separa el 16% del IVA en una subcuenta o apártalo mentalmente. Ese dinero NO es tuyo.</p>

<h2>¿Qué necesito para darme de alta?</h2>
<ol>
    <li>Tu RFC (si no lo tienes, trámitalo en sat.gob.mx con tu e.firma o CIEC)</li>
    <li>Elegir régimen fiscal (RESICO para empezar)</li>
    <li>Registrar tus obligaciones fiscales (ISR, IVA)</li>
    <li>Obtener tu Certificado de Sello Digital (CSD) para poder facturar</li>
</ol>
<p><strong>Todo se hace en línea en sat.gob.mx.</strong> Si te sientes perdido, un contador te cobra $500-$1,500 por hacer el alta completa.</p>

<h2>Ejercicio</h2>
<p>Entra a sat.gob.mx y verifica tu RFC. ¿Tienes obligaciones fiscales registradas? ¿Estás en RESICO o en otro régimen? Si no tienes RFC activo, agenda el trámite esta semana.</p>
',
            ],
            [
                'title' => 'Quiz: Fundamentos financieros',
                'slug' => 'quiz-fundamentos',
                'type' => 'quiz',
                'sort_order' => 4,
                'pass_threshold' => 70,
                'quiz_data' => json_encode([
                    [
                        'question' => '¿Cuál es el error financiero #1 de los emprendedores según este curso?',
                        'options' => ['No cobrar suficiente', 'Mezclar dinero personal con el del negocio', 'No pagar impuestos', 'Gastar mucho en marketing'],
                        'correct' => 1,
                        'explanation' => 'Mezclar el dinero personal con el del negocio hace imposible saber si tu negocio es rentable. La solución: cuentas separadas.',
                    ],
                    [
                        'question' => '¿Qué régimen fiscal recomienda este curso para un emprendedor que empieza?',
                        'options' => ['Actividad Empresarial', 'RESICO', 'Persona Moral', 'Régimen de Incorporación'],
                        'correct' => 1,
                        'explanation' => 'RESICO (Régimen Simplificado de Confianza) cobra entre 1% y 2.5% de ISR. Es la opción más simple y barata para ingresos menores a $3.5M/año.',
                    ],
                    [
                        'question' => 'Cobras $5,000 por un servicio. ¿Cuánto IVA debes cobrar al cliente?',
                        'options' => ['$500', '$800', '$1,000', '$160'],
                        'correct' => 1,
                        'explanation' => 'IVA = 16%. $5,000 × 0.16 = $800. Le cobras al cliente $5,800 total. Los $800 de IVA se los debes al SAT.',
                    ],
                    [
                        'question' => '¿Cuánto recomienda este curso pagarte como "sueldo" al empezar?',
                        'options' => ['Todo lo que entre', 'Lo que sobre', '30-40% de lo que factures', '10% de lo que factures'],
                        'correct' => 2,
                        'explanation' => 'Empieza con 30-40% de lo facturado como sueldo fijo. Lo demás queda para gastos del negocio, reinversión e impuestos.',
                    ],
                    [
                        'question' => '¿En cuál de las 5 categorías entra el pago de tu hosting web?',
                        'options' => ['Costo directo', 'Operación', 'Marketing', 'Tu sueldo'],
                        'correct' => 1,
                        'explanation' => 'El hosting es un gasto de operación — necesario para que el negocio funcione, pero no es un costo directo de producción ni marketing.',
                    ],
                ]),
                'content_html' => '<p>Evalúa tu comprensión de los fundamentos antes de avanzar a facturación y precios.</p>',
            ],
            [
                'title' => 'Facturación electrónica: cómo facturar sin morir en el intento',
                'slug' => 'facturacion',
                'type' => 'lecture',
                'sort_order' => 5,
                'content_html' => '
<h2>¿Qué es una factura electrónica (CFDI)?</h2>
<p>Un CFDI (Comprobante Fiscal Digital por Internet) es el documento oficial que emites cuando cobras por un producto o servicio. Es la factura. En México, <strong>toda factura es electrónica</strong> desde 2014 — no existen las facturas en papel.</p>

<p>Piensa en el CFDI como un recibo inteligente que le dice al SAT: "Esta persona cobró X pesos por Y concepto." Así el SAT sabe cuánto facturaste y cuánto impuesto debes.</p>

<h2>¿Necesito facturar?</h2>
<p><strong>Sí.</strong> Si cobras por un servicio o producto, debes emitir CFDI. ¿Qué pasa si no facturas?</p>
<ul>
    <li>El SAT puede detectar ingresos en tu cuenta bancaria que no tienen factura correspondiente</li>
    <li>Multa de $17,000 a $97,000 MXN por no emitir CFDI</li>
    <li>Tus clientes no pueden deducir el gasto si no les das factura — lo que desincentiva que te contraten</li>
</ul>

<h2>Cómo empezar a facturar</h2>

<h3>Lo que necesitas:</h3>
<ol>
    <li><strong>RFC activo</strong> con obligaciones fiscales (lo vimos en la lección anterior)</li>
    <li><strong>e.firma (FIEL)</strong> — tu firma electrónica. Se tramita presencialmente en oficina del SAT</li>
    <li><strong>Certificado de Sello Digital (CSD)</strong> — se genera en línea con tu e.firma</li>
    <li><strong>Sistema de facturación</strong> — el software que genera los CFDI</li>
</ol>

<h3>Sistemas de facturación (de gratis a pagado):</h3>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr style="border-bottom:2px solid #000;"><th style="text-align:left;padding:10px;">Opción</th><th style="text-align:left;padding:10px;">Costo</th><th style="text-align:left;padding:10px;">Para quién</th></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;"><strong>SAT gratuito</strong></td><td style="padding:10px;">$0</td><td style="padding:10px;">Pocas facturas al mes, lo más básico</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;"><strong>Facturapi</strong></td><td style="padding:10px;">$0-$499/mes</td><td style="padding:10px;">Plan gratis para pocas facturas, API para automatizar</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;"><strong>Bind ERP</strong></td><td style="padding:10px;">$299/mes</td><td style="padding:10px;">Si necesitas inventario + facturación juntos</td></tr>
<tr><td style="padding:10px;"><strong>Alegra</strong></td><td style="padding:10px;">$199/mes</td><td style="padding:10px;">Contabilidad simple + facturación, popular en LATAM</td></tr>
</table>

<h3>Datos que necesitas para cada factura:</h3>
<ul>
    <li><strong>RFC del cliente</strong> — si no te lo da, usa RFC genérico XAXX010101000</li>
    <li><strong>Uso del CFDI</strong> — "Gastos en general" (G03) es el más común</li>
    <li><strong>Clave de producto/servicio</strong> — código SAT que describe qué vendes</li>
    <li><strong>Monto + IVA</strong> — el sistema lo calcula automáticamente</li>
</ul>

<h2>Claves SAT comunes para emprendedores</h2>
<ul>
    <li><strong>80141600</strong> — Servicios de marketing y publicidad</li>
    <li><strong>86101700</strong> — Servicios educativos y de capacitación</li>
    <li><strong>81112100</strong> — Servicios de diseño y desarrollo web</li>
    <li><strong>24111500</strong> — Productos manufacturados (impresión 3D)</li>
</ul>

<h2>Ejercicio</h2>
<p>Si ya tienes CSD, genera tu primera factura de prueba en el portal gratuito del SAT (portalcfdi.facturaelectronica.sat.gob.mx). Si no tienes CSD, agenda cita en el SAT para tramitar tu e.firma (se necesita ir presencialmente).</p>
',
            ],
            [
                'title' => '¿Cuánto cobrar? Calcula tus costos reales',
                'slug' => 'cuanto-cobrar',
                'type' => 'lecture',
                'sort_order' => 6,
                'content_html' => '
<h2>El precio no se inventa — se calcula</h2>
<p>La mayoría de los emprendedores ponen precios de dos formas: (1) ven qué cobra la competencia y cobran lo mismo, o (2) inventan un número que "se siente bien." Ambas están mal.</p>
<p>Tu precio debe cubrir <strong>todos tus costos</strong> y dejarte una <strong>ganancia que valga la pena</strong>. Si no sabes cuánto te cuesta producir lo que vendes, no sabes si estás ganando o regalando tu trabajo.</p>

<h2>La fórmula de pricing</h2>
<p style="font-size:1.2em;font-weight:700;background:#f0f0f0;padding:16px;border:2px solid #000;text-align:center;">
PRECIO = (Costo directo + Costo operativo + Tu sueldo + Impuestos) × (1 + Margen%)
</p>

<h3>Ejemplo real: servicio de diseño web</h3>
<p>Supón que haces un sitio web que te toma 20 horas:</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr style="border-bottom:2px solid #000;"><th style="text-align:left;padding:10px;">Concepto</th><th style="text-align:right;padding:10px;">Monto</th></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;">Costo directo (hosting del cliente, dominio)</td><td style="padding:10px;text-align:right;">$500</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;">Operación prorrateada (tu internet, luz, software: $3,000/mes ÷ 160hrs × 20hrs)</td><td style="padding:10px;text-align:right;">$375</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;">Tu sueldo prorrateado ($15,000/mes ÷ 160hrs × 20hrs)</td><td style="padding:10px;text-align:right;">$1,875</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;">Impuestos estimados (RESICO ~2.5%)</td><td style="padding:10px;text-align:right;">$69</td></tr>
<tr style="border-bottom:2px solid #000;"><td style="padding:10px;font-weight:700;">Costo total</td><td style="padding:10px;text-align:right;font-weight:700;">$2,819</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;">Margen de ganancia (40%)</td><td style="padding:10px;text-align:right;">$1,128</td></tr>
<tr style="background:#f0f0f0;"><td style="padding:10px;font-weight:700;font-size:1.1em;">PRECIO MÍNIMO</td><td style="padding:10px;text-align:right;font-weight:700;font-size:1.1em;">$3,947</td></tr>
</table>

<p>Si cobras $3,000 por ese sitio web, estás <strong>perdiendo dinero</strong>. Tu costo real es $2,819. Solo ganarías $181 — ni siquiera cubre tu tiempo si algo sale mal.</p>

<h2>El margen mínimo</h2>
<p>Nunca cobres con menos de 30% de margen. Idealmente 40-50%. ¿Por qué? Porque siempre hay imprevistos: el proyecto tarda más, el cliente pide cambios, se te descompone algo. El margen es tu colchón.</p>

<h2>¿Y si la competencia cobra menos?</h2>
<p>Dos posibilidades:</p>
<ol>
    <li><strong>No saben calcular sus costos</strong> — están regalando su trabajo sin saberlo. No los imites.</li>
    <li><strong>Tienen costos más bajos que tú</strong> — trabaja en reducir tus costos, no en bajar tu precio.</li>
</ol>
<p>Competir en precio es una carrera al fondo. Compite en <strong>valor</strong>: mejor servicio, más rápido, más confiable, mejor comunicación.</p>

<h2>Ejercicio</h2>
<p>Calcula el costo real de tu servicio o producto principal usando la fórmula. ¿Tu precio actual cubre todos los costos + un margen de al menos 30%? Si no, ya sabes qué ajustar.</p>
',
            ],
            [
                'title' => 'Herramientas para llevar el control financiero',
                'slug' => 'herramientas-control',
                'type' => 'lecture',
                'sort_order' => 7,
                'content_html' => '
<h2>No necesitas un sistema caro — necesitas disciplina</h2>
<p>La herramienta financiera más poderosa es una hoja de cálculo que uses <strong>todos los días</strong>. Un software caro que no actualizas no sirve de nada.</p>

<h2>Nivel 1: Google Sheets (gratis)</h2>
<p>Perfecto para empezar. Crea una hoja con estas columnas:</p>
<ul>
    <li><strong>Fecha</strong></li>
    <li><strong>Descripción</strong> (qué compraste o qué te pagaron)</li>
    <li><strong>Categoría</strong> (costo directo, operación, marketing, impuestos, sueldo)</li>
    <li><strong>Ingreso</strong> (lo que te pagaron)</li>
    <li><strong>Egreso</strong> (lo que pagaste)</li>
    <li><strong>Balance</strong> (fórmula: ingreso - egreso acumulado)</li>
</ul>
<p>Registra cada movimiento el mismo día que ocurre. Al final del mes, una tabla dinámica te dice exactamente cuánto gastaste por categoría.</p>

<h2>Nivel 2: Apps de finanzas</h2>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr style="border-bottom:2px solid #000;"><th style="text-align:left;padding:10px;">App</th><th style="text-align:left;padding:10px;">Costo</th><th style="text-align:left;padding:10px;">Para qué</th></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;"><strong>Alegra</strong></td><td style="padding:10px;">Desde $199/mes</td><td style="padding:10px;">Facturación + contabilidad básica. Popular en LATAM.</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;"><strong>Kontabol</strong></td><td style="padding:10px;">Desde $149/mes</td><td style="padding:10px;">Diseñada para México. Conecta con SAT.</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;"><strong>Notion</strong></td><td style="padding:10px;">Gratis</td><td style="padding:10px;">Si hiciste el Taller 1, ya sabes usarlo. Crea un tracker financiero con bases de datos.</td></tr>
<tr><td style="padding:10px;"><strong>Wave</strong></td><td style="padding:10px;">Gratis</td><td style="padding:10px;">Contabilidad básica, facturas (más enfocado a USA/Canadá).</td></tr>
</table>

<h2>Nivel 3: Contador</h2>
<p>Cuando tu negocio facture más de $50,000 al mes, vale la pena contratar un contador. Costo: $1,500-$3,000/mes. El contador se encarga de:</p>
<ul>
    <li>Declaraciones mensuales (ISR provisional, IVA)</li>
    <li>Declaración anual</li>
    <li>Deducciones fiscales optimizadas</li>
    <li>Evitar multas</li>
</ul>
<p><strong>Tip:</strong> Incluso con contador, TÚ debes llevar tu propio registro. El contador trabaja con lo que le das. Si le das datos desordenados, su trabajo será desordenado.</p>

<h2>Los 3 números que debes revisar cada semana</h2>
<ol>
    <li><strong>Ingresos de la semana</strong> — ¿Cuánto entró?</li>
    <li><strong>Gastos de la semana</strong> — ¿Cuánto salió?</li>
    <li><strong>Saldo disponible</strong> — ¿Cuánto tienes para operar?</li>
</ol>
<p>Si solo revisas estos 3 números cada lunes por la mañana (5 minutos), ya estás mejor que el 80% de los emprendedores.</p>

<h2>Ejercicio</h2>
<p>Crea tu hoja de cálculo financiera en Google Sheets con las 6 columnas. Registra todos los movimientos de esta semana. El lunes, revisa tus 3 números.</p>
',
            ],
            [
                'title' => '¿Qué puedo deducir? Gastos que bajan tus impuestos',
                'slug' => 'deducciones',
                'type' => 'lecture',
                'sort_order' => 8,
                'content_html' => '
<h2>Deducir no es evadir</h2>
<p><strong>Deducir</strong> significa restar ciertos gastos de tus ingresos antes de calcular impuestos. Es 100% legal. Es un derecho. Y si no lo haces, estás pagando más impuestos de los que debes.</p>

<p><strong>Importante:</strong> Si estás en RESICO, no puedes deducir gastos (tu tasa ya es baja: 1-2.5%). Las deducciones aplican cuando estás en Régimen de Actividad Empresarial (donde la tasa puede llegar a 35%).</p>

<h2>Gastos deducibles comunes para emprendedores digitales</h2>
<ul>
    <li><strong>Equipo de cómputo</strong> — Mac, PC, monitor, teclado, mouse (factura a nombre de tu negocio)</li>
    <li><strong>Software y suscripciones</strong> — Adobe, Notion, Claude, hosting, dominio, herramientas SaaS</li>
    <li><strong>Internet y teléfono</strong> — la proporción que uses para el negocio (ej: 70%)</li>
    <li><strong>Electricidad</strong> — si trabajas desde casa, la proporción de tu recibo que corresponde al espacio de trabajo</li>
    <li><strong>Cursos y capacitación</strong> — sí, incluyendo este curso (si lo facturas)</li>
    <li><strong>Material de impresión 3D</strong> — filamento, resina, piezas de repuesto</li>
    <li><strong>Publicidad</strong> — Google Ads, Meta Ads, influencers (con factura)</li>
    <li><strong>Transporte</strong> — Uber, gasolina, estacionamiento (relacionado con el negocio)</li>
    <li><strong>Contador</strong> — los honorarios de tu contador son deducibles</li>
</ul>

<h2>Requisitos para que un gasto sea deducible</h2>
<ol>
    <li><strong>Tener factura (CFDI)</strong> — sin factura, no hay deducción. Pide factura por TODO lo que compres para el negocio</li>
    <li><strong>Pagar con medio bancarizado</strong> — transferencia, tarjeta, cheque. Los pagos en efectivo mayores a $2,000 no son deducibles</li>
    <li><strong>Que sea necesario para tu actividad</strong> — una cena con un cliente es deducible. Una cena de cumpleaños personal, no</li>
    <li><strong>Estar registrado en tu contabilidad</strong> — tener el gasto en tu registro</li>
</ol>

<h2>¿Cuándo me conviene dejar RESICO y deducir?</h2>
<p>Haz esta cuenta: si tus gastos deducibles son más del 60% de tus ingresos, probablemente te conviene cambiarte a Actividad Empresarial y deducir. Ejemplo:</p>
<ul>
    <li>Ingresos: $50,000/mes</li>
    <li>Gastos deducibles: $35,000/mes</li>
    <li>En RESICO pagarías: $50,000 × 2.5% = $1,250 de ISR</li>
    <li>En Act. Empresarial: ($50,000 - $35,000) × ~30% = $4,500 de ISR</li>
    <li>En este caso, RESICO sigue siendo mejor ($1,250 vs $4,500)</li>
</ul>
<p><strong>Moraleja:</strong> RESICO casi siempre gana para negocios pequeños. Consulta con tu contador antes de cambiar.</p>

<h2>Ejercicio</h2>
<p>Lista todos los gastos de tu negocio del último mes. ¿Cuáles tienen factura? ¿Cuáles pagaste en efectivo? Empieza a pedir factura por todo y a pagar con tarjeta o transferencia.</p>
',
            ],
            [
                'title' => 'Quiz final: Finanzas para tu negocio',
                'slug' => 'quiz-final',
                'type' => 'quiz',
                'sort_order' => 9,
                'pass_threshold' => 70,
                'quiz_data' => json_encode([
                    [
                        'question' => 'Vendes un servicio de $8,000. ¿Cuánto debes cobrar al cliente incluyendo IVA?',
                        'options' => ['$8,000', '$8,800', '$9,280', '$9,600'],
                        'correct' => 2,
                        'explanation' => 'IVA 16%: $8,000 × 1.16 = $9,280. Cobras $9,280 al cliente. Los $1,280 de IVA los debes al SAT.',
                    ],
                    [
                        'question' => 'Un gasto de $3,000 pagado en efectivo, ¿es deducible?',
                        'options' => ['Sí, siempre', 'No, los pagos en efectivo mayores a $2,000 no son deducibles', 'Solo si tienes factura', 'Depende del régimen'],
                        'correct' => 1,
                        'explanation' => 'Los pagos en efectivo mayores a $2,000 MXN no son deducibles, incluso si tienes factura. Siempre paga con transferencia o tarjeta.',
                    ],
                    [
                        'question' => 'Tu servicio te cuesta $4,000 producir. ¿Cuál es el precio mínimo con 40% de margen?',
                        'options' => ['$4,400', '$5,200', '$5,600', '$6,400'],
                        'correct' => 2,
                        'explanation' => 'Precio = Costo × (1 + Margen). $4,000 × 1.40 = $5,600. Si cobras menos de $5,600, tu margen es menor al 40%.',
                    ],
                    [
                        'question' => '¿Cuáles son los 3 números que debes revisar cada semana?',
                        'options' => [
                            'Ventas, impuestos, deudas',
                            'Ingresos, gastos, saldo disponible',
                            'Clientes, facturas, utilidad',
                            'IVA, ISR, retenciones'
                        ],
                        'correct' => 1,
                        'explanation' => 'Ingresos de la semana, gastos de la semana, y saldo disponible. 5 minutos cada lunes y ya estás mejor que el 80% de los emprendedores.',
                    ],
                    [
                        'question' => '¿Qué necesitas para poder emitir facturas electrónicas (CFDI)?',
                        'options' => [
                            'Solo tu RFC',
                            'RFC + e.firma + CSD + sistema de facturación',
                            'Solo un sistema de facturación',
                            'Un contador obligatoriamente'
                        ],
                        'correct' => 1,
                        'explanation' => 'Necesitas las 4 cosas: RFC activo, e.firma (FIEL), Certificado de Sello Digital (CSD), y un sistema de facturación (puede ser el gratuito del SAT).',
                    ],
                ]),
                'content_html' => '<p>Último quiz. Necesitas 70% para completar el curso.</p>',
            ],
            [
                'title' => 'Cierre: tu plan financiero de 30 días',
                'slug' => 'cierre',
                'type' => 'lecture',
                'sort_order' => 10,
                'content_html' => '
<h2>Lo que aprendiste</h2>
<ul>
    <li>✅ Por qué separar tu dinero personal del negocio es la base de todo</li>
    <li>✅ Cómo funciona el SAT, qué es RESICO, y cuánto pagas de impuestos</li>
    <li>✅ Cómo facturar electrónicamente (CFDI) y qué sistema usar</li>
    <li>✅ La fórmula para calcular tus precios basándote en costos reales</li>
    <li>✅ Herramientas para llevar el control (desde Google Sheets hasta un contador)</li>
    <li>✅ Qué gastos puedes deducir y cuándo te conviene hacerlo</li>
</ul>

<h2>Tu plan de 30 días</h2>
<ol>
    <li><strong>Semana 1:</strong> Abre una cuenta bancaria separada para el negocio. Empieza a registrar todos los movimientos en una hoja de cálculo.</li>
    <li><strong>Semana 2:</strong> Verifica tu RFC en sat.gob.mx. Si no tienes obligaciones fiscales, agenda el trámite. Si ya las tienes, confirma que estés en RESICO.</li>
    <li><strong>Semana 3:</strong> Calcula el costo real de tu servicio/producto principal con la fórmula. Ajusta tu precio si está por debajo del costo + 30% margen.</li>
    <li><strong>Semana 4:</strong> Emite tu primera factura (CFDI). Si aún no tienes CSD, tramita tu e.firma en oficina del SAT.</li>
</ol>

<h2>La regla de oro</h2>
<p><strong>Registra hoy, revisa el lunes, ajusta cada mes.</strong> No necesitas ser experto en finanzas. Necesitas ser constante. 5 minutos al día y 30 minutos cada lunes. Eso es todo.</p>

<h2>¿Qué sigue?</h2>
<p>Si quieres profundizar, en el curso <strong>Marketing Elite</strong> cubrimos LTV (Lifetime Value) y CAC (Costo de Adquisición de Cliente) — las métricas avanzadas que determinan si tu negocio puede escalar. Pero primero domina lo básico de este curso.</p>

<p><strong>Felicidades por completar Finanzas para tu Negocio.</strong> El emprendedor que controla su dinero es el que sobrevive.</p>
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
