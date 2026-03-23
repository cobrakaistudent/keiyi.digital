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

    {{-- COMPARATIVA COMPETIDORES --}}
    <div style="background:#111;border:1px solid #333;border-radius:12px;padding:24px;overflow-x:auto;">
        <h3 style="color:#fff;font-size:16px;font-weight:800;margin:0 0 16px;text-transform:uppercase;letter-spacing:2px;">Comparativa de Competidores</h3>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:2px solid #333;">
                    <th style="padding:10px;text-align:left;color:#888;">Plataforma</th>
                    <th style="padding:10px;text-align:right;color:#888;">Precio/Mes MXN</th>
                    <th style="padding:10px;text-align:right;color:#888;">Cursos</th>
                    <th style="padding:10px;text-align:left;color:#888;">Modelo</th>
                    <th style="padding:10px;text-align:left;color:#888;">Español</th>
                    <th style="padding:10px;text-align:left;color:#888;">Enfoque</th>
                    <th style="padding:10px;text-align:left;color:#888;">Debilidad</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom:1px solid #222;background:#0a2010;">
                    <td style="padding:10px;font-weight:700;color:#22c55e;">Keiyi Academy</td>
                    <td style="padding:10px;text-align:right;color:#22c55e;font-weight:700;">$49.99 - $199.99</td>
                    <td style="padding:10px;text-align:right;color:#fff;">{{ $totalCourses }}</td>
                    <td style="padding:10px;color:#fff;">Suscripcion</td>
                    <td style="padding:10px;color:#22c55e;">Nativo</td>
                    <td style="padding:10px;color:#fff;">IA + Marketing + Finanzas MX</td>
                    <td style="padding:10px;color:#888;">Catalogo pequeno (creciendo)</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Google Activate</td>
                    <td style="padding:10px;text-align:right;color:#22c55e;">GRATIS</td>
                    <td style="padding:10px;text-align:right;color:#fff;">~40</td>
                    <td style="padding:10px;color:#fff;">Free</td>
                    <td style="padding:10px;color:#22c55e;">Si</td>
                    <td style="padding:10px;color:#fff;">Google tools, basico</td>
                    <td style="padding:10px;color:#888;">Solo introductorio</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">HubSpot Academy</td>
                    <td style="padding:10px;text-align:right;color:#22c55e;">GRATIS</td>
                    <td style="padding:10px;text-align:right;color:#fff;">70+</td>
                    <td style="padding:10px;color:#fff;">Free</td>
                    <td style="padding:10px;color:#f59e0b;">Parcial</td>
                    <td style="padding:10px;color:#fff;">Marketing inbound</td>
                    <td style="padding:10px;color:#888;">Solo marketing, HubSpot-centric</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Udemy</td>
                    <td style="padding:10px;text-align:right;color:#fff;">$165</td>
                    <td style="padding:10px;text-align:right;color:#fff;">250K+</td>
                    <td style="padding:10px;color:#fff;">Sub + per-course</td>
                    <td style="padding:10px;color:#f59e0b;">Parcial</td>
                    <td style="padding:10px;color:#fff;">Todo (calidad variable)</td>
                    <td style="padding:10px;color:#888;">Sin curacion, inconsistente</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Domestika</td>
                    <td style="padding:10px;text-align:right;color:#fff;">$260 - $605</td>
                    <td style="padding:10px;text-align:right;color:#fff;">1,000+</td>
                    <td style="padding:10px;color:#fff;">Hybrid</td>
                    <td style="padding:10px;color:#22c55e;">Nativo</td>
                    <td style="padding:10px;color:#fff;">Creativos (diseno, foto)</td>
                    <td style="padding:10px;color:#888;">No tech/business/IA</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Platzi</td>
                    <td style="padding:10px;text-align:right;color:#dc2626;">$311 - $1,231</td>
                    <td style="padding:10px;text-align:right;color:#fff;">2,000+</td>
                    <td style="padding:10px;color:#fff;">Annual sub</td>
                    <td style="padding:10px;color:#22c55e;">Nativo</td>
                    <td style="padding:10px;color:#fff;">Tech, desarrollo, data</td>
                    <td style="padding:10px;color:#888;">Caro, lock-in anual</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Coursera</td>
                    <td style="padding:10px;text-align:right;color:#dc2626;">$670</td>
                    <td style="padding:10px;text-align:right;color:#fff;">10,000+</td>
                    <td style="padding:10px;color:#fff;">Freemium</td>
                    <td style="padding:10px;color:#dc2626;">Mostly EN</td>
                    <td style="padding:10px;color:#fff;">Universidades, certificados</td>
                    <td style="padding:10px;color:#888;">Caro, impersonal, bajo completion</td>
                </tr>
                <tr style="border-bottom:1px solid #222;">
                    <td style="padding:10px;color:#fff;">Capacitate (Slim)</td>
                    <td style="padding:10px;text-align:right;color:#22c55e;">GRATIS</td>
                    <td style="padding:10px;text-align:right;color:#fff;">337+</td>
                    <td style="padding:10px;color:#fff;">Free</td>
                    <td style="padding:10px;color:#22c55e;">Nativo</td>
                    <td style="padding:10px;color:#fff;">Vocacional, oficios</td>
                    <td style="padding:10px;color:#888;">Basico, sin IA/marketing</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- MERCADO --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:24px;">
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

    <div style="margin-top:16px;font-size:11px;color:#555;text-align:right;">
        Fuente: Investigacion de mercado Keiyi Digital — Marzo 2026. Datos de Platzi, Domestika, Coursera, Udemy, Google, HubSpot.
    </div>

</x-filament-panels::page>
