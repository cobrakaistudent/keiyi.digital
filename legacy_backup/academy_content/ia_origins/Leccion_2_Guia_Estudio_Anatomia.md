# Guía de Estudio: IA Origins - Anatomía de un Cerebro Digital (Día 2)
**Taller:** IA Origins | **Día 2**
**Objetivo:** Entender cómo funciona un LLM (Modelo de Lenguaje Extenso) por dentro, por qué "alucina" y por qué es tan poderoso pero peligroso a la vez.

---

## 1. El Juego de la Predicción
Imagina que tienes un teclado en el celular que sugiere la siguiente palabra. Eso es un LLM, pero con esteroides. Ha leído billones de oraciones y sabe que después de "Había una vez..." lo más probable es que siga "un...".

### ¿Qué es un Token?
La IA no lee letras, lee "Tokens" (fragmentos de palabras). 
*   "Gato" es 1 token.
*   "Fotosíntesis" son 3 o 4 tokens.
Esto es importante porque los modelos tienen un **Límite de Contexto** (una memoria de corto plazo) que se mide en tokens. Si el chat es muy largo, la IA empezará a "olvidar" lo que dijiste al principio.

## 2. El Fantasma en la Máquina: Las Alucinaciones
Como la IA es un motor de predicción estadística, a veces "rellena" los huecos con información que suena lógica pero es **falsa**. Esto se llama **Alucinación**.
*   **Ejemplo:** Le pides que te diga quién ganó el Mundial de 1950 en la Luna. Como "ganó el Mundial" y "Luna" son conceptos que ella conoce, puede inventarte un marcador de 3-0 a favor de los astronautas.
*   **Regla de Oro:** Siempre verifica los datos de la IA si son críticos (fechas, leyes, fórmulas médicas).

## 3. Lo que SÍ y lo que NO
*   **SÍ:** Razonamiento lógico, resumen de textos, traducción, lluvia de ideas creativa.
*   **NO:** Saber qué pasó *ahora mismo* (a menos que tenga búsqueda en la web), sentir empatía real, tomar decisiones éticas por ti.

---

## 4. Laboratorio del Día 2: Pon a prueba a la IA
**Misión:** Engañar a la IA (Jailbreak suave) para entender sus límites.
1.  **Pregunta Capciosa:** Pregúntale a ChatGPT: *"¿Quién fue el primer presidente de Marte?"*. Observa si te corrige o si te inventa un nombre.
2.  **Límite de Razonamiento:** Dale este problema: *"Si tengo 3 camisas y las pongo a secar al sol, tardan 3 horas. ¿Cuánto tardarán 30 camisas?"*. 
*Si responde '30 horas', la IA está fallando en lógica básica. Si responde '3 horas', el modelo es avanzado.*

---
**Resultado:** Ahora sabes que la IA no es un oráculo infalible, es un **Copiloto Estadístico**. Sabes cuándo confiar y cuándo verificar.
