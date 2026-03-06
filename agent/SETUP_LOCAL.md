# MANUAL DE RECUPERACIÓN (DRP) - ENTORNO LOCAL KEIYI BRAIN HUB

Este documento detalla los pasos exactos para replicar el **Keiyi Brain Hub** (Mac M2) en una nueva máquina en caso de fallo crítico o migración de hardware.

## 1. REQUISITOS DEL SISTEMA (MAC M2)
- **Procesador:** Apple Silicon (Optimizado para Inferencia Local).
- **Python:** 3.11+
- **Node.js:** 20+
- **Ollama:** Última versión (Motor de IA Local).

## 2. CONFIGURACIÓN DE INTELIGENCIA (OLLAMA)
1. Descargar e instalar Ollama desde [ollama.com](https://ollama.com).
2. Descargar el modelo maestro de la agencia:
   ```bash
   ollama run qwen3:8b
   ```

## 3. INFRAESTRUCTURA DE COMUNICACIÓN (SSH)
El Command Center y el Agente Scout utilizan **SSH Directo** para comunicarse con Hostinger (API-Less Design).
1. Asegurar que la llave privada `id_rsa` esté en: `/Users/anuarlv/.ssh/id_rsa`.
2. Probar conexión manual al servidor:
   ```bash
   ssh -p 65002 u129237724@185.212.70.24 -i ~/.ssh/id_rsa
   ```

## 4. AGENTE SCOUT (PYTHON)
1. Instalar dependencias necesarias:
   ```bash
   pip install -r agent/requirements.txt
   ```
2. El archivo `agent/prompt.txt` es gestionado por el Command Center. No editar manualmente a menos que sea necesario.

## 5. COMMAND CENTER (NODE.JS)
1. Instalar paquetes de Node:
   ```bash
   cd command-center
   npm install
   ```
2. Crear archivo `.env` local (copiar del respaldo seguro del Jefe) con el token de Sanctum si se decide habilitar APIs de nuevo, aunque actualmente el sistema usa SSH Proxy.

## 6. PUESTA EN MARCHA
1. Iniciar el búnker táctico:
   ```bash
   cd command-center
   node server.js
   ```
2. Acceder vía navegador: `http://localhost:4000`.

## 7. RESOLUCIÓN DE PROBLEMAS
- **Error SSH:** Verificar que la IP de la Mac no haya sido baneada por el firewall de Hostinger tras múltiples intentos fallidos.
- **Error Ollama:** Asegurar que el servicio de Ollama esté corriendo en la barra de tareas de macOS.
- **Error de Scraping:** Verificar que `beautifulsoup4` esté correctamente instalada en el entorno global de Python.

---
**Documento generado por Gemini CLI (Ingeniero Auditor) - 05-Marzo-2026**
