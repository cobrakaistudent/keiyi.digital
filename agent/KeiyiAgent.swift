import AppKit
import Foundation
import SwiftUI

// MARK: - Config Model

struct DetectorEntry: Codable {
    var enabled: Bool
    var idleMinutes: Double?
    var minFreeGb: Double?
    var maxCpuPct: Double?
    enum CodingKeys: String, CodingKey {
        case enabled
        case idleMinutes = "idle_minutes"
        case minFreeGb   = "min_free_gb"
        case maxCpuPct   = "max_cpu_pct"
    }
}

struct AgentConfig: Codable {
    var minIdleVotes: Int
    var detectors: DetectorsConfig
    var schedule: ScheduleConfig
    var agents: AgentsConfig

    struct DetectorsConfig: Codable {
        var keyboardMouse: DetectorEntry
        var ramMonitor: DetectorEntry
        var cpuMonitor: DetectorEntry
        enum CodingKeys: String, CodingKey {
            case keyboardMouse = "keyboard_mouse"
            case ramMonitor    = "ram_monitor"
            case cpuMonitor    = "cpu_monitor"
        }
    }

    struct ScheduleConfig: Codable {
        var checkIntervalSeconds: Double
        var runCooldownHours: Double
        var mode: String          // "always" | "time_window" | "weekly"
        var timeStart: String     // "09:00"
        var timeEnd: String       // "22:00"
        var days: [Int]           // 0=Dom 1=Lun ... 6=Sab
        enum CodingKeys: String, CodingKey {
            case checkIntervalSeconds = "check_interval_seconds"
            case runCooldownHours     = "run_cooldown_hours"
            case mode, timeStart = "time_start", timeEnd = "time_end", days
        }
    }

    struct AgentsConfig: Codable {
        var perry: PerryEntry?
        var dipper: AgentEntry
        var william: AgentEntry
        struct PerryEntry: Codable {
            var enabled: Bool
            var actions: [String]    // ["scrape", "analyze", "discover"]
            var backend: String      // "gemini" | "auto"
        }
        struct AgentEntry: Codable {
            var enabled: Bool
            var backend: String      // "claude" | "ollama" | "hybrid"
            var ollamaModel: String
            var ollamaHost: String
            var draftsPerRun: Int?
            enum CodingKeys: String, CodingKey {
                case enabled, backend
                case ollamaModel  = "ollama_model"
                case ollamaHost   = "ollama_host"
                case draftsPerRun = "drafts_per_run"
            }
        }
    }

    enum CodingKeys: String, CodingKey {
        case minIdleVotes = "min_idle_votes"
        case detectors, schedule, agents
    }
}

// MARK: - Paths & Logging

// WorkingDirectory en launchd apunta a agent/ — usamos eso como base
let agentDir  = URL(fileURLWithPath: FileManager.default.currentDirectoryPath)
let configURL = agentDir.appendingPathComponent("idle_config.json")
let dbURL     = agentDir.appendingPathComponent("research_db.json")
let draftsDir = agentDir.appendingPathComponent("william_drafts")
let logURL    = FileManager.default.homeDirectoryForCurrentUser
    .appendingPathComponent("Library/Logs/keiyi_idle_agent.log")

func log(_ msg: String) {
    let ts   = ISO8601DateFormatter().string(from: Date())
    let line = "[\(ts)] \(msg)\n"
    print(line, terminator: "")
    guard let data = line.data(using: .utf8) else { return }
    if FileManager.default.fileExists(atPath: logURL.path),
       let fh = try? FileHandle(forWritingTo: logURL) {
        fh.seekToEndOfFile(); fh.write(data); fh.closeFile()
    } else {
        try? data.write(to: logURL)
    }
}

func loadConfig() -> AgentConfig? {
    guard let data = try? Data(contentsOf: configURL) else { return nil }
    return try? JSONDecoder().decode(AgentConfig.self, from: data)
}

func saveConfig(_ c: AgentConfig) {
    let enc = JSONEncoder(); enc.outputFormatting = [.prettyPrinted, .sortedKeys]
    if let data = try? enc.encode(c) { try? data.write(to: configURL) }
}

/// Reads/writes the `enabled` flag for an agent in idle_config.json
// MARK: - Notifications
func notify(title: String, body: String, subtitle: String = "") {
    let sub = subtitle.isEmpty ? "" : ", subtitle:\"\(subtitle)\""
    let script = "display notification \"\(body)\" with title \"\(title)\"\(sub)"
    let t = Process()
    t.launchPath = "/usr/bin/osascript"
    t.arguments  = ["-e", script]
    t.standardOutput = Pipe(); t.standardError = Pipe()
    try? t.run()
}

// MARK: - Detectors

@discardableResult
func shell(_ cmd: String) -> String {
    let t = Process(); let p = Pipe()
    t.launchPath = "/bin/bash"; t.arguments = ["-c", cmd]
    t.standardOutput = p; t.standardError = Pipe()
    try? t.run(); t.waitUntilExit()
    return String(data: p.fileHandleForReading.readDataToEndOfFile(), encoding: .utf8) ?? ""
}

func getIdleSeconds() -> Double {
    for line in shell("ioreg -c IOHIDSystem").components(separatedBy: "\n") {
        if line.contains("HIDIdleTime"),
           let raw = line.components(separatedBy: "=").last?.trimmingCharacters(in: .whitespaces),
           let ns = Double(raw) { return ns / 1_000_000_000 }
    }; return 0
}

func getFreeRamGB() -> Double {
    var free = 0.0, inactive = 0.0
    for line in shell("vm_stat").components(separatedBy: "\n") {
        let v = { Double(line.components(separatedBy: ":").last?.trimmingCharacters(in: .whitespacesAndNewlines).replacingOccurrences(of: ".", with: "") ?? "") ?? 0 }
        if line.hasPrefix("Pages free:")     { free     = v() }
        if line.hasPrefix("Pages inactive:") { inactive = v() }
    }
    return (free + inactive) * 16384 / 1_073_741_824
}

func getCpuPercent() -> Double {
    let total = shell("ps -A -o %cpu").components(separatedBy: "\n")
        .compactMap { Double($0.trimmingCharacters(in: .whitespaces)) }.reduce(0, +)
    return min(total / Double(Foundation.ProcessInfo.processInfo.processorCount), 100)
}

struct VoteResult { let passed: Bool; let label: String; let detail: String }

func checkIdleVotes(config: AgentConfig) -> (idle: Bool, votes: [VoteResult]) {
    var results: [VoteResult] = []
    let km = config.detectors.keyboardMouse
    if km.enabled {
        let thr = (km.idleMinutes ?? 10) * 60; let s = getIdleSeconds()
        results.append(VoteResult(passed: s >= thr, label: "Teclado/Mouse",
            detail: "\(Int(s)/60)m \(Int(s)%60)s / mín \(Int(km.idleMinutes ?? 10))m"))
    }
    let ram = config.detectors.ramMonitor
    if ram.enabled {
        let min = ram.minFreeGb ?? 4.0; let free = getFreeRamGB()
        results.append(VoteResult(passed: free >= min, label: "RAM",
            detail: String(format: "%.1f GB / mín %.1f GB", free, min)))
    }
    let cpu = config.detectors.cpuMonitor
    if cpu.enabled {
        let max = cpu.maxCpuPct ?? 15; let pct = getCpuPercent()
        results.append(VoteResult(passed: pct <= max, label: "CPU",
            detail: String(format: "%.1f%% / máx %.0f%%", pct, max)))
    }
    let passes = results.filter { $0.passed }.count
    return (passes >= config.minIdleVotes && !results.isEmpty, results)
}

// MARK: - Schedule Check

func isWithinSchedule(config: AgentConfig) -> (allowed: Bool, reason: String) {
    let sch = config.schedule
    switch sch.mode {
    case "always":
        return (true, "Siempre activo")

    case "time_window":
        let cal = Calendar.current
        let now = Date()
        let weekday = cal.component(.weekday, from: now) - 1 // 0=Dom
        guard sch.days.contains(weekday) else {
            let dayNames = ["Dom","Lun","Mar","Mié","Jue","Vie","Sáb"]
            return (false, "Hoy (\(dayNames[weekday])) no es día laboral")
        }
        let hm = cal.dateComponents([.hour,.minute], from: now)
        let cur = (hm.hour ?? 0) * 60 + (hm.minute ?? 0)
        let parts = { (s: String) -> Int in
            let p = s.components(separatedBy: ":").compactMap { Int($0) }
            return (p.first ?? 0) * 60 + (p.last ?? 0)
        }
        let start = parts(sch.timeStart); let end = parts(sch.timeEnd)
        if cur >= start && cur < end {
            return (true, "Horario activo (\(sch.timeStart)–\(sch.timeEnd))")
        }
        return (false, "Fuera de horario (\(sch.timeStart)–\(sch.timeEnd))")

    case "weekly":
        // Corre solo una vez por semana — el cooldown lo maneja
        return (true, "Modo semanal (cooldown: \(Int(sch.runCooldownHours))h)")

    default:
        return (true, "")
    }
}

// MARK: - Agent Runners

let dipperPromptClaude = """
Eres Dipper, agente de inteligencia de Keiyi Digital.
Analiza tendencias de marketing digital, IA generativa y productividad para 2026.
Extrae un JSON con este formato EXACTO (sin texto adicional antes o después del JSON):
{
  "nombre_herramienta": {
    "count": 5,
    "sources": ["r/digital_marketing"],
    "questions": ["¿Cómo usar X para Y?"],
    "references": ["https://ejemplo.com"]
  }
}
Incluye 5-10 herramientas o temas trending. SOLO el JSON, nada más.
"""

let williamPromptClaude = """
Eres William, redactor senior de Keiyi Digital.
Escribe un artículo de blog de 600-900 palabras basado en las tendencias actuales de marketing digital.
Estructura: Hook directo → señal de comunidad ("Según r/X esta semana...") → análisis con 2-3 puntos accionables → conclusión → CTA sutil hacia Keiyi Digital.
Tono: experto pero conversacional. Sin frases genéricas de IA. Sin relleno.
Formato: Markdown. SOLO el artículo, sin prefacio.
"""

func runClaude(prompt: String, timeout: TimeInterval = 300) -> String? {
    let task = Process(); let pipe = Pipe()
    task.launchPath = "/usr/bin/env"
    task.arguments  = ["claude", "-p", prompt, "--output-format", "text"]
    task.currentDirectoryURL = agentDir
    task.standardOutput = pipe; task.standardError = Pipe()
    do { try task.run() } catch {
        log("ERROR claude CLI: \(error)"); return nil
    }
    let deadline = Date().addingTimeInterval(timeout)
    while task.isRunning && Date() < deadline { Thread.sleep(forTimeInterval: 1) }
    if task.isRunning { task.terminate(); log("TIMEOUT claude"); return nil }
    return String(data: pipe.fileHandleForReading.readDataToEndOfFile(), encoding: .utf8)
}

func runOllama(model: String, prompt: String, host: String = "http://localhost:11434", timeout: TimeInterval = 300) -> String? {
    // Llama a Ollama via API REST
    guard let url = URL(string: "\(host)/api/generate") else { return nil }
    let body: [String: Any] = ["model": model, "prompt": prompt, "stream": false]
    guard let bodyData = try? JSONSerialization.data(withJSONObject: body) else { return nil }

    var req = URLRequest(url: url, timeoutInterval: timeout)
    req.httpMethod = "POST"
    req.httpBody   = bodyData
    req.setValue("application/json", forHTTPHeaderField: "Content-Type")

    var result: String?
    let sem = DispatchSemaphore(value: 0)
    URLSession.shared.dataTask(with: req) { data, _, error in
        defer { sem.signal() }
        if let error = error { log("Ollama error: \(error)"); return }
        if let data = data,
           let json = try? JSONSerialization.jsonObject(with: data) as? [String: Any],
           let resp = json["response"] as? String {
            result = resp
        }
    }.resume()
    sem.wait()
    return result
}

// Lanza múltiples subagentes Claude en paralelo (para Dipper)
func runClaudeParallel(prompts: [String], timeout: TimeInterval = 300) -> [String] {
    var results = [String?](repeating: nil, count: prompts.count)
    let group   = DispatchGroup()
    let lock    = NSLock()

    for (i, prompt) in prompts.enumerated() {
        group.enter()
        DispatchQueue.global().async {
            let out = runClaude(prompt: prompt, timeout: timeout)
            lock.lock(); results[i] = out; lock.unlock()
            group.leave()
        }
    }
    group.wait()
    return results.compactMap { $0 }
}

func mergeToResearchDB(_ newEntries: [String: Any]) {
    var existing: [String: Any] = [:]
    if let data = try? Data(contentsOf: dbURL),
       let parsed = try? JSONSerialization.jsonObject(with: data) as? [String: Any] {
        existing = parsed
    }
    existing.merge(newEntries) { _, new in new }
    if let merged = try? JSONSerialization.data(withJSONObject: existing, options: [.prettyPrinted, .sortedKeys]) {
        try? merged.write(to: dbURL)
    }
}

func extractJSON(from text: String) -> [String: Any]? {
    guard let start = text.firstIndex(of: "{"), let last = text.lastIndex(of: "}") else { return nil }
    let slice = String(text[start...last])
    guard let data = slice.data(using: .utf8),
          let json = try? JSONSerialization.jsonObject(with: data) as? [String: Any] else { return nil }
    return json
}

func runDipper(entry: AgentConfig.AgentsConfig.AgentEntry) {
    log("Dipper iniciando — backend: \(entry.backend)")

    switch entry.backend {

    case "claude":
        // Un solo agente Claude
        guard let out = runClaude(prompt: dipperPromptClaude) else { return }
        if let json = extractJSON(from: out) {
            mergeToResearchDB(json)
            log("Dipper (Claude): \(json.count) temas guardados")
        } else {
            saveRaw(out, prefix: "dipper_claude")
        }

    case "ollama":
        guard let out = runOllama(model: entry.ollamaModel, prompt: dipperPromptClaude, host: entry.ollamaHost) else { return }
        if let json = extractJSON(from: out) {
            mergeToResearchDB(json)
            log("Dipper (Ollama/\(entry.ollamaModel)): \(json.count) temas guardados")
        } else {
            saveRaw(out, prefix: "dipper_ollama")
        }

    case "hybrid":
        // Parallel: múltiples subagentes Claude + Ollama simultáneos
        log("Dipper HYBRID — lanzando subagentes en paralelo...")
        let claudePrompts = [
            dipperPromptClaude + "\n\nFoco: herramientas de automatización y IA.",
            dipperPromptClaude + "\n\nFoco: SEO, contenido y marketing de atracción.",
            dipperPromptClaude + "\n\nFoco: SaaS, productividad y flujos de trabajo.",
        ]
        let group   = DispatchGroup()
        var allData = [String: Any]()
        let lock    = NSLock()

        // Claude subagentes en paralelo
        group.enter()
        DispatchQueue.global().async {
            let outputs = runClaudeParallel(prompts: claudePrompts)
            for out in outputs {
                if let json = extractJSON(from: out) {
                    lock.lock(); allData.merge(json) { _, new in new }; lock.unlock()
                }
            }
            log("Dipper HYBRID Claude: \(outputs.count) subagentes completados")
            group.leave()
        }

        // Ollama en paralelo (si está disponible)
        group.enter()
        DispatchQueue.global().async {
            if let out = runOllama(model: entry.ollamaModel, prompt: dipperPromptClaude, host: entry.ollamaHost) {
                if let json = extractJSON(from: out) {
                    lock.lock(); allData.merge(json) { _, new in new }; lock.unlock()
                    log("Dipper HYBRID Ollama: \(json.count) temas")
                }
            }
            group.leave()
        }

        group.wait()
        if !allData.isEmpty {
            mergeToResearchDB(allData)
            log("Dipper HYBRID completo: \(allData.count) temas únicos en DB")
        }

    default:
        log("Dipper: backend '\(entry.backend)' no reconocido")
    }
}

func runWilliam(entry: AgentConfig.AgentsConfig.AgentEntry) {
    guard FileManager.default.fileExists(atPath: dbURL.path) else {
        log("William: sin research_db.json — ejecuta Dipper primero"); return
    }
    let count = entry.draftsPerRun ?? 3
    log("William iniciando — backend: \(entry.backend), \(count) borradores")
    try? FileManager.default.createDirectory(at: draftsDir, withIntermediateDirectories: true)

    switch entry.backend {

    case "claude":
        for i in 1...count {
            let extra = i > 1 ? "\n\nBorrador #\(i). Elige tema DIFERENTE a los anteriores." : ""
            if let out = runClaude(prompt: williamPromptClaude + extra) {
                saveDraft(out, source: "claude", index: i)
            }
            if i < count { Thread.sleep(forTimeInterval: 3) }
        }

    case "ollama":
        for i in 1...count {
            if let out = runOllama(model: entry.ollamaModel, prompt: williamPromptClaude, host: entry.ollamaHost) {
                saveDraft(out, source: "ollama", index: i)
            }
        }

    case "hybrid":
        // Claude y Ollama en paralelo, cada uno produce su versión
        log("William HYBRID — Claude + Ollama en paralelo...")
        let group = DispatchGroup()

        // Claude produce N borradores en paralelo
        group.enter()
        DispatchQueue.global().async {
            let prompts = (1...count).map { i in
                williamPromptClaude + (i > 1 ? "\n\nBorrador #\(i). Tema DIFERENTE." : "")
            }
            let outputs = runClaudeParallel(prompts: prompts)
            for (i, out) in outputs.enumerated() {
                saveDraft(out, source: "claude", index: i + 1)
            }
            log("William HYBRID Claude: \(outputs.count) borradores")
            group.leave()
        }

        // Ollama produce su versión
        group.enter()
        DispatchQueue.global().async {
            if let out = runOllama(model: entry.ollamaModel, prompt: williamPromptClaude, host: entry.ollamaHost) {
                saveDraft(out, source: "ollama", index: 1)
                log("William HYBRID Ollama: borrador guardado")
            }
            group.leave()
        }

        group.wait()

    case "local":
        // Delega en william.py (deep research: investiga URLs, usa keiyi-william)
        let task = Process(); let pipe = Pipe()
        task.executableURL = URL(fileURLWithPath: "/Library/Frameworks/Python.framework/Versions/3.11/bin/python3")
        task.arguments  = [agentDir.appendingPathComponent("william.py").path]
        task.currentDirectoryURL = agentDir
        task.standardOutput = pipe; task.standardError = Pipe()
        do { try task.run() } catch {
            log("ERROR william.py: \(error)"); return
        }
        let deadline = Date().addingTimeInterval(300)
        while task.isRunning && Date() < deadline { Thread.sleep(forTimeInterval: 1) }
        if task.isRunning { task.terminate(); log("TIMEOUT william.py"); return }
        log("William (local/william.py): completado")

    default:
        log("William: backend '\(entry.backend)' no reconocido")
    }
}

func saveDraft(_ content: String, source: String, index: Int) {
    let fmt = DateFormatter(); fmt.dateFormat = "yyyyMMdd_HHmmss"
    let name = "draft_\(source)_\(fmt.string(from: Date()))_\(index).md"
    let file = draftsDir.appendingPathComponent(name)
    try? content.write(to: file, atomically: true, encoding: .utf8)
    log("William: borrador guardado → \(name)")
}

func saveRaw(_ content: String, prefix: String) {
    let file = agentDir.appendingPathComponent("\(prefix)_raw_\(Int(Date().timeIntervalSince1970)).txt")
    try? content.write(to: file, atomically: true, encoding: .utf8)
    log("Output raw guardado → \(file.lastPathComponent)")
}

// MARK: - Resource Monitor

struct ResourceReading: Codable {
    let ts: String
    let cpu: Double
    let ramFree: Double
    var topProcess: String = ""
}

class ResourceMonitor: ObservableObject {
    @Published var cpuPct: Double = 0
    @Published var ramFreeGB: Double = 0
    @Published var idleMin: Double = 0
    @Published var optimalWindows: String = "Recopilando datos..."
    @Published var hourlyAvgCpu: [Int: Double] = [:]     // hour 0-23 → avg cpu %
    @Published var hourlyAvgRam: [Int: Double] = [:]     // hour 0-23 → avg ram %
    @Published var hourlyTopProcess: [Int: String] = [:] // hour 0-23 → top process name

    private var timer: Timer?
    private var readings: [ResourceReading] = []
    private let logURL = agentDir.appendingPathComponent("resource_log.json")

    init() {
        loadHistory()
        DispatchQueue.global(qos: .background).async { self.collect() }
        timer = Timer.scheduledTimer(withTimeInterval: 300, repeats: true) { _ in
            DispatchQueue.global(qos: .background).async { self.collect() }
        }
    }

    private func getTopProcess() -> String {
        let t = Process(); let p = Pipe()
        t.executableURL = URL(fileURLWithPath: "/bin/sh")
        t.arguments = ["-c", "ps -Arcww -o comm | tail -n +2 | head -1"]
        t.standardOutput = p
        try? t.run(); t.waitUntilExit()
        return String(data: p.fileHandleForReading.readDataToEndOfFile(), encoding: .utf8)?
            .trimmingCharacters(in: .whitespacesAndNewlines) ?? ""
    }

    func collect() {
        let cpu  = getCpuPercent()
        let ram  = getFreeRamGB()
        let idle = getIdleSeconds() / 60
        let top  = getTopProcess()
        let ts   = ISO8601DateFormatter().string(from: Date())
        let r    = ResourceReading(ts: ts, cpu: cpu, ramFree: ram, topProcess: top)
        DispatchQueue.main.async {
            self.cpuPct    = cpu
            self.ramFreeGB = ram
            self.idleMin   = idle
            self.readings.append(r)
            if self.readings.count > 2016 { self.readings.removeFirst() }
            self.saveHistory()
            self.computeOptimal()
        }
    }

    func computeOptimal() {
        var cpuSum = [Int: Double](); var ramSum = [Int: Double](); var cnt = [Int: Int]()
        var procVotes = [Int: [String: Int]]()
        let cal = Calendar.current; let fmt = ISO8601DateFormatter()
        
        for r in readings {
            guard let d = fmt.date(from: r.ts) else { continue }
            let h = cal.component(.hour, from: d)
            cpuSum[h, default: 0] += r.cpu
            ramSum[h, default: 0] += (16.0 - r.ramFree) / 16.0 * 100.0 // RAM usada en %
            cnt[h, default: 0] += 1
            if !r.topProcess.isEmpty { procVotes[h, default: [:]][r.topProcess, default: 0] += 1 }
        }
        
        var newCpuAvgs = [Int: Double]()
        var newRamAvgs = [Int: Double]()
        for h in 0..<24 { 
            if let c = cnt[h], c > 0 { 
                newCpuAvgs[h] = cpuSum[h]! / Double(c)
                newRamAvgs[h] = ramSum[h]! / Double(c)
            } 
        }
        
        DispatchQueue.main.async {
            self.hourlyAvgCpu = newCpuAvgs
            self.hourlyAvgRam = newRamAvgs
            self.hourlyTopProcess = procVotes.mapValues { votes in
                votes.max(by: { $0.value < $1.value })?.key ?? ""
            }
            
            let quiet = (0..<24).filter { (newCpuAvgs[$0] ?? 100) < 25 }.sorted()
            if quiet.isEmpty { self.optimalWindows = "Analizando..."; return }
            var groups: [[Int]] = []; var cur: [Int] = []
            for h in quiet {
                if cur.isEmpty || h == cur.last! + 1 { cur.append(h) }
                else { groups.append(cur); cur = [h] }
            }
            if !cur.isEmpty { groups.append(cur) }
            self.optimalWindows = groups.map { "\($0.first!)h – \($0.last! + 1)h" }.joined(separator: " | ")
        }
    }

