/**
 * JavaScript Melhorado para o Site Plano A
 * Adiciona interatividade moderna mantendo as cores originais
 */

// Aguarda o carregamento completo do DOM
document.addEventListener('DOMContentLoaded', function() {

    // ===== ANIMAÇÕES DE SCROLL =====
    const elements = document.querySelectorAll('.animate-on-scroll');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    });

    elements.forEach(el => observer.observe(el));

    // ===== MENU MOBILE =====
    const menuToggle = document.querySelector('.menu-toggle');
    const mobileNav = document.querySelector('.mobile-nav-container');
    
    if (menuToggle && mobileNav) {
        menuToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            mobileNav.classList.toggle('active');
            document.body.style.overflow = mobileNav.classList.contains('active') ? 'hidden' : '';
        });

        // Fecha menu ao clicar em um link
        const mobileLinks = mobileNav.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                menuToggle.classList.remove('active');
                mobileNav.classList.remove('active');
                document.body.style.overflow = '';
            });
        });

        // Fecha menu ao redimensionar tela
        window.addEventListener('resize', () => {
            if (window.innerWidth > 992) {
                menuToggle.classList.remove('active');
                mobileNav.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    // ===== CONTADOR ANIMADO =====
    const counters = document.querySelectorAll('[data-count]');
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.dataset.count);
                let current = 0;
                const increment = target / 100;
                const updateCounter = () => {
                    if (current < target) {
                        current += increment;
                        counter.textContent = Math.ceil(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target;
                    }
                };
                updateCounter();
                counterObserver.unobserve(counter);
            }
        });
    });

    counters.forEach(counter => counterObserver.observe(counter));

    // ===== EFEITOS DE HOVER MODERNOS =====
    const cards = document.querySelectorAll('.diferencial-item, .plano-card, .post-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // ===== SMOOTH SCROLL PARA ÂNCORAS =====
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});
// Animação dos números das estatísticas
document.addEventListener('DOMContentLoaded', function () {
    // Função para animar um número
    function animateNumber(el, endValue, duration = 2000) {
        let start = 0;
        let startTime = null;
        endValue = parseInt(endValue, 10);

        function updateNumber(timestamp) {
            if (!startTime) startTime = timestamp;
            const progress = Math.min((timestamp - startTime) / duration, 1);
            el.textContent = Math.floor(progress * (endValue - start) + start);

            if (progress < 1) {
                requestAnimationFrame(updateNumber);
            } else {
                el.textContent = endValue; // Garante valor final
            }
        }
        requestAnimationFrame(updateNumber);
    }

    // Observer para animar os números quando aparecem
    const stats = document.querySelectorAll('.estatisticas [data-count]');
    let statsAnimated = false;

    if (stats.length) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting && !statsAnimated) {
                    stats.forEach(function (stat) {
                        animateNumber(stat, stat.getAttribute('data-count'));
                    });
                    statsAnimated = true;
                }
            });
        }, { threshold: 0.3 });

        observer.observe(document.querySelector('.estatisticas'));
    }
});

console.log('🚀 Plano A - JS atualizado carregado!');