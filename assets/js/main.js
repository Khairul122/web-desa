if (typeof gsap !== 'undefined') {
    if (typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
    } else {
        console.warn('ScrollTrigger is not loaded');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const isHome = isHomePage();

    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 700,
            easing: 'ease-out-cubic',
            once: true,
            offset: 40
        });
    }
    if (isHome) {
        initHomeAnime();
    } else {
        initPageAnimations();
    }

    initThreeScene();
    setTimeout(() => {
        const preloader = document.getElementById('preloader');
        if (!preloader) return;

        if (typeof gsap !== 'undefined') {
            gsap.to(preloader, {
                opacity: 0,
                duration: 0.8,
                ease: "power2.out",
                onComplete: () => {
                    preloader.style.display = 'none';
                }
            });
            return;
        }

        preloader.style.opacity = '0';
        preloader.style.transition = 'opacity .6s ease';
        setTimeout(() => {
            preloader.style.display = 'none';
        }, 600);
    }, 1200);
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                if (typeof gsap !== 'undefined' && typeof window.ScrollToPlugin !== 'undefined') {
                    gsap.to(window, {
                        duration: 1,
                        scrollTo: {
                            y: target,
                            offsetY: 80
                        },
                        ease: "power2.inOut"
                    });
                } else {
                    window.scrollTo({
                        top: target.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
});

function isHomePage() {
    const page = document.body?.dataset?.page || '';
    return page === 'home';
}

function prefersReducedMotion() {
    return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

function initHomeAnime() {
    if (typeof anime === 'undefined' || prefersReducedMotion()) {
        return;
    }

    document.querySelectorAll('[data-anim]').forEach((element) => {
        element.removeAttribute('data-aos');
        element.removeAttribute('data-aos-delay');
    });

    anime.timeline({ easing: 'easeOutCubic', duration: 700 })
        .add({
            targets: '[data-anim="hero-badge"]',
            opacity: [0, 1],
            translateY: [18, 0]
        })
        .add({
            targets: '[data-anim="hero-title"]',
            opacity: [0, 1],
            translateY: [28, 0]
        }, '-=450')
        .add({
            targets: '[data-anim="hero-subtitle"]',
            opacity: [0, 1],
            translateY: [20, 0]
        }, '-=420')
        .add({
            targets: '[data-anim="hero-actions"] .btn',
            opacity: [0, 1],
            translateY: [16, 0],
            delay: anime.stagger(90)
        }, '-=380');

    revealOnView('[data-anim="section-title"]', { translateY: [24, 0] });
    revealOnView('[data-anim="service-card"]', { translateY: [24, 0], delay: anime.stagger(80) });
    revealOnView('[data-anim="stat-card"]', { translateY: [28, 0], scale: [0.96, 1], delay: anime.stagger(100) });
    revealOnView('[data-anim="news-card"]', { translateY: [28, 0], delay: anime.stagger(110) });
    revealOnView('[data-anim="gallery-card"]', { translateY: [20, 0], delay: anime.stagger(70) });
    revealOnView('[data-anim="cta"]', { translateY: [24, 0] });

    animateStatNumbers();
}

function revealOnView(selector, animationOptions = {}) {
    const nodes = document.querySelectorAll(selector);
    if (!nodes.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            anime({
                targets: entry.target,
                opacity: [0, 1],
                easing: 'easeOutCubic',
                duration: 700,
                ...animationOptions
            });

            observer.unobserve(entry.target);
        });
    }, { threshold: 0.2 });

    nodes.forEach((node) => {
        node.style.opacity = '0';
        observer.observe(node);
    });
}