    func saveHistory() {
        guard let data = try? JSONEncoder().encode(readings) else { return }
        try? data.write(to: logURL)
    }

    func loadHistory() {
        guard let data = try? Data(contentsOf: logURL),
              let saved = try? JSONDecoder().decode([ResourceReading].self, from: data) else { return }
        readings = Array(saved.suffix(2016))
        computeOptimal()
    }
}

// MARK: - App Delegate

class AppDelegate: NSObject, NSApplicationDelegate {
    var statusItem: NSStatusItem!
    var mainWindow: NSWindow?
    var lastRun: Date?
    var isPaused      = false
    var isRunning     = false
    var currentAgent  = ""   // "Dipper" | "William" | ""
    var currentModel  = ""   // "claude" | "ollama/dipper:latest" | "hybrid"
    var voteResults   = [VoteResult]()
    var scheduleMsg   = ""

    func applicationDidFinishLaunching(_ n: Notification) {
        // ── Protección de instancia única ────────────────────────────────
        // Usa un lock file con PID. Si ya existe otra instancia corriendo, termina.
        let lockFile = URL(fileURLWithPath: NSTemporaryDirectory()).appendingPathComponent("keiyi_agent.lock")
        let myPID = getpid()
        if let existingPIDStr = try? String(contentsOf: lockFile, encoding: .utf8),
           let existingPID = Int32(existingPIDStr.trimmingCharacters(in: .whitespacesAndNewlines)),
           existingPID != myPID {
            // Verificar si el PID anterior sigue vivo (kill -0 no mata, solo verifica existencia)
            if kill(existingPID, 0) == 0 {
                let alert = NSAlert()
                alert.messageText = "Keiyi Agent ya está corriendo"
                alert.informativeText = "Solo puede haber una instancia activa (PID \(existingPID)). El ícono 🤖 ya está en la barra de menú."
                alert.alertStyle = .warning
                alert.addButton(withTitle: "Entendido")
                alert.runModal()
                NSApp.terminate(nil)
                return
            }
        }
        // Escribir nuestro PID en el lock file
        try? String(myPID).write(to: lockFile, atomically: true, encoding: .utf8)

        NSApp.setActivationPolicy(.accessory)
        statusItem = NSStatusBar.system.statusItem(withLength: NSStatusItem.variableLength)
        statusItem.button?.title = "🤖"
        buildMenu()
        DispatchQueue.global().asyncAfter(deadline: .now() + 1) { self.startMonitor() }
        log("Keiyi Agent iniciado (Swift nativo)")
    }

    func applicationWillTerminate(_ n: Notification) {
        // Limpiar lock file al cerrar limpiamente
        let lockFile = URL(fileURLWithPath: NSTemporaryDirectory()).appendingPathComponent("keiyi_agent.lock")
        try? FileManager.default.removeItem(at: lockFile)
    }

    // MARK: Monitor

    func startMonitor() {
        while true {
            guard let config = loadConfig() else { Thread.sleep(forTimeInterval: 30); continue }
            let interval = config.schedule.checkIntervalSeconds

            if !isPaused && !isRunning {
                let (idle, votes)     = checkIdleVotes(config: config)
                let (allowed, schMsg) = isWithinSchedule(config: config)
                voteResults = votes
                scheduleMsg = schMsg
                refreshMenu()

                if idle && allowed && cooldownPassed(config: config) {
                    DispatchQueue.global().async { self.runAgents() }
                }
            }
            Thread.sleep(forTimeInterval: interval)
        }
    }

    func cooldownPassed(config: AgentConfig) -> Bool {
        guard let lr = lastRun else { return true }
        return Date().timeIntervalSince(lr) >= config.schedule.runCooldownHours * 3600
    }

    /// Write a task directly to agent_tasks.json (for auto-run, no OpsMonitor needed)
    func logAutoTask(title: String, agent: String, success: Bool) {
        let file = agentDir.appendingPathComponent("agent_tasks.json")
        var tasks: [[String: Any]] = []
        if let data = try? Data(contentsOf: file),
           let arr = try? JSONSerialization.jsonObject(with: data) as? [[String: Any]] {
            tasks = arr
        }
        let icons = ["Perry":"🦆","Dipper":"📡","William":"✍️"]
        let now = ISO8601DateFormatter().string(from: Date())
        let t: [String: Any] = [
            "id": UUID().uuidString, "title": title,
            "agent": agent, "agent_icon": icons[agent] ?? "🤖",
            "status": "done", "trigger": "auto",
            "notes": success ? "✅ Completado" : "❌ Terminó con error",
            "created_at": now, "updated_at": now
        ]
        tasks.insert(t, at: 0)
        if let data = try? JSONSerialization.data(withJSONObject: tasks, options: .prettyPrinted) {
            try? data.write(to: file)
        }
    }

    func runAgents() {
        guard let config = loadConfig() else { return }
        isRunning = true
        log("=== RUN INICIADO ===")

        // Perry (scrape + analyze before Dipper mines the data)
        if let perry = config.agents.perry, perry.enabled {
            currentAgent = "Perry"
            let actions = perry.actions.isEmpty ? ["scrape"] : perry.actions
            for action in actions {
                log("Perry AUTO: \(action)")
                DispatchQueue.main.async { self.statusItem.button?.title = "🦆"; self.buildMenu() }
                notify(title: "🦆 Perry arrancó", body: "Auto · \(action.uppercased())", subtitle: "Keiyi Command Center")
                let task = Process()
                task.executableURL = URL(fileURLWithPath: "/Library/Frameworks/Python.framework/Versions/3.11/bin/python3")
                task.arguments = [agentDir.appendingPathComponent("perry.py").path, action]
                var env = Foundation.ProcessInfo.processInfo.environment
                env["PYTHONUNBUFFERED"] = "1"
                env["CLAUDECODE"] = ""
                env["PATH"] = "/Users/anuarlv/.local/bin:/opt/homebrew/bin:" + (env["PATH"] ?? "/usr/bin:/bin")
                task.environment = env
                let pipe = Pipe(); task.standardOutput = pipe; task.standardError = pipe
                try? task.run(); task.waitUntilExit()
                let ok = task.terminationStatus == 0
                log("Perry AUTO \(action): \(ok ? "OK" : "FALLO")")
                let labels = ["scrape":"SCRAPE · Descarga fuentes","discover":"DESCUBRIR · Nuevas fuentes"]
                logAutoTask(title: labels[action] ?? "Perry · \(action)", agent: "Perry", success: ok)
            }
            notify(title: "✅ Perry completó", body: "Datos frescos listos para Dipper", subtitle: "Keiyi Command Center")
        }

        if config.agents.dipper.enabled {
            currentAgent = "Dipper"
            currentModel = modelLabel(config.agents.dipper)
            notify(title: "📡 Dipper arrancó", body: "Excavando inteligencia · \(modelLabel(config.agents.dipper))", subtitle: "Keiyi Command Center")
            DispatchQueue.main.async { self.statusItem.button?.title = "🔍"; self.buildMenu() }
            runDipper(entry: config.agents.dipper)
            logAutoTask(title: "RADAR · Excavar fuentes", agent: "Dipper", success: true)
            notify(title: "✅ Dipper completó", body: "Inteligencia guardada en research_db.json", subtitle: "Keiyi Command Center")
        }

        if config.agents.william.enabled {
            currentAgent = "William"
            currentModel = modelLabel(config.agents.william)
            notify(title: "✍️ William arrancó", body: "Redactando borradores · \(modelLabel(config.agents.william))", subtitle: "Keiyi Command Center")
            DispatchQueue.main.async { self.statusItem.button?.title = "✍️"; self.buildMenu() }
            runWilliam(entry: config.agents.william)
            logAutoTask(title: "REDACTAR · Borradores", agent: "William", success: true)
            notify(title: "✅ William completó", body: "Borradores listos para revisión", subtitle: "Keiyi Command Center")
        }

        lastRun      = Date()
        isRunning    = false
        currentAgent = ""
        currentModel = ""
        log("=== RUN COMPLETADO ===")
        notify(title: "🤖 Keiyi · Ciclo completo", body: "Todos los agentes terminaron", subtitle: "Keiyi Command Center")
        DispatchQueue.main.async { self.statusItem.button?.title = "🤖"; self.buildMenu() }
    }

    func modelLabel(_ e: AgentConfig.AgentsConfig.AgentEntry) -> String {
        switch e.backend {
        case "ollama":  return "ollama/\(e.ollamaModel)"
        case "hybrid":  return "claude + ollama/\(e.ollamaModel)"
        case "local":   return "local/william.py"
        default:        return "claude"
        }
    }

    // MARK: Menu Builder

    func buildMenu() {
        DispatchQueue.main.async { self._buildMenu() }
    }

