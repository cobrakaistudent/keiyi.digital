# Documentación de Tipografías - Keiyi.digital

Este documento detalla las fuentes utilizadas en el proyecto para mantener la consistencia visual.

## 1. Space Grotesk (Sans-Serif)
Es la fuente estructural del sitio. Aporta un look moderno, tecnológico y limpio.
*   **Importación:** Google Fonts (Weights: 400, 500, 700)
*   **Uso principal:**
    *   **Cuerpo del sitio:** Todo el texto base.
    *   **Títulos:** H1, H2, H3, H4.
    *   **Navegación:** Enlaces del menú.
    *   **Interfaz:** Botones, formularios y etiquetas de precios.
*   **Variable CSS:** `--font-head`

## 2. Gloria Hallelujah (Cursive/Handwritten)
Es la fuente de acento que da la personalidad "funky" y humana a la marca. Imita la escritura a mano con marcador.
*   **Importación:** Google Fonts
*   **Uso principal:**
    *   **Notas decorativas:** Textos con la clase `.hand-note`.
    *   **Enfasis:** Frases destacadas y misiones.
    *   **Metadatos:** Fechas en las entradas del blog.
*   **Variable CSS:** `--font-hand`

---

## Aplicación por Página

| Página | Space Grotesk | Gloria Hallelujah |
| :--- | :--- | :--- |
| **Home (Inicio)** | Títulos principales, descripción de servicios, tablas de precios. | Notas laterales "funky", frase de misión. |
| **3D-World** | Títulos de proyectos, explicaciones técnicas. | Comentarios casuales sobre los modelos 3D. |
| **Blog** | Títulos de artículos, contenido de lectura. | Fechas de publicación. |
| **Admin Dashboard** | Toda la interfaz de gestión (vía Tailwind). | N/A |
