document.addEventListener('DOMContentLoaded', () => {
    // 1. Funky Hand Wiggle on Logo
    const logo = document.querySelector('.logo');
    logo.addEventListener('mouseover', () => {
        logo.style.transition = 'transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        logo.style.transform = 'rotate(5deg) scale(1.1)';
    });
    logo.addEventListener('mouseout', () => {
        logo.style.transform = 'rotate(0) scale(1)';
    });

    // 2. Scroll Appear with Elasticity
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = 1;
                // Elastic bounce up
                entry.target.style.animation = 'bounceUp 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.funky-card, .price-box, .big-text').forEach(el => {
        el.style.opacity = 0;
        observer.observe(el);
    });

    // Add the keyframe dynamically if not in CSS
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes bounceUp {
            0% { transform: translateY(50px); opacity: 0; }
            60% { transform: translateY(-10px); opacity: 1; }
            100% { transform: translateY(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
});
