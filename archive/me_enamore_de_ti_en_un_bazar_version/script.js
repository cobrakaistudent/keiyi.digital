document.addEventListener('DOMContentLoaded', () => {
    // 1. Snappy Mobile Menu
    const hamburger = document.querySelector('.hamburger');

    // 2. Simple Scroll Logic (Optional for Neo-Pop, usually it's bold and static or intensely animated)
    // We'll keep it simple: no fading, things just ARE there. Neo-brutalism is honest.

    // Add click event for "Push Buttons" to animate them slightly via JS if needed,
    // though CSS :active handles most.

    const buttons = document.querySelectorAll('.push-button');
    buttons.forEach(btn => {
        btn.addEventListener('mousedown', () => {
            btn.style.transform = 'translate(6px, 6px)';
            btn.style.boxShadow = 'none';
        });
        btn.addEventListener('mouseup', () => {
            btn.style.transform = '';
            btn.style.boxShadow = '';
        });
    });
});
