require('dotenv').config();
const express = require('express');
const cors = require('cors');
const { exec } = require('child_process');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 4000;

app.use(cors());
app.use(express.json());
// Servimos el dashboard estático
app.use(express.static(path.join(__dirname, 'public')));

// Endpoint para ejecutar el Inteligente Keiyi Scout (Python)
app.post('/api/run-scout', (req, res) => {
    const scriptPath = path.resolve(__dirname, '../agent/scout.py');
    console.log(`[Keiyi CC] Ejecutando IA Local. Llamando a python3 ${scriptPath}`);

    // Spawn del proceso pesado de Ollama
    exec(`python3 ${scriptPath}`, (error, stdout, stderr) => {
        if (error) {
            console.error(`[Keiyi CC] Fallo: ${error.message}`);
            return res.status(500).json({ success: false, error: error.message, logs: stderr });
        }
        console.log(`[Keiyi CC] Ejecutado con Éxito.`);
        res.json({ success: true, logs: stdout });
    });
});

app.listen(PORT, () => {
    console.log(`\n======================================================`);
    console.log(`🚀 Keiyi Command Center (M2 Local) Operando en API.`);
    console.log(`👉 Visita la interfaz gráfica en: http://localhost:${PORT}`);
    console.log(`======================================================\n`);
});
