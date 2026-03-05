// KEIYI NEXTGEN LAB - DEFINITIVE JS MASTER 2026

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. GSAP Setup & Plugins
    gsap.registerPlugin(ScrollTrigger);

    // 2. HERO ANIMATIONS (Immediate Impact)
    const heroTimeline = gsap.timeline();
    heroTimeline
        .from('.hero-lab h1', { y: 50, opacity: 0, duration: 1.2, ease: "power4.out" })
        .from('.reveal-sub', { y: 30, opacity: 0, duration: 1, ease: "power3.out" }, "-=0.8");

    // 3. REVEAL SYSTEM (Robust & Safe)
    // Usamos gsap.from para que el estado final sea SIEMPRE visible (opacidad 1)
    const revealElements = document.querySelectorAll('.reveal-item');
    
    revealElements.forEach((el) => {
        gsap.from(el, {
            scrollTrigger: {
                trigger: el,
                start: "top 90%", // Empieza cuando el elemento entra un 10% en pantalla
                toggleActions: "play none none none"
            },
            y: 40,
            opacity: 0,
            duration: 0.8,
            ease: "power2.out"
        });
    });

    // 4. PHASE 2: INTERACTIVE DOODLES (Floating & Magnetism)
    const doodles = document.querySelectorAll('.floating-doodle');
    
    doodles.forEach((doodle, index) => {
        gsap.to(doodle, {
            y: "+=40",
            rotation: "+=20",
            duration: 3 + index,
            repeat: -1,
            yoyo: true,
            ease: "sine.inOut"
        });
    });

    document.addEventListener('mousemove', (e) => {
        const { clientX, clientY } = e;
        const centerX = window.innerWidth / 2;
        const centerY = window.innerHeight / 2;
        
        doodles.forEach((doodle, index) => {
            const factor = (index + 1) * 25;
            const moveX = (clientX - centerX) / window.innerWidth * factor;
            const moveY = (clientY - centerY) / window.innerHeight * factor;
            
            gsap.to(doodle, { x: moveX, y: moveY, duration: 1.2, ease: "power2.out" });
        });
    });

    // 5. PHASE 3: IA MAGIC (Creative Engine)
    const aiBtn = document.getElementById('ai-btn');
    const aiInput = document.getElementById('ai-input');
    const aiResult = document.getElementById('ai-result');
    const aiBox = document.querySelector('.ai-box');

    const creativeIdeas = [
        "Tu marca no necesita un logo, necesita un movimiento.",
        "El algoritmo odia lo perfecto. Sé real.",
        "Vende la cura, no la medicina.",
        "Si tu web no carga en 2 segundos, no existe.",
        "El 3D es el nuevo lenguaje de la confianza.",
        "Tu competencia es aburrida. Tú no.",
        "Menos 'Followers', más creyentes.",
        "El marketing de antes murió. Sé el funeral."
    ];

    if (aiBtn) {
        aiBtn.addEventListener('click', () => {
            const val = aiInput.value.trim();
            if (val === "") {
                gsap.to(aiBox, { x: 10, repeat: 5, yoyo: true, duration: 0.05 });
                aiResult.innerText = "¡Dime tu marca!";
                return;
            }

            aiBox.classList.add('thinking');
            aiResult.innerText = "Consultando al cerebro Keiyi...";

            setTimeout(() => {
                aiBox.classList.remove('thinking');
                const randomIdea = creativeIdeas[Math.floor(Math.random() * creativeIdeas.length)];
                gsap.to(aiResult, { opacity: 0, duration: 0.3, onComplete: () => {
                    aiResult.innerText = randomIdea;
                    gsap.to(aiResult, { opacity: 1, duration: 0.5 });
                }});
                gsap.to(aiBox, { scale: 1.05, duration: 0.2, yoyo: true, repeat: 1 });
            }, 1200);
        });
    }

    // 6. PHASE 4: 3D WORLD (Three.js)
    const threeContainer = document.getElementById('three-canvas-container');
    if (threeContainer) {
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, 1, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        
        const size = 180;
        renderer.setSize(size, size);
        threeContainer.appendChild(renderer.domElement);

        const geometry = new THREE.IcosahedronGeometry(1, 1);
        const material = new THREE.MeshBasicMaterial({ color: 0x0f172a, wireframe: true });
        const sphere = new THREE.Mesh(geometry, material);
        scene.add(sphere);

        camera.position.z = 2.5;

        function animate3D() {
            requestAnimationFrame(animate3D);
            sphere.rotation.y += 0.01;
            sphere.rotation.x += 0.005;
            renderer.render(scene, camera);
        }
        animate3D();
    }

    // 7. PHASE 7.1: REAL TRENDS RADAR
    const radarTicker = document.getElementById('radar-ticker');
    async function fetchRealTrends() {
        try {
            const response = await fetch('https://api.rss2json.com/v1/api.json?rss_url=https://www.socialmediatoday.com/feeds/news/');
            const data = await response.json();
            if (data.items) {
                radarTicker.innerHTML = '';
                data.items.slice(0, 10).forEach(item => {
                    const radarItem = document.createElement('div');
                    radarItem.className = 'radar-item';
                    radarItem.innerHTML = `<span class="platform">INSIGHT</span> <span class="trend-name">${item.title}</span> <span class="trend-stat">NUEVO ⚡</span>`;
                    radarTicker.appendChild(radarItem);
                });
                const tickerClone = radarTicker.innerHTML;
                radarTicker.innerHTML = tickerClone + tickerClone;
                gsap.to(radarTicker, { x: "-50%", duration: 45, repeat: -1, ease: "none" });
            }
        } catch (e) {
            radarTicker.innerHTML = '<div class="radar-item">Keiyi Intelligence: Datos actualizados cada 24 horas.</div>';
        }
    }
    fetchRealTrends();

    // Recalcular ScrollTrigger al terminar de cargar todo
    window.addEventListener('load', () => {
        ScrollTrigger.refresh();
    });

    console.log("🚀 KEIYI LAB ENGINE: Stabilized & Ready.");
});