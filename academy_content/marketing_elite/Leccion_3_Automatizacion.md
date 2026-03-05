# Lección 3: El Clon de Ventas 24/7 (Master)
**Promesa:** Construirás un agente inteligente que atiende, califica y agenda citas en piloto automático mientras tú te enfocas en el cierre de alto valor.

---

## 1. La Arquitectura del Profit 2026
Olvídate de los bots de "Presiona 1 para más información". Un sistema moderno requiere el **Stack de Élite**:
1.  **Trigger (ManyChat):** Captura el interés en DMs de Instagram con palabras clave inteligentes (ej. "IA").
2.  **Filtro (Make.com):** El sistema nervioso que conecta las herramientas sin programar una sola línea de código.
3.  **Cerebro (Claude 3.5 Sonnet):** La IA con mejor razonamiento del mercado para entender dudas complejas del cliente.
4.  **Cierre (Calendly):** El destino final donde el lead se convierte en una cita real.

---

## 2. El Master Prompt: "Asistente Senior de Ventas"
No uses prompts genéricos. Copia y adapta este prompt en tu módulo de Claude dentro de Make.com:

> "Actúa como el **Director de Ventas Senior de [TU MARCA]**. Tu tono es profesional, audaz y sumamente servicial, pero sin parecer desesperado por vender. 
> 
> **Tu objetivo:** Resolver las dudas del usuario usando esta base de conocimientos: [LINK/TEXTO DE TU WEB].
> **Tus Reglas:**
> 1. Responde en menos de 100 palabras.
> 2. Si el usuario pregunta algo que no está en la base, dile: 'Esa es una excelente pregunta. Permíteme consultarlo con el equipo técnico para darte la respuesta exacta'.
> 3. Si identificas interés real (usa frases de intención), invítalo cordialmente a agendar una sesión estratégica aquí: [LINK CALENDLY]."

---

## 3. Configuración en 4 Pasos (Live Lab)
1.  **ManyChat:** Crea un flujo que responda a un comentario en un Reel. Envía la data a un "External Request".
2.  **Make.com (Webhook):** Recibe el nombre del usuario y su mensaje.
3.  **Make.com (Claude 3.5):** Pasa el mensaje por el **Master Prompt** anterior.
4.  **Respuesta Final:** ManyChat devuelve la respuesta de Claude al DM del usuario.

---

## 4. Laboratorio Maestro (Entregable)
*   **Misión:** Envía la palabra "IA" a tu propio Instagram.
*   **KPI de Éxito:** Debes recibir una respuesta inteligente (no pregrabada) que mencione tu nombre y resuelva una duda específica sobre tu servicio en menos de 10 segundos.

---
**Resultado:** Has clonado tu cerebro de ventas y ahora tu negocio atiende 24/7 sin fatiga.
