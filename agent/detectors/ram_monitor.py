"""
Detector: RAM libre
Verifica que haya suficiente memoria libre antes de lanzar los agentes.
Requiere: psutil
"""
try:
    import psutil
    PSUTIL_AVAILABLE = True
except ImportError:
    PSUTIL_AVAILABLE = False


def get_free_gb() -> float:
    """Retorna GB de RAM disponible (available, no solo libre)."""
    if not PSUTIL_AVAILABLE:
        return 99.0  # Si no hay psutil, no bloquea
    mem = psutil.virtual_memory()
    return mem.available / (1024 ** 3)


def is_idle(config: dict) -> tuple[bool, str]:
    """
    Retorna (ok: bool, mensaje: str)
    config esperado: { "min_free_gb": 4.0 }
    """
    if not PSUTIL_AVAILABLE:
        return True, "psutil no instalado — detector desactivado automáticamente"

    min_gb = config.get('min_free_gb', 4.0)
    free_gb = get_free_gb()
    ok = free_gb >= min_gb
    msg = f"RAM disponible: {free_gb:.1f} GB / mínimo {min_gb} GB"
    return ok, msg
