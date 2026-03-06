require('dotenv').config();
const express = require('express');
const cors = require('cors');
const { exec } = require('child_process');
const path = require('path');
const fs = require('fs');

const app = express();
const PORT = process.env.PORT || 4000;

app.use(cors({ origin: ['http://localhost:4000', 'http://127.0.0.1:4000'] }));
app.use(express.json());
// Servimos el dashboard estático
app.use(express.static(path.join(__dirname, 'public')));

// ============================================================================
// CONEXIÓN SEGURA "API-LESS" (SSH / Local Exec directo a Laravel)
// ============================================================================
const useSSH = false; // Cambiar a true cuando la BD de Hostinger esté completamente sincronizada
const SSH_CMD = 'ssh -p 65002 -i /Users/anuarlv/.ssh/id_rsa u129237724@185.212.70.24 "cd domains/keiyi.digital/laravel_app && ';
// Usamos una ruta relativa al directorio raíz del proyecto
const PROJECT_ROOT = path.resolve(__dirname, '../');

function runPHP(code) {
    return new Promise((resolve, reject) => {
        // Plantilla segura para ejecutar código sin el ruido textual de Tinker
        const phpTemplate = `require 'vendor/autoload.php'; \\$app = require_once 'bootstrap/app.php'; \\$kernel = \\$app->make(Illuminate\\Contracts\\Console\\Kernel::class); \\$kernel->bootstrap(); ${code}`;

        let command = `cd "${PROJECT_ROOT}" && php -r "${phpTemplate}"`;
        if (useSSH) {
            command = `${SSH_CMD} php -r \\"${phpTemplate}\\""`;
        }

        exec(command, { maxBuffer: 1024 * 1024 * 5 }, (error, stdout, stderr) => {
            if (error) {
                const errorMsg = stderr || error.message;
                console.error('[Exec Error]', errorMsg);
                return reject(new Error(errorMsg));
            }
            try {
                const jsonMatch = stdout.match(/\[.*\]|\{.*\}/s);
                if (!jsonMatch) {
                    return resolve({ success: false, error: 'No se recibió un JSON válido de PHP.', raw: stdout });
                }
                resolve(JSON.parse(jsonMatch[0]));
            } catch (e) {
                resolve({ success: false, error: 'Error parseando JSON de PHP.', raw: stdout });
            }
        });
    });
}

// ============================================================================
// ENDPOINTS DEL COMMAND CENTER AL BÚNKER LA MAC (FRONTEND VÍA DASHBOARD)
// ============================================================================

// 1. Radar de Fuentes
app.get('/api/scout-sources', async (req, res) => {
    try {
        const data = await runPHP(`echo json_encode(\\App\\Models\\ScoutSource::where('is_active', true)->get());`);
        res.json({ data });
    } catch (error) {
        res.status(500).json({ success: false, error: error.message });
    }
});

