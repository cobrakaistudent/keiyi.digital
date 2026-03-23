<x-filament-panels::page>

    {{-- KEIYI STATS --}}
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:24px;">
        <div style="background:#1a1a2e;border:1px solid #333;border-radius:8px;padding:16px;">
            <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:1px;">Cursos</div>
            <div style="font-size:28px;font-weight:800;color:#fff;">{{ $totalCourses }}</div>
        </div>
        <div style="background:#1a1a2e;border:1px solid #333;border-radius:8px;padding:16px;">
            <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:1px;">Lecciones</div>
            <div style="font-size:28px;font-weight:800;color:#fff;">{{ $totalLessons }}</div>
        </div>
        <div style="background:#1a1a2e;border:1px solid #333;border-radius:8px;padding:16px;">
            <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:1px;">Alumnos</div>
            <div style="font-size:28px;font-weight:800;color:#fff;">{{ $approvedStudents }}<span style="font-size:14px;color:#666;">/{{ $totalStudents }}</span></div>
        </div>
        <div style="background:#1a1a2e;border:1px solid #333;border-radius:8px;padding:16px;">
            <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:1px;">Costo/Mes</div>
            <div style="font-size:28px;font-weight:800;color:#f59e0b;">${{ number_format($monthlyCost, 0) }}</div>
        </div>
        <div style="background:#1a1a2e;border:1px solid #333;border-radius:8px;padding:16px;">
            <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:1px;">Breakeven</div>
            <div style="font-size:28px;font-weight:800;color:#22c55e;">{{ $breakeven_general }} <span style="font-size:12px;color:#666;">alumnos</span></div>
        </div>
    </div>

    {{-- PRICING KEIYI --}}
    <div style="background:#0f172a;border:2px solid #f59e0b;border-radius:12px;padding:24px;margin-bottom:24px;">
        <h3 style="color:#f59e0b;font-size:16px;font-weight:800;margin:0 0 16px;text-transform:uppercase;letter-spacing:2px;">Pricing Keiyi Academy</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
            <div style="border:1px solid #333;border-radius:8px;padding:16px;text-align:center;">
                <div style="font-size:12px;color:#888;">GRATIS</div>
                <div style="font-size:32px;font-weight:800;color:#fff;">$0</div>
                <div style="font-size:11px;color:#666;">Intro a la IA (1 curso)</div>
            </div>
            <div style="border:2px solid #22c55e;border-radius:8px;padding:16px;text-align:center;background:#0a2010;">
                <div style="font-size:12px;color:#22c55e;font-weight:700;">ESTUDIANTE (.edu)</div>
                <div style="font-size:32px;font-weight:800;color:#fff;">$49<span style="font-size:16px;">.99</span></div>
                <div style="font-size:11px;color:#666;">Todos los cursos / mes</div>
            </div>
            <div style="border:2px solid #f59e0b;border-radius:8px;padding:16px;text-align:center;background:#1a1500;">
                <div style="font-size:12px;color:#f59e0b;font-weight:700;">GENERAL</div>
                <div style="font-size:32px;font-weight:800;color:#fff;">$199<span style="font-size:16px;">.99</span></div>
                <div style="font-size:11px;color:#666;">Todos los cursos / mes</div>
            </div>
        </div>
    </div>

    {{-- COMPARATIVA CONSUMIDOR --}}
    <div style="background:#111;border:1px solid #333;border-radius:12px;padding:24px;overflow-x:auto;margin-bottom:24px;">
        <h3 style="color:#fff;font-size:16px;font-weight:800;margin:0 0 16px;text-transform:uppercase;letter-spacing:2px;">Competidores — Plataformas de Cursos (Consumidor)</h3>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:2px solid #333;">
                    <th style="padding:10px;text-align:left;color:#888;">Plataforma</th>
                    <th style="padding:10px;text-align:right;color:#888;">Precio/Mes</th>
                    <th style="padding:10px;text-align:left;color:#888;">Modelo</th>
                    <th style="padding:10px;text-align:left;color:#888;">Enfoque</th>
                    <th style="padding:10px;text-align:left;color:#888;">Grupos</th>
                    <th style="padding:10px;text-align:left;color:#888;">Debilidad</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom:1px solid #222;background:#0a2010;">
                    <td style="padding:10px;font-weight:700;color:#22c55e;">Keiyi Academy</td>
                    <td style="padding:10px;text-align:right;color:#22c55e;font-weight:700;">$49 - $199</td>
                    <td style="padding:10px;color:#fff;">Suscripcion</td>
                    <td style="padding:10px;color:#fff;">IA + Marketing + Finanzas MX</td>
                    <td style="padding:10px;color:#22c55e;font-weight:700;">Si</td>
                    <td style="padding:10px;color:#888;">Catalogo pequeno</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Platzi</td>
                    <td style="padding:10px;text-align:right;color:#dc2626;">$311 - $1,231</td>
                    <td style="padding:10px;color:#fff;">Anual</td>
                    <td style="padding:10px;color:#fff;">Tech, desarrollo</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#888;">Caro, lock-in anual</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Domestika</td>
                    <td style="padding:10px;text-align:right;color:#fff;">$260 - $605</td>
                    <td style="padding:10px;color:#fff;">Hybrid</td>
                    <td style="padding:10px;color:#fff;">Creativos</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#888;">No tech/business</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Coursera</td>
                    <td style="padding:10px;text-align:right;color:#dc2626;">$670</td>
                    <td style="padding:10px;color:#fff;">Freemium</td>
                    <td style="padding:10px;color:#fff;">Universidades</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#888;">Impersonal, bajo completion</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Udemy</td>
                    <td style="padding:10px;text-align:right;color:#fff;">$165</td>
                    <td style="padding:10px;color:#fff;">Per-course</td>
                    <td style="padding:10px;color:#fff;">Todo</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#888;">Sin curacion</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Google Activate</td>
                    <td style="padding:10px;text-align:right;color:#22c55e;">GRATIS</td>
                    <td style="padding:10px;color:#fff;">Free</td>
                    <td style="padding:10px;color:#fff;">Google tools</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#888;">Solo introductorio</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- COMPARATIVA LMS / PLATAFORMAS PARA PROFESORES --}}
    <div style="background:#111;border:2px solid #8b5cf6;border-radius:12px;padding:24px;overflow-x:auto;margin-bottom:24px;">
        <h3 style="color:#8b5cf6;font-size:16px;font-weight:800;margin:0 0 4px;text-transform:uppercase;letter-spacing:2px;">Plataformas LMS — Para Profesores</h3>
        <p style="color:#888;font-size:12px;margin:0 0 16px;">Investigacion Marzo 2026 — Plataformas que permiten a profesores crear/gestionar cursos grupales</p>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:2px solid #333;">
                    <th style="padding:10px;text-align:left;color:#888;">Plataforma</th>
                    <th style="padding:10px;text-align:right;color:#888;">Precio</th>
                    <th style="padding:10px;text-align:left;color:#888;">Grupos</th>
                    <th style="padding:10px;text-align:left;color:#888;">LATAM</th>
                    <th style="padding:10px;text-align:left;color:#888;">Agencia</th>
                    <th style="padding:10px;text-align:left;color:#888;">Diferenciador</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom:1px solid #222;background:#0a2010;">
                    <td style="padding:10px;font-weight:700;color:#22c55e;">Keiyi Digital</td>
                    <td style="padding:10px;text-align:right;color:#22c55e;font-weight:700;">$49 - $199</td>
                    <td style="padding:10px;color:#22c55e;font-weight:700;">Si (5-25)</td>
                    <td style="padding:10px;color:#22c55e;">MX nativo</td>
                    <td style="padding:10px;color:#22c55e;">Si</td>
                    <td style="padding:10px;color:#fff;">LMS + agencia + profesor inscribe alumnos</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Teachable</td>
                    <td style="padding:10px;text-align:right;color:#fff;">$39/mo + 7.5%</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#888;">Apps nativas, afiliados</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Thinkific</td>
                    <td style="padding:10px;text-align:right;color:#22c55e;">Free tier</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#888;">Mejor evaluaciones, gratis</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Kajabi</td>
                    <td style="padding:10px;text-align:right;color:#dc2626;">$69/mo</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#f59e0b;">Parcial</td>
                    <td style="padding:10px;color:#888;">Email + funnels integrados</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Hotmart</td>
                    <td style="padding:10px;text-align:right;color:#22c55e;">Free + 9.9%</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#22c55e;">LATAM</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#888;">Red de afiliados LATAM, OXXO</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Moodle</td>
                    <td style="padding:10px;text-align:right;color:#22c55e;">GRATIS</td>
                    <td style="padding:10px;color:#22c55e;">Si</td>
                    <td style="padding:10px;color:#22c55e;">MX #1</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#888;">Open source, complejo, feo</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Chamilo</td>
                    <td style="padding:10px;text-align:right;color:#22c55e;">GRATIS</td>
                    <td style="padding:10px;color:#22c55e;">Si</td>
                    <td style="padding:10px;color:#22c55e;">MX popular</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#888;">18M users, open source</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Coursebox AI</td>
                    <td style="padding:10px;text-align:right;color:#22c55e;">Free - $99/mo</td>
                    <td style="padding:10px;color:#f59e0b;">Limitado</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#888;">IA genera cursos de documentos</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Disco</td>
                    <td style="padding:10px;text-align:right;color:#dc2626;">$75 - $499/mo</td>
                    <td style="padding:10px;color:#22c55e;">Si</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#888;">IA + cohortes, caro</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Sabionet</td>
                    <td style="padding:10px;text-align:right;color:#888;">No publicado</td>
                    <td style="padding:10px;color:#f59e0b;">Parcial</td>
                    <td style="padding:10px;color:#22c55e;">LATAM</td>
                    <td style="padding:10px;color:#dc2626;">No</td>
                    <td style="padding:10px;color:#888;">Academias LATAM, MercadoPago</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- CONCLUSION --}}
    <div style="background:#0f172a;border:2px solid #22c55e;border-radius:12px;padding:24px;margin-bottom:24px;">
        <h3 style="color:#22c55e;font-size:14px;font-weight:800;margin:0 0 12px;text-transform:uppercase;letter-spacing:2px;">Whitespace Identificado</h3>
        <p style="color:#fff;font-size:14px;line-height:1.7;margin:0;">
            <strong>Ninguna plataforma en LATAM combina las 4 cosas que Keiyi ofrece:</strong>
            LMS con gestion grupal por profesor + cursos con quizzes/interactivos + agencia de marketing/consultoria + precios accesibles para grupos de 5-25 alumnos.
            Los LMS institucionales (Moodle, Chamilo) tienen grupos pero son feos y sin monetizacion.
            Los creadores (Teachable, Hotmart) monetizan pero no tienen grupos.
            Los cohort platforms (Disco, Maven) tienen grupos pero son caros ($75-499/mo).
        </p>
    </div>

    {{-- MERCADO --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
        <div style="background:#111;border:1px solid #333;border-radius:12px;padding:24px;">
            <h3 style="color:#fff;font-size:14px;font-weight:800;margin:0 0 12px;">Mercado EdTech Mexico</h3>
            <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;">
                <div style="display:flex;justify-content:space-between;"><span style="color:#888;">Mercado 2024</span><span style="color:#fff;font-weight:700;">$4.4B USD</span></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:#888;">Proyeccion 2033</span><span style="color:#22c55e;font-weight:700;">$14.4B USD</span></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:#888;">CAGR</span><span style="color:#fff;font-weight:700;">12.8%</span></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:#888;">Online learning MX 2025</span><span style="color:#fff;font-weight:700;">$723M USD</span></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:#888;">ARPU Mexico</span><span style="color:#fff;font-weight:700;">~$521 MXN/usuario</span></div>
            </div>
        </div>
        <div style="background:#111;border:1px solid #333;border-radius:12px;padding:24px;">
            <h3 style="color:#fff;font-size:14px;font-weight:800;margin:0 0 12px;">Tendencias en Demanda 2026</h3>
            <div style="display:flex;flex-direction:column;gap:6px;font-size:13px;">
                <div><span style="color:#f59e0b;">1.</span> <span style="color:#fff;">IA / Herramientas IA</span> <span style="color:#22c55e;font-size:11px;">+148%</span></div>
                <div><span style="color:#f59e0b;">2.</span> <span style="color:#fff;">Marketing Digital con IA</span></div>
                <div><span style="color:#f59e0b;">3.</span> <span style="color:#fff;">Data Science / Analytics</span></div>
                <div><span style="color:#f59e0b;">4.</span> <span style="color:#fff;">Productividad + Automatizacion</span></div>
                <div><span style="color:#f59e0b;">5.</span> <span style="color:#fff;">Emprendimiento Digital</span></div>
                <div><span style="color:#f59e0b;">6.</span> <span style="color:#fff;">Content Creation / Social Media</span></div>
                <div><span style="color:#f59e0b;">7.</span> <span style="color:#fff;">No-code / Low-code</span></div>
            </div>
        </div>
    </div>

    <div style="margin-bottom:32px;font-size:11px;color:#555;text-align:right;">
        Fuente: Investigacion de mercado Keiyi Digital — Marzo 2026. Datos recopilados de sitios oficiales de cada plataforma.
    </div>

    {{-- SOLICITAR ESTUDIO DE MERCADO --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
        <div style="background:#111;border:2px solid #f59e0b;border-radius:12px;padding:24px;">
            <h3 style="color:#f59e0b;font-size:14px;font-weight:800;margin:0 0 16px;text-transform:uppercase;letter-spacing:2px;">Solicitar Estudio de Mercado</h3>
            <form wire:submit="submitRequest">
                {{ $this->requestForm }}
                <div style="margin-top:16px;">
                    <x-filament::button type="submit" color="warning">
                        Enviar solicitud
                    </x-filament::button>
                </div>
            </form>
        </div>

        <div style="background:#111;border:1px solid #333;border-radius:12px;padding:24px;">
            <h3 style="color:#fff;font-size:14px;font-weight:800;margin:0 0 16px;text-transform:uppercase;letter-spacing:2px;">Solicitudes</h3>
            @if($requests->isEmpty())
                <p style="color:#555;font-size:13px;text-align:center;padding:20px;">No hay solicitudes aun. Usa el formulario para proponer un estudio.</p>
            @else
                <div style="display:flex;flex-direction:column;gap:12px;">
                    @foreach($requests as $req)
                        <div style="border:1px solid #333;border-radius:8px;padding:14px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                                <span style="color:#fff;font-weight:700;font-size:14px;">{{ $req->title }}</span>
                                <span style="font-size:11px;padding:2px 8px;border-radius:10px;font-weight:700;
                                    {{ $req->status === 'completed' ? 'background:#dcfce7;color:#16a34a;' :
                                       ($req->status === 'in_progress' ? 'background:#dbeafe;color:#2563eb;' :
                                       ($req->status === 'cancelled' ? 'background:#fef2f2;color:#dc2626;' :
                                       'background:#fef3c7;color:#d97706;')) }}">
                                    {{ $req->status === 'completed' ? 'Completado' :
                                       ($req->status === 'in_progress' ? 'En progreso' :
                                       ($req->status === 'cancelled' ? 'Cancelado' : 'Pendiente')) }}
                                </span>
                            </div>
                            <p style="color:#888;font-size:12px;margin:0;line-height:1.5;">{{ Str::limit($req->purpose, 120) }}</p>
                            @if($req->target_market)
                                <div style="margin-top:6px;font-size:11px;color:#666;">Mercado: {{ $req->target_market }}</div>
                            @endif
                            <div style="margin-top:4px;font-size:11px;color:#555;">{{ $req->created_at->diffForHumans() }}
                                @if($req->priority === 'urgent') <span style="color:#dc2626;font-weight:700;">URGENTE</span>
                                @elseif($req->priority === 'high') <span style="color:#f59e0b;font-weight:700;">ALTA</span>
                                @endif
                            </div>
                            @if($req->findings)
                                <div style="margin-top:8px;padding-top:8px;border-top:1px solid #333;">
                                    <div style="font-size:11px;color:#22c55e;font-weight:700;margin-bottom:4px;">HALLAZGOS:</div>
                                    <p style="color:#ccc;font-size:12px;margin:0;line-height:1.5;">{{ Str::limit($req->findings, 200) }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</x-filament-panels::page>
