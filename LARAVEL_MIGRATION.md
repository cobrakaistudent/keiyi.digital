# Migración de Estático a Dinámico: Keiyi Agency

## 1. El Concepto: Estático vs. Dinámico

### ¿Qué tenemos ahora? (Sitio Estático)
Imagina que tu sitio web actual es como un **Folleto Impreso**.
*   **Ventaja:** Es rápido de entregar y barato de producir.
*   **Desventaja:** Si quieres cambiar una foto o corregir un texto, tienes que volver a "imprimir" (editar código HTML) y volver a distribuir (subir archivos al servidor). No tiene "memoria"; no sabe quién lo visita ni puede guardar información nueva.

### ¿A dónde vamos? (Sitio Dinámico con Laravel)
Estamos construyendo una **Casa Inteligente**.
*   **Ventaja:** Tienes interruptores (Panel de Admin) para cambiar las luces (contenido) cuando quieras. Tiene "memoria" (Base de Datos) para guardar tus proyectos, blogs y usuarios.
*   **Cómo funciona:** Cuando alguien visita la página, el servidor "construye" el HTML en ese instante consultando la base de datos. "Ah, ¿el dueño subió una foto nueva? Entonces la muestro".

---

## 2. ¿Qué es Laravel?

**Laravel** es un **Framework de PHP**.
*   **PHP:** Es el lenguaje de programación que vive en el servidor (el "cerebro").
*   **Framework:** Es un conjunto de herramientas pre-fabricadas. En lugar de fabricar los ladrillos y el cemento nosotros mismos para construir la casa, Laravel nos da paredes listas, tuberías seguras y un sistema eléctrico pre-instalado.

### ¿Por qué elegimos Laravel?
1.  **Seguridad:** Viene blindado contra ataques comunes de hackers. Si hiciéramos el panel de admin desde cero ("PHP Puro"), sería muy fácil dejar puertas abiertas por error.
2.  **MVC (Modelo-Vista-Controlador):** Nos obliga a ser ordenados.
    *   **Modelo (Datos):** "Aquí están los datos del Blog".
    *   **Vista (Diseño):** "Aquí está el HTML bonito".
    *   **Controlador (Cerebro):** "El usuario pidió ver el blog, voy a buscar los datos al Modelo y se los paso a la Vista".
3.  **Ecosistema:** Tiene herramientas listas para enviar correos, subir archivos, manejar usuarios y bases de datos. Es el estándar de la industria hoy en día.

### Historia y Curiosidades
*   **Creador:** Taylor Otwell. Lanzó la primera versión en **Junio de 2011**.
*   **El Nombre:** Taylor Otwell es fanático de las Crónicas de Narnia. El nombre "Laravel" está inspirado en **Cair Paravel**, el castillo donde reinan los reyes y reinas de Narnia.
*   **Filosofía:** "Developer Happiness" (Felicidad del Desarrollador). Está hecho para que programar sea elegante y expresivo, no un dolor de cabeza.

---

## 3. Bitácora de Migración

### Fase 1: Preparación (28 Dic 2025)
*   [x] Backup del sitio estático funcional (v15).
*   [ ] Limpieza del directorio `public_html` en el servidor.
*   [ ] Instalación de Laravel vía Composer en el servidor.

### Fase 2: Transplante de Corazón
*   [ ] Convertir archivos `.html` a plantillas Blade (`.blade.php`).
*   [ ] Mover estilos y scripts a la carpeta `public` de Laravel.
*   [ ] Configurar Rutas (`routes/web.php`).

### Fase 3: Creación de Vida (Base de Datos)
*   [ ] Diseñar tabla `projects` (3D World).
*   [ ] Diseñar tabla `posts` (Blog).
*   [ ] Crear Panel de Administración protegido con contraseña.
