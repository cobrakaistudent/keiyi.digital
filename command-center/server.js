require('dotenv').config();
const express = require('express');
const cors = require('cors');
const { exec, spawn } = require('child_process');
const path = require('path');
const fs = require('fs');

const app = express();
const PORT = process.env.PORT || 4000;

app.use(cors({ origin: ['http://localhost:4000', 'http://127.0.0.1:4000', 'http://192.168.50.100:4000'] }));
app.use(express.json());
// Servimos el dashboard estático
app.use(express.static(path.join(__dirname, 'public')));
// Servimos los drafts de William para el preview del blog
app.use('/drafts', express.static(path.join(__dirname, '..', 'agent', 'william_drafts')));

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
        res.json({ success: true, output: stdout });
    });
});

// === PERRY THE DEEP SCOUT — INDAGACIÓN PROFUNDA ===
app.post('/api/deep-dive/:subreddit', (req, res) => {
    const sub = req.params.subreddit;
    const scriptPath = path.resolve(__dirname, '../agent/deep_scout.py');
    console.log(`🕵️‍♂️ Perry iniciando Deep Dive en r/${sub}...`);

    exec(`python3 ${scriptPath} ${sub}`, (error, stdout, stderr) => {
        if (error) return res.status(500).json({ success: false, error: stderr });
        res.json({ success: true, output: stdout });
    });
});

