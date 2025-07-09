// public/js/welcome.js

// Efecto parallax suave
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const background = document.body;
    background.style.transform = `translateY(${scrolled * 0.5}px)`;
});

// Efecto hover en las cards
document.querySelectorAll('.feature-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-10px) scale(1.02)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
    });
});

// Efecto de escritura para el título
const title = document.querySelector('.hero h1');
const titleText = title.textContent;
title.textContent = '';

let i = 0;
const typeWriter = () => {
    if (i < titleText.length) {
        title.textContent += titleText.charAt(i);
        i++;
        setTimeout(typeWriter, 100);
    }
};

setTimeout(typeWriter, 500);