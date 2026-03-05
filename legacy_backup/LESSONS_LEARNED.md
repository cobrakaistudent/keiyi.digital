### Lecciones Aprendidas - Keiyi Digital Evolution (3 de Marzo, 2026)

#### 1. Animaciones Robustas con GSAP & ScrollTrigger
*   **Problema:** Los elementos con `opacity: 0` desaparecían o se quedaban invisibles si el scroll no se calculaba correctamente al inicio.
*   **Solución:** Usar siempre `gsap.from()` para que el estado final sea el predeterminado del navegador (visible). Añadir `ScrollTrigger.refresh()` al evento `window.load` para recalcular posiciones tras la carga de datos externos (como el Radar de tendencias).

#### 2. Diseño de Bento Grid Simétrico
*   **Lección:** Para agencias de marketing, una cuadrícula de 3 columnas fijas es la más versátil. Permite combinaciones de `span 1`, `span 2` (Large) y `span 3` (Full Width) que siempre encajan matemáticamente, evitando huecos vacíos en resoluciones estándar.
*   **Ajuste:** Usar `grid-auto-rows: minmax(320px, auto)` para evitar que el contenido de las cajas empuje o se empalme con la siguiente sección de la página.

#### 3. Backups de Contenido Dinámico (Laravel)
*   **Importancia:** En aplicaciones Laravel con SQLite, el archivo `database/database.sqlite` es el "corazón" del contenido. Realizar un backup de archivos sin este archivo significa perder todo el blog y los datos de la agencia. Se debe incluir siempre en la rutina de `scp`.

#### 4. Tipografía y Marca
*   **Identificación:** El logo de Keiyi utiliza **Space Grotesk** en peso **700 (Bold)**. Para replicarlo en herramientas como Canva, las mejores alternativas son **Archivo Black** o subir directamente el archivo `.ttf` de Google Fonts para mantener la identidad exacta.

#### 5. Curaduría de Contenido Externo
*   **Estrategia:** Evitar el uso de feeds de terceros (como Reddit) sin filtrar en la página principal. Aunque da un aire de "actualidad", el riesgo de mostrar contenido que contradiga los servicios o la ética de la agencia es demasiado alto. Es mejor curar el contenido manualmente o usar fuentes de noticias profesionales y neutras.

#### 7. Arquitectura de Aprobación en LMS (Laravel)
*   **Lección:** Un sistema de membresía premium requiere un control de acceso granular. No basta con `auth`, se necesita un estado `is_approved`.
*   **Técnica:** Implementar un Middleware (`CheckApproved`) que intercepte todas las rutas de la academia. Si el usuario no está aprobado, redirigir a una vista de "Espera de Élite" para mantener el branding incluso en el bloqueo.

#### 8. Coherencia Visual "Pop" en Safari
*   **Problema:** Safari puede ser agresivo con la caché de imágenes y no mostrar cambios inmediatos en el CSS de bordes orgánicos.
*   **Solución:** Usar variables CSS para los bordes "wobbly" y aplicar "cache-busters" (parámetros como `?v=1`) en los enlaces de imágenes críticas para forzar la recarga visual.

#### 9. Estructura Pedagógica "HITL"
*   **Estrategia:** Un curso de IA no debe ser solo teórico. La fórmula ganadora en 2026 es **Lectura + Script de Vídeo + Laboratorio**.
*   **Avance:** El "Script de Vídeo" sirve tanto para el instructor como para el alumno avanzado que quiere entender el "paso a paso" técnico sin ver el vídeo completo.

#### 11. Git como Red de Seguridad en Proyectos de IA
*   **Lección:** Confiar solo en archivos `.md` para la memoria es arriesgado cuando múltiples agentes (como Antigravity y Gemini) operan en el mismo espacio.
*   **Solución:** Inicializar Git desde el día 1. Realizar commits frecuentes con mensajes descriptivos. Esto permite que, si un agente borra o corrompe un archivo de memoria, la recuperación sea instantánea.

#### 12. Protocolo de Memoria Unificada (Multi-Agente)
*   **Estrategia:** En entornos colaborativos, los archivos de memoria (GEMINI.md) no deben ser "sobrescritos" por un agente basándose en su pasado, sino "fusionados" respetando las inyecciones de los otros agentes.
*   **Técnica:** Leer siempre el estado actual del archivo antes de escribir para no borrar los logs de Antigravity. Separar responsabilidades por secciones: Orquestación (Antigravity) vs. Implementación (Gemini).

#### 13. Cache-Busting Visual en Safari
*   **Problema:** Al actualizar imágenes de cursos (ej. de MySQL a Notion), Safari suele mostrar la versión vieja por su política de caché agresiva.
*   **Solución:** Añadir parámetros de versión a las URLs de las imágenes (`?v=5`) para forzar la descarga de los nuevos activos visuales aprobados por el usuario.
