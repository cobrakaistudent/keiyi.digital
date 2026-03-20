#!/usr/bin/env python3
"""
Keiyi Idle Agent — Menu Bar App
Monitorea inactividad del sistema y lanza Dipper + William (versión Claude CLI)
cuando la Mac está quieta.

Requiere: rumps, psutil
Instalar: pip install rumps psutil
"""

import json
import logging
import os
import subprocess
import threading
import time
from datetime import datetime, timedelta
from pathlib import Path

import rumps

# ── Paths ─────────────────────────────────────────────────────────────────────
BASE_DIR    = Path(__file__).parent
CONFIG_FILE = BASE_DIR / 'idle_config.json'
LOG_FILE    = Path.home() / 'Library' / 'Logs' / 'keiyi_idle_agent.log'
DB_FILE     = BASE_DIR / 'research_db.json'
DRAFTS_DIR  = BASE_DIR / 'william_drafts'
DRAFTS_DIR.mkdir(exist_ok=True)

# ── Logging ───────────────────────────────────────────────────────────────────
logging.basicConfig(
    filename=LOG_FILE,
    level=logging.INFO,
    format='%(asctime)s [%(levelname)s] %(message)s',
    datefmt='%Y-%m-%d %H:%M:%S',
)
log = logging.getLogger('keiyi_agent')

# ── Detectores disponibles ────────────────────────────────────────────────────
DETECTOR_MODULES = {
    'keyboard_mouse': 'detectors.keyboard_mouse',
    'ram_monitor':    'detectors.ram_monitor',
    'cpu_monitor':    'detectors.cpu_monitor',
}


def load_config() -> dict:
    try:
        return json.loads(CONFIG_FILE.read_text())
    except Exception as e:
        log.error(f"Error leyendo config: {e}")
        return {}


def save_config(config: dict):
    CONFIG_FILE.write_text(json.dumps(config, indent=2, ensure_ascii=False))


def run_detector(name: str, config: dict) -> tuple[bool, str]:
    """Importa y ejecuta un detector. Retorna (vote, mensaje)."""
    import importlib, sys
    sys.path.insert(0, str(BASE_DIR))
    try:
        module = importlib.import_module(DETECTOR_MODULES[name])
        return module.is_idle(config)
    except Exception as e:
        return False, f"Error en detector {name}: {e}"


def check_idle(config: dict) -> tuple[bool, list[str]]:
    """
    Corre todos los detectores habilitados y vota.
    Retorna (sistema_idle: bool, mensajes: list)
    """
    detectors_cfg = config.get('detectors', {})
    min_votes     = config.get('min_idle_votes', 2)
    votes         = 0
    total_enabled = 0
    messages      = []

    for name, det_cfg in detectors_cfg.items():
        if not det_cfg.get('enabled', False):
            messages.append(f"  ○ {name}: desactivado")
            continue
        total_enabled += 1
        vote, msg = run_detector(name, det_cfg)
        icon = "✓" if vote else "✗"
        messages.append(f"  {icon} {name}: {msg}")
        if vote:
            votes += 1

    if total_enabled == 0:
        return False, ["  Sin detectores activos"]

    idle = votes >= min_votes
    messages.insert(0, f"Votos idle: {votes}/{total_enabled} (mínimo {min_votes})")
    return idle, messages


# ── Agentes Claude CLI ─────────────────────────────────────────────────────────

DIPPER_PROMPT = """Eres Dipper, el agente de inteligencia de Keiyi Digital.
Tu tarea: analizar tendencias de marketing digital, IA y productividad.

Lee el archivo research_db.json si existe y actualiza o amplía los datos.
Extrae: herramientas trending, preguntas frecuentes, referencias útiles.
Genera un JSON estructurado con este formato por cada tema:
{
  "tool_name": { "count": N, "sources": [...], "questions": [...], "references": [...] }
}

Fuentes a considerar: marketing digital, IA generativa, automatización, SaaS, productividad.
Sé conciso y preciso. Solo el JSON, sin explicaciones."""

WILLIAM_PROMPT_TEMPLATE = """Eres William, el redactor de Keiyi Digital.
Tienes acceso a los datos de investigación de Dipper en research_db.json.

Escribe UN artículo de blog de 600-900 palabras sobre el tema más relevante que encuentres.

Estructura obligatoria:
- Hook directo (primera línea que engancha)
- Señal de comunidad: "Según r/[subreddit] esta semana..."
- Análisis con datos concretos
- Conclusión accionable
- CTA sutil hacia Keiyi Digital

Tono: experto pero conversacional. Sin relleno. Sin frases genéricas de IA.
Atribuye la fuente al final: "Fuente: r/[subreddit]"

Solo el artículo en Markdown, sin explicaciones previas."""