// 1.5 Agregar nueva fuente manualmente al Backend
app.post('/api/scout-sources', async (req, res) => {
    console.log('[Keiyi CC] Recibida solicitud para nueva fuente:', req.body);
    try {
        let { name, url, type } = req.body;
        
        if (!url) return res.status(400).json({ success: false, error: 'La URL es obligatoria.' });
        if (!type) type = 'web'; // Default por seguridad

        // Si no hay nombre, extraemos el dominio de la URL de forma robusta
        if (!name || name.trim() === '') {
            try {
                let cleanUrl = url.trim();
                if (!cleanUrl.startsWith('http')) cleanUrl = 'https://' + cleanUrl;
                const urlObj = new URL(cleanUrl);
                name = urlObj.hostname.replace('www.', '');
            } catch (e) {
                name = url.split('/')[0] || 'Fuente Desconocida';
            }
        }

        console.log(`[Keiyi CC] Procesando fuente: ${name} (${type}) -> ${url}`);

        const safeName = name.replace(/'/g, "\\'");
        const safeUrl = url.replace(/'/g, "\\'");
        const safeType = type === 'rss' ? 'rss' : 'web';

        const code = `
            \\$s = new \\App\\Models\\ScoutSource();
            \\$s->name = '${safeName}';
            \\$s->url = '${safeUrl}';
            \\$s->type = '${safeType}';
            \\$s->is_active = true;
            \\$s->relevance_score = 90;
            if (\\$s->save()) {
                echo json_encode(['success' => true, 'name' => '${safeName}']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al guardar en BD Eloquent']);
            }
        `;
        const data = await runPHP(code);
        console.log('[Keiyi CC] Resultado de inserción:', data);
        res.json(data);
    } catch (error) {
        console.error('[Keiyi CC Error]', error.message);
        res.status(500).json({ success: false, error: 'Error interno: ' + error.message });
    }
});

// 2. Alumnos Pendientes de Aprobación
app.get('/api/proxy/users/pending', async (req, res) => {
    try {
        const query = `\\App\\Models\\User::where('approval_status', 'pending')->where('role', 'student')->select('id', 'name', 'email', 'created_at')->orderBy('created_at', 'desc')->get()`;
        const data = await runPHP(`echo json_encode(${query});`);
        res.json({ data });
    } catch (error) {
        res.status(500).json({ success: false, error: 'Hostinger SSH Inaccesible.' });
    }
});

// 3. Aprobar/Rechazar Alumnos
app.post('/api/proxy/users/:id/status', async (req, res) => {
    try {
        const { status } = req.body;
        const id = parseInt(req.params.id);
        const cleanStatus = status === 'approved' ? 'approved' : 'rejected';

        const code = `\\$u = \\App\\Models\\User::find(${id}); if(\\$u){ \\$u->approval_status = '${cleanStatus}'; \\$u->save(); echo json_encode(['success'=>true]); } else { echo json_encode(['success'=>false, 'error'=>'User not found']); }`;
        const data = await runPHP(code);
        res.json(data);
    } catch (error) {
        res.status(500).json({ success: false, error: 'Fallo al actualizar vía SSH.' });
    }
});

// 4. Ejecutar el Inteligente Keiyi Scout (Araña Python)
app.post('/api/run-scout', (req, res) => {
    const scriptPath = path.resolve(__dirname, '../agent/scout.py');
    console.log(`[Keiyi CC] Ejecutando IA Local. Llamando a python3 ${scriptPath}`);
    exec(`python3 ${scriptPath}`, (error, stdout, stderr) => {
        if (error) {
            console.error(`[Keiyi CC] Fallo: ${error.message}`);
            return res.status(500).json({ success: false, error: error.message, logs: stderr });
        }
        res.json({ success: true, logs: stdout });
    });
});

// 5. Fábrica de Reportes Ejecutivos (Extracción SSH de Insights)
app.get('/api/generate-report', async (req, res) => {
    console.log(`[Reporte] Extrayendo insights en crudo vía SSH/Local...`);
    try {
        const data = await runPHP(`echo json_encode(\\App\\Models\\ScoutInsight::latest()->get());`);
        // Note: data is now an Array of insights not nested under data.data
        const insights = data || [];

        if (!insights.length) {
            return res.status(404).json({ error: 'No hay insights disponibles. Ejecuta el Scout AI primero.' });
        }

        console.log(`[Reporte] Insight recibido vía SSH. Generando Plantilla Visual PDF...`);
        res.setHeader('Content-Type', 'text/html; charset=utf-8');
        res.send(buildReportHTML(insights[0]));

    } catch (err) {
        console.error(`[Reporte] Error: ${err.message}`);
        res.status(500).json({ error: err.message });
    }
});

// 6. Eliminar fuente del radar
app.delete('/api/scout-sources/:id', async (req, res) => {
    try {
        const id = parseInt(req.params.id);
        const code = `\\$s = \\App\\Models\\ScoutSource::find(${id}); if(\\$s){ \\$s->delete(); echo json_encode(['success'=>true]); } else { echo json_encode(['success'=>false,'error'=>'Not found']); }`;
        const data = await runPHP(code);
        res.json(data);
    } catch (error) {
        res.status(500).json({ success: false, error: 'Fallo al eliminar fuente.' });
    }
});

// 7. Banear fuente (is_active = false, permanente)
app.post('/api/scout-sources/:id/ban', async (req, res) => {
    try {
        const id = parseInt(req.params.id);
        const code = `\\$s = \\App\\Models\\ScoutSource::find(${id}); if(\\$s){ \\$s->is_active = false; \\$s->save(); echo json_encode(['success'=>true]); } else { echo json_encode(['success'=>false,'error'=>'Not found']); }`;
        const data = await runPHP(code);
        res.json(data);
    } catch (error) {
        res.status(500).json({ success: false, error: 'Fallo al banear fuente.' });
    }
});

// 7.5 Prueba de Conexión Manual (SSH + SCP)
app.get('/api/check-hostinger', (req, res) => {
    const start = Date.now();
    const sshCheck = `ssh -p 65002 -i /Users/anuarlv/.ssh/id_rsa -o ConnectTimeout=5 -o BatchMode=yes u129237724@185.212.70.24 "exit"`;
    const scpCheck = `scp -P 65002 -i /Users/anuarlv/.ssh/id_rsa -o ConnectTimeout=5 -o BatchMode=yes u129237724@185.212.70.24:domains/keiyi.digital/laravel_app/composer.json /tmp/test_scp_${start} && rm /tmp/test_scp_${start}`;

    // Ambas pruebas corren en PARALELO — tiempo total = MAX(ssh, scp) en vez de ssh + scp
    const runCheck = (cmd) => new Promise((resolve) => {
        exec(cmd, { timeout: 8000 }, (err) => resolve(!err));
    });

    Promise.all([runCheck(sshCheck), runCheck(scpCheck)]).then(([sshStatus, scpStatus]) => {
        res.json({
            success: true,
            ssh: sshStatus,
            scp: scpStatus,
            timestamp: new Date().toLocaleString('es-MX'),
            latency: `${Date.now() - start}ms`
        });
    });
});

// 8. Transparencia de IA: LECTURA y ESCRITURA del Prompt de Ollama
app.get('/api/prompt', async (req, res) => {
    const promptPath = path.resolve(__dirname, '../agent/prompt.txt');
    try {
        const promptContent = await fs.promises.readFile(promptPath, 'utf8');
        res.json({ success: true, prompt: promptContent });
    } catch (err) {
        if (err.code === 'ENOENT') return res.json({ success: true, prompt: "Archivo prompt.txt no encontrado. Por favor constrúyelo." });
        console.error(`[Keiyi CC] Error leyendo Prompt: ${err.message}`);
        res.status(500).json({ success: false, error: err.message });
    }
});

app.post('/api/prompt', async (req, res) => {
    const promptPath = path.resolve(__dirname, '../agent/prompt.txt');
    const versionsDir = path.resolve(__dirname, '../agent/prompt_versions');
    try {
        const { prompt } = req.body;
        if (!prompt) return res.status(400).json({ success: false, error: "Prompt vacío." });

        // Backup de la versión actual antes de sobreescribir
        try {
            await fs.promises.access(promptPath);
            await fs.promises.mkdir(versionsDir, { recursive: true });
            const ts = new Date().toISOString().slice(0, 19).replace('T', '_').replace(/:/g, '-');
            await fs.promises.copyFile(promptPath, path.join(versionsDir, `prompt_${ts}.txt`));
        } catch (e) { /* prompt.txt aún no existe, nada que respaldar */ }

        await fs.promises.writeFile(promptPath, prompt, 'utf8');
        console.log(`[Keiyi CC] Prompt Maestro actualizado. Backup guardado en prompt_versions/`);
        res.json({ success: true, message: "Instrucciones base guardadas en bloque de sistema." });
    } catch (err) {
        console.error(`[Keiyi CC] Error Guardando Prompt: ${err.message}`);
        res.status(500).json({ success: false, error: err.message });
    }
});

// Historial de versiones del prompt
app.get('/api/prompt/versions', async (req, res) => {
    const versionsDir = path.resolve(__dirname, '../agent/prompt_versions');
    try {
        const allFiles = await fs.promises.readdir(versionsDir);
        const files = allFiles
            .filter(f => f.match(/^prompt_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.txt$/))
            .sort(); // ISO timestamps ordenan cronológicamente

        const now = Date.now();
        const versions = files.map((filename, idx) => {
            const raw = filename.replace('prompt_', '').replace('.txt', '');
            const [datePart, timePart] = raw.split('_');
            const savedAt = new Date(`${datePart}T${timePart.replace(/-/g, ':')}`);

            let endDate;
            if (idx < files.length - 1) {
                const nextRaw = files[idx + 1].replace('prompt_', '').replace('.txt', '');
                const [nd, nt] = nextRaw.split('_');
                endDate = new Date(`${nd}T${nt.replace(/-/g, ':')}`);
            } else {
                endDate = new Date(now);
            }
            const daysLasted = Math.max(0, Math.round((endDate - savedAt) / (1000 * 60 * 60 * 24)));

            return { filename, savedAt: savedAt.toLocaleString('es-MX'), daysLasted, isActive: idx === files.length - 1 };
        }).reverse(); // Más reciente primero

        res.json({ success: true, versions });
    } catch (err) {
        if (err.code === 'ENOENT') return res.json({ success: true, versions: [] });
        res.status(500).json({ success: false, error: err.message });
    }
});

// Restaurar una versión anterior
app.post('/api/prompt/restore/:filename', async (req, res) => {
    const { filename } = req.params;
    if (!filename.match(/^prompt_[\d-]+_[\d-]+\.txt$/)) {
        return res.status(400).json({ success: false, error: 'Nombre de archivo inválido.' });
    }
    const versionsDir = path.resolve(__dirname, '../agent/prompt_versions');
    const sourcePath = path.join(versionsDir, filename);
    const promptPath = path.resolve(__dirname, '../agent/prompt.txt');

    try {
        await fs.promises.access(sourcePath);
    } catch {
        return res.status(404).json({ success: false, error: 'Versión no encontrada.' });
    }

    try {
        // Backup de la versión actual antes de restaurar
        try {
            await fs.promises.access(promptPath);
            const ts = new Date().toISOString().slice(0, 19).replace('T', '_').replace(/:/g, '-');
            await fs.promises.copyFile(promptPath, path.join(versionsDir, `prompt_${ts}.txt`));
        } catch (e) { /* prompt.txt no existe, nada que respaldar */ }

        await fs.promises.copyFile(sourcePath, promptPath);
        console.log(`[Keiyi CC] Prompt restaurado desde ${filename}`);
        res.json({ success: true, message: `Versión ${filename} restaurada como prompt activo.` });
    } catch (err) {
        res.status(500).json({ success: false, error: err.message });
    }
});

// ============================================================================
// DEEP SCOUT — Fuentes Profundas (Reddit) + Inteligencia Acumulada
// ============================================================================

const DEEP_SOURCES_PATH = path.resolve(__dirname, '../agent/deep_sources.json');
const RESEARCH_DB_PATH  = path.resolve(__dirname, '../agent/research_db.json');

async function loadDeepSources() {
    try { return JSON.parse(await fs.promises.readFile(DEEP_SOURCES_PATH, 'utf8')); }
    catch { return []; }
}
async function saveDeepSources(sources) {
    await fs.promises.writeFile(DEEP_SOURCES_PATH, JSON.stringify(sources, null, 2), 'utf8');
}

// 10. Listar fuentes profundas
app.get('/api/deep-sources', async (req, res) => {
    res.json({ success: true, sources: await loadDeepSources() });
});

// 11. Agregar fuente profunda (subreddit)
app.post('/api/deep-sources', async (req, res) => {
    let { subreddit } = req.body;
    if (!subreddit) return res.status(400).json({ success: false, error: 'Subreddit requerido.' });
    // Limpiar: quitar "r/" inicial y caracteres inválidos
    subreddit = subreddit.replace(/^r\//, '').replace(/[^a-zA-Z0-9_]/g, '').trim();
    if (!subreddit) return res.status(400).json({ success: false, error: 'Nombre de subreddit inválido.' });
    const sources = await loadDeepSources();
    if (sources.find(s => s.subreddit.toLowerCase() === subreddit.toLowerCase())) {
        return res.status(400).json({ success: false, error: `r/${subreddit} ya está en la lista.` });
    }
    sources.push({ subreddit, added_at: new Date().toISOString().slice(0, 10) });
    await saveDeepSources(sources);
    res.json({ success: true, subreddit });
});

// 12. Eliminar fuente profunda
app.delete('/api/deep-sources/:subreddit', async (req, res) => {
    const { subreddit } = req.params;
    const sources = await loadDeepSources();
    await saveDeepSources(sources.filter(s => s.subreddit !== subreddit));
    res.json({ success: true });
});

// 13. Ejecutar Deep Scout (Reddit crawler)
app.post('/api/run-deep-scout', (req, res) => {
    const scriptPath = path.resolve(__dirname, '../agent/deep_scout.py');
    console.log(`[Deep Scout] Iniciando análisis profundo de Reddit...`);
    exec(`python3 ${scriptPath}`, { timeout: 600000 }, (error, stdout, stderr) => {
        if (error) {
            console.error(`[Deep Scout] Fallo: ${error.message}`);
            return res.status(500).json({ success: false, error: error.message, logs: stderr });
        }
        res.json({ success: true, logs: stdout });
    });
});

// 14. Inteligencia acumulada (top herramientas, preguntas, referencias)
app.get('/api/research-intel', async (req, res) => {
    try {
        const db = JSON.parse(await fs.promises.readFile(RESEARCH_DB_PATH, 'utf8'));
        const top = (obj, n = 20) => Object.values(obj || {})
            .sort((a, b) => b.count - a.count)
            .slice(0, n);
        res.json({
            success: true,
            last_updated: db.last_updated || null,
            tools:      top(db.tools),
            questions:  top(db.questions),
            references: top(db.references),
        });
    } catch {
        res.json({ success: true, last_updated: null, tools: [], questions: [], references: [] });
    }
});

function buildReportHTML(insight) {
    const date = new Date(insight.report_date).toLocaleDateString('es-MX', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });
    const trends = Array.isArray(insight.detected_trends) ? insight.detected_trends : [];
    const actions = Array.isArray(insight.recommended_actions) ? insight.recommended_actions : [];
    const sources = insight.raw_sources_used ?? 'Keiyi Scout AI · Análisis Local M2';
    const generated = new Date().toLocaleString('es-MX');

    const trendCards = trends.map((t, i) => `
        <div class="trend-card">
            <span class="trend-number">0${i + 1}</span>
            <p class="trend-text">${t}</p>
        </div>`).join('');

    const actionItems = actions.map(a => `
        <div class="action-item">
            <span class="action-check">✓</span>
            <p class="action-text">${a}</p>
        </div>`).join('');

    return `<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Keiyi Intelligence</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Space Grotesk', sans-serif; background: #f4efeb; color: #1a1a1a; padding: 40px 20px; }
        .report-wrap { max-width: 860px; margin: 0 auto; }

        .print-btn { display: block; width: 100%; padding: 16px; margin-bottom: 32px; background: #a3e635; color: #1a1a1a; border: 3px solid #000; box-shadow: 4px 4px 0 #000; font-family: 'Space Grotesk', sans-serif; font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; transition: all 0.15s; }
        .print-btn:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0 #000; }
        .print-btn:active { transform: translate(2px, 2px); box-shadow: 2px 2px 0 #000; }

        .report-header { background: #1a1a1a; color: #fff; padding: 36px 40px; border: 3px solid #000; box-shadow: 6px 6px 0 #a3e635; margin-bottom: 32px; }
        .agency-label { font-size: 11px; font-weight: 700; letter-spacing: 3px; color: #a3e635; text-transform: uppercase; margin-bottom: 8px; }
        .report-title { font-size: 32px; font-weight: 800; text-transform: uppercase; line-height: 1.1; }
        .report-date { margin-top: 12px; font-size: 14px; color: #aaa; text-transform: capitalize; }

        .section-label { font-size: 11px; font-weight: 800; letter-spacing: 3px; text-transform: uppercase; background: #1a1a1a; color: #facc15; display: inline-block; padding: 4px 12px; margin-bottom: 16px; }

        .trends-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 32px; }
        .trend-card { background: #fff; border: 3px solid #000; box-shadow: 4px 4px 0 #000; padding: 24px; }
        .trend-number { display: block; font-size: 42px; font-weight: 800; color: #f4efeb; -webkit-text-stroke: 2px #000; line-height: 1; margin-bottom: 12px; }
        .trend-text { font-size: 15px; font-weight: 600; line-height: 1.5; }

        .actions-block { background: #facc15; border: 3px solid #000; box-shadow: 4px 4px 0 #000; padding: 28px; margin-bottom: 32px; }
        .action-item { display: flex; gap: 16px; align-items: flex-start; padding: 12px 0; border-bottom: 2px solid rgba(0,0,0,0.15); }
        .action-item:last-child { border-bottom: none; }
        .action-check { font-size: 18px; font-weight: 800; background: #1a1a1a; color: #a3e635; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .action-text { font-size: 15px; font-weight: 600; line-height: 1.5; padding-top: 4px; }

        .report-footer { border-top: 3px solid #000; padding-top: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .footer-sources { font-size: 12px; color: #666; max-width: 500px; }
        .footer-stamp { font-size: 11px; font-weight: 700; background: #1a1a1a; color: #a3e635; padding: 6px 14px; letter-spacing: 1px; }

        @media print {
            body { background: #fff; padding: 0; }
            .print-btn { display: none; }
            .report-header, .trend-card, .actions-block { box-shadow: none; }
        }
    </style>
</head>
<body>
<div class="report-wrap">
    <button class="print-btn" onclick="window.print()">⬇ Guardar como PDF — Imprimir → Guardar PDF en Mac</button>
    <header class="report-header">
        <p class="agency-label">Keiyi Digital Agency · Inteligencia Educativa</p>
        <h1 class="report-title">Reporte Ejecutivo<br>de Megatendencias EdTech</h1>
        <p class="report-date">Análisis del ${date}</p>
    </header>
    <section>
        <span class="section-label">Megatendencias Detectadas</span>
        <div class="trends-grid">${trendCards}</div>
    </section>
    <section>
        <span class="section-label">Acciones Recomendadas para Keiyi</span>
        <div class="actions-block">${actionItems}</div>
    </section>
    <footer class="report-footer">
        <p class="footer-sources"><strong>Fuentes:</strong> ${sources}</p>
        <span class="footer-stamp">Keiyi Scout AI · ${generated}</span>
    </footer>
</div>
</body>
</html>`;
}

// 9. Página dedicada del Editor de Prompt (nueva pestaña)
app.get('/prompt-editor', async (req, res) => {
    const promptPath = path.resolve(__dirname, '../agent/prompt.txt');
    let currentPrompt = '';
    try {
        currentPrompt = await fs.promises.readFile(promptPath, 'utf8');
    } catch (e) { /* archivo no existe aún */ }

    const escaped = currentPrompt
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');

    res.setHeader('Content-Type', 'text/html; charset=utf-8');
    res.send(`<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Búsqueda — Keiyi Scout AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Space Grotesk', sans-serif; background: #f4efeb; color: #1a1a1a; padding: 40px 20px; }
        .wrap { max-width: 860px; margin: 0 auto; }
        .header { background: #1a1a1a; color: #fff; padding: 28px 36px; border: 3px solid #000; box-shadow: 6px 6px 0 #a3e635; margin-bottom: 28px; }
        .header h1 { font-size: 24px; font-weight: 800; text-transform: uppercase; }
        .header p { color: #aaa; font-size: 13px; margin-top: 6px; }
        .warning { background: #facc15; border: 3px solid #000; padding: 14px 18px; font-size: 13px; font-weight: 600; margin-bottom: 20px; box-shadow: 3px 3px 0 #000; }
        code { background: #1a1a1a; color: #a3e635; padding: 2px 6px; border-radius: 3px; font-size: 12px; }
        .label { font-size: 11px; font-weight: 800; letter-spacing: 3px; text-transform: uppercase; background: #1a1a1a; color: #facc15; display: inline-block; padding: 4px 12px; margin-bottom: 10px; }
        textarea { width: 100%; height: 420px; padding: 16px; font-family: 'Courier New', monospace; font-size: 13px; border: 3px solid #000; box-shadow: 4px 4px 0 #000; resize: vertical; outline: none; background: #fff; line-height: 1.7; }
        textarea:focus { box-shadow: 4px 4px 0 #a3e635; }
        .actions { display: flex; gap: 16px; margin-top: 20px; align-items: center; flex-wrap: wrap; }
        .btn-save { background: #a3e635; border: 3px solid #000; box-shadow: 4px 4px 0 #000; padding: 14px 32px; font-family: 'Space Grotesk', sans-serif; font-size: 16px; font-weight: 800; text-transform: uppercase; cursor: pointer; transition: all 0.15s; letter-spacing: 1px; }
        .btn-save:hover { transform: translate(-2px,-2px); box-shadow: 6px 6px 0 #000; }
        .btn-save:active { transform: translate(2px,2px); box-shadow: 2px 2px 0 #000; }
        .btn-save:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .feedback { font-size: 14px; font-weight: 700; padding: 10px 16px; border: 2px solid #000; display: none; }
        .feedback.ok { background: #a3e635; display: block; }
        .feedback.err { background: #f87171; color: #fff; display: block; }
    </style>
</head>
<body>
<div class="wrap">
    <header class="header">
        <h1>🧠 Búnker de Configuración — Scout AI</h1>
        <p>Edita las instrucciones base que Qwen3 (Ollama) recibe antes de analizar los datos raspados.</p>
    </header>
    <div class="warning">
        <strong>Tag crítico:</strong> No borres <code>{context_text}</code> — ahí se inyecta la data raspada en tiempo real. Si lo borras, Ollama trabajará sin datos reales.
    </div>
    <span class="label">Prompt Maestro (Cerebro de Keiyi Scout)</span>
    <textarea id="promptTextarea">${escaped}</textarea>
    <div class="actions">
        <button class="btn-save" id="btnSave" onclick="savePrompt()">💾 Guardar Cerebro</button>
        <div id="feedback" class="feedback"></div>
    </div>

    <div style="margin-top:48px;">
        <span style="font-size:11px;font-weight:800;letter-spacing:3px;text-transform:uppercase;background:#1a1a1a;color:#a3e635;display:inline-block;padding:4px 12px;margin-bottom:12px;">Historial de Versiones</span>
        <p style="font-size:12px;color:#666;margin-bottom:16px;">Cada versión muestra cuántos días estuvo activa antes de ser reemplazada. La marcada con ⭐ es la más eficiente.</p>
        <div id="versionsList" style="display:flex;flex-direction:column;gap:10px;">
            <p style="color:#999;font-size:13px;">Cargando historial...</p>
        </div>
    </div>
</div>
<script>
    async function savePrompt() {
        const textarea = document.getElementById('promptTextarea');
        const feedback = document.getElementById('feedback');
        const btn = document.getElementById('btnSave');
        const prompt = textarea.value;
        if (!prompt.includes('{context_text}')) {
            feedback.className = 'feedback err';
            feedback.textContent = '⚠️ El prompt no contiene {context_text}. La IA no recibirá datos reales. Agrégalo antes de guardar.';
            return;
        }
        btn.textContent = 'Guardando...';
        btn.disabled = true;
        feedback.style.display = 'none';
        try {
            const res = await fetch('/api/prompt', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ prompt })
            });
            const data = await res.json();
            if (data.success) {
                feedback.className = 'feedback ok';
                feedback.textContent = '✅ ¡Cerebro actualizado! Versión anterior guardada en historial.';
                loadVersions();
            } else {
                feedback.className = 'feedback err';
                feedback.textContent = 'Error: ' + data.error;
            }
        } catch (e) {
            feedback.className = 'feedback err';
            feedback.textContent = 'Error de conexión con el Command Center (¿está corriendo Node.js?)';
        } finally {
            btn.textContent = '💾 Guardar Cerebro';
            btn.disabled = false;
        }
    }

    async function restoreVersion(filename) {
        if (!confirm('¿Restaurar esta versión como prompt activo? El actual se guardará en historial.')) return;
        try {
            const res = await fetch('/api/prompt/restore/' + filename, { method: 'POST' });
            const data = await res.json();
            if (data.success) { location.reload(); }
            else { alert('Error al restaurar: ' + data.error); }
        } catch (e) { alert('Error de conexión.'); }
    }

    async function loadVersions() {
        const container = document.getElementById('versionsList');
        try {
            const res = await fetch('/api/prompt/versions');
            const data = await res.json();
            if (!data.success || data.versions.length === 0) {
                container.innerHTML = '<p style="color:#999;font-size:13px;">Sin historial aún. Se crea al guardar cambios.</p>';
                return;
            }
            const maxDays = Math.max(...data.versions.map(v => v.daysLasted));
            container.innerHTML = data.versions.map(v => {
                const isBest = v.daysLasted === maxDays && maxDays > 0;
                const cardStyle = isBest
                    ? 'border:2px solid #000;background:#f0fce8;box-shadow:3px 3px 0 #a3e635;padding:14px 18px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;'
                    : 'border:2px solid #000;background:#fff;padding:14px 18px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;';
                const daysBg = isBest ? '#a3e635' : (v.daysLasted === 0 ? '#e5e7eb' : '#facc15');
                const star = isBest ? '⭐ ' : '';
                const action = v.isActive
                    ? '<span style="background:#a3e635;border:2px solid #000;padding:3px 10px;font-size:11px;font-weight:800;text-transform:uppercase;">✓ Reciente</span>'
                    : '<button onclick="restoreVersion(\\'' + v.filename + '\\')" style="background:#1a1a1a;color:#fff;border:2px solid #000;padding:6px 14px;font-family:Space Grotesk,sans-serif;font-weight:700;font-size:12px;cursor:pointer;text-transform:uppercase;">↩ Restaurar</button>';
                return '<div style="' + cardStyle + '">'
                    + '<div><div style="font-weight:700;font-size:14px;">' + star + v.savedAt + '</div>'
                    + '<div style="font-size:11px;color:#888;margin-top:3px;">' + v.filename + '</div></div>'
                    + '<div style="display:flex;gap:12px;align-items:center;">'
                    + '<span style="background:' + daysBg + ';border:2px solid #000;padding:3px 10px;font-weight:800;font-size:12px;">' + v.daysLasted + ' día' + (v.daysLasted !== 1 ? 's' : '') + '</span>'
                    + action + '</div></div>';
            }).join('');
        } catch (e) {
            container.innerHTML = '<p style="color:#f87171;font-size:13px;">Error al cargar historial.</p>';
        }
    }

    loadVersions();
</script>
</body>
</html>`);
});

// ─────────────────────────────────────────────────────────────────────────────

app.listen(PORT, () => {
    console.log(`\n======================================================`);
    console.log(`🚀 Keiyi Command Center (M2 Local) Operando en API.`);
    console.log(`👉 Visita la interfaz gráfica en: http://localhost:${PORT}`);
    console.log(`======================================================\n`);
});