function animateStatNumbers() {
    const numbers = document.querySelectorAll('.stat-number[data-target]');
    if (!numbers.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            const el = entry.target;
            const target = parseInt((el.dataset.target || '').replace(/[^0-9]/g, ''), 10);
            if (Number.isNaN(target)) {
                observer.unobserve(el);
                return;
            }

            anime({
                targets: { value: 0 },
                value: target,
                duration: 1200,
                easing: 'easeOutExpo',
                round: 1,
                update: (anim) => {
                    el.textContent = new Intl.NumberFormat('id-ID').format(anim.animations[0].currentValue);
                }
            });

            observer.unobserve(el);
        });
    }, { threshold: 0.5 });

    numbers.forEach((el) => observer.observe(el));
}
function initPageAnimations() {
    if (typeof gsap === 'undefined') {
        return;
    }
    gsap.from('.navbar', {
        y: -100,
        opacity: 0,
        duration: 1,
        ease: "back.out(1.7)"
    });
    gsap.from('.caption-content h2', {
        y: 50,
        opacity: 0,
        duration: 1,
        delay: 0.5,
        ease: "power2.out"
    });
    
    gsap.from('.caption-content p', {
        y: 30,
        opacity: 0,
        duration: 1,
        delay: 0.7,
        ease: "power2.out"
    });
    
    gsap.from('.btn-warning', {
        y: 30,
        opacity: 0,
        duration: 1,
        delay: 0.9,
        ease: "power2.out"
    });
    gsap.utils.toArray('.section-title').forEach(title => {
        gsap.fromTo(title, 
            { opacity: 0, y: 50 },
            {
                opacity: 1,
                y: 0,
                duration: 0.8,
                scrollTrigger: {
                    trigger: title,
                    start: "top 85%",
                    toggleActions: "play none none reverse"
                },
                ease: "power2.out"
            }
        );
    });
    gsap.utils.toArray('.section-header p').forEach(desc => {
        gsap.fromTo(desc, 
            { opacity: 0, y: 30 },
            {
                opacity: 1,
                y: 0,
                duration: 0.8,
                scrollTrigger: {
                    trigger: desc,
                    start: "top 90%",
                    toggleActions: "play none none reverse"
                },
                ease: "power2.out"
            }
        );
    });
    gsap.utils.toArray('.stat-card').forEach((card, index) => {
        gsap.fromTo(card, 
            { opacity: 0, y: 50, scale: 0.9 },
            {
                opacity: 1,
                y: 0,
                scale: 1,
                duration: 0.8,
                delay: index * 0.1,
                scrollTrigger: {
                    trigger: card,
                    start: "top 90%",
                    toggleActions: "play none none reverse"
                },
                ease: "back.out(1.7)"
            }
        );
    });
    gsap.utils.toArray('.news-card').forEach((card, index) => {
        gsap.fromTo(card, 
            { opacity: 0, y: 50 },
            {
                opacity: 1,
                y: 0,
                duration: 0.8,
                delay: index * 0.15,
                scrollTrigger: {
                    trigger: card,
                    start: "top 90%",
                    toggleActions: "play none none reverse"
                },
                ease: "power2.out"
            }
        );
    });
    gsap.fromTo('.about-content', 
        { opacity: 0, x: -50 },
        {
            opacity: 1,
            x: 0,
            duration: 1,
            scrollTrigger: {
                trigger: '.about-content',
                start: "top 85%",
                toggleActions: "play none none reverse"
            },
            ease: "power2.out"
        }
    );
    
    gsap.fromTo('.about-image-container', 
        { opacity: 0, x: 50 },
        {
            opacity: 1,
            x: 0,
            duration: 1,
            scrollTrigger: {
                trigger: '.about-image-container',
                start: "top 85%",
                toggleActions: "play none none reverse"
            },
            ease: "power2.out"
        }
    );
    gsap.utils.toArray('.fade-on-scroll').forEach(element => {
        gsap.fromTo(element, 
            { opacity: 0, y: 30 },
            {
                opacity: 1,
                y: 0,
                duration: 0.8,
                scrollTrigger: {
                    trigger: element,
                    start: "top 90%",
                    toggleActions: "play none none reverse"
                },
                ease: "power2.out"
            }
        );
    });
    gsap.utils.toArray('.btn').forEach(button => {
        button.addEventListener('mouseenter', () => {
            gsap.to(button, {
                y: -3,
                boxShadow: "0 10px 25px rgba(0,0,0,0.15)",
                duration: 0.3,
                ease: "power2.out"
            });
        });
        
        button.addEventListener('mouseleave', () => {
            gsap.to(button, {
                y: 0,
                boxShadow: "0 4px 15px rgba(0,0,0,0.1)",
                duration: 0.3,
                ease: "power2.out"
            });
        });
    });
}
let scene, camera, renderer, mesh;