def run_dipper_claude() -> bool:
    """Ejecuta Dipper usando Claude CLI."""
    log.info("Iniciando Dipper (Claude CLI)...")
    try:
        result = subprocess.run(
            ['claude', '-p', DIPPER_PROMPT, '--output-format', 'text'],
            capture_output=True, text=True, timeout=300,
            cwd=str(BASE_DIR)
        )
        if result.returncode != 0:
            log.error(f"Dipper error: {result.stderr[:500]}")
            return False

        output = result.stdout.strip()
        # Intentar parsear JSON del output
        try:
            start = output.find('{')
            end   = output.rfind('}') + 1
            if start >= 0 and end > start:
                data = json.loads(output[start:end])
                # Merge con research_db existente
                existing = {}
                if DB_FILE.exists():
                    try:
                        existing = json.loads(DB_FILE.read_text())
                    except Exception:
                        pass
                existing.update(data)
                DB_FILE.write_text(json.dumps(existing, indent=2, ensure_ascii=False))
                log.info(f"Dipper: research_db.json actualizado ({len(data)} temas)")
        except Exception as e:
            # Si no es JSON limpio, guardar el output raw
            raw_file = BASE_DIR / f"dipper_raw_{datetime.now().strftime('%Y%m%d_%H%M')}.txt"
            raw_file.write_text(output)
            log.warning(f"Dipper: output no era JSON puro, guardado en {raw_file.name}: {e}")

        return True
    except subprocess.TimeoutExpired:
        log.error("Dipper: timeout (5 min)")
        return False
    except FileNotFoundError:
        log.error("Dipper: comando 'claude' no encontrado. ¿Está Claude CLI instalado?")
        return False


def run_william_claude(drafts_count: int = 3) -> bool:
    """Ejecuta William usando Claude CLI. Genera N borradores."""
    if not DB_FILE.exists():
        log.warning("William: no hay research_db.json — ejecuta Dipper primero")
        return False

    log.info(f"Iniciando William (Claude CLI) — {drafts_count} borradores...")
    success_count = 0

    for i in range(drafts_count):
        try:
            prompt = WILLIAM_PROMPT_TEMPLATE
            if i > 0:
                prompt += f"\n\nIMPORTANTE: Este es el borrador #{i+1}. Elige un tema DIFERENTE a los anteriores de esta sesión."

            result = subprocess.run(
                ['claude', '-p', prompt, '--output-format', 'text'],
                capture_output=True, text=True, timeout=300,
                cwd=str(BASE_DIR)
            )
            if result.returncode != 0:
                log.error(f"William borrador {i+1} error: {result.stderr[:300]}")
                continue

            content = result.stdout.strip()
            timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
            draft_file = DRAFTS_DIR / f"draft_claude_{timestamp}_{i+1}.md"
            draft_file.write_text(content)
            log.info(f"William: borrador {i+1} guardado → {draft_file.name}")
            success_count += 1
            time.sleep(3)  # Pausa entre borradores

        except subprocess.TimeoutExpired:
            log.error(f"William borrador {i+1}: timeout")
        except FileNotFoundError:
            log.error("William: comando 'claude' no encontrado")
            return False

    log.info(f"William: {success_count}/{drafts_count} borradores completados")
    return success_count > 0


# ── App Menu Bar ───────────────────────────────────────────────────────────────

