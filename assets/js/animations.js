document.addEventListener('DOMContentLoaded', function() {
    const page = document.body?.dataset?.page || '';
    const reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (page === 'home' || reducedMotion) {
        return;
    }
    runInitialAnimations();
    if (typeof ScrollTrigger !== 'undefined') {
        setupScrollAnimations();
    } else {
        console.warn('ScrollTrigger is not loaded, skipping scroll-triggered animations');
    }
    initParallax();
});

function runInitialAnimations() {
    if (typeof gsap !== 'undefined') {
        gsap.from(".hero-image", {
            duration: 1.5,
            scale: 1.1,
            ease: "power2.inOut",
            opacity: 0
        });
        gsap.from(".caption-content > *", {
            duration: 1,
            y: 50,
            opacity: 0,
            stagger: 0.2,
            delay: 0.5
        });
        gsap.from(".navbar-brand, .navbar-nav .nav-link", {
            duration: 0.8,
            y: -50,
            opacity: 0,
            stagger: 0.1,
            ease: "back.out(1.7)"
        });
    }
}

function setupScrollAnimations() {
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
        gsap.utils.toArray('.stat-card, .news-card').forEach(card => {
            gsap.from(card, {
                scrollTrigger: {
                    trigger: card,
                    start: "top 85%",
                    toggleActions: "play none none reverse"
                },
                duration: 0.8,
                y: 50,
                opacity: 0,
                ease: "power2.out"
            });
        });
        gsap.utils.toArray('.section-title').forEach(title => {
            gsap.from(title, {
                scrollTrigger: {
                    trigger: title,
                    start: "top 85%",
                    toggleActions: "play none none reverse"
                },
                duration: 0.8,
                x: -50,
                opacity: 0,
                ease: "power2.out"
            });
        });
        gsap.from(".about-image-container, .about-section .col-lg-6:first-child", {
            scrollTrigger: {
                trigger: ".about-section",
                start: "top 85%"
            },
            duration: 1,
            x: (index) => index === 0 ? 50 : -50,
            opacity: 0,
            stagger: 0.2,
            ease: "power2.out"
        });
        gsap.utils.toArray('.footer .row > div').forEach(section => {
            gsap.from(section, {
                scrollTrigger: {
                    trigger: ".footer",
                    start: "top 90%"
                },
                duration: 0.8,
                y: 30,
                opacity: 0,
                stagger: 0.1
            });
        });
        gsap.utils.toArray('a.btn').forEach(button => {
            button.addEventListener('mouseenter', () => {
                gsap.to(button, {
                    duration: 0.3,
                    y: -3,
                    boxShadow: "0 10px 20px rgba(0,0,0,0.1)",
                    ease: "power2.out"
                });
            });
            
            button.addEventListener('mouseleave', () => {
                gsap.to(button, {
                    duration: 0.3,
                    y: 0,
                    boxShadow: "0 4px 6px rgba(0,0,0,0.1)",
                    ease: "power2.out"
                });
            });
        });
    }
}
function initParallax() {
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelector('.hero-image');
        const speed = scrolled * -0.5;
        
        if (parallax) {
            parallax.style.transform = `translateY(${speed}px)`;
        }
    });
}