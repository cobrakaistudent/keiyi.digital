<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class ContabilidadPracticaSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::updateOrCreate(
            ['slug' => 'contabilidad-practica'],
            [
                'title' => 'Contabilidad Práctica (Parte 2)',
                'slug' => 'contabilidad-practica',
                'description' => 'El siguiente nivel después de Finanzas para tu Negocio. Aprende contabilidad real: partida doble, estados financieros, IVA contable, y cómo leer los números de tu negocio como un profesional. Basado en el programa de Contabilidad de la UNAM y las NIF vigentes.',
                'emoji' => '📊',
                'tag' => 'Finanzas Avanzado',
                'is_published' => true,
                'sort_order' => 7,
            ]
        );

        Lesson::where('course_id', $course->id)->delete();

        $lessons = [
            // ── LECCIÓN 1 ──────────────────────────────────
            [
                'title' => 'La ecuación fundamental: Activo = Pasivo + Capital',
                'slug' => 'ecuacion-fundamental',
                'type' => 'lecture',
                'sort_order' => 1,
                'content_html' => '
<h2>Prerrequisito</h2>
<p>Este curso es la <strong>Parte 2</strong> de nuestra serie de finanzas. Si no has tomado <em>Finanzas para tu Negocio (Parte 1)</em>, hazlo primero — ahí cubrimos SAT, RESICO, facturación y pricing.</p>

<h2>Todo negocio se resume en una ecuación</h2>
<p>No importa si eres un freelancer o una empresa de 500 personas. La contabilidad de cualquier negocio se reduce a una sola ecuación:</p>
<p style="font-size:1.3em;font-weight:700;background:#f0f0f0;padding:16px;border:2px solid #000;text-align:center;">
ACTIVO = PASIVO + CAPITAL
</p>

<h3>¿Qué significa cada parte?</h3>
<ul>
    <li><strong>Activo</strong> — Todo lo que TIENES. El dinero en el banco, tu equipo de cómputo, tu impresora 3D, lo que te deben los clientes, tu inventario de filamento.</li>
    <li><strong>Pasivo</strong> — Todo lo que DEBES. Lo que le debes a proveedores, préstamos bancarios, impuestos por pagar, el IVA que cobraste y que le debes al SAT.</li>
    <li><strong>Capital</strong> — Lo que VALE tu negocio. Es la diferencia entre lo que tienes y lo que debes. Si tienes $100,000 en activos y debes $30,000, tu capital es $70,000.</li>
</ul>

<h3>Ejemplo real: Keiyi Digital</h3>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr style="background:#000;color:#fff;"><th style="padding:10px;text-align:left;">ACTIVO (lo que tenemos)</th><th style="padding:10px;text-align:right;">Monto</th></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;">Banco (cuenta del negocio)</td><td style="padding:8px;text-align:right;">$25,000</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;">Mac Mini M2</td><td style="padding:8px;text-align:right;">$15,000</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;">Impresora 3D Bambu Lab</td><td style="padding:8px;text-align:right;">$12,000</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;">Inventario filamento (5 spools)</td><td style="padding:8px;text-align:right;">$2,250</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;">Clientes por cobrar</td><td style="padding:8px;text-align:right;">$8,000</td></tr>
<tr style="background:#f0f0f0;font-weight:700;"><td style="padding:8px;">TOTAL ACTIVO</td><td style="padding:8px;text-align:right;">$62,250</td></tr>
</table>

<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr style="background:#dc2626;color:#fff;"><th style="padding:10px;text-align:left;">PASIVO (lo que debemos)</th><th style="padding:10px;text-align:right;">Monto</th></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;">IVA por pagar al SAT</td><td style="padding:8px;text-align:right;">$4,800</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;">Hostinger (mensualidad pendiente)</td><td style="padding:8px;text-align:right;">$149</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;">Claude Pro (pendiente)</td><td style="padding:8px;text-align:right;">$1,787</td></tr>
<tr style="background:#fef2f2;font-weight:700;"><td style="padding:8px;">TOTAL PASIVO</td><td style="padding:8px;text-align:right;">$6,736</td></tr>
</table>

<p style="font-size:1.1em;font-weight:700;background:#dcfce7;padding:12px;border:2px solid #16a34a;text-align:center;">
CAPITAL = $62,250 - $6,736 = <strong>$55,514</strong>
</p>
<p>Eso es lo que vale el negocio hoy. Si mañana vendieras todo y pagaras todas las deudas, te quedarían $55,514.</p>

<h2>¿Por qué importa?</h2>
<p>Si tu Capital crece mes a mes, tu negocio está sano. Si baja, estás perdiendo valor — aunque tengas dinero en la cuenta (porque podrías tener más deudas que activos).</p>

<h2>Referencia</h2>
<p><em>Fuente: NIF A-1 (Estructura de las Normas de Información Financiera), Guía UNAM CCH Contabilidad con Informática — Módulo III.</em></p>

<h2>Ejercicio</h2>
<p>Haz tu propia tabla de Activo, Pasivo y Capital con los números reales de tu negocio. Lista todo lo que tienes (banco, equipo, inventario, lo que te deben) y todo lo que debes (proveedores, impuestos, suscripciones). Resta Pasivo del Activo. ¿Cuánto vale tu negocio hoy?</p>
',
            ],
            // ── LECCIÓN 2 ──────────────────────────────────
            [
                'title' => 'Partida doble: cada peso tiene dos lados',
                'slug' => 'partida-doble',
                'type' => 'lecture',
                'sort_order' => 2,
                'content_html' => '
<h2>La regla de oro de la contabilidad</h2>
<p>Desde el siglo XV (sí, hace más de 500 años), la contabilidad se basa en un principio simple: <strong>cada operación afecta al menos dos cuentas</strong>. Esto se llama <em>partida doble</em>.</p>
<p>Piensa en una balanza: siempre debe estar equilibrada. Si un lado sube, el otro también.</p>

<h2>Cargo y Abono</h2>
<ul>
    <li><strong>CARGO (Debe)</strong> — El lado izquierdo. Registra lo que entra o aumenta.</li>
    <li><strong>ABONO (Haber)</strong> — El lado derecho. Registra lo que sale o disminuye.</li>
</ul>
<p><strong>Regla:</strong> Total de Cargos = Total de Abonos. Siempre.</p>

<h2>Reglas del cargo y abono por tipo de cuenta</h2>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr style="background:#000;color:#fff;"><th style="padding:10px;">Tipo de cuenta</th><th style="padding:10px;">Aumenta con</th><th style="padding:10px;">Disminuye con</th></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;"><strong>Activo</strong> (lo que tienes)</td><td style="padding:8px;text-align:center;">CARGO</td><td style="padding:8px;text-align:center;">ABONO</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;"><strong>Pasivo</strong> (lo que debes)</td><td style="padding:8px;text-align:center;">ABONO</td><td style="padding:8px;text-align:center;">CARGO</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;"><strong>Capital</strong> (lo que vale)</td><td style="padding:8px;text-align:center;">ABONO</td><td style="padding:8px;text-align:center;">CARGO</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;"><strong>Ingresos</strong></td><td style="padding:8px;text-align:center;">ABONO</td><td style="padding:8px;text-align:center;">CARGO</td></tr>
<tr><td style="padding:8px;"><strong>Gastos</strong></td><td style="padding:8px;text-align:center;">CARGO</td><td style="padding:8px;text-align:center;">ABONO</td></tr>
</table>

<h2>Ejemplo: Vendes un servicio de marketing por $5,000 + IVA</h2>
<p>El cliente te paga $5,800 ($5,000 + $800 de IVA) por transferencia:</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;border:2px solid #000;">
<tr style="background:#000;color:#fff;"><th style="padding:8px;">Cuenta</th><th style="padding:8px;">Cargo (Debe)</th><th style="padding:8px;">Abono (Haber)</th></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;">Bancos (Activo ↑)</td><td style="padding:8px;text-align:right;">$5,800</td><td style="padding:8px;"></td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;">Ingresos por servicios (Ingreso ↑)</td><td style="padding:8px;"></td><td style="padding:8px;text-align:right;">$5,000</td></tr>
<tr><td style="padding:8px;">IVA trasladado (Pasivo ↑)</td><td style="padding:8px;"></td><td style="padding:8px;text-align:right;">$800</td></tr>
<tr style="background:#f0f0f0;font-weight:700;"><td style="padding:8px;">TOTAL</td><td style="padding:8px;text-align:right;">$5,800</td><td style="padding:8px;text-align:right;">$5,800</td></tr>
</table>
<p>La balanza cuadra: $5,800 = $5,800. Tu banco subió $5,800. Tus ingresos subieron $5,000. Y le debes $800 al SAT (IVA trasladado).</p>

<h2>Ejemplo: Compras filamento por $450 + IVA</h2>
<p>Pagas $522 ($450 + $72 de IVA) con tarjeta:</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;border:2px solid #000;">
<tr style="background:#000;color:#fff;"><th style="padding:8px;">Cuenta</th><th style="padding:8px;">Cargo (Debe)</th><th style="padding:8px;">Abono (Haber)</th></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;">Inventario filamento (Activo ↑)</td><td style="padding:8px;text-align:right;">$450</td><td style="padding:8px;"></td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;">IVA acreditable (Activo ↑)</td><td style="padding:8px;text-align:right;">$72</td><td style="padding:8px;"></td></tr>
<tr><td style="padding:8px;">Bancos (Activo ↓)</td><td style="padding:8px;"></td><td style="padding:8px;text-align:right;">$522</td></tr>
<tr style="background:#f0f0f0;font-weight:700;"><td style="padding:8px;">TOTAL</td><td style="padding:8px;text-align:right;">$522</td><td style="padding:8px;text-align:right;">$522</td></tr>
</table>

<h2>Referencia</h2>
<p><em>Teoría de la partida doble — Fray Luca Pacioli (1494). Guía UNAM CCH Módulo III, tema 1.2. NIF A-2 (Postulados básicos).</em></p>

<h2>Ejercicio</h2>
<p>Registra estas operaciones con cargo y abono: (1) Un cliente te paga $3,000 + IVA por transferencia. (2) Pagas $200 de internet con tarjeta. (3) Compras equipo de $8,000 + IVA con transferencia. Verifica que los totales cuadren en cada operación.</p>
',
            ],
            // ── LECCIÓN 3 ──────────────────────────────────
            [
                'title' => 'IVA contable: trasladado, acreditable y por pagar',
                'slug' => 'iva-contable',
                'type' => 'lecture',
                'sort_order' => 3,
                'content_html' => '
<h2>El IVA tiene 3 caras</h2>
<p>En la Parte 1 aprendiste que el IVA es 16% y que debes apartarlo. Ahora vamos a entender cómo funciona contablemente — porque esto es lo que determina cuánto le pagas al SAT cada mes.</p>

<h3>1. IVA Trasladado (lo que cobras)</h3>
<p>Cada vez que vendes algo, cobras 16% de IVA al cliente. Ese IVA se llama <strong>trasladado</strong> porque se lo "trasladas" al cliente. Es un <strong>pasivo</strong> — se lo debes al SAT.</p>

<h3>2. IVA Acreditable (lo que pagas)</h3>
<p>Cada vez que compras algo para tu negocio y te dan factura, pagas 16% de IVA. Ese IVA se llama <strong>acreditable</strong> porque puedes "acreditarlo" (restarlo) contra el IVA que cobraste. Es un <strong>activo</strong> — el SAT te lo "debe".</p>

<h3>3. IVA por Pagar (la diferencia)</h3>
<p style="font-size:1.1em;font-weight:700;background:#f0f0f0;padding:12px;border:2px solid #000;text-align:center;">
IVA por Pagar = IVA Trasladado − IVA Acreditable
</p>

<h2>Ejemplo mensual completo</h2>
<p>En marzo facturaste $30,000 en servicios y compraste $8,000 en insumos:</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr style="background:#000;color:#fff;"><th style="padding:10px;text-align:left;">Concepto</th><th style="padding:10px;text-align:right;">Base</th><th style="padding:10px;text-align:right;">IVA 16%</th></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;">Ventas del mes</td><td style="padding:8px;text-align:right;">$30,000</td><td style="padding:8px;text-align:right;color:#dc2626;">$4,800 (trasladado)</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:8px;">Compras del mes (filamento, hosting, software)</td><td style="padding:8px;text-align:right;">$8,000</td><td style="padding:8px;text-align:right;color:#16a34a;">$1,280 (acreditable)</td></tr>
<tr style="background:#fef3c7;font-weight:700;"><td style="padding:8px;">IVA POR PAGAR AL SAT</td><td style="padding:8px;"></td><td style="padding:8px;text-align:right;">$3,520</td></tr>
</table>
<p>Le debes $3,520 al SAT, no $4,800. Porque los $1,280 que pagaste de IVA en tus compras se restan.</p>

<h2>¿Y si mi IVA acreditable es mayor que el trasladado?</h2>
<p>Ejemplo: vendiste $5,000 (IVA $800) pero compraste equipo de $20,000 (IVA $3,200).</p>
<p>IVA por pagar = $800 - $3,200 = <strong>-$2,400 (IVA a favor)</strong></p>
<p>El SAT te debe $2,400. Puedes pedir devolución o acreditarlo contra IVA de meses siguientes.</p>

<h2>Requisitos para acreditar IVA</h2>
<ul>
    <li>Tener <strong>CFDI (factura)</strong> del gasto</li>
    <li>Que el gasto sea <strong>estrictamente necesario</strong> para tu actividad</li>
    <li>Que esté <strong>pagado</strong> (no basta con tener la factura, debe estar pagado)</li>
    <li>Pagar con medio <strong>bancarizado</strong> (transferencia/tarjeta) si es mayor a $2,000</li>
</ul>

<h2>Referencia</h2>
<p><em>Ley del IVA, artículos 1 (tasa general), 4 (acreditamiento), 5 (requisitos). Guía UNAM CCH Módulo III, tema 8. Reglamento del CFF.</em></p>

<h2>Ejercicio</h2>
<p>Con los datos de tu negocio del último mes, calcula: (1) Total de IVA trasladado (16% de todas tus ventas facturadas). (2) Total de IVA acreditable (16% de todas tus compras con factura). (3) IVA por pagar = (1) - (2). ¿Cuánto le debes al SAT este mes?</p>
',
            ],
            // ── QUIZ 1 ─────────────────────────────────────
            [
                'title' => 'Quiz: Contabilidad básica',
                'slug' => 'quiz-contabilidad-basica',
                'type' => 'quiz',
                'sort_order' => 4,
                'pass_threshold' => 70,
                'quiz_data' => json_encode([
                    [
                        'question' => 'Tu negocio tiene $80,000 en activos y $25,000 en pasivos. ¿Cuánto es tu Capital?',
                        'options' => ['$105,000', '$55,000', '$80,000', '$25,000'],
                        'correct' => 1,
                        'explanation' => 'Capital = Activo - Pasivo = $80,000 - $25,000 = $55,000. El Capital representa lo que realmente vale tu negocio.',
                    ],
                    [
                        'question' => 'Un cliente te paga $11,600 por un servicio ($10,000 + IVA). ¿Cuánto es IVA trasladado?',
                        'options' => ['$11,600', '$10,000', '$1,600', '$1,856'],
                        'correct' => 2,
                        'explanation' => 'IVA trasladado = $10,000 × 16% = $1,600. Es la parte que le debes al SAT, no es tu ingreso.',
                    ],
                    [
                        'question' => 'Cuando compras filamento con factura y pagas IVA, ese IVA es:',
                        'options' => ['IVA trasladado — se lo debes al SAT', 'IVA acreditable — se resta del IVA que cobraste', 'IVA por pagar — lo pagas inmediatamente', 'No aplica — el filamento no causa IVA'],
                        'correct' => 1,
                        'explanation' => 'El IVA que pagas en tus compras con factura es IVA acreditable. Se resta del IVA trasladado (el que cobraste) para calcular cuánto le debes al SAT.',
                    ],
                    [
                        'question' => 'Vendiste $20,000 (IVA trasladado $3,200) y compraste $15,000 en insumos (IVA acreditable $2,400). ¿Cuánto IVA le debes al SAT?',
                        'options' => ['$3,200', '$2,400', '$800', '$5,600'],
                        'correct' => 2,
                        'explanation' => 'IVA por pagar = Trasladado - Acreditable = $3,200 - $2,400 = $800. Solo pagas la diferencia.',
                    ],
                    [
                        'question' => 'En partida doble, cuando recibes un pago en tu cuenta de banco, ¿qué tipo de registro es?',
                        'options' => ['Abono a Bancos', 'Cargo a Bancos', 'Cargo a Ingresos', 'Abono a Capital'],
                        'correct' => 1,
                        'explanation' => 'Bancos es una cuenta de Activo. Los activos aumentan con CARGO. Cuando entra dinero al banco, se registra como Cargo a Bancos.',
                    ],
                    [
                        'question' => '¿Cuál es la ecuación fundamental de la contabilidad?',
                        'options' => ['Ingresos - Gastos = Utilidad', 'Activo = Pasivo + Capital', 'Cargo = Abono', 'Ventas - Costos = Margen'],
                        'correct' => 1,
                        'explanation' => 'Activo = Pasivo + Capital es la ecuación fundamental. Todo lo que tienes (Activo) se financia con lo que debes (Pasivo) más lo que vale tu negocio (Capital).',
                    ],
                ]),
                'content_html' => '<p>Evalúa tu comprensión de la ecuación contable, partida doble e IVA. Necesitas 70% para aprobar.</p>',
            ],
            // ── LECCIÓN 4 ──────────────────────────────────
            [
                'title' => 'El Balance General: la foto de tu negocio',
                'slug' => 'balance-general',
                'type' => 'lecture',
                'sort_order' => 5,
                'content_html' => '
<h2>¿Qué es el Balance General?</h2>
<p>El Balance General (o Estado de Situación Financiera) es una <strong>foto de tu negocio en un momento específico</strong>. Te dice: "Al día de hoy, esto es lo que tienes, esto es lo que debes, y esto es lo que vales."</p>
<p>Es el estado financiero más importante. Bancos, inversionistas y tú mismo lo usan para evaluar la salud del negocio.</p>

<h2>Estructura</h2>
<p>Se presenta en dos columnas que deben ser iguales:</p>

<table style="width:100%;border-collapse:collapse;margin:16px 0;border:3px solid #000;">
<tr><th style="background:#000;color:#fff;padding:12px;width:50%;text-align:center;">ACTIVO</th><th style="background:#000;color:#fff;padding:12px;width:50%;text-align:center;">PASIVO + CAPITAL</th></tr>
<tr><td style="padding:12px;vertical-align:top;border-right:2px solid #000;">
<p><strong>Activo Circulante</strong> (se convierte en dinero rápido):</p>
<ul>
<li>Caja y Bancos: $25,000</li>
<li>Clientes (cuentas por cobrar): $8,000</li>
<li>Inventario: $2,250</li>
<li>IVA acreditable: $1,280</li>
</ul>
<p><strong>Subtotal Circulante: $36,530</strong></p>
<br>
<p><strong>Activo Fijo</strong> (bienes de uso prolongado):</p>
<ul>
<li>Equipo de cómputo: $15,000</li>
<li>Maquinaria (impresora 3D): $12,000</li>
<li>(-) Depreciación acumulada: -$2,700</li>
</ul>
<p><strong>Subtotal Fijo: $24,300</strong></p>
<br>
<p style="font-weight:700;font-size:1.1em;border-top:2px solid #000;padding-top:8px;">TOTAL ACTIVO: $60,830</p>
</td>
<td style="padding:12px;vertical-align:top;">
<p><strong>Pasivo a Corto Plazo</strong> (debes pagar en menos de 1 año):</p>
<ul>
<li>Proveedores: $1,936</li>
<li>IVA por pagar: $3,520</li>
<li>ISR por pagar: $550</li>
</ul>
<p><strong>Subtotal Pasivo: $6,006</strong></p>
<br>
<p><strong>Capital Contable:</strong></p>
<ul>
<li>Capital social: $30,000</li>
<li>Utilidad del ejercicio: $24,824</li>
</ul>
<p><strong>Subtotal Capital: $54,824</strong></p>
<br>
<p style="font-weight:700;font-size:1.1em;border-top:2px solid #000;padding-top:8px;">TOTAL PASIVO + CAPITAL: $60,830</p>
</td></tr>
</table>
<p>Ambos lados suman $60,830. Si no cuadran, hay un error.</p>

<h2>Cómo leerlo</h2>
<ul>
<li><strong>Activo circulante alto + Pasivo bajo</strong> = Negocio líquido, puede pagar sus deudas</li>
<li><strong>Pasivo mayor que Activo</strong> = Problemas. Debes más de lo que tienes</li>
<li><strong>Capital creciendo</strong> = Tu negocio está generando valor</li>
</ul>

<h2>Referencia</h2>
<p><em>NIF B-6 (Estado de Situación Financiera). Guía UNAM CCH Módulo III, tema 5. Lara, E. "Primer curso de contabilidad" — Editorial Trillas.</em></p>

<h2>Ejercicio</h2>
<p>Con los datos de tu negocio, elabora un Balance General simplificado. Clasifica cada cuenta en Activo Circulante, Activo Fijo, Pasivo, o Capital. Verifica que la ecuación cuadre.</p>
',
            ],
            // ── LECCIÓN 5 ──────────────────────────────────
            [
                'title' => 'El Estado de Resultados: ¿ganaste o perdiste?',
                'slug' => 'estado-resultados',
                'type' => 'lecture',
                'sort_order' => 6,
                'content_html' => '
<h2>La película vs la foto</h2>
<p>Si el Balance General es una <strong>foto</strong> (tu negocio en un momento), el Estado de Resultados es una <strong>película</strong> (qué pasó durante un periodo). Te dice: "En este mes/trimestre/año, ¿ganaste o perdiste dinero?"</p>

<h2>Estructura</h2>
<table style="width:100%;border-collapse:collapse;margin:16px 0;border:3px solid #000;">
<tr style="background:#000;color:#fff;"><th colspan="2" style="padding:12px;text-align:center;">ESTADO DE RESULTADOS — Marzo 2026</th></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;">Ingresos por servicios de marketing</td><td style="padding:10px;text-align:right;">$30,000</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;">Ingresos por venta de cursos</td><td style="padding:10px;text-align:right;">$5,000</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;">Ingresos por impresión 3D</td><td style="padding:10px;text-align:right;">$3,000</td></tr>
<tr style="background:#dcfce7;font-weight:700;"><td style="padding:10px;">TOTAL INGRESOS</td><td style="padding:10px;text-align:right;">$38,000</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;">(-) Costo de filamento utilizado</td><td style="padding:10px;text-align:right;color:#dc2626;">-$900</td></tr>
<tr style="background:#f0f0f0;font-weight:700;"><td style="padding:10px;">UTILIDAD BRUTA</td><td style="padding:10px;text-align:right;">$37,100</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;">(-) Hosting + Dominio</td><td style="padding:10px;text-align:right;color:#dc2626;">-$170</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;">(-) Claude Pro + Gemini</td><td style="padding:10px;text-align:right;color:#dc2626;">-$2,148</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;">(-) Electricidad (impresora + Mac)</td><td style="padding:10px;text-align:right;color:#dc2626;">-$250</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;">(-) Internet</td><td style="padding:10px;text-align:right;color:#dc2626;">-$500</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;">(-) Depreciación del mes</td><td style="padding:10px;text-align:right;color:#dc2626;">-$225</td></tr>
<tr style="background:#f0f0f0;font-weight:700;"><td style="padding:10px;">TOTAL GASTOS DE OPERACIÓN</td><td style="padding:10px;text-align:right;color:#dc2626;">-$3,293</td></tr>
<tr style="background:#fef3c7;font-weight:700;font-size:1.1em;"><td style="padding:12px;">UTILIDAD ANTES DE IMPUESTOS</td><td style="padding:12px;text-align:right;">$33,807</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:10px;">(-) ISR RESICO (1.10%)</td><td style="padding:10px;text-align:right;color:#dc2626;">-$418</td></tr>
<tr style="background:#16a34a;color:#fff;font-weight:700;font-size:1.2em;"><td style="padding:12px;">UTILIDAD NETA</td><td style="padding:12px;text-align:right;">$33,389</td></tr>
</table>

<h2>Métricas clave</h2>
<ul>
<li><strong>Margen bruto:</strong> Utilidad Bruta ÷ Ingresos = $37,100 ÷ $38,000 = 97.6% (excelente para servicios digitales)</li>
<li><strong>Margen neto:</strong> Utilidad Neta ÷ Ingresos = $33,389 ÷ $38,000 = 87.9% (muy sano)</li>
<li>Si tu margen neto es menor al 10%, revisa tus gastos</li>
</ul>

<h2>Referencia</h2>
<p><em>NIF B-3 (Estado de Resultado Integral). Guía UNAM CCH Módulo III, tema 6. Moreno, J. "Contabilidad Básica" — McGraw-Hill.</em></p>

<h2>Ejercicio</h2>
<p>Elabora tu Estado de Resultados del último mes. Lista todos tus ingresos, resta los costos directos, resta los gastos de operación, resta el ISR. ¿Cuál fue tu utilidad neta? ¿Cuál es tu margen neto?</p>
',
            ],
            // ── LECCIÓN 6 ──────────────────────────────────
            [
                'title' => 'Balanza de comprobación: verifica que todo cuadre',
                'slug' => 'balanza-comprobacion',
                'type' => 'lecture',
                'sort_order' => 7,
                'content_html' => '
<h2>¿Qué es la Balanza de Comprobación?</h2>
<p>Es un resumen de TODAS las cuentas de tu negocio con sus cargos y abonos totales. Su único propósito es <strong>verificar que la partida doble se cumplió</strong> — que no hay errores.</p>
<p>Si los cargos totales no son iguales a los abonos totales, hay un error en algún registro.</p>

<h2>Estructura</h2>
<table style="width:100%;border-collapse:collapse;margin:16px 0;border:2px solid #000;">
<tr style="background:#000;color:#fff;"><th style="padding:8px;">Cuenta</th><th style="padding:8px;">Cargos</th><th style="padding:8px;">Abonos</th><th style="padding:8px;">Saldo Deudor</th><th style="padding:8px;">Saldo Acreedor</th></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:6px;">Bancos</td><td style="padding:6px;text-align:right;">$43,800</td><td style="padding:6px;text-align:right;">$18,800</td><td style="padding:6px;text-align:right;">$25,000</td><td style="padding:6px;"></td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:6px;">Clientes</td><td style="padding:6px;text-align:right;">$8,000</td><td style="padding:6px;text-align:right;">$0</td><td style="padding:6px;text-align:right;">$8,000</td><td style="padding:6px;"></td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:6px;">Equipo de cómputo</td><td style="padding:6px;text-align:right;">$15,000</td><td style="padding:6px;text-align:right;">$0</td><td style="padding:6px;text-align:right;">$15,000</td><td style="padding:6px;"></td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:6px;">IVA acreditable</td><td style="padding:6px;text-align:right;">$1,280</td><td style="padding:6px;text-align:right;">$0</td><td style="padding:6px;text-align:right;">$1,280</td><td style="padding:6px;"></td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:6px;">IVA trasladado</td><td style="padding:6px;text-align:right;">$0</td><td style="padding:6px;text-align:right;">$4,800</td><td style="padding:6px;"></td><td style="padding:6px;text-align:right;">$4,800</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:6px;">Ingresos</td><td style="padding:6px;text-align:right;">$0</td><td style="padding:6px;text-align:right;">$38,000</td><td style="padding:6px;"></td><td style="padding:6px;text-align:right;">$38,000</td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:6px;">Gastos de operación</td><td style="padding:6px;text-align:right;">$3,293</td><td style="padding:6px;text-align:right;">$0</td><td style="padding:6px;text-align:right;">$3,293</td><td style="padding:6px;"></td></tr>
<tr style="border-bottom:1px solid #ddd;"><td style="padding:6px;">Capital</td><td style="padding:6px;text-align:right;">$0</td><td style="padding:6px;text-align:right;">$30,000</td><td style="padding:6px;"></td><td style="padding:6px;text-align:right;">$30,000</td></tr>
<tr style="background:#f0f0f0;font-weight:700;"><td style="padding:8px;">TOTALES</td><td style="padding:8px;text-align:right;">$71,373</td><td style="padding:8px;text-align:right;">$91,600</td><td style="padding:8px;text-align:right;">$52,573</td><td style="padding:8px;text-align:right;">$72,800</td></tr>
</table>
<p><em>Nota: Este es un ejemplo simplificado. En la práctica, la balanza tiene más cuentas y los totales de saldos deudores = saldos acreedores.</em></p>

<h2>¿Cuándo se hace?</h2>
<ul>
<li><strong>Mensualmente</strong> — antes de presentar declaraciones al SAT</li>
<li><strong>Antes del cierre anual</strong> — para verificar que todo esté correcto antes de la declaración anual</li>
<li><strong>Obligatoria</strong> para contribuyentes que llevan contabilidad electrónica (Art. 28 CFF)</li>
</ul>

<h2>Referencia</h2>
<p><em>CFF Art. 28 (obligación de llevar contabilidad). Guía UNAM CCH Módulo III, tema 4. NIF A-2 (Postulados básicos — devengación contable).</em></p>

<h2>Ejercicio</h2>
<p>Con los registros de partida doble que hiciste en lecciones anteriores, elabora una balanza de comprobación. Suma todos los cargos y todos los abonos de cada cuenta. ¿Cuadran los totales?</p>
',
            ],
            // ── QUIZ FINAL ─────────────────────────────────
            [
                'title' => 'Quiz final: Contabilidad Práctica',
                'slug' => 'quiz-final',
                'type' => 'quiz',
                'sort_order' => 8,
                'pass_threshold' => 70,
                'quiz_data' => json_encode([
                    [
                        'question' => 'En un Estado de Resultados, la Utilidad Bruta se calcula como:',
                        'options' => ['Ingresos - Gastos de operación', 'Ingresos - Costos directos', 'Activo - Pasivo', 'Ingresos - Impuestos'],
                        'correct' => 1,
                        'explanation' => 'Utilidad Bruta = Ingresos - Costos directos (lo que gastas para producir lo que vendes). Después restas gastos de operación para obtener la utilidad operativa.',
                    ],
                    [
                        'question' => 'Vendiste $50,000 en servicios y tus gastos totales (incluyendo ISR) fueron $12,000. Tu margen neto es:',
                        'options' => ['24%', '76%', '12%', '50%'],
                        'correct' => 1,
                        'explanation' => 'Utilidad neta = $50,000 - $12,000 = $38,000. Margen neto = $38,000 ÷ $50,000 = 76%. Un margen neto del 76% es excelente.',
                    ],
                    [
                        'question' => 'Tu impresora 3D costó $12,000. Con depreciación del 10% anual, ¿cuánto vale después de 2 años?',
                        'options' => ['$12,000', '$9,600', '$10,800', '$2,400'],
                        'correct' => 1,
                        'explanation' => 'Depreciación anual = $12,000 × 10% = $1,200/año. En 2 años: $1,200 × 2 = $2,400 de depreciación acumulada. Valor: $12,000 - $2,400 = $9,600.',
                    ],
                    [
                        'question' => 'El Balance General muestra:',
                        'options' => ['Ingresos y gastos de un periodo', 'Activo, Pasivo y Capital en un momento específico', 'Los cargos y abonos de todas las cuentas', 'Las facturas emitidas en el mes'],
                        'correct' => 1,
                        'explanation' => 'El Balance General es una "foto" del negocio: muestra lo que tienes (Activo), lo que debes (Pasivo), y lo que vales (Capital) en un momento específico.',
                    ],
                    [
                        'question' => 'IVA trasladado del mes: $6,400. IVA acreditable del mes: $2,800. ¿Cuánto IVA pagas al SAT?',
                        'options' => ['$6,400', '$2,800', '$3,600', '$9,200'],
                        'correct' => 2,
                        'explanation' => 'IVA por pagar = Trasladado - Acreditable = $6,400 - $2,800 = $3,600. Siempre restas el IVA que pagaste en compras del IVA que cobraste en ventas.',
                    ],
                    [
                        'question' => '¿Para qué sirve la Balanza de Comprobación?',
                        'options' => ['Para calcular cuánto pagas de impuestos', 'Para verificar que los cargos totales sean iguales a los abonos totales', 'Para saber cuánto dinero tienes en el banco', 'Para generar facturas electrónicas'],
                        'correct' => 1,
                        'explanation' => 'La balanza de comprobación verifica que la partida doble se cumplió correctamente. Si cargos ≠ abonos, hay un error en la contabilidad.',
                    ],
                ]),
                'content_html' => '<p>Examen final de Contabilidad Práctica. Necesitas 70% para completar el curso. Los ejercicios son con números reales.</p>',
            ],
            // ── CIERRE ──────────────────────────────────────
            [
                'title' => 'Cierre: ya sabes leer los números de tu negocio',
                'slug' => 'cierre',
                'type' => 'lecture',
                'sort_order' => 9,
                'content_html' => '
<h2>Lo que aprendiste</h2>
<ul>
<li>✅ La ecuación fundamental: Activo = Pasivo + Capital</li>
<li>✅ Partida doble: cada operación tiene cargo y abono</li>
<li>✅ IVA contable: trasladado (cobras) - acreditable (pagas) = por pagar (SAT)</li>
<li>✅ Balance General: la foto de tu negocio (qué tienes, qué debes, qué vales)</li>
<li>✅ Estado de Resultados: la película del periodo (ganaste o perdiste)</li>
<li>✅ Balanza de comprobación: verificación de que todo cuadra</li>
</ul>

<h2>Con Finanzas Parte 1 + Parte 2 ya puedes:</h2>
<ol>
<li>Separar tu dinero personal del negocio</li>
<li>Elegir el régimen fiscal correcto (RESICO)</li>
<li>Facturar electrónicamente (CFDI 4.0)</li>
<li>Calcular precios basados en costos reales</li>
<li>Saber cuánto IVA le debes al SAT cada mes</li>
<li>Elaborar un Balance General y un Estado de Resultados</li>
<li>Detectar si tu negocio está creciendo o perdiendo valor</li>
</ol>

<h2>¿Necesitas un contador?</h2>
<p>Con lo que aprendiste, puedes manejar las finanzas básicas de tu negocio. Pero cuando factures más de $50,000/mes, te recomendamos contratar un contador. No para que él lleve tus números (tú ya sabes), sino para optimizar impuestos, presentar declaraciones sin errores, y protegerte legalmente.</p>

<h2>Bibliografía del curso</h2>
<ul>
<li>Ley del ISR — Última reforma DOF 01-04-2024</li>
<li>Ley del IVA — Vigente</li>
<li>Código Fiscal de la Federación y su Reglamento</li>
<li>NIF A-1 (Estructura de las NIF), NIF A-2 (Postulados básicos)</li>
<li>NIF B-3 (Estado de Resultado Integral), NIF B-6 (Estado de Situación Financiera)</li>
<li>Guía UNAM CCH — Estudio Técnico Especializado: Contabilidad con Informática</li>
<li>Lara, E. "Primer curso de contabilidad" — Editorial Trillas</li>
<li>Moreno, J. "Contabilidad Básica" — McGraw-Hill</li>
<li>Hatzacorsian, V. "Fundamentos de contabilidad" — ECAFSA</li>
</ul>

<p><strong>Felicidades por completar Contabilidad Práctica.</strong> Ya hablas el idioma de los números. Eso te da una ventaja sobre el 90% de los emprendedores.</p>
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
