"""
Detector: Teclado y Mouse
Usa ioreg (nativo macOS) para leer el tiempo de inactividad del HID system.
No requiere dependencias externas.
"""
import subprocess


def get_idle_seconds() -> float:
    """Retorna segundos desde la última actividad de teclado o mouse."""
    try:
        result = subprocess.run(
            ['ioreg', '-c', 'IOHIDSystem'],
            capture_output=True, text=True, timeout=5
        )
        for line in result.stdout.splitlines():
            if 'HIDIdleTime' in line:
                # Valor en nanosegundos
                nanoseconds = int(line.split('=')[-1].strip())
                return nanoseconds / 1_000_000_000
    except Exception:
        pass
    return 0.0


def is_idle(config: dict) -> tuple[bool, str]:
    """
    Retorna (idle: bool, mensaje: str)
    config esperado: { "idle_minutes": 10 }
    """
    threshold = config.get('idle_minutes', 10) * 60
    idle_secs = get_idle_seconds()
    idle = idle_secs >= threshold
    minutes = int(idle_secs // 60)
    seconds = int(idle_secs % 60)
    msg = f"Inactivo {minutes}m {seconds}s / mínimo {config.get('idle_minutes', 10)}m"
    return idle, msg