function initThreeScene() {
    const container = document.getElementById('threejs-container');
    if (!container) return;
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0xf8f9fa);
    camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
    camera.position.set(5, 5, 7);
    camera.lookAt(0, 0, 0);
    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.setPixelRatio(window.devicePixelRatio);
    renderer.shadowMap.enabled = true;
    container.appendChild(renderer.domElement);
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
    scene.add(ambientLight);
    
    const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
    directionalLight.position.set(10, 20, 15);
    directionalLight.castShadow = true;
    scene.add(directionalLight);
    
    const backLight = new THREE.DirectionalLight(0xffffff, 0.4);
    backLight.position.set(-10, -10, -10);
    scene.add(backLight);
    const villageGroup = new THREE.Group();
    const groundGeometry = new THREE.PlaneGeometry(20, 20, 50, 50);
    const groundMaterial = new THREE.MeshStandardMaterial({ 
        color: 0x4CAF50,
        wireframe: false,
        roughness: 0.9,
        metalness: 0.1
    });
    const vertices = groundGeometry.attributes.position.array;
    for (let i = 0; i < vertices.length; i += 3) {
        const x = vertices[i];
        const z = vertices[i + 1];
        const distanceFactor = Math.exp(-(x*x + z*z) / 100);
        vertices[i + 2] = Math.sin(x * 0.2) * Math.cos(z * 0.2) * 0.5 * distanceFactor;
    }
    
    groundGeometry.computeVertexNormals();
    const ground = new THREE.Mesh(groundGeometry, groundMaterial);
    ground.rotation.x = -Math.PI / 2;
    ground.position.y = -1;
    ground.receiveShadow = true;
    villageGroup.add(ground);
    const waterGeometry = new THREE.PlaneGeometry(8, 4);
    const waterMaterial = new THREE.MeshStandardMaterial({ 
        color: 0x2196F3,
        transparent: true,
        opacity: 0.8,
        roughness: 0.1,
        metalness: 0.9
    });
    const water = new THREE.Mesh(waterGeometry, waterMaterial);
    water.rotation.x = -Math.PI / 2;
    water.position.set(6, -0.9, 4);
    villageGroup.add(water);
    function createHouse(x, y, z, color = 0xFF8A65) {
        const houseGroup = new THREE.Group();
        const baseGeometry = new THREE.BoxGeometry(1.5, 1, 1.5);
        const baseMaterial = new THREE.MeshStandardMaterial({ color: color });
        const base = new THREE.Mesh(baseGeometry, baseMaterial);
        base.position.y = y;
        base.castShadow = true;
        base.receiveShadow = true;
        houseGroup.add(base);
        const roofGeometry = new THREE.ConeGeometry(1.2, 0.8, 4);
        const roofMaterial = new THREE.MeshStandardMaterial({ color: 0xD32F2F });
        const roof = new THREE.Mesh(roofGeometry, roofMaterial);
        roof.position.y = y + 1.2;
        roof.rotation.y = Math.PI / 4;
        roof.castShadow = true;
        houseGroup.add(roof);
        const doorGeometry = new THREE.BoxGeometry(0.3, 0.6, 0.1);
        const doorMaterial = new THREE.MeshStandardMaterial({ color: 0x5D4037 });
        const door = new THREE.Mesh(doorGeometry, doorMaterial);
        door.position.set(0, y - 0.2, 0.81);
        houseGroup.add(door);
        for (let i = -1; i <= 1; i += 2) {
            const windowGeometry = new THREE.BoxGeometry(0.3, 0.3, 0.11);
            const windowMaterial = new THREE.MeshStandardMaterial({ color: 0xFFF9C4 });
            const window = new THREE.Mesh(windowGeometry, windowMaterial);
            window.position.set(i * 0.5, y + 0.3, 0.81);
            houseGroup.add(window);
        }
        
        houseGroup.position.set(x, 0, z);
        villageGroup.add(houseGroup);
        return houseGroup;
    }
    const housePositions = [
        [-5, 0], [-3, 0], [-1, 0], [1, 0], [3, 0],
        [-4, -3], [-2, -3], [0, -3], [2, -3], [4, -3],
        [-3, 3], [-1, 3], [1, 3], [3, 3]
    ];
    
    const houseColors = [0xFF8A65, 0x81C784, 0xFFB74D, 0xBA68C8, 0x4FC3F7];
    
    housePositions.forEach((pos, index) => {
        const colorIndex = index % houseColors.length;
        createHouse(pos[0], 0, pos[1], houseColors[colorIndex]);
    });
    function createTree(x, z) {
        const treeGroup = new THREE.Group();
        const trunkGeometry = new THREE.CylinderGeometry(0.15, 0.2, 1.5);
        const trunkMaterial = new THREE.MeshStandardMaterial({ color: 0x795548 });
        const trunk = new THREE.Mesh(trunkGeometry, trunkMaterial);
        trunk.position.y = -0.25;
        trunk.castShadow = true;
        treeGroup.add(trunk);
        const topGeometry = new THREE.SphereGeometry(0.8, 8, 8);
        const topMaterial = new THREE.MeshStandardMaterial({ color: 0x388E3C });
        const top = new THREE.Mesh(topGeometry, topMaterial);
        top.position.y = 0.8;
        top.castShadow = true;
        treeGroup.add(top);
        
        treeGroup.position.set(x, 0, z);
        villageGroup.add(treeGroup);
        return treeGroup;
    }
    const treePositions = [
        [-8, -6], [-7, -8], [-5, -7], [-6, -4],
        [7, -6], [8, -8], [6, -7], [5, -4],
        [-8, 6], [-7, 8], [-5, 7], [-6, 4],
        [7, 6], [8, 8], [6, 7], [5, 4],
        [0, 7], [0, -7], [-9, 0], [9, 0]
    ];
    
    treePositions.forEach(pos => {
        createTree(pos[0], pos[1]);
    });
    scene.add(villageGroup);
    function createCloud(x, y, z) {
        const cloudGroup = new THREE.Group();
        
        for (let i = 0; i < 5; i++) {
            const geometry = new THREE.SphereGeometry(Math.random() * 0.5 + 0.5, 8, 8);
            const material = new THREE.MeshPhongMaterial({ 
                color: 0xffffff,
                transparent: true,
                opacity: 0.8
            });
            const sphere = new THREE.Mesh(geometry, material);
            
            sphere.position.set(
                (Math.random() - 0.5) * 2,
                (Math.random() - 0.5) * 0.5,
                (Math.random() - 0.5) * 2
            );
            
            cloudGroup.add(sphere);
        }
        
        cloudGroup.position.set(x, y, z);
        scene.add(cloudGroup);
        return cloudGroup;
    }
    const clouds = [];
    for (let i = 0; i < 5; i++) {
        const x = (Math.random() - 0.5) * 20;
        const y = Math.random() * 3 + 8;
        const z = (Math.random() - 0.5) * 20;
        clouds.push(createCloud(x, y, z));
    }
    const sunGeometry = new THREE.SphereGeometry(1.5, 32, 32);
    const sunMaterial = new THREE.MeshBasicMaterial({ color: 0xFFEB3B });
    const sun = new THREE.Mesh(sunGeometry, sunMaterial);
    sun.position.set(10, 10, 10);
    scene.add(sun);
    const moonGeometry = new THREE.SphereGeometry(1, 32, 32);
    const moonMaterial = new THREE.MeshPhongMaterial({ color: 0xF5F5F5 });
    const moon = new THREE.Mesh(moonGeometry, moonMaterial);
    moon.position.set(-10, 8, -10);
    scene.add(moon);
    const starsGeometry = new THREE.BufferGeometry();
    const starsCount = 1000;
    const starsPosition = new Float32Array(starsCount * 3);
    
    for (let i = 0; i < starsCount * 3; i += 3) {
        starsPosition[i] = (Math.random() - 0.5) * 200; // x
        starsPosition[i + 1] = (Math.random() - 0.5) * 200; // y
        starsPosition[i + 2] = (Math.random() - 0.5) * 200; // z
    }
    
    starsGeometry.setAttribute('position', new THREE.BufferAttribute(starsPosition, 3));
    
    const starsMaterial = new THREE.PointsMaterial({
        color: 0xFFFFFF,
        size: 0.2,
        transparent: true
    });
    
    const stars = new THREE.Points(starsGeometry, starsMaterial);
    scene.add(stars);
    function animate() {
        requestAnimationFrame(animate);
        villageGroup.rotation.y = Date.now() * 0.0001;
        clouds.forEach((cloud, index) => {
            cloud.position.x += Math.sin(Date.now() * 0.0001 + index) * 0.001;
            cloud.position.z += Math.cos(Date.now() * 0.0001 + index) * 0.001;
        });
        const time = Date.now() * 0.00005;
        sun.position.x = 10 * Math.cos(time);
        sun.position.z = 10 * Math.sin(time);
        moon.position.x = -10 * Math.cos(time);
        moon.position.z = -10 * Math.sin(time);
        
        renderer.render(scene, camera);
    }
    window.addEventListener('resize', () => {
        camera.aspect = container.clientWidth / container.clientHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(container.clientWidth, container.clientHeight);
    });
    container.addEventListener('mousemove', (event) => {
        const rect = container.getBoundingClientRect();
        const mouseX = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        const mouseY = -((event.clientY - rect.top) / rect.height) * 2 + 1;
        
        villageGroup.rotation.y = mouseX * 0.2;
        villageGroup.rotation.x = mouseY * 0.2;
    });
    animate();
}