    func _buildMenu() {
        let menu = NSMenu()
        guard let config = loadConfig() else { return }

        // ── Estado ──
        add(menu, title: stateText(), enabled: false)
        add(menu, title: lastRunText(), enabled: false)
        add(menu, title: scheduleMsg.isEmpty ? "" : "Horario: \(scheduleMsg)", enabled: false)
        menu.addItem(.separator())
        
        // ── Dashboard ──
        add(menu, title: "🎛 Abrir Command Center", action: #selector(showCommandCenter))
        menu.addItem(.separator())

        // ── Dipper ──
        let dipperSub = NSMenu()
        add(dipperSub, title: "Habilitado", action: #selector(toggleDipper), checked: config.agents.dipper.enabled)
        add(dipperSub, title: "── Backend ──", enabled: false)
        add(dipperSub, title: "Claude solo",   action: #selector(setDipperClaude),  checked: config.agents.dipper.backend == "claude")
        add(dipperSub, title: "Ollama solo",   action: #selector(setDipperOllama),  checked: config.agents.dipper.backend == "ollama")
        add(dipperSub, title: "Híbrido (ambos + paralelo)", action: #selector(setDipperHybrid), checked: config.agents.dipper.backend == "hybrid")
        add(dipperSub, title: "── Modelo Ollama ──", enabled: false)
        add(dipperSub, title: config.agents.dipper.ollamaModel, action: #selector(cfgDipperModel))
        addParent(menu, title: "🔍 Dipper  [\(config.agents.dipper.backend)]", submenu: dipperSub)

        // ── William ──
        let williamSub = NSMenu()
        add(williamSub, title: "Habilitado", action: #selector(toggleWilliam), checked: config.agents.william.enabled)
        add(williamSub, title: "── Backend ──", enabled: false)
        add(williamSub, title: "Claude solo",   action: #selector(setWilliamClaude),  checked: config.agents.william.backend == "claude")
        add(williamSub, title: "Ollama solo",   action: #selector(setWilliamOllama),  checked: config.agents.william.backend == "ollama")
        add(williamSub, title: "Híbrido (ambos + paralelo)", action: #selector(setWilliamHybrid), checked: config.agents.william.backend == "hybrid")
        add(williamSub, title: "Local (william.py — deep research)", action: #selector(setWilliamLocal), checked: config.agents.william.backend == "local")
        add(williamSub, title: "── Modelo Ollama ──", enabled: false)
        add(williamSub, title: config.agents.william.ollamaModel, action: #selector(cfgWilliamModel))
        add(williamSub, title: "── Borradores por run ──", enabled: false)
        add(williamSub, title: "\(config.agents.william.draftsPerRun ?? 3) borradores", action: #selector(cfgDrafts))
        addParent(menu, title: "✍️ William  [\(config.agents.william.backend)]", submenu: williamSub)

        menu.addItem(.separator())

        // ── Horario ──
        let schSub = NSMenu()
        add(schSub, title: "Siempre activo",    action: #selector(setSchedAlways),     checked: config.schedule.mode == "always")
        add(schSub, title: "Horario por horas", action: #selector(setSchedTimeWindow), checked: config.schedule.mode == "time_window")
        add(schSub, title: "Una vez por semana",action: #selector(setSchedWeekly),     checked: config.schedule.mode == "weekly")
        schSub.addItem(.separator())
        add(schSub, title: "Hora inicio: \(config.schedule.timeStart)", action: #selector(cfgTimeStart))
        add(schSub, title: "Hora fin:    \(config.schedule.timeEnd)",   action: #selector(cfgTimeEnd))
        add(schSub, title: "Cooldown:    \(Int(config.schedule.runCooldownHours))h entre runs", action: #selector(cfgCooldown))
        schSub.addItem(.separator())
        let dayNames = ["Dom","Lun","Mar","Mié","Jue","Vie","Sáb"]
        for (i, name) in dayNames.enumerated() {
            let on = config.schedule.days.contains(i)
            let item = NSMenuItem(title: "\(on ? "✓" : "○") \(name)", action: #selector(toggleDay(_:)), keyEquivalent: "")
            item.target = self; item.tag = i; schSub.addItem(item)
        }
        addParent(menu, title: "🕐 Horario  [\(config.schedule.mode)]", submenu: schSub)

        // ── Detectores ──
        let detSub = NSMenu()
        add(detSub, title: "Teclado/Mouse (\(config.detectors.keyboardMouse.enabled ? "✓" : "✗")) — \(Int(config.detectors.keyboardMouse.idleMinutes ?? 10))min", action: #selector(toggleKM))
        add(detSub, title: "RAM           (\(config.detectors.ramMonitor.enabled    ? "✓" : "✗")) — \(config.detectors.ramMonitor.minFreeGb ?? 4.0)GB libre", action: #selector(toggleRAM))
        add(detSub, title: "CPU           (\(config.detectors.cpuMonitor.enabled    ? "✓" : "✗")) — <\(Int(config.detectors.cpuMonitor.maxCpuPct ?? 15))%",    action: #selector(toggleCPU))
        detSub.addItem(.separator())
        add(detSub, title: "Votos mínimos: \(config.minIdleVotes)", action: #selector(cfgVotes))

        if !voteResults.isEmpty {
            detSub.addItem(.separator())
            add(detSub, title: "── Último chequeo ──", enabled: false)
            for v in voteResults {
                add(detSub, title: "\(v.passed ? "✓" : "✗") \(v.label): \(v.detail)", enabled: false)
            }
        }
        addParent(menu, title: "🎛 Detectores", submenu: detSub)

        menu.addItem(.separator())

        // ── Terminal / Comandos ──
        let termSub = NSMenu()
        add(termSub, title: "📜 Ver logs en tiempo real",       action: #selector(termLogs))
        add(termSub, title: "🔬 Ver Research DB (JSON)",         action: #selector(termDB))
        add(termSub, title: "📝 Ver borradores de William",      action: #selector(termDrafts))
        add(termSub, title: "📡 Estado del proceso",             action: #selector(termStatus))
        termSub.addItem(.separator())
        add(termSub, title: "🛑 Detener KeiyiAgent",             action: #selector(termKill))
        addParent(menu, title: "🖥 Terminal / Comandos", submenu: termSub)

        // ── Ayuda ──
        let helpSub = NSMenu()
        add(helpSub, title: "── ¿Qué hace cada cosa? ──", enabled: false)
        add(helpSub, title: "🔍 Dipper → investiga temas trending de marketing", enabled: false)
        add(helpSub, title: "✍️ William → redacta artículos de blog (usa lo de Dipper)", enabled: false)
        helpSub.addItem(.separator())
        add(helpSub, title: "── Backends ──", enabled: false)
        add(helpSub, title: "Claude solo → usa Claude CLI (requiere internet)", enabled: false)
        add(helpSub, title: "Ollama solo → usa modelo local (sin internet, privado)", enabled: false)
        add(helpSub, title: "Híbrido → Claude + Ollama en paralelo (más resultados)", enabled: false)
        helpSub.addItem(.separator())
        add(helpSub, title: "── Detectores de inactividad ──", enabled: false)
        add(helpSub, title: "Teclado/Mouse → detecta si llevas X minutos sin usar el Mac", enabled: false)
        add(helpSub, title: "RAM → solo corre si hay suficiente RAM libre", enabled: false)
        add(helpSub, title: "CPU → solo corre si la CPU está tranquila", enabled: false)
        add(helpSub, title: "Votos mínimos → cuántos detectores deben coincidir para activar", enabled: false)
        helpSub.addItem(.separator())
        add(helpSub, title: "── Horario ──", enabled: false)
        add(helpSub, title: "Siempre activo → corre cualquier día/hora si hay inactividad", enabled: false)
        add(helpSub, title: "Horario por horas → solo entre las horas configuradas", enabled: false)
        add(helpSub, title: "Una vez por semana → cooldown de 168h entre runs", enabled: false)
        add(helpSub, title: "Cooldown → horas mínimas entre dos runs consecutivos", enabled: false)
        addParent(menu, title: "❓ Ayuda", submenu: helpSub)

        menu.addItem(.separator())

        add(menu, title: "⚡ Forzar run ahora", action: #selector(forceRun))
        add(menu, title: "📋 Ver logs",         action: #selector(openLog))
        add(menu, title: isPaused ? "▶️ Reanudar agente" : "⏸ Pausar agente", action: #selector(togglePause))

        menu.addItem(.separator())
        add(menu, title: "Salir", action: #selector(quitApp))

        statusItem.menu = menu
    }

    // MARK: Menu Helpers

    @discardableResult
    func add(_ menu: NSMenu, title: String, action: Selector? = nil, enabled: Bool = true, checked: Bool = false) -> NSMenuItem {
        let item = NSMenuItem(title: title, action: action, keyEquivalent: "")
        item.target  = self
        item.isEnabled = enabled
        item.state   = checked ? .on : .off
        menu.addItem(item)
        return item
    }

    func addParent(_ menu: NSMenu, title: String, submenu: NSMenu) {
        let item = NSMenuItem(title: title, action: nil, keyEquivalent: "")
        item.submenu = submenu
        menu.addItem(item)
    }

    // MARK: Text Helpers

    func stateText() -> String {
        if isPaused  { return "Estado: Pausado ⏸" }
        if isRunning {
            if currentAgent.isEmpty { return "Estado: Ejecutando... ⚙️" }
            return "⚙️ BUSY — \(currentAgent) [\(currentModel)]"
        }
        return "Estado: Monitoreando..."
    }

    func lastRunText() -> String {
        guard let lr = lastRun else { return "Último run: nunca" }
        let m = Int(Date().timeIntervalSince(lr)) / 60
        return m < 60 ? "Último run: hace \(m)m" : "Último run: hace \(m/60)h \(m%60)m"
    }

    func refreshMenu() { DispatchQueue.main.async { self._buildMenu() } }

    // MARK: Actions — Agentes

    @objc func toggleDipper()  { mutate { $0.agents.dipper.enabled.toggle() } }
    @objc func toggleWilliam() { mutate { $0.agents.william.enabled.toggle() } }

    @objc func setDipperClaude()  { mutate { $0.agents.dipper.backend = "claude" } }
    @objc func setDipperOllama()  { mutate { $0.agents.dipper.backend = "ollama" } }
    @objc func setDipperHybrid()  { mutate { $0.agents.dipper.backend = "hybrid" } }

    @objc func setWilliamClaude()  { mutate { $0.agents.william.backend = "claude" } }
    @objc func setWilliamOllama()  { mutate { $0.agents.william.backend = "ollama" } }
    @objc func setWilliamHybrid()  { mutate { $0.agents.william.backend = "hybrid" } }
    @objc func setWilliamLocal()   { mutate { $0.agents.william.backend = "local" } }

    @objc func cfgDipperModel()  { dialog("Modelo Ollama para Dipper")  { v in self.mutate { $0.agents.dipper.ollamaModel  = v } } }
    @objc func cfgWilliamModel() { dialog("Modelo Ollama para William") { v in self.mutate { $0.agents.william.ollamaModel = v } } }
    @objc func cfgDrafts()       { numDialog("Borradores por run (1-10)") { v in self.mutate { $0.agents.william.draftsPerRun = max(1, min(10, Int(v))) } } }

    // MARK: Actions — Horario

    @objc func setSchedAlways()     { mutate { $0.schedule.mode = "always" } }
    @objc func setSchedTimeWindow() { mutate { $0.schedule.mode = "time_window" } }
    @objc func setSchedWeekly()     { mutate { $0.schedule.mode = "weekly"; $0.schedule.runCooldownHours = 168 } }

    @objc func cfgTimeStart() { dialog("Hora de inicio (HH:MM)")  { v in self.mutate { $0.schedule.timeStart = v } } }
    @objc func cfgTimeEnd()   { dialog("Hora de fin (HH:MM)")     { v in self.mutate { $0.schedule.timeEnd   = v } } }
    @objc func cfgCooldown()  { numDialog("Horas de cooldown entre runs") { v in self.mutate { $0.schedule.runCooldownHours = v } } }

    @objc func toggleDay(_ sender: NSMenuItem) {
        let day = sender.tag
        mutate { c in
            if c.schedule.days.contains(day) {
                c.schedule.days.removeAll { $0 == day }
            } else {
                c.schedule.days.append(day)
                c.schedule.days.sort()
            }
        }
    }

    // MARK: Actions — Detectores

    @objc func toggleKM()  { mutate { $0.detectors.keyboardMouse.enabled.toggle() } }
    @objc func toggleRAM() { mutate { $0.detectors.ramMonitor.enabled.toggle() } }
    @objc func toggleCPU() { mutate { $0.detectors.cpuMonitor.enabled.toggle() } }
    @objc func cfgVotes()  { numDialog("Votos idle mínimos para activar") { v in self.mutate { $0.minIdleVotes = max(1, Int(v)) } } }

    // MARK: Actions — Terminal

    func openTerminal(_ command: String) {
        let escaped = command.replacingOccurrences(of: "\"", with: "\\\"")
        let script  = "tell application \"Terminal\"\nactivate\ndo script \"\(escaped)\"\nend tell"
        NSAppleScript(source: script)?.executeAndReturnError(nil)
    }

    @objc func termLogs()   { openTerminal("tail -f ~/Library/Logs/keiyi_idle_agent.log") }
    @objc func termDB()     { openTerminal("cat ~/gemini/keiyi.digital/agent/research_db.json | python3 -m json.tool 2>/dev/null || cat ~/gemini/keiyi.digital/agent/research_db.json") }
    @objc func termDrafts() { openTerminal("ls -lt ~/gemini/keiyi.digital/agent/william_drafts/ | head -20") }
    @objc func termStatus() { openTerminal("pgrep -l KeiyiAgent; echo '---'; launchctl list | grep keiyi") }
    @objc func termKill()   { openTerminal("pkill KeiyiAgent && echo 'KeiyiAgent detenido'") }

    // MARK: Actions — Generales

    @objc func forceRun() {
        guard !isRunning else { alert("Ya hay un run en progreso."); return }
        DispatchQueue.global().async { self.runAgents() }
    }

    @objc func togglePause() {
        isPaused.toggle()
        statusItem.button?.title = isPaused ? "⏸️" : "🤖"
        log("Agente \(isPaused ? "pausado" : "reanudado")")
        buildMenu()
    }

    @objc func openLog() { NSWorkspace.shared.open(logURL) }

    @objc func quitApp() { log("Keiyi Agent cerrado"); NSApp.terminate(nil) }

    // MARK: Mutation Helper

    func mutate(_ block: (inout AgentConfig) -> Void) {
        guard var c = loadConfig() else { return }
        block(&c)
        saveConfig(c)
        buildMenu()
    }

    // MARK: Dialog Helpers

    func dialog(_ message: String, completion: @escaping (String) -> Void) {
        DispatchQueue.main.async {
            let a = NSAlert()
            a.messageText = message; a.addButton(withTitle: "Guardar"); a.addButton(withTitle: "Cancelar")
            let f = NSTextField(frame: NSRect(x: 0, y: 0, width: 220, height: 24)); a.accessoryView = f
            NSApp.activate(ignoringOtherApps: true)
            if a.runModal() == .alertFirstButtonReturn && !f.stringValue.isEmpty {
                completion(f.stringValue); self.buildMenu()
            }
        }
    }

    func numDialog(_ message: String, completion: @escaping (Double) -> Void) {
        dialog(message) { v in if let n = Double(v) { completion(n) } }
    }

    func alert(_ msg: String) {
        DispatchQueue.main.async {
            let a = NSAlert(); a.messageText = msg
            NSApp.activate(ignoringOtherApps: true); a.runModal()
        }
    }
    
    // MARK: - Dashboard Window Logic
    
    @objc func showCommandCenter() {
        if mainWindow == nil {
            let contentView = ContentView()
            let hostingController = NSHostingController(rootView: contentView)
            let window = NSWindow(
                contentRect: NSRect(x: 0, y: 0, width: 1000, height: 1150),
                styleMask: [.titled, .closable, .miniaturizable, .resizable, .fullSizeContentView],
                backing: .buffered, defer: false)
            window.center()
            window.title = "Keiyi Command Center"
            window.contentViewController = hostingController
            window.isReleasedWhenClosed = false
            self.mainWindow = window
        }
        NSApp.activate(ignoringOtherApps: true)
        mainWindow?.makeKeyAndOrderFront(nil)
    }
}

// MARK: - SwiftUI Dashboard Views

struct ContentView: View {
    @State private var selectedTab: String? = "Overview"
    @StateObject private var monitor = ResourceMonitor()
    @StateObject private var perry   = PerryMonitor()
    @StateObject private var dipper  = DipperMonitor()
    @StateObject private var ops     = OpsMonitor()

    var body: some View {
        NavigationView {
            // SIDEBAR
            List {
                Section(header: Text("KEIYI OS - Búnker Local").font(.caption).bold()) {
                    NavigationLink(destination: OverviewView(monitor: monitor), tag: "Overview", selection: $selectedTab) {
                        Label("Overview", systemImage: "house.fill")
                    }
                }

                Section(header: Text("AGENTES IA").font(.caption).bold()) {
                    NavigationLink(destination: PerryView(monitor: monitor, perry: perry), tag: "Perry", selection: $selectedTab) {
                        Label(perry.isRunning ? "Perry · ●" : "Perry · Reconocimiento", systemImage: "globe.americas.fill")
                            .foregroundColor(perry.isRunning ? .orange : .primary)
                    }
                    NavigationLink(destination: DipperOpsView(dipper: dipper), tag: "Dipper", selection: $selectedTab) {
                        Label("Dipper · Inteligencia", systemImage: "antenna.radiowaves.left.and.right")
                    }
                    NavigationLink(destination: WilliamsDeskView(), tag: "William", selection: $selectedTab) {
                        Label("William · Redactor", systemImage: "pencil.and.outline")
                    }
                }

                Section(header: Text("OPERACIONES").font(.caption).bold()) {
                    NavigationLink(destination: MissionControlView(ops: ops), tag: "Missions", selection: $selectedTab) {
                        let inProgress = ops.tasks.filter { $0["status"] as? String == "in_progress" }.count
                        Label(inProgress > 0 ? "Misiones · \(inProgress) activas" : "Misiones · Kanban", systemImage: "checklist")
                            .foregroundColor(inProgress > 0 ? .orange : .primary)
                    }
                }

                Section(header: Text("COMANDO EXTERNO").font(.caption).bold()) {
                    NavigationLink(destination: Text("Portal Alumnos"), tag: "Portal", selection: $selectedTab) {
                        Label("Portal Alumnos", systemImage: "graduationcap.fill")
                    }
                }

                Section(header: Text("SISTEMA").font(.caption).bold()) {
                    NavigationLink(destination: SettingsView(), tag: "Settings", selection: $selectedTab) {
                        Label("Automatización", systemImage: "gearshape.fill")
                    }
                    NavigationLink(destination: HelpPortalView(), tag: "Help", selection: $selectedTab) {
                        Label("Guía del Sistema", systemImage: "book.pages.fill")
                    }
                }
            }
            .listStyle(SidebarListStyle())
            .frame(minWidth: 220)

            // MAIN CONTENT (Default)
            OverviewView(monitor: monitor)
        }
        .frame(minWidth: 1000, minHeight: 1150)
        .onAppear {
            perry.opsMonitor  = ops
            dipper.opsMonitor = ops
        }
    }
}

struct ResourceCard: View {
    let label: String
    let value: String
    let progress: Double
    let color: Color

    var body: some View {
        VStack(alignment: .leading, spacing: 6) {
            Text(label).font(.caption).foregroundColor(.secondary)
            Text(value).font(.title2).bold()
                .foregroundColor(color)
            ProgressView(value: min(progress, 1.0))
                .accentColor(color)
        }
        .padding()
        .frame(maxWidth: .infinity, alignment: .leading)
        .background(Color(NSColor.textBackgroundColor))
        .cornerRadius(10)
        .shadow(radius: 2)
    }
}

struct ResourceHeatmapView: View {
    let title: String
    let hourlyAvg: [Int: Double]
    var hourlyTopProcess: [Int: String] = [:]
    var colorTheme: Color = .orange

    @State private var hoveredHour: Int? = nil

    private let barMaxH: CGFloat = 72
    private let barMinH: CGFloat = 4
    private let barW:    CGFloat = 26

    private var currentHour: Int { Calendar.current.component(.hour, from: Date()) }

    private func barColor(_ val: Double) -> Color {
        let t  = min(max(val / 100.0, 0), 1)
        if colorTheme == .orange {
            let hue = 0.38 - t * 0.38 // Verde (0.38) → Rojo (0.0)
            return Color(hue: hue, saturation: 0.65 + t * 0.25, brightness: 0.80 + (1 - t) * 0.12)
        } else {
            // Azul (0.6) → Púrpura (0.8) para RAM
            let hue = 0.60 + t * 0.20
            return Color(hue: hue, saturation: 0.60 + t * 0.20, brightness: 0.85)
        }
    }

    private func barH(_ h: Int) -> CGFloat {
        guard let avg = hourlyAvg[h] else { return barMinH }
        return barMinH + CGFloat(avg / 100.0) * (barMaxH - barMinH)
    }

    private var optimalHours: Set<Int> {
        Set((0..<24).filter { (hourlyAvg[$0] ?? 100) < 25 })
    }

    var body: some View {
        VStack(alignment: .leading, spacing: 10) {

            // Header
            HStack {
                Text(title)
                    .font(.caption).bold().foregroundColor(.white.opacity(0.55))
                Spacer()
                HStack(spacing: 8) {
                    HStack(spacing: 3) {
                        Circle().fill(colorTheme == .orange ? Color.green : Color.blue).frame(width: 7, height: 7)
                        Text("Bajo").font(.system(size: 9)).foregroundColor(.white.opacity(0.45))
                    }
                    HStack(spacing: 3) {
                        Circle().fill(colorTheme == .orange ? Color.red : Color.purple).frame(width: 7, height: 7)
                        Text("Alto").font(.system(size: 9)).foregroundColor(.white.opacity(0.45))
                    }
                }
            }

            // Barras
            HStack(alignment: .bottom, spacing: 3) {
                ForEach(0..<24, id: \.self) { h in
                    VStack(spacing: 3) {
                        if h == currentHour, let avg = hourlyAvg[h] {
                            Text("\(Int(avg))%")
                                .font(.system(size: 7, weight: .bold))
                                .foregroundColor(.white)
                        } else {
                            Color.clear.frame(height: 11)
                        }

                        ZStack(alignment: .bottom) {
                            RoundedRectangle(cornerRadius: 4)
                                .fill(Color.white.opacity(0.05))
                                .frame(width: barW, height: barMaxH)

                            if optimalHours.contains(h) && colorTheme == .orange {
                                RoundedRectangle(cornerRadius: 4)
                                    .fill(Color.green.opacity(0.14))
                                    .frame(width: barW, height: barMaxH)
                            }

                            RoundedRectangle(cornerRadius: 4)
                                .fill(hourlyAvg[h] == nil
                                      ? Color.white.opacity(0.07)
                                      : barColor(hourlyAvg[h]!))
                                .frame(width: barW, height: barH(h))

                            if h == currentHour {
                                RoundedRectangle(cornerRadius: 4)
                                    .strokeBorder(Color.white.opacity(0.6), lineWidth: 1.5)
                                    .frame(width: barW, height: barMaxH)
                            }
                        }
                        .onHover { isHovering in hoveredHour = isHovering ? h : nil }
                        .overlay(alignment: .bottom) {
                            if hoveredHour == h {
                                VStack(spacing: 2) {
                                    Text("\(h):00 h").font(.system(size: 9, weight: .bold)).foregroundColor(.white)
                                    if let avg = hourlyAvg[h] {
                                        Text("\(colorTheme == .orange ? "CPU" : "RAM") \(Int(avg))%").font(.system(size: 9)).foregroundColor(barColor(avg))
                                    }
                                    if let proc = hourlyTopProcess[h], !proc.isEmpty {
                                        Text(proc).font(.system(size: 8)).foregroundColor(.white.opacity(0.65)).lineLimit(1)
                                    }
                                }
                                .padding(.horizontal, 6).padding(.vertical, 4)
                                .background(RoundedRectangle(cornerRadius: 6).fill(Color.black.opacity(0.9)))
                                .offset(y: -(barMaxH + 8)).fixedSize().zIndex(10)
                            }
                        }

                        Group {
                            if h == currentHour { Text("NOW").font(.system(size: 7, weight: .bold)).foregroundColor(.white) }
                            else if h % 6 == 0 { Text("\(h)h").font(.system(size: 7)).foregroundColor(.white.opacity(0.35)) }
                            else { Color.clear.frame(height: 10) }
                        }
                    }
                }
            }
            .frame(height: barMaxH + 32)
        }
        .padding(16)
        .background(Color(red: 0.08, green: 0.10, blue: 0.13))
        .cornerRadius(14)
    }
}

class HostingerMonitor: ObservableObject {
    @Published var sshStatus: String
    @Published var scpStatus: String
    @Published var lastCheck: String
    @Published var diskInfo: String
    @Published var isChecking: Bool = false
    
    init() {
        let defaults = UserDefaults.standard
        sshStatus = defaults.string(forKey: "hostinger_ssh") ?? "OFFLINE"
        scpStatus = defaults.string(forKey: "hostinger_scp") ?? "OFFLINE"
        lastCheck = defaults.string(forKey: "hostinger_lastCheck") ?? "Nunca revisado"
        diskInfo  = defaults.string(forKey: "hostinger_disk") ?? "Desconocido"
    }
    
    func checkConnection() {
        guard !isChecking else { return }
        isChecking = true
        sshStatus = "TESTING..."
        scpStatus = "TESTING..."
        diskInfo = "Calculando..."
        
        DispatchQueue.global(qos: .userInitiated).async {
            let sshCmd = "ssh -o ConnectTimeout=5 -p 65002 -i ~/.ssh/id_rsa u129237724@185.212.70.24 \"echo 'OK'; df -h / | tail -n 1 | awk '{print \\$2, \\$3, \\$4, \\$5}'\""
            let tSsh = shell(sshCmd)
            let sshOk = tSsh.contains("OK")
            
            var dInfo = "No disponible"
            if sshOk {
                let lines = tSsh.components(separatedBy: "\n").filter { !$0.trimmingCharacters(in: .whitespaces).isEmpty }
                if lines.count >= 2 {
                    // lines[0] is "OK", lines[1] is "874G 615G 251G 72%"
                    let parts = lines[1].components(separatedBy: .whitespaces).filter { !$0.isEmpty }
                    if parts.count >= 4 {
                        let total = parts[0]; let used = parts[1]; let free = parts[2]; let pct = parts[3]
                        dInfo = "\(used) de \(total) (\(free) libres)"
                    }
                }
            }
            
            let df = DateFormatter()
            df.dateFormat = "HH:mm:ss"
            let timeStr = df.string(from: Date())
            
            DispatchQueue.main.async {
                self.sshStatus = sshOk ? "ONLINE" : "OFFLINE"
                self.scpStatus = sshOk ? "ONLINE" : "OFFLINE" // Si SSH pasa, SCP pasa
                self.diskInfo = dInfo
                self.lastCheck = timeStr
                
                // Persist to disk
                let defaults = UserDefaults.standard
                defaults.set(self.sshStatus, forKey: "hostinger_ssh")
                defaults.set(self.scpStatus, forKey: "hostinger_scp")
                defaults.set(self.lastCheck, forKey: "hostinger_lastCheck")
                defaults.set(self.diskInfo, forKey: "hostinger_disk")
                
                self.isChecking = false
            }
        }
    }
}

struct HostingerMonitorView: View {
    @StateObject private var monitor = HostingerMonitor()
    
    var body: some View {
        HStack {
            VStack(alignment: .leading, spacing: 4) {
                Text("🛰️ Producción (Hostinger)").bold()
                HStack(spacing: 12) {
                    HStack(spacing: 4) {
                        Circle().fill(monitor.sshStatus == "ONLINE" ? Color.green : (monitor.sshStatus == "TESTING..." ? Color.yellow : Color.red)).frame(width: 8, height: 8)
                        Text("SSH: \(monitor.sshStatus)").font(.caption).bold().foregroundColor(monitor.sshStatus == "ONLINE" ? .green : .secondary)
                    }
                    HStack(spacing: 4) {
                        Circle().fill(monitor.scpStatus == "ONLINE" ? Color.green : (monitor.scpStatus == "TESTING..." ? Color.yellow : Color.red)).frame(width: 8, height: 8)
                        Text("SCP: \(monitor.scpStatus)").font(.caption).bold().foregroundColor(monitor.scpStatus == "ONLINE" ? .green : .secondary)
                    }
                }
                Text("Disco: \(monitor.diskInfo)").font(.caption2).foregroundColor(.secondary)
                Text("Último ping: \(monitor.lastCheck)").font(.caption2).foregroundColor(.secondary)
            }
            Spacer()
            Button(monitor.isChecking ? "Revisando..." : "⚡ Test") {
                monitor.checkConnection()
            }
            .buttonStyle(BorderedProminentButtonStyle())
            .tint(Color.blue)
            .disabled(monitor.isChecking)
        }
        .padding()
        .frame(maxWidth: .infinity, alignment: .leading)
        .background(Color(NSColor.textBackgroundColor))
        .cornerRadius(10)
    }
}

struct OverviewView: View {
    @ObservedObject var monitor: ResourceMonitor

    private var cpuColor: Color {
        monitor.cpuPct < 25 ? .green : monitor.cpuPct < 55 ? .yellow : .red
    }
    private var ramUsedGB: Double { 16.0 - monitor.ramFreeGB }

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 20) {

                // Header
                HStack {
                    VStack(alignment: .leading) {
                        Text("Overview · Mac M2")
                            .font(.largeTitle).bold()
                        Text("Datos en tiempo real — actualiza cada 5 min")
                            .foregroundColor(.secondary)
                    }
                    Spacer()
                    VStack(alignment: .trailing, spacing: 4) {
                        Text("Ventana óptima para agentes:")
                            .font(.caption).foregroundColor(.secondary)
                        Text(monitor.optimalWindows)
                            .font(.caption).bold()
                            .padding(.horizontal, 8).padding(.vertical, 4)
                            .background(Color.green.opacity(0.15))
                            .cornerRadius(6)
                    }
                    .padding()
                    .background(RoundedRectangle(cornerRadius: 8).stroke(Color.green.opacity(0.4), lineWidth: 1))
                }
                .padding(.bottom, 4)

                // Métricas en tiempo real
                HStack(spacing: 16) {
                    ResourceCard(
                        label: "CPU",
                        value: String(format: "%.0f%%", monitor.cpuPct),
                        progress: monitor.cpuPct / 100,
                        color: cpuColor
                    )
                    ResourceCard(
                        label: "RAM USADA",
                        value: String(format: "%.1f / 16 GB", ramUsedGB),
                        progress: ramUsedGB / 16.0,
                        color: ramUsedGB < 10 ? .green : ramUsedGB < 13 ? .yellow : .red
                    )
                    ResourceCard(
                        label: "INACTIVIDAD",
                        value: String(format: "%.0f min", monitor.idleMin),
                        progress: min(monitor.idleMin / 30.0, 1.0),
                        color: monitor.idleMin > 10 ? .green : .orange
                    )
                }

                // Heatmap de recursos (CPU y RAM por hora del día)
                VStack(alignment: .leading, spacing: 12) {
                    ResourceHeatmapView(title: "Patrón de uso CPU · últimos 7 días", hourlyAvg: monitor.hourlyAvgCpu, hourlyTopProcess: monitor.hourlyTopProcess, colorTheme: .orange)

                    ResourceHeatmapView(title: "Consumo de Memoria RAM · últimos 7 días", hourlyAvg: monitor.hourlyAvgRam, colorTheme: .blue)
                }
                // Límites del sistema
                HStack(spacing: 16) {
                    VStack(alignment: .leading, spacing: 4) {
                        Text("Mac Mini M2 Silicon").bold()
                        Text("RAM total: 16 GB unificada")
                        Text("LLM seguro máx: ~10 GB (2 modelos 4b)")
                        Text("Dipper + William: nunca simultáneos").font(.caption).foregroundColor(.secondary)
                    }
                    .padding().frame(maxWidth: .infinity, alignment: .leading)
                    .background(Color(NSColor.textBackgroundColor)).cornerRadius(10)

                    HostingerMonitorView()
                }
                
                // Top Consumidores (10 Servicios)
                TopConsumersView()

            }
            .padding()
        }
    }
}

struct ProcessInfo: Identifiable {
    let id = UUID()
    let name: String
    let cpu: Double
    let ramMB: Double
    var cpuColor: Color { cpu > 15 ? .red : cpu > 5 ? .orange : .green }
}

class TopConsumersMonitor: ObservableObject {
    @Published var processes: [ProcessInfo] = []
    private var timer: Timer?

    init() {
        refresh()
        timer = Timer.scheduledTimer(withTimeInterval: 10, repeats: true) { _ in self.refresh() }
    }

    func refresh() {
        DispatchQueue.global(qos: .background).async {
            let t = Process(); let p = Pipe()
            t.executableURL = URL(fileURLWithPath: "/bin/sh")
            t.arguments = ["-c", "ps -axm -o %cpu,rss,comm | tail -n +2"]
            t.standardOutput = p
            guard (try? t.run()) != nil else { return }
            t.waitUntilExit()
            let raw = String(data: p.fileHandleForReading.readDataToEndOfFile(), encoding: .utf8) ?? ""
            var parsed: [ProcessInfo] = []
            for line in raw.components(separatedBy: "\n") {
                let parts = line.trimmingCharacters(in: .whitespaces).components(separatedBy: .whitespaces)
                guard parts.count >= 3,
                      let cpu = Double(parts[0]),
                      let rssKB = Double(parts[1]) else { continue }
                let name = parts[2...].joined(separator: " ")
                if name.isEmpty || name == "-" { continue }
                parsed.append(ProcessInfo(name: name, cpu: cpu, ramMB: rssKB / 1024))
            }
            // Aggregate by process name (same binary can appear multiple times)
            var agg: [String: (cpu: Double, ram: Double)] = [:]
            for p in parsed { agg[p.name, default: (0, 0)].cpu += p.cpu; agg[p.name, default: (0, 0)].ram += p.ramMB }
            let top10 = agg.map { ProcessInfo(name: $0.key, cpu: $0.value.cpu, ramMB: $0.value.ram) }
                .sorted { $0.cpu > $1.cpu }
                .prefix(10)
            DispatchQueue.main.async { self.processes = Array(top10) }
        }
    }
}

struct TopConsumersView: View {
    @StateObject private var monitor = TopConsumersMonitor()

    var body: some View {
        VStack(alignment: .leading, spacing: 10) {
            HStack {
                Text("🔥 Top 10 Consumidores actuales").font(.headline)
                Spacer()
                Button("↻") { monitor.refresh() }.font(.caption)
            }
            VStack(spacing: 8) {
                ForEach(Array(monitor.processes.enumerated()), id: \.element.id) { i, proc in
                    HStack {
                        Text("\(i + 1). \(proc.name)").bold().lineLimit(1)
                            .textSelection(.enabled)
                        Spacer()
                        Text(String(format: "CPU %.1f%% · RAM %.0f MB", proc.cpu, proc.ramMB))
                            .foregroundColor(proc.cpuColor).font(.caption)
                            .textSelection(.enabled)
                    }
                    if i < monitor.processes.count - 1 { Divider() }
                }

                if monitor.processes.isEmpty {
                    Text("Recopilando datos...").foregroundColor(.secondary).font(.caption)
                }
            }
            .padding()
            .background(Color(NSColor.textBackgroundColor))
            .cornerRadius(10)
        }
    }
}

// ==============================================================================
// MARK: — DipperMonitor (sobrevive cambios de tab, integra Kanban)
// ==============================================================================

class DipperMonitor: ObservableObject {
    weak var opsMonitor: OpsMonitor?
    private var currentTaskId: String?
    private var runningTask: Process?

    @Published var isRunning: Bool     = false
    @Published var status: String      = "SISTEMA LISTO"
    @Published var logLines: [String]  = ["Selecciona una acción para ver el output en tiempo real →"]
    @Published var backend: String     = "ollama"
    @Published var subredditInput: String = ""

    private let configFile = agentDir.appendingPathComponent("dipper_config.json")
    private let python3    = "/Library/Frameworks/Python.framework/Versions/3.11/bin/python3"

    init() { loadBackend() }

    func loadBackend() {
        guard let data = try? Data(contentsOf: configFile),
              let json = try? JSONSerialization.jsonObject(with: data) as? [String: Any],
              let b    = json["backend"] as? String else { return }
        backend = b
    }

    func saveBackend() {
        let json: [String: Any] = ["backend": backend]
        if let data = try? JSONSerialization.data(withJSONObject: json, options: .prettyPrinted) {
            try? data.write(to: configFile)
        }
    }

    func run(subreddit: String = "") {
        guard !isRunning else { return }
        isRunning = true
        logLines  = []
        status    = subreddit.isEmpty ? "EJECUTANDO RADAR..." : "EXCAVANDO \(subreddit)..."

        let label = subreddit.isEmpty ? "📡 Dipper · RADAR — todas las fuentes" : "⛏️ Dipper · EXCAVAR \(subreddit)"
        currentTaskId = opsMonitor?.addTask(title: label, agent: "Dipper", status: "in_progress", notes: "Iniciado desde Command Center")
        notify(title: "📡 Dipper arrancó", body: label, subtitle: "Keiyi Command Center")

        let scriptPath = agentDir.appendingPathComponent("dipper_scout.py").path

        DispatchQueue.global().async {
            let task = Process()
            task.executableURL   = URL(fileURLWithPath: self.python3)
            task.arguments       = subreddit.isEmpty ? [scriptPath] : [scriptPath, subreddit]
            task.currentDirectoryURL = agentDir
            task.environment     = Foundation.ProcessInfo.processInfo.environment
                .merging(["PYTHONUNBUFFERED": "1"]) { _, new in new }

            let pipe    = Pipe()
            let errPipe = Pipe()
            task.standardOutput = pipe
            task.standardError  = errPipe

            pipe.fileHandleForReading.readabilityHandler = { handle in
                let data = handle.availableData
                guard !data.isEmpty, let text = String(data: data, encoding: .utf8) else { return }
                let lines = text.components(separatedBy: "\n").filter { !$0.isEmpty }
                DispatchQueue.main.async {
                    self.logLines.append(contentsOf: lines)
                    if self.logLines.count > 300 { self.logLines.removeFirst(self.logLines.count - 300) }
                }
            }
            errPipe.fileHandleForReading.readabilityHandler = { handle in
                let data = handle.availableData
                guard !data.isEmpty, let text = String(data: data, encoding: .utf8) else { return }
                let lines = text.components(separatedBy: "\n").filter { !$0.isEmpty }
                DispatchQueue.main.async {
                    self.logLines.append(contentsOf: lines.map { "⚠️ \($0)" })
                }
            }

            self.runningTask = task
            try? task.run()
            task.waitUntilExit()

            pipe.fileHandleForReading.readabilityHandler    = nil
            errPipe.fileHandleForReading.readabilityHandler = nil

            let success = task.terminationStatus == 0
            DispatchQueue.main.async {
                self.runningTask  = nil
                self.isRunning    = false
                self.status       = success ? "MISIÓN COMPLETADA" : "ERROR — ver log"
                self.logLines.append("─────────────────────────────────────")
                self.logLines.append(success
                    ? "✅ Dipper completado. DB actualizada."
                    : "❌ Dipper terminó con error \(task.terminationStatus)")
                if let tid = self.currentTaskId {
                    self.opsMonitor?.completeTask(id: tid, success: success)
                    self.currentTaskId = nil
                }
                notify(title: success ? "✅ Dipper completó" : "❌ Dipper falló",
                       body: self.status, subtitle: "Keiyi Command Center")
            }
        }
    }

    func cancel() {
        runningTask?.terminate()
        runningTask = nil
        isRunning   = false
        status      = "CANCELADO"
        logLines.append("⛔ Cancelado por el usuario")
        if let tid = currentTaskId {
            opsMonitor?.completeTask(id: tid, success: false)
            currentTaskId = nil
        }
    }
}

// ==============================================================================
// MARK: — DipperOpsView
// ==============================================================================

struct DipperOpsView: View {
    @ObservedObject var dipper: DipperMonitor

    var body: some View {
        let dimBg    = Color(red: 0.10, green: 0.10, blue: 0.12)
        let panelBg  = Color(red: 0.13, green: 0.13, blue: 0.16)
        let divColor = Color(red: 0.25, green: 0.25, blue: 0.28)

        VStack(spacing: 0) {

            // ── HEADER ──────────────────────────────────────────────────────
            HStack(alignment: .center) {
                VStack(alignment: .leading, spacing: 2) {
                    Text("🔍 DIPPER OPS")
                        .font(.system(size: 18, weight: .black, design: .monospaced))
                    Text("Agente Detective de Tendencias Globales")
                        .font(.system(size: 10, design: .monospaced))
                        .foregroundColor(.secondary)
                }
                Spacer()
                HStack(spacing: 6) {
                    Circle()
                        .fill(dipper.isRunning ? Color.orange : Color.green)
                        .frame(width: 8, height: 8)
                        .shadow(color: dipper.isRunning ? .orange : .green, radius: 4)
                    Text(dipper.status)
                        .font(.system(size: 10, weight: .bold, design: .monospaced))
                        .foregroundColor(dipper.isRunning ? .orange : .green)
                }
            }
            .padding(.horizontal, 20).padding(.vertical, 12)
            .background(dimBg)

            // ── CONTENIDO PRINCIPAL — 2 columnas ────────────────────────────
            HStack(spacing: 0) {

                // COLUMNA IZQUIERDA — Terminal
                VStack(spacing: 0) {
                    HStack(spacing: 8) {
                        Circle().fill(Color.red.opacity(0.8)).frame(width: 10, height: 10)
                        Circle().fill(Color.yellow.opacity(0.8)).frame(width: 10, height: 10)
                        Circle().fill(Color.green.opacity(0.8)).frame(width: 10, height: 10)
                        Text("dipper · intel stream")
                            .font(.system(size: 10, design: .monospaced))
                            .foregroundColor(.secondary)
                        Spacer()
                        Button { dipper.logLines = [] } label: {
                            Text("CLR").font(.system(size: 9, design: .monospaced))
                        }
                        .buttonStyle(PlainButtonStyle()).foregroundColor(.secondary)
                    }
                    .padding(.horizontal, 12).padding(.vertical, 6)
                    .background(Color(red: 0.14, green: 0.14, blue: 0.17))

                    ScrollViewReader { proxy in
                        ScrollView {
                            LazyVStack(alignment: .leading, spacing: 1) {
                                ForEach(Array(dipper.logLines.enumerated()), id: \.offset) { idx, line in
                                    Text(line)
                                        .font(.system(size: 11, design: .monospaced))
                                        .foregroundColor(
                                            line.hasPrefix("❌") ? Color(red: 1, green: 0.35, blue: 0.35) :
                                            line.hasPrefix("⚠️") ? Color.yellow :
                                            line.hasPrefix("🔥") ? Color.orange :
                                            line.hasPrefix("🟣") ? Color(red: 0.75, green: 0.5, blue: 1) :
                                            line.hasPrefix("✅") || line.hasPrefix("🏁") ? Color(red: 0.3, green: 1, blue: 0.5) :
                                            Color(red: 0.55, green: 0.95, blue: 0.55)
                                        )
                                        .frame(maxWidth: .infinity, alignment: .leading)
                                        .id(idx)
                                }
                            }
                            .padding(12)
                        }
                        .onChange(of: dipper.logLines.count) { _ in
                            if let last = dipper.logLines.indices.last {
                                proxy.scrollTo(last, anchor: .bottom)
                            }
                        }
                    }
                    .background(Color(red: 0.04, green: 0.06, blue: 0.04))
                }
                .frame(maxWidth: .infinity, maxHeight: .infinity)

                divColor.frame(width: 1).frame(maxHeight: .infinity)

                // COLUMNA DERECHA — Motor + Intel
                VStack(spacing: 0) {
                    VStack(alignment: .leading, spacing: 10) {
                        Text("MOTOR")
                            .font(.system(size: 9, weight: .black, design: .monospaced))
                            .foregroundColor(Color(red: 0.5, green: 0.5, blue: 0.55))
                        HStack(spacing: 6) {
                            ForEach([("ollama","🧠","LOCAL"), ("gemini","🟣","GEMINI"), ("max","🔥","MAX")], id: \.0) { val, icon, label in
                                Button(action: { dipper.backend = val; dipper.saveBackend() }) {
                                    VStack(spacing: 2) {
                                        Text(icon).font(.system(size: 20))
                                        Text(label)
                                            .font(.system(size: 7, weight: .black, design: .monospaced))
                                            .foregroundColor(dipper.backend == val ? .white : Color(red: 0.5, green: 0.5, blue: 0.55))
                                    }
                                    .frame(maxWidth: .infinity)
                                    .padding(.vertical, 8)
                                    .background(dipper.backend == val
                                        ? Color(red: 0.2, green: 0.2, blue: 0.35)
                                        : Color(red: 0.15, green: 0.15, blue: 0.18))
                                    .cornerRadius(8)
                                    .overlay(RoundedRectangle(cornerRadius: 8)
                                        .stroke(dipper.backend == val ? Color.blue.opacity(0.5) : Color.clear, lineWidth: 1.5))
                                }
                                .buttonStyle(PlainButtonStyle())
                            }
                        }
                    }
                    .padding(14)
                    .background(panelBg)

                    divColor.frame(height: 1)

                    TopToolsSection(onRunCompleted: dipper.isRunning)
                        .padding(14)
                        .frame(maxHeight: .infinity, alignment: .top)
                }
                .frame(minWidth: 240, maxWidth: 300)
                .background(panelBg)
            }
            .frame(maxHeight: .infinity)

            // ── BARRA DE CONTROLES ───────────────────────────────────────────
            VStack(spacing: 0) {
                divColor.frame(height: 1)

                HStack(spacing: 16) {

                    // RADAR
                    Button(action: { dipper.run() }) {
                        HStack(spacing: 8) {
                            ZStack {
                                Circle()
                                    .fill(dipper.isRunning
                                        ? Color(red: 0.22, green: 0.22, blue: 0.25)
                                        : Color(red: 0.1, green: 0.35, blue: 0.75))
                                    .frame(width: 38, height: 38)
                                    .shadow(color: dipper.isRunning ? .clear : Color.blue.opacity(0.5),
                                            radius: 6, x: 0, y: 0)
                                Image(systemName: dipper.isRunning
                                    ? "waveform"
                                    : "antenna.radiowaves.left.and.right")
                                    .font(.system(size: 16))
                                    .foregroundColor(dipper.isRunning ? .gray : .white)
                            }
                            VStack(alignment: .leading, spacing: 1) {
                                Text("RADAR")
                                    .font(.system(size: 9, weight: .black, design: .monospaced))
                                    .foregroundColor(dipper.isRunning ? .gray : .white)
                                Text(dipper.isRunning ? "en curso..." : "escanear fuentes")
                                    .font(.system(size: 8, design: .monospaced))
                                    .foregroundColor(dipper.isRunning ? .orange : Color.blue.opacity(0.8))
                            }
                        }
                        .padding(.horizontal, 12).padding(.vertical, 8)
                        .background(dipper.isRunning
                            ? Color(red: 0.17, green: 0.17, blue: 0.20)
                            : Color(red: 0.12, green: 0.22, blue: 0.40))
                        .cornerRadius(10)
                        .overlay(RoundedRectangle(cornerRadius: 10)
                            .stroke(dipper.isRunning ? Color.gray.opacity(0.2) : Color.blue.opacity(0.35), lineWidth: 1))
                    }
                    .buttonStyle(PlainButtonStyle())
                    .disabled(dipper.isRunning)

                    // EXCAVAR
                    HStack(spacing: 6) {
                        Text("⛏️ r/")
                            .font(.system(size: 11, weight: .bold, design: .monospaced))
                            .foregroundColor(Color(red: 0.7, green: 0.5, blue: 0.2))
                        TextField("subreddit", text: $dipper.subredditInput)
                            .textFieldStyle(.plain)
                            .font(.system(size: 11, design: .monospaced))
                            .frame(minWidth: 80)
                            .padding(.horizontal, 7).padding(.vertical, 4)
                            .background(Color(red: 0.10, green: 0.10, blue: 0.13))
                            .cornerRadius(6)
                            .overlay(RoundedRectangle(cornerRadius: 6)
                                .stroke(Color(red: 0.35, green: 0.35, blue: 0.45), lineWidth: 1))
                            .foregroundColor(.white)
                        Button(action: {
                            let sub = dipper.subredditInput.trimmingCharacters(in: .whitespaces)
                            if !sub.isEmpty { dipper.run(subreddit: sub) }
                        }) {
                            Text("GO")
                                .font(.system(size: 10, weight: .black, design: .monospaced))
                                .padding(.horizontal, 10).padding(.vertical, 5)
                                .background(dipper.subredditInput.isEmpty || dipper.isRunning
                                    ? Color(red: 0.18, green: 0.18, blue: 0.20)
                                    : Color(red: 0.65, green: 0.35, blue: 0.05))
                                .foregroundColor(dipper.subredditInput.isEmpty || dipper.isRunning ? .gray : .white)
                                .cornerRadius(6)
                        }
                        .buttonStyle(PlainButtonStyle())
                        .disabled(dipper.isRunning || dipper.subredditInput.isEmpty)
                    }
                    .padding(.horizontal, 12).padding(.vertical, 8)
                    .background(Color(red: 0.15, green: 0.14, blue: 0.10))
                    .cornerRadius(10)
                    .overlay(RoundedRectangle(cornerRadius: 10)
                        .stroke(Color(red: 0.4, green: 0.35, blue: 0.15).opacity(0.5), lineWidth: 1))

                    Spacer()

                    // CLR LOG
                    Button(action: { dipper.logLines = [] }) {
                        HStack(spacing: 6) {
                            Image(systemName: "trash")
                                .font(.system(size: 13))
                                .foregroundColor(.red.opacity(0.7))
                            Text("CLR LOG")
                                .font(.system(size: 9, weight: .black, design: .monospaced))
                                .foregroundColor(.red.opacity(0.6))
                        }
                        .padding(.horizontal, 10).padding(.vertical, 7)
                        .background(Color(red: 0.18, green: 0.10, blue: 0.10))
                        .cornerRadius(8)
                        .overlay(RoundedRectangle(cornerRadius: 8)
                            .stroke(Color.red.opacity(0.25), lineWidth: 1))
                    }
                    .buttonStyle(PlainButtonStyle())
                }
                .padding(.horizontal, 16).padding(.vertical, 10)
                .background(panelBg)
            }
        }
    }
}

struct TopToolsSection: View {
    let onRunCompleted: Bool
    @StateObject private var loader = TrendDataLoader()

    var body: some View {
        VStack(alignment: .leading, spacing: 10) {
            HStack {
                Text("💡 TOP HERRAMIENTAS")
                    .font(.system(size: 9, weight: .black, design: .monospaced))
                    .foregroundColor(Color(red: 0.5, green: 0.5, blue: 0.55))
                Spacer()
                Button { loader.load() } label: {
                    Image(systemName: "arrow.clockwise")
                        .font(.system(size: 10))
                        .foregroundColor(.secondary)
                }
                .buttonStyle(PlainButtonStyle())
                .help("Recargar desde research_db.json")
            }

            if loader.tools.isEmpty {
                VStack(spacing: 6) {
                    Image(systemName: "waveform.slash")
                        .font(.system(size: 28))
                        .foregroundColor(.secondary.opacity(0.4))
                    Text("Sin datos aún")
                        .font(.system(size: 11, design: .monospaced))
                        .foregroundColor(.secondary)
                    Text("ejecuta Radar o Excavar")
                        .font(.system(size: 9, design: .monospaced))
                        .foregroundColor(.secondary.opacity(0.6))
                }
                .frame(maxWidth: .infinity)
                .padding(.vertical, 20)
            } else {
                ScrollView {
                    VStack(spacing: 4) {
                        ForEach(loader.tools.prefix(12)) { tool in
                            SignalRow(
                                name: tool.name,
                                trend: tool.intensity >= 7 ? "ALTO" : tool.intensity >= 4 ? "MED" : "BAJO",
                                signal: "\(tool.count)x · \(tool.lastSeenLabel)"
                            )
                        }
                    }
                }
            }
        }
        .onAppear { loader.load() }
        .onChange(of: onRunCompleted) { _ in loader.load() }
    }
}

struct SignalRow: View {
    let name: String
    let trend: String
    let signal: String
    
    var body: some View {
        HStack {
            VStack(alignment: .leading, spacing: 2) {
                Text(name).bold().font(.subheadline)
                Text(signal).font(.caption).foregroundColor(.secondary)
            }
            Spacer()
            Text(trend)
                .font(.system(size: 9, weight: .black))
                .padding(.horizontal, 8).padding(.vertical, 4)
                .background(trend == "ALTO" ? Color.red.opacity(0.2) : Color.yellow.opacity(0.2))
                .foregroundColor(trend == "ALTO" ? .red : .orange)
                .cornerRadius(4)
        }
        .padding(10)
        .background(Color(NSColor.textBackgroundColor))
        .cornerRadius(8)
        .overlay(RoundedRectangle(cornerRadius: 8).stroke(Color.secondary.opacity(0.15), lineWidth: 1))
    }
}

// ==============================================================================
// MARK: — SettingsView (Auto-run + Scheduling)
// ==============================================================================

struct SettingsView: View {
    @StateObject private var schedule = ScheduleController()

    private let barHeight: CGFloat = 36
    private let hourLabels = [0, 3, 6, 9, 12, 15, 18, 21]

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 0) {

                // HEADER
                VStack(alignment: .leading, spacing: 6) {
                    Text("Automatización")
                        .font(.system(size: 22, weight: .black))
                    Text("Configura qué agentes corren solos, cuándo y con qué frecuencia")
                        .font(.subheadline).foregroundColor(.secondary)
                }
                .padding(.horizontal, 28).padding(.top, 24).padding(.bottom, 20)

                // ── HORARIO ──────────────────────────────────────────────────
                VStack(alignment: .leading, spacing: 14) {
                    Label("Ventana de ejecución", systemImage: "clock.fill")
                        .font(.system(size: 13, weight: .bold))

                    // Visual bar
                    ZStack(alignment: .leading) {
                        RoundedRectangle(cornerRadius: 8)
                            .fill(Color(red: 0.10, green: 0.10, blue: 0.13))
                            .frame(height: barHeight)

                        GeometryReader { geo in
                            let w = geo.size.width
                            let x1 = schedule.startHour / 24 * w
                            let x2 = schedule.endHour / 24 * w
                            RoundedRectangle(cornerRadius: 6)
                                .fill(
                                    LinearGradient(
                                        colors: [Color.green.opacity(0.4), Color.green.opacity(0.15)],
                                        startPoint: .leading, endPoint: .trailing
                                    )
                                )
                                .frame(width: max(0, x2 - x1), height: barHeight - 4)
                                .offset(x: x1, y: 2)
                                .overlay(
                                    HStack {
                                        Text(schedule.fmtH(schedule.startHour))
                                        Spacer()
                                        Text(schedule.fmtH(schedule.endHour))
                                    }
                                    .font(.system(size: 10, weight: .bold, design: .monospaced))
                                    .foregroundColor(.green)
                                    .padding(.horizontal, 8)
                                    .frame(width: max(0, x2 - x1), height: barHeight - 4)
                                    .offset(x: x1, y: 2),
                                    alignment: .leading
                                )
                        }
                        .frame(height: barHeight)

                        GeometryReader { geo in
                            let w = geo.size.width
                            ForEach(hourLabels, id: \.self) { h in
                                Rectangle()
                                    .fill(Color.gray.opacity(0.25))
                                    .frame(width: 1, height: barHeight)
                                    .offset(x: CGFloat(h) / 24 * w)
                            }
                        }
                        .frame(height: barHeight)
                    }
                    .frame(height: barHeight)

                    // Hour labels
                    HStack(spacing: 0) {
                        ForEach(hourLabels, id: \.self) { h in
                            Text("\(h)")
                                .font(.system(size: 9, design: .monospaced))
                                .foregroundColor(.secondary)
                                .frame(maxWidth: .infinity, alignment: .leading)
                        }
                        Text("24")
                            .font(.system(size: 9, design: .monospaced))
                            .foregroundColor(.secondary)
                    }

                    // Sliders
                    HStack(spacing: 20) {
                        HStack(spacing: 6) {
                            Text("DESDE").font(.system(size: 9, weight: .bold, design: .monospaced)).foregroundColor(.secondary)
                            Slider(value: $schedule.startHour, in: 0...23, step: 1)
                                .onChange(of: schedule.startHour) { _ in
                                    if schedule.startHour >= schedule.endHour { schedule.endHour = min(24, schedule.startHour + 1) }
                                    schedule.persist()
                                }
                            Text(schedule.fmtH(schedule.startHour))
                                .font(.system(size: 12, weight: .bold, design: .monospaced))
                                .frame(width: 48)
                        }
                        HStack(spacing: 6) {
                            Text("HASTA").font(.system(size: 9, weight: .bold, design: .monospaced)).foregroundColor(.secondary)
                            Slider(value: $schedule.endHour, in: 1...24, step: 1)
                                .onChange(of: schedule.endHour) { _ in
                                    if schedule.endHour <= schedule.startHour { schedule.startHour = max(0, schedule.endHour - 1) }
                                    schedule.persist()
                                }
                            Text(schedule.fmtH(schedule.endHour))
                                .font(.system(size: 12, weight: .bold, design: .monospaced))
                                .frame(width: 48)
                        }
                    }

                    // Cooldown
                    HStack {
                        Label("Repetir cada:", systemImage: "arrow.clockwise")
                            .font(.system(size: 12))
                        Spacer()
                        Stepper("\(Int(schedule.cooldownHours))h", value: $schedule.cooldownHours, in: 1...24, step: 1)
                            .font(.system(size: 12, weight: .bold, design: .monospaced))
                            .frame(width: 120)
                            .onChange(of: schedule.cooldownHours) { _ in schedule.persist() }
                    }
                }
                .padding(20)
                .background(Color(NSColor.controlBackgroundColor))
                .cornerRadius(12)
                .padding(.horizontal, 24)

                // ── AGENTES ──────────────────────────────────────────────────
                VStack(alignment: .leading, spacing: 12) {
                    Label("Pipeline automático", systemImage: "arrow.triangle.branch")
                        .font(.system(size: 13, weight: .bold))
                        .padding(.bottom, 4)

                    Text("Los agentes activados corren en secuencia dentro de la ventana horaria. Orden: Perry → Dipper → William.")
                        .font(.system(size: 11)).foregroundColor(.secondary)

                    // Perry
                    AgentAutoCard(
                        icon: "🦆", name: "Perry", role: "Reconocimiento",
                        color: .green,
                        enabled: $schedule.perryOn,
                        actions: [
                            ActionToggle(key: "scrape",   icon: "📡", label: "SCRAPE",    desc: "Descarga posts de todas las fuentes aprobadas",              on: $schedule.perryScrape),
                            ActionToggle(key: "discover", icon: "🌐", label: "DESCUBRIR", desc: "Busca nuevas comunidades globales con Gemini (CEO aprueba)", on: $schedule.perryDiscover),
                        ],
                        onChange: { schedule.persist() }
                    )

                    // Dipper
                    AgentAutoCard(
                        icon: "📡", name: "Dipper", role: "Inteligencia",
                        color: .orange,
                        enabled: $schedule.dipperOn,
                        actions: [
                            ActionToggle(key: "radar", icon: "🔍", label: "RADAR", desc: "Excava fuentes calientes y extrae herramientas/preguntas con IA local", on: $schedule.dipperRadar),
                        ],
                        onChange: { schedule.persist() }
                    )

                    // William
                    AgentAutoCard(
                        icon: "✍️", name: "William", role: "Redacción",
                        color: .purple,
                        enabled: $schedule.williamOn,
                        actions: [
                            ActionToggle(key: "redactar", icon: "📝", label: "REDACTAR", desc: "Genera borradores de blog SEO desde la inteligencia de Dipper", on: $schedule.williamRedactar),
                        ],
                        onChange: { schedule.persist() }
                    )
                }
                .padding(20)
                .background(Color(NSColor.controlBackgroundColor))
                .cornerRadius(12)
                .padding(.horizontal, 24)
                .padding(.top, 16)

                // ── PIPELINE PREVIEW ─────────────────────────────────────────
                let perryActions = [schedule.perryScrape ? "scrape" : nil, schedule.perryDiscover ? "discover" : nil].compactMap { $0 }
                let active = [
                    schedule.perryOn && !perryActions.isEmpty ? "🦆 Perry (\(perryActions.joined(separator: " → ")))" : nil,
                    schedule.dipperOn && schedule.dipperRadar ? "📡 Dipper (radar)" : nil,
                    schedule.williamOn && schedule.williamRedactar ? "✍️ William (redactar)" : nil
                ].compactMap { $0 }

                VStack(alignment: .leading, spacing: 8) {
                    Label("Resumen", systemImage: "list.bullet.clipboard")
                        .font(.system(size: 13, weight: .bold))
                    if active.isEmpty {
                        HStack(spacing: 6) {
                            Image(systemName: "exclamationmark.triangle.fill")
                                .foregroundColor(.yellow)
                            Text("Ningún agente activado — el auto-run no ejecutará nada.")
                                .font(.system(size: 11))
                                .foregroundColor(.secondary)
                        }
                    } else {
                        ForEach(Array(active.enumerated()), id: \.offset) { idx, step in
                            HStack(spacing: 6) {
                                Text("\(idx + 1).")
                                    .font(.system(size: 11, weight: .bold, design: .monospaced))
                                    .foregroundColor(.green)
                                Text(step)
                                    .font(.system(size: 11, design: .monospaced))
                            }
                        }
                        Divider()
                        Text("Horario: \(schedule.fmtH(schedule.startHour))–\(schedule.fmtH(schedule.endHour))  ·  cada \(Int(schedule.cooldownHours))h  ·  requiere ≥4 GB RAM libre")
                            .font(.system(size: 10, design: .monospaced))
                            .foregroundColor(.secondary)
                    }
                }
                .padding(20)
                .background(Color(NSColor.controlBackgroundColor))
                .cornerRadius(12)
                .padding(.horizontal, 24)
                .padding(.top, 16)

                // ── LOG DE ACTIVIDAD ──────────────────────────────────────
                ActivityLogPanel()

                Spacer(minLength: 40)
            }
        }
    }
}

// ==============================================================================
// MARK: — ActivityLogPanel
// ==============================================================================

class ActivityLogLoader: ObservableObject {
    @Published var lines: [String] = []
    private var timer: Timer?
    private let logPath = FileManager.default.homeDirectoryForCurrentUser
        .appendingPathComponent("Library/Logs/keiyi_idle_agent.log")
    private var lastSize: UInt64 = 0

    init() {
        load()
        timer = Timer.scheduledTimer(withTimeInterval: 5, repeats: true) { [weak self] _ in
            self?.load()
        }
    }

    func load() {
        guard let attrs = try? FileManager.default.attributesOfItem(atPath: logPath.path),
              let size = attrs[.size] as? UInt64 else {
            if lines.isEmpty { lines = ["Sin archivo de log aún — los agentes no han corrido."] }
            return
        }
        // Only reload if file changed
        guard size != lastSize else { return }
        lastSize = size

        guard let content = try? String(contentsOf: logPath, encoding: .utf8) else { return }
        let all = content.components(separatedBy: "\n").filter { !$0.isEmpty }
        DispatchQueue.main.async {
            self.lines = Array(all.suffix(200))  // last 200 lines
        }
    }

    func clear() {
        try? "".write(to: logPath, atomically: true, encoding: .utf8)
        lastSize = 0
        lines = []
    }
}

struct ActivityLogPanel: View {
    @StateObject private var loader = ActivityLogLoader()
    @State private var autoScroll = true

    var body: some View {
        VStack(alignment: .leading, spacing: 0) {
            // Header
            HStack {
                Label("Log de actividad", systemImage: "text.alignleft")
                    .font(.system(size: 13, weight: .bold))
                Spacer()
                Text("\(loader.lines.count) líneas")
                    .font(.system(size: 9, design: .monospaced))
                    .foregroundColor(.secondary)
                Button(action: { loader.clear() }) {
                    Text("CLR")
                        .font(.system(size: 9, weight: .bold, design: .monospaced))
                        .foregroundColor(.red.opacity(0.7))
                        .padding(.horizontal, 8).padding(.vertical, 3)
                        .background(Color.red.opacity(0.08))
                        .cornerRadius(4)
                }
                .buttonStyle(PlainButtonStyle())
                Button(action: { loader.load() }) {
                    Image(systemName: "arrow.clockwise")
                        .font(.system(size: 10))
                        .foregroundColor(.secondary)
                }
                .buttonStyle(PlainButtonStyle())
            }
            .padding(.horizontal, 16).padding(.vertical, 10)
            .background(Color(red: 0.10, green: 0.10, blue: 0.12))

            // Terminal
            ScrollViewReader { proxy in
                ScrollView {
                    LazyVStack(alignment: .leading, spacing: 0) {
                        ForEach(Array(loader.lines.enumerated()), id: \.offset) { idx, line in
                            Text(line)
                                .font(.system(size: 10, design: .monospaced))
                                .foregroundColor(logColor(line))
                                .frame(maxWidth: .infinity, alignment: .leading)
                                .padding(.horizontal, 12)
                                .padding(.vertical, 1)
                                .id(idx)
                        }
                    }
                }
                .onChange(of: loader.lines.count) { _ in
                    if autoScroll, let last = loader.lines.indices.last {
                        proxy.scrollTo(last, anchor: .bottom)
                    }
                }
            }
            .frame(height: 260)
            .background(Color(red: 0.04, green: 0.04, blue: 0.06))
        }
        .cornerRadius(12)
        .overlay(RoundedRectangle(cornerRadius: 12)
            .stroke(Color.secondary.opacity(0.15), lineWidth: 1))
        .padding(.horizontal, 24)
        .padding(.top, 16)
    }

    private func logColor(_ line: String) -> Color {
        if line.contains("ERROR") || line.contains("FALLO") || line.contains("❌") { return Color(red: 1, green: 0.35, blue: 0.35) }
        if line.contains("✅") || line.contains("COMPLETADO") || line.contains("OK") { return Color(red: 0.3, green: 0.9, blue: 0.4) }
        if line.contains("⚠️") || line.contains("WARNING") { return .yellow }
        if line.contains("===") || line.contains("RUN INICIADO") { return .cyan }
        if line.contains("Perry") { return Color(red: 0.4, green: 0.9, blue: 0.4) }
        if line.contains("Dipper") { return .orange }
        if line.contains("William") { return Color(red: 0.7, green: 0.5, blue: 1) }
        if line.contains("iniciado") || line.contains("arrancó") { return .blue }
        return Color(red: 0.5, green: 0.5, blue: 0.55)
    }
}

struct ActionToggle: Identifiable {
    let id: String
    let icon: String
    let label: String
    let desc: String
    var on: Binding<Bool>

    init(key: String, icon: String, label: String, desc: String, on: Binding<Bool>) {
        self.id = key; self.icon = icon; self.label = label; self.desc = desc; self.on = on
    }
}

struct AgentAutoCard: View {
    let icon: String
    let name: String
    let role: String
    let color: Color
    @Binding var enabled: Bool
    let actions: [ActionToggle]
    let onChange: () -> Void

    var body: some View {
        VStack(alignment: .leading, spacing: 0) {
            // Header row
            HStack(spacing: 12) {
                Text(icon)
                    .font(.system(size: 28))
                    .frame(width: 44, height: 44)
                    .background(enabled ? color.opacity(0.15) : Color(red: 0.15, green: 0.15, blue: 0.18))
                    .cornerRadius(10)

                VStack(alignment: .leading, spacing: 2) {
                    HStack(spacing: 6) {
                        Text(name).font(.system(size: 14, weight: .bold))
                        Text("· \(role)")
                            .font(.system(size: 11))
                            .foregroundColor(.secondary)
                    }
                    let activeCount = actions.filter { $0.on.wrappedValue }.count
                    Text(enabled ? "\(activeCount) de \(actions.count) acciones activas" : "Desactivado")
                        .font(.system(size: 10))
                        .foregroundColor(enabled ? .secondary : .red.opacity(0.6))
                }

                Spacer()

                Toggle("", isOn: $enabled)
                    .toggleStyle(SwitchToggleStyle(tint: color))
                    .labelsHidden()
                    .onChange(of: enabled) { _ in onChange() }
            }

            // Action toggles (visible when enabled)
            if enabled {
                VStack(spacing: 0) {
                    ForEach(actions) { action in
                        HStack(spacing: 10) {
                            Text(action.icon).font(.system(size: 14))
                            VStack(alignment: .leading, spacing: 1) {
                                Text(action.label)
                                    .font(.system(size: 10, weight: .bold, design: .monospaced))
                                    .foregroundColor(action.on.wrappedValue ? .primary : .secondary)
                                Text(action.desc)
                                    .font(.system(size: 9))
                                    .foregroundColor(.secondary)
                                    .lineLimit(1)
                            }
                            Spacer()
                            Toggle("", isOn: action.on)
                                .toggleStyle(SwitchToggleStyle(tint: color))
                                .labelsHidden()
                                .scaleEffect(0.8)
                                .onChange(of: action.on.wrappedValue) { _ in onChange() }
                        }
                        .padding(.vertical, 6)
                        .padding(.horizontal, 10)
                        if action.id != actions.last?.id {
                            Divider().padding(.leading, 36)
                        }
                    }
                }
                .padding(.top, 8)
                .background(Color(NSColor.windowBackgroundColor).opacity(0.5))
                .cornerRadius(8)
                .padding(.top, 8)
            }
        }
        .padding(12)
        .background(enabled
            ? color.opacity(0.05)
            : Color(NSColor.windowBackgroundColor))
        .cornerRadius(10)
        .overlay(RoundedRectangle(cornerRadius: 10)
            .stroke(enabled ? color.opacity(0.3) : Color.secondary.opacity(0.1), lineWidth: 1))
    }
}

/// Reads/writes schedule + agent enabled flags from idle_config.json
class ScheduleController: ObservableObject {
    @Published var startHour: Double = 9
    @Published var endHour: Double = 22
    @Published var cooldownHours: Double = 6
    // Agent toggles
    @Published var perryOn: Bool = true
    @Published var dipperOn: Bool = true
    @Published var williamOn: Bool = true
    // Perry actions
    @Published var perryScrape: Bool = true
    @Published var perryDiscover: Bool = false
    // Dipper actions
    @Published var dipperRadar: Bool = true
    // William actions
    @Published var williamRedactar: Bool = true

    init() { load() }

    func load() {
        guard let cfg = loadConfig() else { return }
        startHour     = Double(parseHour(cfg.schedule.timeStart))
        endHour       = Double(parseHour(cfg.schedule.timeEnd))
        cooldownHours = cfg.schedule.runCooldownHours
        perryOn       = cfg.agents.perry?.enabled ?? false
        dipperOn      = cfg.agents.dipper.enabled
        williamOn     = cfg.agents.william.enabled
        // Perry actions from config
        let perryActions = cfg.agents.perry?.actions ?? ["scrape"]
        perryScrape   = perryActions.contains("scrape")
        perryDiscover = perryActions.contains("discover")
    }

    func persist() {
        guard var cfg = loadConfig() else { return }
        cfg.schedule.mode = "time_window"
        cfg.schedule.timeStart = fmtH(startHour)
        cfg.schedule.timeEnd   = fmtH(endHour)
        cfg.schedule.runCooldownHours = cooldownHours
        // Perry with granular actions
        var perryActions: [String] = []
        if perryScrape   { perryActions.append("scrape") }
        if perryDiscover { perryActions.append("discover") }
        if cfg.agents.perry != nil {
            cfg.agents.perry!.enabled = perryOn
            cfg.agents.perry!.actions = perryActions
        } else {
            cfg.agents.perry = .init(enabled: perryOn, actions: perryActions, backend: "gemini")
        }
        cfg.agents.dipper.enabled  = dipperOn
        cfg.agents.william.enabled = williamOn
        cfg.minIdleVotes = 1
        cfg.detectors.ramMonitor.enabled = true
        cfg.detectors.keyboardMouse.enabled = false
        cfg.detectors.cpuMonitor.enabled = false
        saveConfig(cfg)
    }

    func fmtH(_ h: Double) -> String {
        let hour = Int(h)
        return String(format: "%02d:00", hour == 24 ? 0 : hour)
    }

    private func parseHour(_ s: String) -> Int {
        s.components(separatedBy: ":").compactMap { Int($0) }.first ?? 0
    }
}

// ==============================================================================
// MARK: — HelpPortalView
// ==============================================================================

struct HelpPortalView: View {

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 0) {

                // HEADER
                VStack(alignment: .leading, spacing: 6) {
                    Text("📖 Guía del Sistema")
                        .font(.system(size: 22, weight: .black))
                    Text("Cómo funciona el pipeline de inteligencia de Keiyi Digital")
                        .font(.subheadline).foregroundColor(.secondary)
                }
                .padding(.horizontal, 28).padding(.top, 24).padding(.bottom, 20)

                // EL PIPELINE
                HelpSection(icon: "arrow.right.circle.fill", title: "El Pipeline completo", color: .blue) {
                    VStack(alignment: .leading, spacing: 12) {
                        HelpPipelineRow(
                            agent: "🦆 Perry",
                            role: "Reconocimiento",
                            arrow: true,
                            description: "Monitorea fuentes, detecta actividad, propone nuevas"
                        )
                        HelpPipelineRow(
                            agent: "📡 Dipper",
                            role: "Inteligencia",
                            arrow: true,
                            description: "Extrae herramientas, pain-points y tendencias con IA"
                        )
                        HelpPipelineRow(
                            agent: "✍️ William",
                            role: "Redacción",
                            arrow: false,
                            description: "Redacta borradores de artículos para el blog"
                        )
                        Divider()
                        Text("La secuencia es siempre en ese orden. Perry alimenta a Dipper, Dipper alimenta a William.")
                            .font(.caption).foregroundColor(.secondary)
                    }
                }

                // PERRY
                HelpSection(icon: "globe.americas.fill", title: "Perry — Reconocimiento", color: .green) {
                    VStack(alignment: .leading, spacing: 10) {
                        HelpTask(
                            name: "SCRAPE",
                            when: "Cada 30 min (cuando el Mac está idle)",
                            what: "Descarga posts de las 41 fuentes aprobadas. Calcula engagement (posts × upvotes × comentarios) y actualiza el score de cada fuente.",
                            saves: "hot_sources.json — top 22 fuentes más activas\nsources_radar.json — scores actualizados\nraw_cache/ — texto crudo descargado"
                        )
                        Divider()
                        HelpTask(
                            name: "DISCOVER",
                            when: "Cada 7 días (cuando el Mac está idle)",
                            what: "Claude + Gemini buscan comunidades nuevas globalmente en todos los idiomas. Las propone con una razón de relevancia.",
                            saves: "sources_radar.json — nuevas fuentes con status 'pending'"
                        )
                        Divider()
                        HelpHighlight(
                            icon: "👤",
                            text: "Tu participación: revisar las fuentes pendientes que DISCOVER propone y aprobarlas o rechazarlas. SCRAPE solo trabaja con fuentes aprobadas."
                        )
                    }
                }

                // DIPPER
                HelpSection(icon: "antenna.radiowaves.left.and.right", title: "Dipper — Inteligencia", color: .orange) {
                    VStack(alignment: .leading, spacing: 10) {
                        HelpTask(
                            name: "RADAR",
                            when: "Después de Perry (en idle automático)",
                            what: "Lee hot_sources.json de Perry y excava las top 12 fuentes. Para cada una extrae con IA las herramientas mencionadas, pain-points y preguntas frecuentes. Al terminar genera el brief semanal.",
                            saves: "research_db.json — inteligencia acumulada por fuente\nweekly_brief.json — brief consolidado listo para William"
                        )
                        Divider()
                        HelpTask(
                            name: "EXCAVAR",
                            when: "Manual — cuando tú lo pides",
                            what: "Excava una sola fuente en profundidad (subreddit, blog, GitHub, Hacker News). Actualiza research_db.json pero no genera brief.",
                            saves: "research_db.json — actualiza solo esa fuente"
                        )
                        Divider()
                        HelpHighlight(
                            icon: "👤",
                            text: "Tu participación: lanzar EXCAVAR cuando quieres profundizar en una fuente concreta. El RADAR corre solo."
                        )
                    }
                }

                // WILLIAM
                HelpSection(icon: "pencil.and.outline", title: "William — Redactor", color: .purple) {
                    VStack(alignment: .leading, spacing: 10) {
                        HelpTask(
                            name: "REDACTAR",
                            when: "Manual o cuando hay brief nuevo",
                            what: "Lee weekly_brief.json de Dipper. Usa los ángulos editoriales generados por Gemini para redactar borradores de artículos completos.",
                            saves: "william_drafts/ — borradores en texto plano"
                        )
                        Divider()
                        HelpHighlight(
                            icon: "👤",
                            text: "Tu participación: revisar los borradores y decidir cuáles publicar en el blog."
                        )
                    }
                }

                // ARCHIVOS
                HelpSection(icon: "folder.fill", title: "Archivos — dónde está todo", color: .gray) {
                    VStack(alignment: .leading, spacing: 6) {
                        HelpFileRow(file: "sources_radar.json",  owner: "Perry",  desc: "41 fuentes monitoreadas (aprobadas + pendientes)")
                        HelpFileRow(file: "hot_sources.json",    owner: "Perry",  desc: "Top 22 fuentes más activas → entrada de Dipper")
                        HelpFileRow(file: "research_db.json",    owner: "Dipper", desc: "Inteligencia acumulada por fuente (herramientas, preguntas, refs)")
                        HelpFileRow(file: "weekly_brief.json",   owner: "Dipper", desc: "Brief consolidado + 6 ángulos editoriales → entrada de William")
                        HelpFileRow(file: "william_drafts/",     owner: "William",desc: "Borradores de artículos listos para revisar")
                        HelpFileRow(file: "seen_comments.json",  owner: "Sistema",desc: "Hashes anti-duplicado — evita reprocesar lo ya visto")
                        Divider()
                        Text("Todos los archivos viven en Google Drive → keiyi_scout_intelligence/")
                            .font(.caption).foregroundColor(.secondary)
                    }
                }

                // TU ROL
                HelpSection(icon: "person.fill.checkmark", title: "Tu rol como CEO", color: .yellow) {
                    VStack(alignment: .leading, spacing: 8) {
                        HelpHighlight(icon: "✅", text: "Aprobar/rechazar fuentes nuevas que DISCOVER propone (en Perry → tab Pendientes)")
                        HelpHighlight(icon: "🔍", text: "Lanzar EXCAVAR cuando una fuente merece análisis más profundo")
                        HelpHighlight(icon: "📋", text: "Revisar el weekly_brief.json — los ángulos editoriales que generó Gemini")
                        HelpHighlight(icon: "✍️", text: "Revisar y publicar los borradores de William")
                        HelpHighlight(icon: "⚙️", text: "Configurar backends (LOCAL/GEMINI/MAX) según RAM disponible")
                    }
                }

                Spacer().frame(height: 30)
            }
        }
    }
}

// MARK: Help Components

struct HelpSection<Content: View>: View {
    let icon: String
    let title: String
    let color: Color
    @ViewBuilder let content: () -> Content

    var body: some View {
        VStack(alignment: .leading, spacing: 12) {
            HStack(spacing: 8) {
                Image(systemName: icon)
                    .foregroundColor(color)
                    .font(.system(size: 14, weight: .bold))
                Text(title)
                    .font(.system(size: 14, weight: .black))
            }
            content()
        }
        .padding(20)
        .background(Color(NSColor.windowBackgroundColor))
        .cornerRadius(12)
        .overlay(RoundedRectangle(cornerRadius: 12)
            .stroke(color.opacity(0.2), lineWidth: 1))
        .padding(.horizontal, 20).padding(.bottom, 12)
    }
}

struct HelpTask: View {
    let name: String
    let when: String
    let what: String
    let saves: String

    var body: some View {
        VStack(alignment: .leading, spacing: 6) {
            Text(name)
                .font(.system(size: 12, weight: .black, design: .monospaced))
                .foregroundColor(.accentColor)
            HStack(alignment: .top, spacing: 6) {
                Text("⏱").font(.caption)
                Text(when).font(.caption).foregroundColor(.secondary)
            }
            HStack(alignment: .top, spacing: 6) {
                Text("▶").font(.caption).foregroundColor(.secondary)
                Text(what).font(.caption)
            }
            HStack(alignment: .top, spacing: 6) {
                Text("💾").font(.caption)
                Text(saves)
                    .font(.system(size: 10, design: .monospaced))
                    .foregroundColor(.secondary)
            }
        }
    }
}

struct HelpPipelineRow: View {
    let agent: String
    let role: String
    let arrow: Bool
    let description: String

    var body: some View {
        HStack(spacing: 12) {
            VStack(spacing: 2) {
                Text(agent).font(.system(size: 13, weight: .black))
                Text(role).font(.system(size: 9, design: .monospaced)).foregroundColor(.secondary)
            }
            .frame(width: 100)
            if arrow {
                Image(systemName: "arrow.right").foregroundColor(.secondary).font(.caption)
            }
            Text(description).font(.caption).foregroundColor(.secondary)
            Spacer()
        }
    }
}

struct HelpFileRow: View {
    let file: String
    let owner: String
    let desc: String

    var body: some View {
        HStack(alignment: .top, spacing: 10) {
            Text(file)
                .font(.system(size: 10, weight: .bold, design: .monospaced))
                .frame(width: 170, alignment: .leading)
            Text(owner)
                .font(.system(size: 9, weight: .black, design: .monospaced))
                .foregroundColor(.secondary)
                .frame(width: 55)
            Text(desc).font(.caption).foregroundColor(.secondary)
        }
        .padding(.vertical, 2)
    }
}

struct HelpHighlight: View {
    let icon: String
    let text: String

    var body: some View {
        HStack(alignment: .top, spacing: 8) {
            Text(icon).font(.system(size: 14))
            Text(text).font(.caption)
        }
        .padding(10)
        .background(Color.accentColor.opacity(0.06))
        .cornerRadius(8)
    }
}

// ==============================================================================
// MARK: — WilliamsDeskView
// ==============================================================================

struct DraftItem: Identifiable {
    let id = UUID()
    let url: URL
    let title: String
    let excerpt: String
    let category: String
    let wordCount: Int
    let editor: String   // "william" | "claude"
    let date: String
    let format: String   // "json" | "md"
    let issues: [String] // validation issues
    var isValid: Bool { issues.isEmpty }
}

struct WilliamsDeskView: View {
    @State private var drafts: [DraftItem] = []
    @State private var isGenerating = false
    @State private var selectedDraft: DraftItem? = nil
    @State private var draftHTML: String = ""
    @State private var showingDraft = false
    @State private var publishMsg: String = ""

    private let gdriveDrafts: URL = {
        URL(fileURLWithPath: NSHomeDirectory())
            .appendingPathComponent("Library/CloudStorage/GoogleDrive-anuarlezama@gmail.com/My Drive/gemini/william_drafts")
    }()

    private func loadDrafts() {
        DispatchQueue.global(qos: .userInitiated).async {
            let fm = FileManager.default
            guard let files = try? fm.contentsOfDirectory(at: gdriveDrafts,
                includingPropertiesForKeys: [.contentModificationDateKey], options: [.skipsHiddenFiles]) else {
                return
            }
            let jsonFiles = files.filter { $0.pathExtension == "json" }
                .sorted {
                    let d1 = (try? $0.resourceValues(forKeys: [.contentModificationDateKey]).contentModificationDate) ?? .distantPast
                    let d2 = (try? $1.resourceValues(forKeys: [.contentModificationDateKey]).contentModificationDate) ?? .distantPast
                    return d1 > d2
                }

            var items: [DraftItem] = []
            let fmt = DateFormatter(); fmt.dateFormat = "d MMM yyyy"; fmt.locale = Locale(identifier: "es_MX")

            for file in jsonFiles {
                guard let data = try? Data(contentsOf: file),
                      let json = try? JSONSerialization.jsonObject(with: data) as? [String: Any] else { continue }
                let modDate = (try? file.resourceValues(forKeys: [.contentModificationDateKey]).contentModificationDate) ?? Date()
                items.append(DraftItem(
                    url: file,
                    title: json["title"] as? String ?? file.deletingPathExtension().lastPathComponent,
                    excerpt: json["excerpt"] as? String ?? "",
                    category: json["category"] as? String ?? "—",
                    wordCount: json["word_count"] as? Int ?? 0,
                    editor: json["editor"] as? String ?? "william",
                    date: fmt.string(from: modDate)
                ))
            }
            DispatchQueue.main.async { self.drafts = items }
        }
    }

    private func runWilliam() {
        guard !isGenerating else { return }
        isGenerating = true
        let scriptPath = agentDir.appendingPathComponent("william.py").path
        DispatchQueue.global().async {
            let task = Process()
            task.executableURL = URL(fileURLWithPath: "/Library/Frameworks/Python.framework/Versions/3.11/bin/python3")
            task.arguments = [scriptPath]
            var env = Foundation.ProcessInfo.processInfo.environment
            env["PYTHONUNBUFFERED"] = "1"
            task.environment = env
            let pipe = Pipe(); task.standardOutput = pipe; task.standardError = pipe
            try? task.run(); task.waitUntilExit()
            DispatchQueue.main.async { self.isGenerating = false; self.loadDrafts() }
        }
    }

    private func openDraft(_ item: DraftItem) {
        guard let data = try? Data(contentsOf: item.url),
              let json = try? JSONSerialization.jsonObject(with: data) as? [String: Any] else { return }
        draftHTML = json["content"] as? String ?? "Sin contenido"
        selectedDraft = item
        showingDraft = true
    }

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 16) {

                // ── HEADER ───────────────────────────────────────────────
                HStack {
                    VStack(alignment: .leading, spacing: 4) {
                        Text("✍️ William · Mesa de Redacción")
                            .font(.system(size: 22, weight: .black))
                        Text("Borradores listos para revisión del CEO")
                            .font(.subheadline).foregroundColor(.secondary)
                    }
                    Spacer()
                    Button(action: { runWilliam() }) {
                        HStack(spacing: 6) {
                            if isGenerating {
                                ProgressView().scaleEffect(0.7)
                            }
                            Text(isGenerating ? "Escribiendo..." : "Generar nuevos")
                                .font(.system(size: 12, weight: .bold))
                        }
                    }
                    .buttonStyle(BorderedProminentButtonStyle())
                    .disabled(isGenerating)

                    Button(action: { loadDrafts() }) {
                        Image(systemName: "arrow.clockwise")
                    }
                    .buttonStyle(BorderedButtonStyle())
                }

                // ── DRAFTS LIST ──────────────────────────────────────────
                if drafts.isEmpty {
                    VStack(spacing: 8) {
                        Image(systemName: "doc.text.magnifyingglass")
                            .font(.system(size: 36)).foregroundColor(.secondary.opacity(0.4))
                        Text("Sin borradores").font(.headline).foregroundColor(.secondary)
                        Text("Ejecuta William o usa /blog en Claude Code para generar artículos")
                            .font(.caption).foregroundColor(.secondary)
                    }
                    .frame(maxWidth: .infinity).padding(40)
                } else {
                    Text("\(drafts.count) borradores").font(.caption).foregroundColor(.secondary)

                    ForEach(drafts) { item in
                        HStack(spacing: 12) {
                            // Color accent
                            RoundedRectangle(cornerRadius: 2)
                                .fill(item.editor == "claude" ? Color.blue : Color.purple)
                                .frame(width: 3)

                            VStack(alignment: .leading, spacing: 4) {
                                // Title
                                Text(item.title)
                                    .font(.system(size: 14, weight: .bold))
                                    .lineLimit(2)

                                // Excerpt
                                if !item.excerpt.isEmpty {
                                    Text(item.excerpt)
                                        .font(.system(size: 11))
                                        .foregroundColor(.secondary)
                                        .lineLimit(2)
                                }

                                // Meta
                                HStack(spacing: 8) {
                                    Text(item.category)
                                        .font(.system(size: 9, weight: .bold, design: .monospaced))
                                        .padding(.horizontal, 6).padding(.vertical, 2)
                                        .background(Color.secondary.opacity(0.1))
                                        .cornerRadius(4)
                                    if item.wordCount > 0 {
                                        Text("\(item.wordCount) palabras")
                                            .font(.system(size: 9, design: .monospaced))
                                            .foregroundColor(.secondary)
                                    }
                                    Text(item.editor == "claude" ? "Claude" : "William")
                                        .font(.system(size: 9, weight: .medium, design: .monospaced))
                                        .foregroundColor(item.editor == "claude" ? .blue : .purple)
                                    Spacer()
                                    Text(item.date)
                                        .font(.system(size: 9, design: .monospaced))
                                        .foregroundColor(.secondary)
                                }
                            }

                            // Actions
                            VStack(spacing: 6) {
                                Button("Revisar") { openDraft(item) }
                                    .font(.system(size: 10, weight: .bold))
                                    .buttonStyle(BorderedProminentButtonStyle())

                                Button(action: {
                                    deleteDraft(item)
                                }) {
                                    Image(systemName: "trash")
                                        .font(.system(size: 10))
                                        .foregroundColor(.red.opacity(0.6))
                                }
                                .buttonStyle(PlainButtonStyle())
                            }
                        }
                        .padding(12)
                        .background(Color(NSColor.controlBackgroundColor))
                        .cornerRadius(8)
                        .overlay(RoundedRectangle(cornerRadius: 8)
                            .stroke(Color.secondary.opacity(0.12), lineWidth: 1))
                    }
                }

                // ── INFO ─────────────────────────────────────────────────
                if !publishMsg.isEmpty {
                    Text(publishMsg)
                        .font(.system(size: 11, weight: .bold, design: .monospaced))
                        .foregroundColor(.green)
                        .padding(8)
                        .background(Color.green.opacity(0.08))
                        .cornerRadius(6)
                }
            }
            .padding()
        }
        .onAppear { loadDrafts() }
        .sheet(isPresented: $showingDraft) {
            if let draft = selectedDraft {
                DraftReviewSheet(draft: draft, html: draftHTML, onPublish: { publishDraft(draft) }, onClose: { showingDraft = false })
            }
        }
    }

    private func deleteDraft(_ item: DraftItem) {
        try? FileManager.default.removeItem(at: item.url)
        loadDrafts()
    }

    private func publishDraft(_ item: DraftItem) {
        // Write to agent/publish_queue.json for Laravel to pick up
        let queueFile = agentDir.appendingPathComponent("publish_queue.json")
        var queue: [[String: Any]] = []
        if let data = try? Data(contentsOf: queueFile),
           let arr = try? JSONSerialization.jsonObject(with: data) as? [[String: Any]] {
            queue = arr
        }
        if let data = try? Data(contentsOf: item.url),
           let json = try? JSONSerialization.jsonObject(with: data) as? [String: Any] {
            var entry = json
            entry["approved_at"] = ISO8601DateFormatter().string(from: Date())
            entry["status"] = "approved"
            queue.append(entry)
            if let out = try? JSONSerialization.data(withJSONObject: queue, options: .prettyPrinted) {
                try? out.write(to: queueFile)
            }
            publishMsg = "✅ \"\(item.title)\" aprobado → publish_queue.json"
            DispatchQueue.main.asyncAfter(deadline: .now() + 5) { publishMsg = "" }
        }
        showingDraft = false
    }
}

struct DraftReviewSheet: View {
    let draft: DraftItem
    let html: String
    let onPublish: () -> Void
    let onClose: () -> Void

    var body: some View {
        VStack(spacing: 0) {
            // Header
            HStack {
                VStack(alignment: .leading, spacing: 3) {
                    Text(draft.title).font(.system(size: 18, weight: .bold))
                    HStack(spacing: 8) {
                        Text(draft.category)
                            .font(.system(size: 10, weight: .bold))
                            .padding(.horizontal, 6).padding(.vertical, 2)
                            .background(Color.secondary.opacity(0.1)).cornerRadius(4)
                        Text("\(draft.wordCount) palabras")
                            .font(.system(size: 10, design: .monospaced)).foregroundColor(.secondary)
                        Text("Editor: \(draft.editor)")
                            .font(.system(size: 10, design: .monospaced)).foregroundColor(.secondary)
                    }
                }
                Spacer()
                Button("✕") { onClose() }
                    .font(.system(size: 14, weight: .bold))
                    .buttonStyle(PlainButtonStyle())
            }
            .padding()
            .background(Color(NSColor.windowBackgroundColor))

            Divider()

            // Excerpt
            if !draft.excerpt.isEmpty {
                Text(draft.excerpt)
                    .font(.system(size: 13))
                    .foregroundColor(.secondary)
                    .padding(.horizontal).padding(.vertical, 8)
                    .frame(maxWidth: .infinity, alignment: .leading)
                    .background(Color.secondary.opacity(0.05))
            }

            // Content
            ScrollView {
                // Render as attributed string from HTML
                if let attrStr = try? NSAttributedString(
                    data: Data(html.utf8),
                    options: [.documentType: NSAttributedString.DocumentType.html,
                              .characterEncoding: String.Encoding.utf8.rawValue],
                    documentAttributes: nil
                ) {
                    Text(AttributedString(attrStr))
                        .padding()
                        .frame(maxWidth: .infinity, alignment: .leading)
                } else {
                    Text(html)
                        .font(.system(.body, design: .monospaced))
                        .padding()
                }
            }

            Divider()

            // Actions
            HStack {
                Button("Rechazar") { onClose() }
                    .buttonStyle(BorderedButtonStyle())
                Spacer()
                Button(action: { onPublish() }) {
                    HStack(spacing: 6) {
                        Image(systemName: "checkmark.circle.fill")
                        Text("Aprobar y Publicar")
                    }
                }
                .buttonStyle(BorderedProminentButtonStyle())
                .tint(.green)
            }
            .padding()
        }
        .frame(minWidth: 700, minHeight: 500)
    }
}

// MARK: - Perry View

struct PerryFileInfo: Identifiable {
    let id = UUID()
    let name: String
    let sizeMB: Double
    let modified: String
    let isDir: Bool
    let fileCount: Int
}

class PerryMonitor: ObservableObject {
    // Kanban bridge — set by ContentView after both objects are created
    weak var opsMonitor: OpsMonitor?
    private var currentTaskId: String?
    private var runningTask: Process?

    // Config
    @Published var backend: String = "auto"        // auto|perry|claude|gemini|all
    @Published var analyzeMode: String  = "auto"   // auto|max|eco|claude|gemini|perry
    @Published var discoverMode: String = "auto"   // auto|max|claude|gemini
    @Published var storagePath: String = ""
    @Published var monitorIntervalMin: Int = 30
    @Published var discoveryIntervalDays: Int = 7
    // Conditions
    @Published var checkRam: Bool  = true
    @Published var checkCpu: Bool  = true
    @Published var checkIdle: Bool = true
    @Published var minRamGB: Double   = 4.0
    @Published var maxCpuPct: Double  = 30.0
    @Published var minIdleMin: Double = 10.0
    // Runtime
    @Published var currentMode: String   = "—"
    @Published var isRunning: Bool       = false
    @Published var lastRunAt: String     = "Nunca"
    @Published var statusLog: [String]   = []
    @Published var pendingSources: [[String: Any]] = []
    @Published var approvedSources: [[String: Any]] = []
    @Published var latestResults: [[String: Any]]   = []
    @Published var chatHistory: [[String: Any]]     = []
    @Published var storageFiles: [PerryFileInfo]    = []

    private let configFile = agentDir.appendingPathComponent("perry_config.json")
    private var timer: Timer?

    init() {
        loadConfig()
        refreshData()
        timer = Timer.scheduledTimer(withTimeInterval: 15, repeats: true) { _ in self.refreshData() }
    }

    // MARK: Config persistence
    func loadConfig() {
        guard let data = try? Data(contentsOf: configFile),
              let cfg  = try? JSONSerialization.jsonObject(with: data) as? [String: Any] else {
            storagePath = defaultStoragePath()
            return
        }
        backend              = cfg["backend"] as? String ?? "auto"
        analyzeMode          = cfg["analyze_mode"]  as? String ?? "auto"
        discoverMode         = cfg["discover_mode"] as? String ?? "auto"
        storagePath          = cfg["storage_path"] as? String ?? defaultStoragePath()
        monitorIntervalMin   = cfg["monitor_interval_min"] as? Int ?? 30
        discoveryIntervalDays = cfg["discovery_interval_days"] as? Int ?? 7
        if let cond = cfg["conditions"] as? [String: Any] {
            checkRam  = cond["check_ram"]  as? Bool   ?? true
            checkCpu  = cond["check_cpu"]  as? Bool   ?? true
            checkIdle = cond["check_idle"] as? Bool   ?? true
            minRamGB  = cond["min_ram_gb"] as? Double ?? 4.0
            maxCpuPct = cond["max_cpu_pct"] as? Double ?? 30.0
            minIdleMin = cond["min_idle_min"] as? Double ?? 10.0
        }
    }

    func saveConfig() {
        let cfg: [String: Any] = [
            "backend": backend,
            "analyze_mode": analyzeMode,
            "discover_mode": discoverMode,
            "storage_path": storagePath,
            "monitor_interval_min": monitorIntervalMin,
            "discovery_interval_days": discoveryIntervalDays,
            "conditions": [
                "check_ram": checkRam, "check_cpu": checkCpu, "check_idle": checkIdle,
                "min_ram_gb": minRamGB, "max_cpu_pct": maxCpuPct, "min_idle_min": minIdleMin
            ]
        ]
        if let data = try? JSONSerialization.data(withJSONObject: cfg, options: .prettyPrinted) {
            try? data.write(to: configFile)
        }
    }

    private func defaultStoragePath() -> String {
        return NSHomeDirectory() + "/Library/CloudStorage/GoogleDrive-anuarlezama@gmail.com/My Drive/gemini/keiyi_scout_intelligence"
    }

    // MARK: Data refresh
    func refreshData() {
        DispatchQueue.global(qos: .background).async {
            let base = URL(fileURLWithPath: self.storagePath)
            // Sources
            var pending: [[String: Any]] = []
            var approved: [[String: Any]] = []
            if let data = try? Data(contentsOf: base.appendingPathComponent("sources_radar.json")),
               let arr = try? JSONSerialization.jsonObject(with: data) as? [[String: Any]] {
                pending  = arr.filter { $0["status"] as? String == "pending" }
                approved = arr.filter { $0["status"] as? String == "approved" }
            }
            // Consensus results
            var results: [[String: Any]] = []
            if let data = try? Data(contentsOf: base.appendingPathComponent("perry_consensus.json")),
               let arr = try? JSONSerialization.jsonObject(with: data) as? [[String: Any]] {
                results = Array(arr.prefix(5))
            }
            // Chat history
            var chat: [[String: Any]] = []
            if let data = try? Data(contentsOf: base.appendingPathComponent("perry_chat.json")),
               let arr = try? JSONSerialization.jsonObject(with: data) as? [[String: Any]] {
                chat = Array(arr.suffix(20))
            }
            // Storage info
            var files: [PerryFileInfo] = []
            let fm = FileManager.default
            let targets = ["sources_radar.json", "perry_consensus.json", "perry_chat.json",
                           "seen_comments.json", "raw_cache"]
            for name in targets {
                let url = base.appendingPathComponent(name)
                var isDir: ObjCBool = false
                guard fm.fileExists(atPath: url.path, isDirectory: &isDir) else {
                    files.append(PerryFileInfo(name: name, sizeMB: 0, modified: "—", isDir: false, fileCount: 0))
                    continue
                }
                if isDir.boolValue {
                    let contents = (try? fm.contentsOfDirectory(atPath: url.path)) ?? []
                    let total = contents.compactMap { try? fm.attributesOfItem(atPath: url.appendingPathComponent($0).path)[.size] as? Int64 }.reduce(0, +)
                    files.append(PerryFileInfo(name: name, sizeMB: Double(total)/1024/1024, modified: "—", isDir: true, fileCount: contents.count))
                } else {
                    let attrs  = try? fm.attributesOfItem(atPath: url.path)
                    let size   = (attrs?[.size] as? Int64 ?? 0)
                    let mtime  = (attrs?[.modificationDate] as? Date).map {
                        DateFormatter.localizedString(from: $0, dateStyle: .none, timeStyle: .short)
                    } ?? "—"
                    files.append(PerryFileInfo(name: name, sizeMB: Double(size)/1024/1024, modified: mtime, isDir: false, fileCount: 0))
                }
            }
            DispatchQueue.main.async {
                self.pendingSources  = pending
                self.approvedSources = approved
                self.latestResults   = results
                self.chatHistory     = chat
                self.storageFiles    = files
            }
        }
    }

    // MARK: Run commands
    @Published var currentStep: String = ""
    @Published var runStartTime: Date? = nil

    func runPerry(cmd: String, extra: String = "") {
        guard !isRunning else { return }
        isRunning = true
        statusLog.removeAll()
        currentStep = cmd
        runStartTime = Date()
        // Create Kanban task + notify
        let labels = ["scrape":"🔎 SCRAPE · Descarga fuentes","analyze":"🧠 ANALIZAR · Debate de IAs","discover":"🌐 DESCUBRIR · Nuevas fuentes"]
        let taskTitle = labels[cmd] ?? "🦆 Perry · \(cmd.uppercased())"
        currentTaskId = opsMonitor?.addTask(title: taskTitle, agent: "Perry", status: "in_progress", notes: "Iniciado automáticamente")
        notify(title: "🦆 Perry arrancó", body: taskTitle, subtitle: "Keiyi Command Center")
        let script = agentDir.appendingPathComponent("perry.py").path
        var args = [script, cmd]
        if !extra.isEmpty { args.append(extra) }
        var env = Foundation.ProcessInfo.processInfo.environment
        env["PYTHONUNBUFFERED"] = "1"
        env["CLAUDECODE"] = ""
        // Asegurar que ~/.local/bin (claude) y homebrew (gemini) estén en PATH
        let extraPaths = "/Users/anuarlv/.local/bin:/opt/homebrew/bin"
        env["PATH"] = extraPaths + ":" + (env["PATH"] ?? "/usr/bin:/bin")
        DispatchQueue.global().async {
            let task = Process()
            task.executableURL = URL(fileURLWithPath: "/Library/Frameworks/Python.framework/Versions/3.11/bin/python3")
            task.arguments = args
            task.environment = env
            DispatchQueue.main.async { self.runningTask = task }
            let pipe = Pipe()
            task.standardOutput = pipe
            task.standardError  = pipe
            // Output en tiempo real — cada línea aparece inmediatamente
            pipe.fileHandleForReading.readabilityHandler = { handle in
                let data = handle.availableData
                guard !data.isEmpty,
                      let chunk = String(data: data, encoding: .utf8) else { return }
                let lines = chunk.components(separatedBy: "\n").filter { !$0.isEmpty }
                DispatchQueue.main.async {
                    lines.forEach { self.appendLog($0) }
                }
            }
            try? task.run()
            task.waitUntilExit()
            pipe.fileHandleForReading.readabilityHandler = nil
            DispatchQueue.main.async {
                self.isRunning    = false
                self.currentStep  = ""
                self.runStartTime = nil
                self.lastRunAt    = DateFormatter.localizedString(from: Date(), dateStyle: .none, timeStyle: .short)
                let success = task.terminationStatus == 0
                self.appendLog(success ? "✅ Completado" : "❌ Error (código \(task.terminationStatus))")
                notify(title: success ? "✅ Perry completó" : "❌ Perry con error",
                       body: self.currentStep.isEmpty ? "Tarea finalizada" : labels[self.currentStep] ?? self.currentStep,
                       subtitle: "Keiyi Command Center")
                // Update Kanban task
                if let tid = self.currentTaskId {
                    self.opsMonitor?.completeTask(id: tid, success: success)
                    self.currentTaskId = nil
                }
                self.refreshData()
            }
        }
    }

    func cancelRun() {
        runningTask?.terminate()
        runningTask = nil
        isRunning = false
        currentStep = ""
        runStartTime = nil
        appendLog("⛔ Cancelado por el usuario")
        if let tid = currentTaskId {
            opsMonitor?.completeTask(id: tid, success: false)
            currentTaskId = nil
        }
    }

    func sendChat(_ question: String) {
        runPerry(cmd: "chat", extra: question)
    }

    func approveSource(_ source: [String: Any]) {
        updateSourceStatus(source, status: "approved")
    }

    func rejectSource(_ source: [String: Any]) {
        updateSourceStatus(source, status: "rejected")
    }

    func banSource(_ source: [String: Any]) {
        updateSourceStatus(source, status: "banned")
    }

    func removeSource(_ source: [String: Any]) {
        let url = source["url"] as? String ?? ""
        let base = URL(fileURLWithPath: storagePath)
        let file = base.appendingPathComponent("sources_radar.json")
        guard var arr = (try? Data(contentsOf: file)).flatMap({ try? JSONSerialization.jsonObject(with: $0) as? [[String: Any]] }) else { return }
        arr.removeAll { $0["url"] as? String == url }
        if let data = try? JSONSerialization.data(withJSONObject: arr, options: .prettyPrinted) {
            try? data.write(to: file)
        }
        refreshData()
    }

    func addSource(_ urlString: String) {
        let cleaned = urlString.trimmingCharacters(in: .whitespacesAndNewlines)
        guard !cleaned.isEmpty else { return }
        let base = URL(fileURLWithPath: storagePath)
        let file = base.appendingPathComponent("sources_radar.json")
        var arr = (try? Data(contentsOf: file)).flatMap({ try? JSONSerialization.jsonObject(with: $0) as? [[String: Any]] }) ?? []
        // Skip if already exists
        guard !arr.contains(where: { $0["url"] as? String == cleaned }) else { return }
        arr.append(["url": cleaned, "status": "approved", "added_by": "CEO", "added_at": ISO8601DateFormatter().string(from: Date())])
        if let data = try? JSONSerialization.data(withJSONObject: arr, options: .prettyPrinted) {
            try? data.write(to: file)
        }
        refreshData()
    }

    private func updateSourceStatus(_ source: [String: Any], status: String) {
        let url = source["url"] as? String ?? ""
        let base = URL(fileURLWithPath: storagePath)
        let file = base.appendingPathComponent("sources_radar.json")
        guard var arr = (try? Data(contentsOf: file)).flatMap({ try? JSONSerialization.jsonObject(with: $0) as? [[String: Any]] }) else { return }
        for i in arr.indices { if arr[i]["url"] as? String == url { arr[i]["status"] = status } }
        if let data = try? JSONSerialization.data(withJSONObject: arr, options: .prettyPrinted) {
            try? data.write(to: file)
        }
        refreshData()
    }

    private func appendLog(_ line: String) {
        statusLog.append("[\(DateFormatter.localizedString(from: Date(), dateStyle: .none, timeStyle: .short))] \(line)")
        if statusLog.count > 80 { statusLog.removeFirst() }
    }
}

// MARK: - Perry Action Button Component
// MARK: - TrainSelector
struct TrainSelector: View {
    @Binding var selected: String
    var onChange: () -> Void = {}
    var stops: [(tag: String, emoji: String, label: String, sublabel: String, color: Color)] = [
        ("auto",   "⚡", "AUTO",   "Adaptativo", .gray),
        ("perry",  "🌱", "ECO",    "Ollama",      .green),
        ("claude", "🔵", "CLAUDE", "CLI",         .blue),
        ("gemini", "🟣", "GEMINI", "CLI",         .purple),
        ("all",    "🔥", "MAX",    "3 + debate",  .orange),
    ]

    private var selectedIndex: Int {
        stops.firstIndex(where: { $0.tag == selected }) ?? 0
    }

    private let circleSize: CGFloat = 26

    var body: some View {
        ZStack(alignment: .top) {
            // Track lines — debajo de los círculos
            GeometryReader { geo in
                let count  = CGFloat(stops.count)
                let slotW  = geo.size.width / count
                let lineY  = circleSize / 2 - 1

                // Línea gris de fondo (de primer a último stop)
                Rectangle()
                    .fill(Color.gray.opacity(0.22))
                    .frame(width: geo.size.width - slotW, height: 2)
                    .offset(x: slotW / 2, y: lineY)

                // Línea coloreada hasta la parada seleccionada
                if selectedIndex > 0 {
                    Rectangle()
                        .fill(stops[selectedIndex].color.opacity(0.65))
                        .frame(width: slotW * CGFloat(selectedIndex), height: 2)
                        .offset(x: slotW / 2, y: lineY)
                }
            }
            .frame(height: circleSize)

            // Paradas
            HStack(alignment: .top, spacing: 0) {
                ForEach(Array(stops.enumerated()), id: \.offset) { i, stop in
                    Button {
                        selected = stop.tag
                        onChange()
                    } label: {
                        VStack(spacing: 4) {
                            ZStack {
                                // Anillo exterior (pulso) solo en seleccionado
                                if i == selectedIndex {
                                    Circle()
                                        .stroke(stop.color.opacity(0.25), lineWidth: 3)
                                        .frame(width: circleSize + 7, height: circleSize + 7)
                                }
                                Circle()
                                    .fill(i <= selectedIndex
                                          ? stop.color
                                          : Color(NSColor.windowBackgroundColor))
                                    .frame(width: circleSize, height: circleSize)
                                Circle()
                                    .stroke(i <= selectedIndex
                                            ? stop.color
                                            : Color.gray.opacity(0.3), lineWidth: 1.5)
                                    .frame(width: circleSize, height: circleSize)
                                Text(stop.emoji)
                                    .font(.system(size: 11))
                            }
                            Text(stop.label)
                                .font(.system(size: 7, weight: i == selectedIndex ? .black : .regular, design: .monospaced))
                                .foregroundColor(i == selectedIndex ? stop.color : .secondary)
                                .multilineTextAlignment(.center)
                            Text(stop.sublabel)
                                .font(.system(size: 6, design: .monospaced))
                                .foregroundColor(.secondary.opacity(0.6))
                                .multilineTextAlignment(.center)
                        }
                        .frame(maxWidth: .infinity)
                    }
                    .buttonStyle(PlainButtonStyle())
                }
            }
        }
        .padding(.horizontal, 4)
        .frame(height: 62)
    }
}

struct PerryActionButton: View {
    let icon: String
    let title: String
    let description: String
    let accentColor: Color
    let isRunning: Bool
    let isActive: Bool   // este botón es el que está corriendo ahora
    let action: () -> Void

    var body: some View {
        Button(action: action) {
            HStack(spacing: 0) {
                // Barra de acento izquierda — más gruesa cuando está activo
                Rectangle()
                    .fill(isActive ? accentColor : accentColor.opacity(0.25))
                    .frame(width: isActive ? 5 : 3)
                VStack(alignment: .leading, spacing: 5) {
                    HStack(spacing: 8) {
                        Text(icon).font(.title2)
                            .opacity(isRunning && !isActive ? 0.4 : 1.0)
                        Text(title)
                            .font(.system(size: 12, weight: .black, design: .monospaced))
                            .foregroundColor(isActive ? accentColor : accentColor.opacity(isRunning ? 0.3 : 0.7))
                        Spacer()
                        if isActive {
                            HStack(spacing: 5) {
                                ProgressView()
                                    .scaleEffect(0.6)
                                    .progressViewStyle(CircularProgressViewStyle(tint: accentColor))
                                Text("EN CURSO")
                                    .font(.system(size: 8, weight: .bold, design: .monospaced))
                                    .foregroundColor(accentColor)
                                    .padding(.horizontal, 5).padding(.vertical, 2)
                                    .background(accentColor.opacity(0.15))
                                    .clipShape(Capsule())
                            }
                        }
                    }
                    Text(description)
                        .font(.system(size: 9, weight: .regular, design: .monospaced))
                        .foregroundColor(isRunning && !isActive ? .secondary.opacity(0.4) : .secondary)
                        .multilineTextAlignment(.leading)
                        .fixedSize(horizontal: false, vertical: true)
                }
                .padding(.horizontal, 12)
                .padding(.vertical, 14)
            }
            .background(
                isActive
                    ? accentColor.opacity(0.13)
                    : (isRunning
                        ? Color(NSColor.windowBackgroundColor).opacity(0.2)
                        : Color(NSColor.windowBackgroundColor).opacity(0.4))
            )
            .overlay(
                isActive
                    ? RoundedRectangle(cornerRadius: 0).stroke(accentColor.opacity(0.3), lineWidth: 1)
                    : nil
            )
            .contentShape(Rectangle())
        }
        .buttonStyle(PlainButtonStyle())
        .disabled(isRunning)
        .opacity(isRunning && !isActive ? 0.45 : 1.0)
    }
}

// MARK: - PasteableTextField (NSTextField wrapper — paste works inside ScrollView)
struct PasteableTextField: NSViewRepresentable {
    @Binding var text: String
    var placeholder: String = ""
    var onCommit: (() -> Void)? = nil

    func makeCoordinator() -> Coordinator { Coordinator(self) }

    func makeNSView(context: Context) -> NSTextField {
        let tf = NSTextField()
        tf.placeholderString = placeholder
        tf.delegate = context.coordinator
        tf.bezelStyle = .roundedBezel
        tf.font = NSFont.monospacedSystemFont(ofSize: 11, weight: .regular)
        return tf
    }

    func updateNSView(_ tf: NSTextField, context: Context) {
        if tf.stringValue != text { tf.stringValue = text }
    }

    class Coordinator: NSObject, NSTextFieldDelegate {
        var parent: PasteableTextField
        init(_ parent: PasteableTextField) { self.parent = parent }

        func controlTextDidChange(_ obj: Notification) {
            if let tf = obj.object as? NSTextField {
                parent.text = tf.stringValue
            }
        }

        func control(_ control: NSControl, textView: NSTextView, doCommandBy selector: Selector) -> Bool {
            if selector == #selector(NSResponder.insertNewline(_:)) {
                parent.onCommit?()
                return true
            }
            return false
        }
    }
}

// MARK: - PerryView
struct PerryView: View {
    @ObservedObject var monitor: ResourceMonitor
    @ObservedObject var perry: PerryMonitor
    @State private var newStoragePath: String = ""
    @State private var editingPath: Bool = false
    @State private var tick: Int = 0   // se incrementa cada segundo para refrescar el timer
    @State private var newSourceURL: String = ""
    @State private var addSourceMsg: String = ""
    @State private var sourcesTab: String = "pending"
    @State private var pendingCmd: String? = nil   // confirmación antes de ejecutar
    let clockTimer = Timer.publish(every: 1, on: .main, in: .common).autoconnect()

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 16) {

                // ── HEADER ──────────────────────────────────────────────────
                HStack(alignment: .center, spacing: 16) {
                    VStack(alignment: .leading, spacing: 3) {
                        Text("🦆 Perry · Agente de Reconocimiento")
                            .font(.title2).bold()
                        Text("Descubre, monitorea y sintetiza inteligencia global")
                            .font(.caption).foregroundColor(.secondary)
                    }
                    Spacer()
                    // Status chips
                    HStack(spacing: 10) {
                        // RAM
                        Label(String(format: "%.1f GB libre", monitor.ramFreeGB), systemImage: "memorychip")
                            .font(.caption2)
                            .padding(.horizontal, 8).padding(.vertical, 4)
                            .background(Color.blue.opacity(0.1))
                            .cornerRadius(6)
                        // Estado
                        HStack(spacing: 5) {
                            Circle()
                                .fill(perry.isRunning ? Color.orange : Color.green)
                                .frame(width: 7, height: 7)
                                .shadow(color: perry.isRunning ? .orange : .green, radius: 3)
                            Text(perry.isRunning
                                ? perry.currentStep.uppercased() + "..."
                                : "EN ESPERA")
                                .font(.system(size: 10, weight: .bold, design: .monospaced))
                        }
                        .padding(.horizontal, 8).padding(.vertical, 4)
                        .background(Color(NSColor.windowBackgroundColor))
                        .cornerRadius(6)
                        .overlay(RoundedRectangle(cornerRadius: 6)
                            .stroke(perry.isRunning ? Color.orange.opacity(0.5) : Color.green.opacity(0.3), lineWidth: 1))
                    }
                }

                // ── BLOQUE OPERACIONAL: Botones + Terminal ───────────────────
                // Los botones de acción están JUNTO al terminal para ver el
                // output en tiempo real al dar click.
                HStack(alignment: .top, spacing: 0) {

                    // ── COLUMNA IZQUIERDA: Acciones Perry ────────────────────
                    VStack(alignment: .leading, spacing: 0) {

                        // Info de lo que hace Perry — compacto
                        VStack(alignment: .leading, spacing: 2) {
                            Text("ACCIONES")
                                .font(.system(size: 9, weight: .bold, design: .monospaced))
                                .foregroundColor(.secondary)
                            Text("Sin IA · Perry es el sensor, Dipper es el cerebro")
                                .font(.system(size: 8, design: .monospaced))
                                .foregroundColor(.secondary.opacity(0.6))
                        }
                        .padding(.horizontal, 12).padding(.vertical, 10)

                        Divider()

                        // Botón SCRAPE
                        PerryActionButton(
                            icon: "📡", title: "SCRAPE",
                            description: "Paralelo · \(perry.approvedSources.count) fuentes · hot + top posts · actualiza scores",
                            accentColor: .green,
                            isRunning: perry.isRunning,
                            isActive: perry.isRunning && perry.currentStep == "scrape"
                        ) { pendingCmd = "scrape" }

                        Divider()

                        // Selector de backends para DESCUBRIR
                        VStack(alignment: .leading, spacing: 4) {
                            Text("BACKENDS · DESCUBRIR")
                                .font(.system(size: 9, weight: .bold, design: .monospaced))
                                .foregroundColor(.secondary)
                                .padding(.horizontal, 12).padding(.top, 10)
                            TrainSelector(
                                selected: $perry.backend,
                                onChange: { perry.saveConfig() },
                                stops: [
                                    ("gemini", "🟣", "GEMINI", "solo",   .purple),
                                    ("claude", "🔵", "CLAUDE", "solo",   .blue),
                                    ("max",    "🔥", "MAX",    "ambos",  .orange),
                                ]
                            )
                            .padding(.horizontal, 8)
                        }
                        .padding(.bottom, 6)

                        Divider()

                        // Botón DESCUBRIR
                        PerryActionButton(
                            icon: "🌐", title: "DESCUBRIR",
                            description: "Comunidades globales · todos los idiomas · CEO aprueba",
                            accentColor: .blue,
                            isRunning: perry.isRunning,
                            isActive: perry.isRunning && perry.currentStep == "discover"
                        ) { pendingCmd = "discover" }

                        Divider()

                        // Botón CANCELAR — solo visible mientras corre
                        if perry.isRunning {
                            Button(action: { perry.cancelRun() }) {
                                HStack(spacing: 6) {
                                    Image(systemName: "stop.fill")
                                    Text("CANCELAR")
                                        .font(.system(size: 11, weight: .black, design: .monospaced))
                                }
                                .foregroundColor(.red)
                                .frame(maxWidth: .infinity)
                                .padding(.vertical, 10)
                                .background(Color.red.opacity(0.08))
                                .overlay(Rectangle().stroke(Color.red.opacity(0.25), lineWidth: 1))
                            }
                            .buttonStyle(PlainButtonStyle())
                        }

                        Spacer()

                        // Último run + refresh
                        Divider()
                        HStack {
                            Text("Último run: \(perry.lastRunAt)")
                                .font(.system(size: 9, design: .monospaced))
                                .foregroundColor(.secondary)
                            Spacer()
                            Button { perry.refreshData() } label: {
                                Image(systemName: "arrow.clockwise")
                                    .font(.caption)
                            }
                            .buttonStyle(BorderedButtonStyle())
                            .help("Refrescar datos")
                        }
                        .padding(.horizontal, 12).padding(.vertical, 8)
                    }
                    .frame(width: 260)
                    .background(Color(NSColor.windowBackgroundColor).opacity(0.5))

                    Divider()

                    // ── COLUMNA DERECHA: Terminal en vivo ────────────────────
                    VStack(alignment: .leading, spacing: 0) {
                        // Barra del terminal
                        HStack(spacing: 8) {
                            Circle().fill(Color.red.opacity(0.7)).frame(width: 10, height: 10)
                            Circle().fill(Color.yellow.opacity(0.7)).frame(width: 10, height: 10)
                            Circle().fill(Color.green.opacity(0.7)).frame(width: 10, height: 10)
                            Text("perry · terminal")
                                .font(.system(size: 10, design: .monospaced))
                                .foregroundColor(Color(NSColor.secondaryLabelColor))
                            Spacer()
                            if perry.isRunning {
                                Text(timerLabel())
                                    .font(.system(size: 10, design: .monospaced))
                                    .foregroundColor(.orange)
                            }
                            Button {
                                perry.statusLog.removeAll()
                            } label: {
                                Text("CLR").font(.system(size: 9, design: .monospaced))
                            }
                            .buttonStyle(PlainButtonStyle())
                            .foregroundColor(.secondary)
                        }
                        .padding(.horizontal, 12).padding(.vertical, 6)
                        .background(Color(red: 0.12, green: 0.12, blue: 0.15))

                        // Área de output
                        ScrollViewReader { proxy in
                            ScrollView {
                                LazyVStack(alignment: .leading, spacing: 1) {
                                    if perry.statusLog.isEmpty {
                                        Text("Selecciona una acción para ver el output en tiempo real →")
                                            .font(.system(size: 11, design: .monospaced))
                                            .foregroundColor(Color(NSColor.tertiaryLabelColor))
                                            .padding(.top, 8)
                                    } else {
                                        ForEach(Array(perry.statusLog.enumerated()), id: \.offset) { idx, line in
                                            Text(line)
                                                .font(.system(size: 10, design: .monospaced))
                                                .foregroundColor(logLineColor(line))
                                                .frame(maxWidth: .infinity, alignment: .leading)
                                                .textSelection(.enabled)
                                                .id(idx)
                                        }
                                    }
                                }
                                .padding(.horizontal, 12).padding(.vertical, 8)
                            }
                            .onChange(of: perry.statusLog.count) { _ in
                                if let last = perry.statusLog.indices.last {
                                    withAnimation { proxy.scrollTo(last, anchor: .bottom) }
                                }
                            }
                        }
                        .frame(height: 380)
                        .background(Color(red: 0.05, green: 0.05, blue: 0.07))
                    }
                    .frame(maxWidth: .infinity)
                }
                .cornerRadius(10)
                .overlay(RoundedRectangle(cornerRadius: 10)
                    .stroke(Color(NSColor.separatorColor), lineWidth: 1))

                // ── FILA: Resultados + Bandeja (lado a lado) ─────────────────
                HStack(alignment: .top, spacing: 12) {

                    // Última inteligencia
                    GroupBox(label: Label("Última inteligencia", systemImage: "chart.bar.doc.horizontal").font(.subheadline)) {
                        if perry.latestResults.isEmpty {
                            Text("Sin resultados · Ejecuta Analizar")
                                .font(.caption).foregroundColor(.secondary).padding(8)
                        } else {
                            VStack(alignment: .leading, spacing: 10) {
                                ForEach(perry.latestResults.indices, id: \.self) { i in
                                    let r = perry.latestResults[i]
                                    VStack(alignment: .leading, spacing: 4) {
                                        HStack {
                                            Text(r["source"] as? String ?? "").bold().font(.subheadline)
                                                .textSelection(.enabled)
                                            Spacer()
                                            let score = r["relevance_score"] as? Int ?? 0
                                            Text("\(score)%")
                                                .font(.caption).bold()
                                                .foregroundColor(score > 70 ? .green : score > 40 ? .yellow : .red)
                                            Text(r["language"] as? String ?? "")
                                                .font(.caption2).padding(3)
                                                .background(Color.blue.opacity(0.12)).cornerRadius(4)
                                        }
                                        Text(r["signal_summary"] as? String ?? "")
                                            .font(.caption).foregroundColor(.secondary).textSelection(.enabled)
                                        if let topics = r["top_topics"] as? [String] {
                                            ScrollView(.horizontal, showsIndicators: false) {
                                                HStack(spacing: 4) {
                                                    ForEach(topics.prefix(5), id: \.self) { t in
                                                        Text(t).font(.system(size: 9))
                                                            .padding(.horizontal, 6).padding(.vertical, 2)
                                                            .background(Color.purple.opacity(0.12)).cornerRadius(8)
                                                            .textSelection(.enabled)
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if i < perry.latestResults.count - 1 { Divider() }
                                }
                            }
                        }
                    }
                    .frame(maxWidth: .infinity)

                    // Fuentes — cola + aprobadas
                    GroupBox(label: Label("Fuentes de Perry", systemImage: "tray.2.fill").font(.subheadline)) {
                        VStack(spacing: 8) {
                            // Pestañas
                            HStack(spacing: 0) {
                                Button {
                                    sourcesTab = "pending"
                                } label: {
                                    Text("Pendientes (\(perry.pendingSources.count))")
                                        .font(.system(size: 11, weight: sourcesTab == "pending" ? .bold : .regular))
                                        .padding(.horizontal, 10).padding(.vertical, 5)
                                        .background(sourcesTab == "pending" ? Color.accentColor.opacity(0.15) : Color.clear)
                                        .cornerRadius(6)
                                }
                                .buttonStyle(PlainButtonStyle())

                                Button {
                                    sourcesTab = "approved"
                                } label: {
                                    Text("Aprobadas (\(perry.approvedSources.count))")
                                        .font(.system(size: 11, weight: sourcesTab == "approved" ? .bold : .regular))
                                        .padding(.horizontal, 10).padding(.vertical, 5)
                                        .background(sourcesTab == "approved" ? Color.green.opacity(0.15) : Color.clear)
                                        .cornerRadius(6)
                                }
                                .buttonStyle(PlainButtonStyle())

                                Spacer()
                            }

                            Divider()

                            // Lista
                            if sourcesTab == "pending" {
                                if perry.pendingSources.isEmpty {
                                    Text("Sin pendientes · Ejecuta Descubrir")
                                        .font(.caption).foregroundColor(.secondary).padding(.vertical, 4)
                                } else {
                                    VStack(spacing: 6) {
                                        ForEach(perry.pendingSources.indices, id: \.self) { i in
                                            let src = perry.pendingSources[i]
                                            HStack {
                                                VStack(alignment: .leading, spacing: 2) {
                                                    Text(src["url"] as? String ?? "").font(.caption).bold().lineLimit(1).textSelection(.enabled)
                                                    Text(src["reason"] as? String ?? "").font(.caption2).foregroundColor(.secondary).lineLimit(2).textSelection(.enabled)
                                                }
                                                Spacer()
                                                Button("✓") { perry.approveSource(src) }.buttonStyle(BorderedProminentButtonStyle()).tint(.green)
                                                Button("✕") { perry.rejectSource(src) }.buttonStyle(BorderedButtonStyle()).foregroundColor(.red)
                                            }
                                            .padding(.vertical, 3)
                                            if i < perry.pendingSources.count - 1 { Divider() }
                                        }
                                    }
                                }
                            } else {
                                if perry.approvedSources.isEmpty {
                                    Text("Sin fuentes aprobadas")
                                        .font(.caption).foregroundColor(.secondary).padding(.vertical, 4)
                                } else {
                                    VStack(spacing: 4) {
                                        ForEach(perry.approvedSources.indices, id: \.self) { i in
                                            let src = perry.approvedSources[i]
                                            HStack(spacing: 6) {
                                                Text(src["url"] as? String ?? "")
                                                    .font(.system(size: 11))
                                                    .lineLimit(1)
                                                    .truncationMode(.middle)
                                                    .textSelection(.enabled)
                                                Spacer()
                                                Button("🚫 Ban") {
                                                    perry.banSource(src)
                                                }
                                                .buttonStyle(BorderedButtonStyle())
                                                .foregroundColor(.orange)
                                                .font(.system(size: 10))
                                                Button("🗑") {
                                                    perry.removeSource(src)
                                                }
                                                .buttonStyle(BorderedButtonStyle())
                                                .foregroundColor(.red)
                                                .font(.system(size: 10))
                                            }
                                            .padding(.vertical, 2)
                                            if i < perry.approvedSources.count - 1 { Divider() }
                                        }
                                    }
                                }
                            }

                            Divider()
                            // ── Agregar fuente manual ────────────────────────
                            HStack(spacing: 6) {
                                PasteableTextField(text: $newSourceURL, placeholder: "https://...") {
                                    perry.addSource(newSourceURL)
                                    addSourceMsg = "✅ \(newSourceURL)"
                                    newSourceURL = ""
                                    sourcesTab = "approved"
                                    DispatchQueue.main.asyncAfter(deadline: .now() + 3) { addSourceMsg = "" }
                                }
                                Button("+ Agregar") {
                                    perry.addSource(newSourceURL)
                                    addSourceMsg = "✅ \(newSourceURL)"
                                    newSourceURL = ""
                                    sourcesTab = "approved"
                                    DispatchQueue.main.asyncAfter(deadline: .now() + 3) { addSourceMsg = "" }
                                }
                                .buttonStyle(BorderedProminentButtonStyle())
                                .font(.system(size: 11))
                                .disabled(newSourceURL.trimmingCharacters(in: .whitespacesAndNewlines).isEmpty)
                            }
                            if !addSourceMsg.isEmpty {
                                Text(addSourceMsg)
                                    .font(.system(size: 10, design: .monospaced))
                                    .foregroundColor(.green)
                                    .lineLimit(1).truncationMode(.middle)
                            }
                        }
                    }
                    .frame(maxWidth: .infinity)
                }

                // ── ALMACENAMIENTO ───────────────────────────────────────────
                GroupBox(label: Label("Google Drive · Almacenamiento", systemImage: "externaldrive.fill.badge.checkmark").font(.subheadline)) {
                    VStack(alignment: .leading, spacing: 6) {
                        // Path
                        if editingPath {
                            HStack {
                                TextField("Ruta", text: $newStoragePath).textFieldStyle(RoundedBorderTextFieldStyle())
                                Button("Guardar") {
                                    perry.storagePath = newStoragePath; perry.saveConfig()
                                    editingPath = false; perry.refreshData()
                                }.buttonStyle(BorderedProminentButtonStyle())
                                Button("Cancelar") { editingPath = false }.buttonStyle(BorderedButtonStyle())
                            }
                        } else {
                            HStack {
                                Text(perry.storagePath)
                                    .font(.system(size: 10, design: .monospaced)).foregroundColor(.secondary)
                                    .lineLimit(1).truncationMode(.middle)
                                Spacer()
                                Button("Editar") { newStoragePath = perry.storagePath; editingPath = true }
                                    .font(.caption).buttonStyle(BorderedButtonStyle())
                                Button("Finder") { NSWorkspace.shared.open(URL(fileURLWithPath: perry.storagePath)) }
                                    .font(.caption).buttonStyle(BorderedButtonStyle())
                            }
                        }
                        Divider()
                        // Files grid
                        LazyVGrid(columns: [GridItem(.flexible()), GridItem(.flexible()), GridItem(.flexible())], spacing: 6) {
                            ForEach(perry.storageFiles) { file in
                                HStack(spacing: 6) {
                                    Image(systemName: file.isDir ? "folder.fill" : "doc.fill")
                                        .foregroundColor(file.isDir ? .yellow : .secondary).font(.caption)
                                    VStack(alignment: .leading, spacing: 1) {
                                        Text(file.name).font(.system(size: 9, design: .monospaced)).lineLimit(1)
                                        Text(file.sizeMB < 0.01 ? "vacío"
                                             : file.isDir ? "\(file.fileCount) archivos"
                                             : String(format: "%.2f MB", file.sizeMB))
                                            .font(.system(size: 9)).foregroundColor(.secondary)
                                    }
                                    Spacer()
                                }
                                .padding(6).background(Color(NSColor.windowBackgroundColor)).cornerRadius(6)
                            }
                        }
                    }
                }

            }
            .padding()
        }
        .onAppear { perry.refreshData() }
        .onReceive(clockTimer) { _ in if perry.isRunning { tick += 1 } }
        .alert(isPresented: Binding(
            get: { pendingCmd != nil },
            set: { if !$0 { pendingCmd = nil } }
        )) {
            let cmd = pendingCmd ?? ""
            let icons  = ["scrape": "📡", "discover": "🌐"]
            let titles = ["scrape": "Ejecutar SCRAPE", "discover": "Ejecutar DESCUBRIR"]
            let descs  = [
                "scrape":   "Descarga paralela de \(perry.approvedSources.count) fuentes · hot + top posts · sin IA · actualiza scores de actividad.",
                "discover": "Claude + Gemini buscan comunidades nuevas en todos los idiomas. Las sugerencias quedan pendientes — tú apruebas cada una."
            ]
            return Alert(
                title: Text("\(icons[cmd] ?? "🦆") \(titles[cmd] ?? cmd.uppercased())"),
                message: Text(descs[cmd] ?? "¿Confirmas la ejecución?"),
                primaryButton: .default(Text("Ejecutar")) {
                    perry.runPerry(cmd: cmd)
                    pendingCmd = nil
                },
                secondaryButton: .cancel(Text("Cancelar")) { pendingCmd = nil }
            )
        }
    }

    private func logLineColor(_ line: String) -> Color {
        if line.contains("✅") { return .green }
        if line.contains("❌") || line.contains("Error") || line.contains("error") { return .red }
        if line.contains("⚠️") { return .yellow }
        if line.hasPrefix("[") { return Color(NSColor.tertiaryLabelColor) }
        return Color(NSColor.labelColor)
    }

    private func timerLabel() -> String {
        _ = tick  // fuerza re-render cada segundo vía clockTimer
        guard let start = perry.runStartTime else { return "" }
        let elapsed = Int(Date().timeIntervalSince(start))
        return String(format: "%02d:%02d", elapsed / 60, elapsed % 60)
    }
}

// MARK: - Scott View (Radar de Fuentes — En construcción)

struct ScottView: View {
    @State private var sources: [[String: Any]] = []

    private func loadSources() {
        let path = agentDir.appendingPathComponent("sources_radar.json")
        guard let data = try? Data(contentsOf: path),
              let json = try? JSONSerialization.jsonObject(with: data) as? [String: Any],
              let arr  = json["sources"] as? [[String: Any]] else { return }
        sources = arr
    }

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 20) {
                Text("Scott · Radar de Fuentes")
                    .font(.largeTitle).bold()
                Text("Monitoreo continuo — sin LLM — corre cada 30 min todo el día.")
                    .foregroundColor(.secondary)

                VStack(alignment: .leading, spacing: 8) {
                    Text("Fuentes activas").font(.headline)
                    if sources.isEmpty {
                        Text("scott.py aún no ha corrido. En construcción — próxima sesión.")
                            .font(.caption).foregroundColor(.secondary).padding()
                    } else {
                        ForEach(sources.indices, id: \.self) { i in
                            let s = sources[i]
                            HStack {
                                VStack(alignment: .leading, spacing: 2) {
                                    Text(s["nombre"] as? String ?? s["url"] as? String ?? "")
                                        .bold().font(.subheadline)
                                    Text("\(s["tipo"] as? String ?? "") · \(s["idioma"] as? String ?? "")")
                                        .font(.caption).foregroundColor(.secondary)
                                }
                                Spacer()
                                Text("Utilidad: \(s["utilidad_william"] as? Int ?? 0)%")
                                    .font(.caption2).padding(4)
                                    .background(Color.blue.opacity(0.1)).cornerRadius(4)
                            }
                            .padding(.vertical, 4)
                            Divider()
                        }
                    }
                }
                .padding()
                .background(Color(NSColor.textBackgroundColor)).cornerRadius(10)
            }
            .padding()
        }
        .onAppear { loadSources() }
    }
}

/// MARK: - Heatmap Component

struct TrendTool: Identifiable {
    let id = UUID()
    let name: String
    let count: Int
    let lastSeen: Date
    let intensity: Int   // 1-10, ya calculado con time decay

    var lastSeenLabel: String {
        let days = Int(Date().timeIntervalSince(lastSeen) / 86400)
        if days == 0 { return "hoy" }
        if days == 1 { return "ayer" }
        return "hace \(days) días"
    }
}

class TrendDataLoader: ObservableObject {
    @Published var tools: [TrendTool] = []
    private let dbURL: URL = {
        // Primary: Google Drive (datos frescos de Dipper)
        let gdrive = URL(fileURLWithPath: NSHomeDirectory())
            .appendingPathComponent("Library/CloudStorage/GoogleDrive-anuarlezama@gmail.com/My Drive/gemini/keiyi_scout_intelligence/research_db.json")
        if FileManager.default.fileExists(atPath: gdrive.path) { return gdrive }
        // Fallback: local agent dir
        return agentDir.appendingPathComponent("research_db.json")
    }()
    private var timer: Timer?

    init() {
        load()
        timer = Timer.scheduledTimer(withTimeInterval: 60, repeats: true) { _ in self.load() }
    }

    func load() {
        DispatchQueue.global(qos: .background).async {
            guard let data = try? Data(contentsOf: self.dbURL),
                  let db = try? JSONSerialization.jsonObject(with: data) as? [String: Any] else {
                DispatchQueue.main.async { self.tools = [] }
                return
            }

            // Aggregate tools across all subreddits
            // keyed by lowercase name; value: (displayName, count, lastSeen)
            var agg: [String: (display: String, count: Int, lastSeen: String)] = [:]
            for (_, subVal) in db {
                guard let subData = subVal as? [String: Any],
                      let toolsArr = subData["tools"] as? [[String: Any]] else { continue }
                for t in toolsArr {
                    guard let name = t["name"] as? String, !name.isEmpty else { continue }
                    let count = (t["count"] as? Int) ?? 1
                    let ls    = (t["last_seen"] as? String) ?? ""
                    let key   = name.lowercased()
                    if let existing = agg[key] {
                        agg[key] = (existing.display, existing.count + count,
                                    ls > existing.lastSeen ? ls : existing.lastSeen)
                    } else {
                        agg[key] = (name, count, ls)
                    }
                }
            }

            guard !agg.isEmpty else {
                DispatchQueue.main.async { self.tools = [] }
                return
            }

            // Intensity formula (Antigravity spec)
            let maxCount = agg.values.map { $0.count }.max() ?? 1
            let fmt = DateFormatter(); fmt.dateFormat = "yyyy-MM-dd"
            let today = Date()

            var result: [TrendTool] = []
            for (_, val) in agg {
                let normalized = Int(round(Double(val.count) / Double(maxCount) * 10))
                var decay = 0
                if let lastDate = fmt.date(from: val.lastSeen) {
                    let days = Int(today.timeIntervalSince(lastDate) / 86400)
                    decay = days / 7  // 1 point per week (was /2 — too aggressive)
                }
                let intensity = max(0, normalized - decay)
                if intensity < 1 { continue }
                let lastDate = fmt.date(from: val.lastSeen) ?? today
                result.append(TrendTool(name: val.display, count: val.count, lastSeen: lastDate, intensity: intensity))
            }
            let sorted = result.sorted { $0.intensity > $1.intensity }
            DispatchQueue.main.async { self.tools = sorted }
        }
    }
}

struct TrendHeatmapView: View {
    @StateObject private var loader = TrendDataLoader()

    let columns = [GridItem(.adaptive(minimum: 100))]

    func colorFor(intensity: Int) -> Color {
        let t = Double(intensity) / 10.0
        // Teal (fría/baja) → naranja → rojo (caliente/alta)
        let hue = 0.38 - t * 0.38
        return Color(hue: hue, saturation: 0.75, brightness: 0.85)
    }

    var body: some View {
        Group {
            if loader.tools.isEmpty {
                HStack {
                    Spacer()
                    VStack(spacing: 6) {
                        Text("Sin datos de inteligencia aún")
                            .foregroundColor(.secondary).font(.caption)
                        Text("Ejecuta Dipper para poblar el mapa")
                            .foregroundColor(.secondary).font(.caption)
                    }
                    Spacer()
                }
                .padding(20)
            } else {
                LazyVGrid(columns: columns, spacing: 10) {
                    ForEach(loader.tools) { tool in
                        VStack(spacing: 2) {
                            Text(tool.name)
                                .font(.caption).bold()
                                .foregroundColor(.white)
                                .lineLimit(1).minimumScaleFactor(0.7)
                            Text("⚡\(tool.intensity) · \(tool.count)x")
                                .font(.system(size: 9))
                                .foregroundColor(.white.opacity(0.8))
                        }
                        .padding(.vertical, 8).padding(.horizontal, 4)
                        .frame(maxWidth: .infinity)
                        .background(colorFor(intensity: tool.intensity))
                        .cornerRadius(6)
                    }
                }
                .padding(10)
                .background(Color(NSColor.windowBackgroundColor))
                .cornerRadius(8)
            }
        }
        .onAppear { loader.load() }
    }
}

// MARK: - OpsMonitor
class OpsMonitor: ObservableObject {
    @Published var tasks: [[String: Any]] = []
    @Published var showingAdd: Bool = false
    @Published var addTargetStatus: String = "scheduled"

    private let tasksFile = agentDir.appendingPathComponent("agent_tasks.json")
    private var refreshTimer: Timer?

    init() {
        loadTasks()
        // Auto-refresh every 10s so external agent writes show up
        refreshTimer = Timer.scheduledTimer(withTimeInterval: 10, repeats: true) { [weak self] _ in
            self?.loadTasks()
        }
    }

    func loadTasks() {
        guard let data = try? Data(contentsOf: tasksFile),
              let arr  = try? JSONSerialization.jsonObject(with: data) as? [[String: Any]] else { return }
        DispatchQueue.main.async { self.tasks = arr }
    }

    func saveTasks() {
        if let data = try? JSONSerialization.data(withJSONObject: tasks, options: .prettyPrinted) {
            try? data.write(to: tasksFile)
        }
    }

    @discardableResult
    func addTask(title: String, agent: String, status: String, notes: String, trigger: String = "manual") -> String {
        let icons = ["Perry":"🦆","Dipper":"📡","William":"✍️","Claude":"🤖","Gemini":"💫","Antigravity":"🚀"]
        let now = ISO8601DateFormatter().string(from: Date())
        let id = UUID().uuidString
        let t: [String: Any] = [
            "id": id, "title": title,
            "agent": agent, "agent_icon": icons[agent] ?? "🤖",
            "status": status, "notes": notes,
            "trigger": trigger,
            "created_at": now, "updated_at": now
        ]
        tasks.insert(t, at: 0)
        saveTasks()
        return id
    }

    func completeTask(id: String, success: Bool) {
        let now = ISO8601DateFormatter().string(from: Date())
        for i in tasks.indices where tasks[i]["id"] as? String == id {
            tasks[i]["status"]     = success ? "done" : "done"
            tasks[i]["updated_at"] = now
            tasks[i]["notes"]      = success ? "✅ Completado" : "❌ Terminó con error"
        }
        saveTasks()
    }

    func moveTask(_ task: [String: Any], to newStatus: String) {
        guard let id = task["id"] as? String else { return }
        let now = ISO8601DateFormatter().string(from: Date())
        for i in tasks.indices where tasks[i]["id"] as? String == id {
            tasks[i]["status"]     = newStatus
            tasks[i]["updated_at"] = now
        }
        saveTasks()
    }

    func deleteTask(_ task: [String: Any]) {
        guard let id = task["id"] as? String else { return }
        tasks.removeAll { $0["id"] as? String == id }
        saveTasks()
    }

    func startAdd(status: String) { addTargetStatus = status; showingAdd = true }
}

// MARK: - TaskCard
struct TaskCard: View {
    let task: [String: Any]
    @ObservedObject var ops: OpsMonitor

    private var status:  String { task["status"]  as? String ?? "scheduled" }
    private var agent:   String { task["agent"]   as? String ?? "" }
    private var icon:    String { task["agent_icon"] as? String ?? "🤖" }
    private var trigger: String { task["trigger"] as? String ?? "manual" }

    private var parsedDate: Date? {
        let ts = task["updated_at"] as? String ?? task["created_at"] as? String ?? ""
        return ISO8601DateFormatter().date(from: ts)
    }

    var body: some View {
        HStack(spacing: 8) {
            // Agent color bar
            RoundedRectangle(cornerRadius: 2)
                .fill(agentColor(agent))
                .frame(width: 3)

            VStack(alignment: .leading, spacing: 3) {
                // Row 1: title + delete
                HStack(alignment: .top, spacing: 4) {
                    Text(task["title"] as? String ?? "")
                        .font(.system(size: 11, weight: .semibold))
                        .lineLimit(2)
                    Spacer(minLength: 4)
                    Button { ops.deleteTask(task) } label: {
                        Image(systemName: "xmark").font(.system(size: 7)).foregroundColor(.secondary.opacity(0.5))
                    }
                    .buttonStyle(PlainButtonStyle())
                }

                // Row 2: agent · date · status · actions
                HStack(spacing: 6) {
                    Text("\(icon) \(agent)")
                        .font(.system(size: 9, weight: .medium))
                        .foregroundColor(agentColor(agent))

                    Text(trigger == "auto" ? "⚡ auto" : "👆 manual")
                        .font(.system(size: 8, weight: .medium, design: .monospaced))
                        .foregroundColor(trigger == "auto" ? .cyan : .secondary)
                        .padding(.horizontal, 4).padding(.vertical, 1)
                        .background(trigger == "auto" ? Color.cyan.opacity(0.1) : Color.secondary.opacity(0.08))
                        .cornerRadius(3)

                    if let date = parsedDate {
                        Text("·").foregroundColor(.secondary.opacity(0.4)).font(.system(size: 9))
                        Text(formatDate(date))
                            .font(.system(size: 9, design: .monospaced))
                            .foregroundColor(.secondary)
                    }

                    Spacer()

                    // Move buttons (only for non-done)
                    if status != "done" {
                        if status != "scheduled" {
                            Button { ops.moveTask(task, to: prevStatus(status)) } label: {
                                Image(systemName: "chevron.left")
                                    .font(.system(size: 8, weight: .bold))
                                    .foregroundColor(.secondary)
                            }
                            .buttonStyle(PlainButtonStyle())
                        }
                        Button { ops.moveTask(task, to: nextStatus(status)) } label: {
                            Text(status == "in_progress" ? "✓" : "→")
                                .font(.system(size: 9, weight: .bold))
                                .foregroundColor(status == "in_progress" ? .green : .accentColor)
                        }
                        .buttonStyle(PlainButtonStyle())
                    } else {
                        Text("✅")
                            .font(.system(size: 9))
                    }
                }
            }
        }
        .padding(.horizontal, 8).padding(.vertical, 6)
        .background(Color(NSColor.controlBackgroundColor))
        .cornerRadius(6)
        .overlay(RoundedRectangle(cornerRadius: 6).stroke(agentColor(agent).opacity(0.2), lineWidth: 0.5))
    }

    private func formatDate(_ date: Date) -> String {
        let cal = Calendar.current
        let now = Date()
        if cal.isDateInToday(date) {
            return DateFormatter.localizedString(from: date, dateStyle: .none, timeStyle: .short)
        } else if cal.isDateInYesterday(date) {
            return "Ayer " + DateFormatter.localizedString(from: date, dateStyle: .none, timeStyle: .short)
        } else {
            let fmt = DateFormatter()
            fmt.dateFormat = "d MMM HH:mm"
            fmt.locale = Locale(identifier: "es_MX")
            return fmt.string(from: date)
        }
    }

    private func agentColor(_ a: String) -> Color {
        switch a {
        case "Perry":       return .green
        case "Dipper":      return .orange
        case "William":     return .purple
        case "Claude":      return .blue
        case "Gemini":      return .cyan
        case "Antigravity": return .pink
        default:            return .gray
        }
    }
    private func nextStatus(_ s: String) -> String {
        switch s { case "scheduled": return "queue"; case "queue": return "in_progress"; default: return "done" }
    }
    private func prevStatus(_ s: String) -> String {
        switch s { case "done": return "in_progress"; case "in_progress": return "queue"; default: return "scheduled" }
    }
}

// MARK: - AddTaskView
struct AddTaskView: View {
    @ObservedObject var ops: OpsMonitor
    @State private var title:  String = ""
    @State private var agent:  String = "Perry"
    @State private var notes:  String = ""

    let agents = ["Perry","Dipper","William","Claude","Gemini","Antigravity"]
    let statusLabels = ["scheduled":"Programado","queue":"En Cola","in_progress":"En Progreso","done":"Completado"]

    var body: some View {
        VStack(alignment: .leading, spacing: 12) {
            HStack {
                Text("Nueva misión")
                    .font(.headline)
                Spacer()
                Button("✕ Cancelar") { ops.showingAdd = false }
                    .buttonStyle(BorderedButtonStyle()).font(.caption)
            }
            Divider()

            VStack(alignment: .leading, spacing: 6) {
                Text("Título").font(.caption).foregroundColor(.secondary)
                PasteableTextField(text: $title, placeholder: "ej. SCRAPE · 20 fuentes nuevas") {}
            }

            HStack(spacing: 16) {
                VStack(alignment: .leading, spacing: 6) {
                    Text("Agente").font(.caption).foregroundColor(.secondary)
                    Picker("", selection: $agent) {
                        ForEach(agents, id: \.self) { Text($0) }
                    }
                    .pickerStyle(MenuPickerStyle())
                    .labelsHidden()
                    .frame(maxWidth: 140)
                }
                VStack(alignment: .leading, spacing: 6) {
                    Text("Columna").font(.caption).foregroundColor(.secondary)
                    Picker("", selection: $ops.addTargetStatus) {
                        ForEach(["scheduled","queue","in_progress","done"], id: \.self) { s in
                            Text(statusLabels[s] ?? s).tag(s)
                        }
                    }
                    .pickerStyle(MenuPickerStyle())
                    .labelsHidden()
                    .frame(maxWidth: 140)
                }
            }

            VStack(alignment: .leading, spacing: 6) {
                Text("Notas").font(.caption).foregroundColor(.secondary)
                PasteableTextField(text: $notes, placeholder: "Contexto opcional…") {}
            }

            HStack {
                Spacer()
                Button("Crear misión") {
                    guard !title.trimmingCharacters(in: .whitespacesAndNewlines).isEmpty else { return }
                    ops.addTask(title: title, agent: agent, status: ops.addTargetStatus, notes: notes)
                    ops.showingAdd = false
                }
                .buttonStyle(BorderedProminentButtonStyle())
                .disabled(title.trimmingCharacters(in: .whitespacesAndNewlines).isEmpty)
            }
        }
        .padding(16)
        .background(Color(NSColor.windowBackgroundColor))
        .cornerRadius(10)
        .overlay(RoundedRectangle(cornerRadius: 10).stroke(Color(NSColor.separatorColor), lineWidth: 1))
        .frame(width: 420)
        .shadow(radius: 12)
    }
}

// MARK: - MissionControlView
struct MissionControlView: View {
    @ObservedObject var ops: OpsMonitor

    private let columns    = ["scheduled", "queue", "in_progress", "done"]
    private let colTitles  = ["Programado", "En Cola", "En Progreso", "Completado"]
    private let colColors: [Color] = [.secondary, .blue, .orange, .green]

    var body: some View {
        ZStack(alignment: .center) {
            ScrollView {
                VStack(alignment: .leading, spacing: 16) {

                    // Header
                    HStack(alignment: .center) {
                        VStack(alignment: .leading, spacing: 3) {
                            Text("🎯 Misiones · Flujo de Agentes")
                                .font(.title2).bold()
                            Text("Qué hacen, qué hicieron y qué harán los agentes")
                                .font(.caption).foregroundColor(.secondary)
                        }
                        Spacer()
                        Button { ops.loadTasks() } label: {
                            Image(systemName: "arrow.clockwise")
                        }
                        .buttonStyle(BorderedButtonStyle())
                        Button("+ Nueva misión") { ops.startAdd(status: "scheduled") }
                            .buttonStyle(BorderedProminentButtonStyle())
                    }

                    Divider()

                    // Agent legend
                    HStack(spacing: 10) {
                        ForEach(["Perry 🦆","Dipper 📡","William ✍️","Claude 🤖","Gemini 💫","Antigravity 🚀"], id: \.self) { label in
                            let agent = String(label.split(separator: " ").first ?? "")
                            Text(label)
                                .font(.system(size: 10))
                                .padding(.horizontal, 8).padding(.vertical, 3)
                                .background(agentColor(agent).opacity(0.12))
                                .cornerRadius(10)
                        }
                        Spacer()
                        Text("\(ops.tasks.count) misiones totales")
                            .font(.caption2).foregroundColor(.secondary)
                    }

                    // Kanban board
                    HStack(alignment: .top, spacing: 10) {
                        ForEach(0..<4, id: \.self) { ci in
                            let col   = columns[ci]
                            let items = ops.tasks.filter { $0["status"] as? String == col }
                            let isDone = col == "done"
                            // Show only the latest 5 completed tasks, all for other columns
                            let visibleItems = isDone ? Array(items.prefix(5)) : items
                            let hiddenCount  = isDone ? max(0, items.count - 5) : 0

                            VStack(alignment: .leading, spacing: 6) {
                                // Column header
                                HStack(spacing: 6) {
                                    Circle().fill(colColors[ci]).frame(width: 8, height: 8)
                                        .shadow(color: colColors[ci], radius: 2)
                                    Text(colTitles[ci])
                                        .font(.system(size: 12, weight: .semibold))
                                    Spacer()
                                    Text("\(items.count)")
                                        .font(.caption2).bold()
                                        .padding(.horizontal, 6).padding(.vertical, 2)
                                        .background(colColors[ci].opacity(0.15))
                                        .cornerRadius(8)
                                }
                                .padding(.horizontal, 10).padding(.vertical, 8)
                                .background(Color(NSColor.controlBackgroundColor))
                                .cornerRadius(8)

                                // Cards
                                ForEach(visibleItems.indices, id: \.self) { i in
                                    TaskCard(task: visibleItems[i], ops: ops)
                                }

                                // "N more" indicator for completed column
                                if hiddenCount > 0 {
                                    Text("+ \(hiddenCount) anteriores")
                                        .font(.system(size: 9))
                                        .foregroundColor(.secondary.opacity(0.6))
                                        .frame(maxWidth: .infinity)
                                        .padding(.vertical, 4)
                                }

                                // Add button per column
                                Button {
                                    ops.startAdd(status: col)
                                } label: {
                                    HStack(spacing: 4) {
                                        Image(systemName: "plus")
                                        Text("Agregar")
                                    }
                                    .font(.system(size: 10))
                                    .foregroundColor(.secondary)
                                    .frame(maxWidth: .infinity)
                                    .padding(.vertical, 6)
                                    .background(Color(NSColor.controlBackgroundColor).opacity(0.5))
                                    .cornerRadius(6)
                                    .overlay(RoundedRectangle(cornerRadius: 6)
                                        .stroke(Color(NSColor.separatorColor).opacity(0.5), style: StrokeStyle(lineWidth: 1, dash: [4])))
                                }
                                .buttonStyle(PlainButtonStyle())

                                Spacer()
                            }
                            .frame(maxWidth: .infinity)
                        }
                    }
                    .frame(minHeight: 300)
                }
                .padding(20)
            }

            // Add task overlay
            if ops.showingAdd {
                Color.black.opacity(0.3)
                    .ignoresSafeArea()
                    .onTapGesture { ops.showingAdd = false }
                AddTaskView(ops: ops)
                    .transition(.scale(scale: 0.95).combined(with: .opacity))
            }
        }
        .animation(.easeInOut(duration: 0.15), value: ops.showingAdd)
    }

    private func agentColor(_ a: String) -> Color {
        switch a {
        case "Perry":       return .cyan
        case "Dipper":      return .purple
        case "William":     return .orange
        case "Claude":      return .blue
        case "Gemini":      return .green
        case "Antigravity": return .pink
        default:            return .gray
        }
    }
}

// MARK: - Main
let app = NSApplication.shared
let delegate = AppDelegate()
app.delegate = delegate
app.run()
