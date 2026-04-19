document.addEventListener('DOMContentLoaded', function() {
    const threeContainer = document.getElementById('threejs-container');
    if (threeContainer) {
        initVillageScene(threeContainer);
    }
});

function initVillageScene(container) {
    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0xe0f7fa);
    scene.fog = new THREE.Fog(0xe0f7fa, 10, 20);
    const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
    camera.position.set(0, 3, 8);
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.setPixelRatio(window.devicePixelRatio);
    renderer.shadowMap.enabled = true;
    container.appendChild(renderer.domElement);
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
    scene.add(ambientLight);
    
    const sunLight = new THREE.DirectionalLight(0xffffff, 0.8);
    sunLight.position.set(5, 10, 7);
    sunLight.castShadow = true;
    sunLight.shadow.mapSize.width = 1024;
    sunLight.shadow.mapSize.height = 1024;
    scene.add(sunLight);
    
    const backLight = new THREE.DirectionalLight(0xffffff, 0.4);
    backLight.position.set(-5, -2, -5);
    scene.add(backLight);
    const villageGroup = new THREE.Group();
    const terrainGeometry = new THREE.PlaneGeometry(30, 30, 50, 50);
    const terrainMaterial = new THREE.MeshStandardMaterial({ 
        color: 0x4CAF50,
        wireframe: false,
        roughness: 0.9,
        metalness: 0.1
    });
    const vertices = terrainGeometry.attributes.position.array;
    for (let i = 0; i < vertices.length; i += 3) {
        const x = vertices[i];
        const z = vertices[i + 1];
        const distanceFactor = Math.exp(-(x*x + z*z) / 100);
        vertices[i + 2] = Math.sin(x * 0.2) * Math.cos(z * 0.2) * 0.5 * distanceFactor;
    }
    
    terrainGeometry.computeVertexNormals();
    const terrain = new THREE.Mesh(terrainGeometry, terrainMaterial);
    terrain.rotation.x = -Math.PI / 2;
    terrain.position.y = -1;
    villageGroup.add(terrain);
    const waterGeometry = new THREE.PlaneGeometry(10, 5);
    const waterMaterial = new THREE.MeshStandardMaterial({ 
        color: 0x2196F3,
        transparent: true,
        opacity: 0.8,
        roughness: 0.1,
        metalness: 0.9
    });
    const water = new THREE.Mesh(waterGeometry, waterMaterial);
    water.rotation.x = -Math.PI / 2;
    water.position.set(8, -0.9, 5);
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
        const roofGeometry = new THREE.ConeGeometry(1, 0.6, 4);
        const roofMaterial = new THREE.MeshStandardMaterial({ color: 0xD32F2F });
        const roof = new THREE.Mesh(roofGeometry, roofMaterial);
        roof.position.y = y + 1;
        roof.rotation.y = Math.PI / 4;
        roof.castShadow = true;
        houseGroup.add(roof);
        const doorGeometry = new THREE.BoxGeometry(0.3, 0.6, 0.1);
        const doorMaterial = new THREE.MeshStandardMaterial({ color: 0x5D4037 });
        const door = new THREE.Mesh(doorGeometry, doorMaterial);
        door.position.set(0, y - 0.2, 0.8);
        houseGroup.add(door);
        for (let i = -1; i <= 1; i += 2) {
            const windowGeometry = new THREE.BoxGeometry(0.3, 0.3, 0.1);
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
    const sunGeometry = new THREE.SphereGeometry(1, 32, 32);
    const sunMaterial = new THREE.MeshBasicMaterial({ color: 0xFFEB3B });
    const sun = new THREE.Mesh(sunGeometry, sunMaterial);
    sun.position.set(10, 10, 10);
    scene.add(sun);
    const moonGeometry = new THREE.SphereGeometry(0.7, 32, 32);
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
        const time = Date.now() * 0.0001;
        sun.position.x = 10 * Math.cos(time * 0.1);
        sun.position.z = 10 * Math.sin(time * 0.1);
        moon.position.x = -10 * Math.cos(time * 0.1);
        moon.position.z = -10 * Math.sin(time * 0.1);
        
        renderer.render(scene, camera);
    }
    window.addEventListener('resize', () => {
        camera.aspect = container.clientWidth / container.clientHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(container.clientWidth, container.clientHeight);
    });
    container.addEventListener('mousemove', (event) => {
        const mouseX = (event.clientX / window.innerWidth) * 2 - 1;
        const mouseY = -(event.clientY / window.innerHeight) * 2 + 1;
        
        villageGroup.rotation.y = mouseX * 0.2;
        villageGroup.rotation.x = mouseY * 0.2;
    });
    animate();
}