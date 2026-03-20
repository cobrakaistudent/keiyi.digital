"""
Detector: CPU
Verifica que el uso de CPU esté por debajo de un umbral.
Requiere: psutil
Estado: DESACTIVADO por defecto — activar en idle_config.json cuando se quiera usar.
"""
try:
    import psutil
    PSUTIL_AVAILABLE = True
except ImportError:
    PSUTIL_AVAILABLE = False


def get_cpu_percent(interval: float = 2.0) -> float:
    """Retorna uso de CPU promedio en los últimos `interval` segundos."""
    if not PSUTIL_AVAILABLE:
        return 0.0
    return psutil.cpu_percent(interval=interval)


def is_idle(config: dict) -> tuple[bool, str]:
    """
    Retorna (ok: bool, mensaje: str)
    config esperado: { "max_cpu_pct": 15 }
    """
    if not PSUTIL_AVAILABLE:
        return True, "psutil no instalado — detector desactivado automáticamente"

    max_pct = config.get('max_cpu_pct', 15)
    cpu = get_cpu_percent(interval=1.0)
    ok = cpu <= max_pct
    msg = f"CPU: {cpu:.1f}% / máximo {max_pct}%"
    return ok, msg