// Obtener los "Tesoros" de Perry (Base de Datos de Investigación)
app.get('/api/research-db', (req, res) => {
    const dbPath = path.join(__dirname, '../agent/research_db.json');
    if (!fs.existsSync(dbPath)) return res.json({});
    try {
        const db = JSON.parse(fs.readFileSync(dbPath, 'utf8'));
        res.json(db);
    } catch (e) {
        res.status(500).json({ error: "Error leyendo research_db.json" });
    }
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
const GDRIVE_INTEL      = path.join(process.env.HOME, 'Library/CloudStorage/GoogleDrive-anuarlezama@gmail.com/My Drive/gemini/keiyi_scout_intelligence');
const RESEARCH_DB_PATH  = path.join(GDRIVE_INTEL, 'research_db.json');
const PERRY_SCRIPT      = path.resolve(__dirname, '../agent/perry.py');
const PERRY_STATUS_PATH = path.resolve(__dirname, '../agent/perry_status.json');
const PERRY_DIRECTIVES  = path.resolve(__dirname, '../agent/ceo_directives.json');
const PERRY_CONFIG_PATH = path.resolve(__dirname, '../agent/perry_config.json');

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
// research_db.json is organized by subreddit: { "SubName": { tools: [...], questions: [...], references: [...] } }
// This endpoint consolidates across all subreddits into ranked lists.
app.get('/api/research-intel', async (req, res) => {
    try {
        const db = JSON.parse(await fs.promises.readFile(RESEARCH_DB_PATH, 'utf8'));

        // Consolidate tools, questions, references across all subreddits
        const toolsMap = {};      // name -> { name, count, sources: Set, sources_count: { sub: n } }
        const questionsMap = {};   // text -> { name, count }
        const referencesMap = {};  // url -> { name, count }
        let latestUpdate = null;

        for (const [subName, subData] of Object.entries(db)) {
            // Track latest update
            if (subData.last_update && (!latestUpdate || subData.last_update > latestUpdate)) {
                latestUpdate = subData.last_update;
            }

            // Consolidate tools
            for (const t of (subData.tools || [])) {
                const key = t.name.toLowerCase();
                if (!toolsMap[key]) {
                    toolsMap[key] = { name: t.name, display: t.name, count: 0, sources: new Set(), sources_count: {} };
                }
                toolsMap[key].count += (t.count || 1);
                toolsMap[key].sources.add(subName);
                toolsMap[key].sources_count[subName] = (toolsMap[key].sources_count[subName] || 0) + (t.count || 1);
            }

            // Consolidate questions
            for (const q of (subData.questions || [])) {
                const text = q.text || q.name || '';
                const key = text.toLowerCase().slice(0, 100);
                if (!key) continue;
                if (!questionsMap[key]) {
                    questionsMap[key] = { name: text, display: text, count: 0 };
                }
                questionsMap[key].count += (q.count || 1);
            }

            // Consolidate references
            for (const r of (subData.references || [])) {
                const url = r.url || r.name || '';
                if (!url) continue;
                const key = url.toLowerCase();
                if (!referencesMap[key]) {
                    referencesMap[key] = { name: url, display: url, count: 0 };
                }
                referencesMap[key].count += (r.count || 1);
            }
        }

        // Convert Sets to arrays and find dominant source
        const toolsList = Object.values(toolsMap).map(t => {
            const sources = [...t.sources];
            const sc = t.sources_count;
            const dominant = Object.entries(sc).sort((a, b) => b[1] - a[1])[0];
            return {
                name: t.name,
                display: t.display,
                count: t.count,
                sources,
                sources_count: sc,
                dominant_source: dominant ? dominant[0] : null,
            };
        });

        const topN = (arr, n = 20) => arr.sort((a, b) => b.count - a.count).slice(0, n);

        res.json({
            success: true,
            last_updated: latestUpdate,
            tools:      topN(toolsList, 20),
            questions:  topN(Object.values(questionsMap), 20),
            references: topN(Object.values(referencesMap), 20),
        });
    } catch (e) {
        console.error('[research-intel] Error:', e.message);
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
// PERRY EL ORNITORRINCO — Streaming SSE + Control Panel
// ─────────────────────────────────────────────────────────────────────────────

// SSE helper: ejecuta perry.py con streaming línea a línea al browser
function perryStream(args, req, res) {
    res.setHeader('Content-Type', 'text/event-stream');
    res.setHeader('Cache-Control', 'no-cache');
    res.setHeader('Connection', 'keep-alive');
    res.flushHeaders();

    const send = (type, text) =>
        res.write(`data: ${JSON.stringify({ type, text })}\n\n`);

    const env = { ...process.env, CLAUDECODE: '', PYTHONUNBUFFERED: '1' };
    const child = spawn('python3', [PERRY_SCRIPT, ...args], { env });

    child.stdout.on('data', d => send('out', d.toString()));
    child.stderr.on('data', d => send('err', d.toString()));
    child.on('close', code => {
        send('done', code === 0 ? '✅ Operación completada.' : `❌ Terminó con código ${code}`);
        res.end();
    });
    req.on('close', () => child.kill()); // Si el browser cierra la conexión, matamos el proceso
}

// GET /api/perry/run?action=scrape|analyze|discover  (&backend=auto|perry|claude|gemini|all)
app.get('/api/perry/run', (req, res) => {
    const { action, backend = 'auto' } = req.query;
    const allowed = ['scrape', 'analyze', 'discover'];
    if (!allowed.includes(action))
        return res.status(400).json({ error: `action inválido: ${action}` });
    const args = action === 'analyze' ? ['analyze', '--backend', backend] : [action];
    perryStream(args, req, res);
});

// GET /api/perry/status
app.get('/api/perry/status', (req, res) => {
    try {
        if (!fs.existsSync(PERRY_STATUS_PATH)) return res.json({ running: false });
        res.json(JSON.parse(fs.readFileSync(PERRY_STATUS_PATH, 'utf8')));
    } catch (e) { res.status(500).json({ error: e.message }); }
});

// GET/POST /api/perry/directives — Instrucciones del CEO que Perry lee antes de cada análisis
app.get('/api/perry/directives', (req, res) => {
    try {
        if (!fs.existsSync(PERRY_DIRECTIVES)) return res.json({ directives: '' });
        const d = JSON.parse(fs.readFileSync(PERRY_DIRECTIVES, 'utf8'));
        res.json(d);
    } catch (e) { res.status(500).json({ error: e.message }); }
});

app.post('/api/perry/directives', (req, res) => {
    try {
        const { directives } = req.body;
        if (directives === undefined) return res.status(400).json({ error: 'Campo "directives" requerido.' });
        const payload = { directives, updated_at: new Date().toISOString() };
        fs.writeFileSync(PERRY_DIRECTIVES, JSON.stringify(payload, null, 2), 'utf8');
        res.json({ success: true });
    } catch (e) { res.status(500).json({ error: e.message }); }
});

// GET /api/perry/sources — fuentes aprobadas/pendientes/rechazadas
app.get('/api/perry/sources', (req, res) => {
    try {
        const p = path.join(GDRIVE_INTEL, 'sources_radar.json');
        if (!fs.existsSync(p)) return res.json({ approved: [], pending: [], rejected: [] });
        res.json(JSON.parse(fs.readFileSync(p, 'utf8')));
    } catch (e) { res.status(500).json({ error: e.message }); }
});

// POST /api/perry/sources/:id/approve|reject
app.post('/api/perry/sources/:id/:action', (req, res) => {
    const { id, action } = req.params;
    if (!['approve', 'reject'].includes(action))
        return res.status(400).json({ error: 'Acción inválida.' });
    const status = action === 'approve' ? 'approved' : 'rejected';
    try {
        const p = path.join(GDRIVE_INTEL, 'sources_radar.json');
        if (!fs.existsSync(p)) return res.status(404).json({ error: 'sources_radar.json no encontrado.' });
        const db = JSON.parse(fs.readFileSync(p, 'utf8'));
        const idx = (db.pending || []).findIndex(s => s.id === id || s.url === id);
        if (idx === -1) return res.status(404).json({ error: 'Fuente no encontrada en cola de pendientes.' });
        const [source] = db.pending.splice(idx, 1);
        source.status = status;
        source.updated_at = new Date().toISOString().slice(0, 10);
        db[status] = db[status] || [];
        db[status].push(source);
        fs.writeFileSync(p, JSON.stringify(db, null, 2), 'utf8');
        res.json({ success: true, status });
    } catch (e) { res.status(500).json({ error: e.message }); }
});

// GET /api/perry/consensus — Último reporte consolidado
app.get('/api/perry/consensus', (req, res) => {
    try {
        const p = path.join(GDRIVE_INTEL, 'perry_consensus.json');
        if (!fs.existsSync(p)) return res.json({ exists: false });
        res.json({ exists: true, data: JSON.parse(fs.readFileSync(p, 'utf8')) });
    } catch (e) { res.status(500).json({ error: e.message }); }
});

// GET /api/perry/storage — Tamaños de archivos en Google Drive
app.get('/api/perry/storage', async (req, res) => {
    const files = [
        { key: 'sources_radar',    path: path.join(GDRIVE_INTEL, 'sources_radar.json') },
        { key: 'perry_results',    path: path.join(GDRIVE_INTEL, 'perry_results.json') },
        { key: 'perry_consensus',  path: path.join(GDRIVE_INTEL, 'perry_consensus.json') },
        { key: 'research_db',      path: RESEARCH_DB_PATH },
        { key: 'seen_comments',    path: path.join(GDRIVE_INTEL, 'seen_comments.json') },
        { key: 'ceo_directives',   path: PERRY_DIRECTIVES },
    ];
    const result = {};
    for (const f of files) {
        try {
            const stat = await fs.promises.stat(f.path);
            result[f.key] = { size_kb: +(stat.size / 1024).toFixed(1), modified: stat.mtime.toISOString().slice(0, 16).replace('T', ' '), exists: true };
        } catch {
            result[f.key] = { size_kb: 0, modified: null, exists: false };
        }
    }
    res.json({ success: true, base_path: GDRIVE_INTEL, files: result });
});

// ─────────────────────────────────────────────────────────────────────────────
// WILLIAM — Proxy hacia Laravel local (api/posts)
// ─────────────────────────────────────────────────────────────────────────────
const LARAVEL_URL   = process.env.LARAVEL_URL   || 'http://localhost:8000';
const SANCTUM_TOKEN = process.env.SANCTUM_TOKEN || '';

const laravelPost = async (path, body = null) => {
    const opts = {
        method: body ? 'POST' : 'GET',
        headers: {
            'Authorization': `Bearer ${SANCTUM_TOKEN}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    };
    if (body) opts.body = JSON.stringify(body);
    const r = await fetch(`${LARAVEL_URL}/api${path}`, opts);
    return r.json();
};

app.get('/api/william/pending', async (req, res) => {
    try { res.json(await laravelPost('/posts/pending')); }
    catch (e) { res.status(500).json({ error: e.message }); }
});

app.post('/api/william/:id/approve', async (req, res) => {
    try { res.json(await laravelPost(`/posts/${req.params.id}/approve`)); }
    catch (e) { res.status(500).json({ error: e.message }); }
});

app.post('/api/william/:id/publish', async (req, res) => {
    try { res.json(await laravelPost(`/posts/${req.params.id}/publish`)); }
    catch (e) { res.status(500).json({ error: e.message }); }
});

app.post('/api/william/:id/reject', async (req, res) => {
    try { res.json(await laravelPost(`/posts/${req.params.id}/reject`, { reason: req.body?.reason })); }
    catch (e) { res.status(500).json({ error: e.message }); }
});

// ─────────────────────────────────────────────────────────────────────────────
// WILLIAM — Feedback / Comments del editor sobre drafts
// ─────────────────────────────────────────────────────────────────────────────

const FEEDBACK_FILE = path.join(__dirname, '..', 'agent', 'william_feedback.json');

function loadFeedback() {
    try { return JSON.parse(fs.readFileSync(FEEDBACK_FILE, 'utf8')); }
    catch { return {}; }
}

function saveFeedback(data) {
    fs.writeFileSync(FEEDBACK_FILE, JSON.stringify(data, null, 2), 'utf8');
}

// Get all feedback
app.get('/api/william/feedback', (req, res) => {
    res.json(loadFeedback());
});

// Get feedback for a specific draft
app.get('/api/william/feedback/:draftFile', (req, res) => {
    const all = loadFeedback();
    res.json(all[req.params.draftFile] || { comments: [], status: 'pending' });
});

// Add comment to a draft
app.post('/api/william/feedback/:draftFile/comment', (req, res) => {
    const { text, type } = req.body; // type: "correction" | "suggestion" | "approval"
    if (!text) return res.status(400).json({ error: 'text is required' });

    const all = loadFeedback();
    if (!all[req.params.draftFile]) {
        all[req.params.draftFile] = { comments: [], status: 'pending' };
    }
    all[req.params.draftFile].comments.push({
        id: Date.now().toString(36),
        text,
        type: type || 'correction',
        created_at: new Date().toISOString()
    });
    saveFeedback(all);
    res.json({ success: true, data: all[req.params.draftFile] });
});

// Delete a comment
app.delete('/api/william/feedback/:draftFile/comment/:commentId', (req, res) => {
    const all = loadFeedback();
    const entry = all[req.params.draftFile];
    if (!entry) return res.status(404).json({ error: 'draft not found' });
    entry.comments = entry.comments.filter(c => c.id !== req.params.commentId);
    saveFeedback(all);
    res.json({ success: true, data: entry });
});

// Set draft status (approved / needs_revision / rejected)
app.post('/api/william/feedback/:draftFile/status', (req, res) => {
    const { status } = req.body;
    if (!['approved', 'needs_revision', 'rejected', 'pending'].includes(status)) {
        return res.status(400).json({ error: 'invalid status' });
    }
    const all = loadFeedback();
    if (!all[req.params.draftFile]) {
        all[req.params.draftFile] = { comments: [], status: 'pending' };
    }
    all[req.params.draftFile].status = status;
    all[req.params.draftFile].status_changed_at = new Date().toISOString();
    saveFeedback(all);
    res.json({ success: true, data: all[req.params.draftFile] });
});

// ─────────────────────────────────────────────────────────────────────────────

app.listen(PORT, () => {
    console.log(`\n======================================================`);
    console.log(`🚀 Keiyi Command Center (M2 Local) Operando en API.`);
    console.log(`👉 Visita la interfaz gráfica en: http://localhost:${PORT}`);
    console.log(`======================================================\n`);
});
