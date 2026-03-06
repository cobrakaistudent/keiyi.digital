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

### Lecciones de Ingeniería de Precisión - Fase 4: Scout AI (5 de Marzo, 2026)

#### 14. Paridad de Entorno: SQLite vs. MySQL (Strictness)
*   **Problema:** SQLite es permisivo con los campos `ENUM`, aceptando cualquier string que se le envíe. MySQL (Hostinger/Producción) es estricto y lanza errores de integridad si el valor no coincide exactamente con la definición de la migración.
*   **Lección:** Al diseñar recursos de Filament o formularios, el valor enviado a la base de datos debe ser validado contra la migración original, no solo contra el label visual de la interfaz.

#### 15. Integridad de Modelos Eloquent y Mass-Assignment
*   **Problema:** Campos críticos (como `role` o `approval_status`) pueden ser editados en la interfaz pero fallar silenciosamente al guardarse si no están declarados en el array `$fillable` del modelo.
*   **Lección:** La implementación de un nuevo campo es un proceso de tres pasos ineludibles: 1) Migración de BD, 2) Inclusión en `$fillable` del Modelo, y 3) Mapeo en el Recurso (Filament/Controller). Omitir el paso 2 es un "bug invisible" que solo se manifiesta bajo carga real.

#### 16. Arquitectura Híbrida (Local-First AI Integration)
*   **Estrategia:** Para maximizar recursos en hosting compartido (Hostinger), la mejor arquitectura es delegar el procesamiento pesado (Scraping, Inferencia IA con Ollama, Generación de PDFs) a un "Brain Hub" local (Mac M2).
*   **Técnica:** El servidor de producción actúa solo como una "Capa de Exhibición y Aprobaciones". La comunicación debe estar blindada con **Laravel Sanctum (Tokens de Larga Duración)** y restricciones de **CORS** específicas para el Command Center local.

#### 17. Higiene de Infraestructura en Micro-servicios Locales
*   **Lección:** Al integrar sub-proyectos como el `command-center` (Node.js) dentro de un repo Laravel, es vital actualizar el `.gitignore` raíz de forma recursiva.
*   **Riesgo:** Olvidar excluir `node_modules/` o archivos `.env` locales puede comprometer la seguridad del repositorio y saturar el historial de Git con miles de archivos innecesarios.

#### 18. Compatibilidad Estricta de Versiones (Filament v3)
*   **Problema:** Al intentar proteger un recurso directamente desde su `PanelProvider` empleando métodos como `->authorize()`, Laravel Colapsa (BadMethodCallException). Esto se debe a la descontinuación o alteración de *Macros* directos de interfaces en versiones nuevas de Filament.
*   **Lección:** Nunca emular o inyectar código ciegamente que dependa de versiones antiguas de paquetería (Laravel / Filament). El daño no es local, tira el Service Container, inhabilitando incluso los comandos para migrar la base de datos o servir la página web entera. Revisar siempre la sintaxis de Filament v3.

#### 19. El Paradigma "API-Less" (Seguridad Extrema en Infraestructura Dividida)
*   **Decisión Pivotante:** Para el Búnker Local (Command Center Node.js/Python), la premisa original dictaba establecer endpoints API en Laravel (Sanctum Tokens). Sin embargo, esto abre innecesariamente la superficie de ataque del servidor público.
*   **Lección:** Si la máquina cliente (Mac M2) es propiedad del Director y tiene llaves SSH configuradas, **elimina los endpoints HTTP**. 
*   **Técnica Impenetrable:** Construir un túnel SSH y correr código PHP Raw (`php -r "echo json_encode(...)"`) para "ordeñar" la base de datos de Hostinger usando la stdout. Y para transferencia de carga pesada (JSON de Scraping), usar **SCP** nativo y luego un trigger SSH. Totalmente hermético, ultra seguro, y bypass a los límites de CORS/Apache.

#### 20. Preservación Crítica de la Memoria de IAs (Regla de Oro)
*   **Problema Histórico:** Durante rutinas de refactorización o limpieza, agentes IA asumieron el control total del árbol de directorios local, procediendo a sobrescribir o destruir por completo archivos vitales de configuración u orquestación (como el GEMINI.md o notas de contexto del usuario).
*   **Lección Irrevocable:** Antes de que cualquier IA empiece a crear, mover o reescribir un archivo, **DEBE** obligatoriamente verificar su existencia usando `view_file` o herramientas de lectura. 
*   **Técnica:** Nunca usar comandos de reemplazo total (`cat >` / `write_to_file` con overwrite=true) sobre archivos estructurales sin primero consultar el contexto, cruzarlo con el usuario, o utilizar exclusiones explícitas de edición (`multi_replace_file_content` puntual en lugar de borrado masivo). Otras IAs operando en el mismo Búnker **NO DEBEN REPETIR ESTE ERROR O EL CONTEXTO SE PERDERÁ.**
