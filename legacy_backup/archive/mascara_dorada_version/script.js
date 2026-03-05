document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Menu (Basic toggle)
    const hamburger = document.querySelector('.hamburger');

    // 2. Intersection Observer for Rigid Reveal
    const observerOptions = {
        threshold: 0.15,
        rootMargin: "0px"
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active-reveal');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    const revealElements = document.querySelectorAll('.reveal-text, .reveal-up');

    revealElements.forEach(el => {
        observer.observe(el);
    });
});