class KeiyiAgentApp(rumps.App):

    def __init__(self):
        super().__init__("🤖", quit_button=None)
        self.config        = load_config()
        self.paused        = False
        self.last_run      = None
        self.running_agents = False
        self._build_menu()
        self._start_monitor_thread()

    # ── Construcción del menú ──────────────────────────────────────────────────

    def _build_menu(self):
        self.menu.clear()
        cfg = self.config

        # Estado
        self.status_item = rumps.MenuItem("Estado: Iniciando...")
        self.last_run_item = rumps.MenuItem(self._last_run_text())
        self.menu.update([self.status_item, self.last_run_item, None])

        # Agentes
        dipper_enabled = cfg.get('agents', {}).get('dipper', {}).get('enabled', True)
        william_enabled = cfg.get('agents', {}).get('william', {}).get('enabled', True)

        dipper_item  = rumps.MenuItem(
            f"{'●' if dipper_enabled else '○'} Dipper  {'Activo' if dipper_enabled else 'Pausado'}",
            callback=self.toggle_dipper
        )
        william_item = rumps.MenuItem(
            f"{'●' if william_enabled else '○'} William {'Activo' if william_enabled else 'Pausado'}",
            callback=self.toggle_william
        )
        self.menu.update([dipper_item, william_item, None])

        # Detectores
        detectors_menu = rumps.MenuItem("Detectores")
        for name, det_cfg in cfg.get('detectors', {}).items():
            enabled = det_cfg.get('enabled', False)
            item = rumps.MenuItem(
                f"{'✓' if enabled else '✗'} {name}",
                callback=lambda _, n=name: self.toggle_detector(n)
            )
            detectors_menu[name] = item
        self.menu['Detectores'] = detectors_menu

        # Configuración
        config_menu = rumps.MenuItem("Configuración")
        config_menu['Inactividad mínima...'] = rumps.MenuItem(
            'Inactividad mínima...', callback=self.config_idle_minutes
        )
        config_menu['RAM libre mínima...'] = rumps.MenuItem(
            'RAM libre mínima...', callback=self.config_ram
        )
        config_menu['Cooldown entre runs...'] = rumps.MenuItem(
            'Cooldown entre runs...', callback=self.config_cooldown
        )
        config_menu['Borradores por run...'] = rumps.MenuItem(
            'Borradores por run...', callback=self.config_drafts
        )
        self.menu['Configuración'] = config_menu

        self.menu.update([None])

        # Acciones
        self.menu['Forzar run ahora'] = rumps.MenuItem('Forzar run ahora', callback=self.force_run)
        self.menu['Ver último log']   = rumps.MenuItem('Ver último log',   callback=self.open_log)

        pause_label = 'Reanudar agente' if self.paused else 'Pausar agente'
        self.menu[pause_label] = rumps.MenuItem(pause_label, callback=self.toggle_pause)

        self.menu.update([None])
        self.menu['Salir'] = rumps.MenuItem('Salir', callback=self.quit_app)

    def _last_run_text(self) -> str:
        if not self.last_run:
            return "Último run: nunca"
        delta = datetime.now() - self.last_run
        minutes = int(delta.total_seconds() // 60)
        if minutes < 60:
            return f"Último run: hace {minutes}m"
        hours = minutes // 60
        return f"Último run: hace {hours}h {minutes % 60}m"

    def _refresh_menu(self):
        """Reconstruye el menú con el estado actual."""
        self._build_menu()

    # ── Monitor thread ─────────────────────────────────────────────────────────

    def _start_monitor_thread(self):
        t = threading.Thread(target=self._monitor_loop, daemon=True)
        t.start()

    def _monitor_loop(self):
        while True:
            try:
                self.config = load_config()
                interval    = self.config.get('schedule', {}).get('check_interval_seconds', 60)

                if self.paused or self.running_agents:
                    time.sleep(interval)
                    continue

                idle, messages = check_idle(self.config)
                status_text = "Idle detectado" if idle else "Monitoreando..."
                self.status_item.title = f"Estado: {status_text}"

                if idle and self._cooldown_passed():
                    self._run_agents()

                time.sleep(interval)
            except Exception as e:
                log.error(f"Monitor loop error: {e}")
                time.sleep(30)

    def _cooldown_passed(self) -> bool:
        if not self.last_run:
            return True
        cooldown_h = self.config.get('schedule', {}).get('run_cooldown_hours', 6)
        return datetime.now() - self.last_run >= timedelta(hours=cooldown_h)

    def _run_agents(self):
        self.running_agents = True
        self.title = "⚙️"
        self.status_item.title = "Estado: Ejecutando agentes..."
        log.info("=== Iniciando run de agentes ===")

        try:
            agents_cfg = self.config.get('agents', {})

            if agents_cfg.get('dipper', {}).get('enabled', True):
                self.status_item.title = "Estado: Dipper trabajando..."
                run_dipper_claude()

            if agents_cfg.get('william', {}).get('enabled', True):
                self.status_item.title = "Estado: William redactando..."
                drafts = agents_cfg.get('william', {}).get('drafts_per_run', 3)
                run_william_claude(drafts)

            self.last_run = datetime.now()
            self.last_run_item.title = self._last_run_text()
            log.info("=== Run completado ===")
            rumps.notification(
                "Keiyi Agent", "Run completado",
                "Dipper y William terminaron. Revisa el Command Center."
            )
        except Exception as e:
            log.error(f"Error en run de agentes: {e}")
        finally:
            self.running_agents = False
            self.title = "🤖"
            self.status_item.title = "Estado: Monitoreando..."

    # ── Callbacks del menú ─────────────────────────────────────────────────────

    @rumps.clicked('Forzar run ahora')
    def force_run(self, _):
        if self.running_agents:
            rumps.alert("Keiyi Agent", "Ya hay un run en progreso.")
            return
        t = threading.Thread(target=self._run_agents, daemon=True)
        t.start()

    def toggle_pause(self, _):
        self.paused = not self.paused
        state = "pausado" if self.paused else "reanudado"
        self.title = "⏸️" if self.paused else "🤖"
        self.status_item.title = f"Estado: {'Pausado' if self.paused else 'Monitoreando...'}"
        log.info(f"Agente {state} manualmente")
        self._refresh_menu()

    def toggle_dipper(self, _):
        cfg = load_config()
        current = cfg.get('agents', {}).get('dipper', {}).get('enabled', True)
        cfg.setdefault('agents', {}).setdefault('dipper', {})['enabled'] = not current
        save_config(cfg)
        self.config = cfg
        self._refresh_menu()

    def toggle_william(self, _):
        cfg = load_config()
        current = cfg.get('agents', {}).get('william', {}).get('enabled', True)
        cfg.setdefault('agents', {}).setdefault('william', {})['enabled'] = not current
        save_config(cfg)
        self.config = cfg
        self._refresh_menu()

    def toggle_detector(self, name: str):
        cfg = load_config()
        current = cfg.get('detectors', {}).get(name, {}).get('enabled', False)
        cfg.setdefault('detectors', {}).setdefault(name, {})['enabled'] = not current
        save_config(cfg)
        self.config = cfg
        self._refresh_menu()

    def config_idle_minutes(self, _):
        cfg = load_config()
        current = cfg.get('detectors', {}).get('keyboard_mouse', {}).get('idle_minutes', 10)
        w = rumps.Window(
            message=f"Minutos de inactividad para activar (actual: {current})",
            title="Inactividad mínima",
            default_text=str(current),
            ok="Guardar", cancel="Cancelar", dimensions=(200, 24)
        )
        r = w.run()
        if r.clicked and r.text.strip().isdigit():
            val = int(r.text.strip())
            cfg.setdefault('detectors', {}).setdefault('keyboard_mouse', {})['idle_minutes'] = val
            save_config(cfg)
            self.config = cfg

    def config_ram(self, _):
        cfg = load_config()
        current = cfg.get('detectors', {}).get('ram_monitor', {}).get('min_free_gb', 4.0)
        w = rumps.Window(
            message=f"GB de RAM libre mínima (actual: {current})",
            title="RAM mínima",
            default_text=str(current),
            ok="Guardar", cancel="Cancelar", dimensions=(200, 24)
        )
        r = w.run()
        if r.clicked and r.text.strip():
            try:
                val = float(r.text.strip())
                cfg.setdefault('detectors', {}).setdefault('ram_monitor', {})['min_free_gb'] = val
                save_config(cfg)
                self.config = cfg
            except ValueError:
                pass

    def config_cooldown(self, _):
        cfg = load_config()
        current = cfg.get('schedule', {}).get('run_cooldown_hours', 6)
        w = rumps.Window(
            message=f"Horas mínimas entre runs (actual: {current})",
            title="Cooldown",
            default_text=str(current),
            ok="Guardar", cancel="Cancelar", dimensions=(200, 24)
        )
        r = w.run()
        if r.clicked and r.text.strip().isdigit():
            val = int(r.text.strip())
            cfg.setdefault('schedule', {})['run_cooldown_hours'] = val
            save_config(cfg)
            self.config = cfg

    def config_drafts(self, _):
        cfg = load_config()
        current = cfg.get('agents', {}).get('william', {}).get('drafts_per_run', 3)
        w = rumps.Window(
            message=f"Número de borradores por run (actual: {current})",
            title="Borradores",
            default_text=str(current),
            ok="Guardar", cancel="Cancelar", dimensions=(200, 24)
        )
        r = w.run()
        if r.clicked and r.text.strip().isdigit():
            val = max(1, min(10, int(r.text.strip())))
            cfg.setdefault('agents', {}).setdefault('william', {})['drafts_per_run'] = val
            save_config(cfg)
            self.config = cfg

    def open_log(self, _):
        subprocess.run(['open', str(LOG_FILE)])

    def quit_app(self, _):
        log.info("Keiyi Agent cerrado por el usuario")
        rumps.quit_application()


# ── Entry point ────────────────────────────────────────────────────────────────

if __name__ == '__main__':
    log.info("Keiyi Idle Agent iniciando...")
    KeiyiAgentApp().run()
