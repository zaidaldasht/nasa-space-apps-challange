<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Solar System</title>
    <style>
        body {
            margin: 0;
            overflow: hidden;
            background: radial-gradient(circle, #001133, #000000);
        }
        canvas {
            display: block;
        }
    </style>
</head>
<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/mrdoob/three.js@r128/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/mrdoob/three.js@r128/examples/js/loaders/GLTFLoader.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dat-gui/0.7.6/dat.gui.min.js"></script>
    <script>
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.body.appendChild(renderer.domElement);

        // إضاءة أكثر قوة للتأكد من ظهور الأجسام
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.9); 
        scene.add(ambientLight);
        const pointLight = new THREE.PointLight(0xffffff, 1.5, 100); 
        pointLight.position.set(10, 10, 10); // تحريك الضوء لنقاط مختلفة
        scene.add(pointLight);

        // إضافة الشمس
        const sunGeometry = new THREE.SphereGeometry(1, 32, 32);
        const sunMaterial = new THREE.MeshStandardMaterial({ color: 0xffff00 });
        const sun = new THREE.Mesh(sunGeometry, sunMaterial);
        scene.add(sun);

        // إعداد الكواكب
        const planets = [
            { name: "Mercury", semiMajorAxis: 2.439, eccentricity: 0.2056, period: 0.2408467, model: '3d/mercury.gltf' },
            { name: "Venus", semiMajorAxis: 3.515, eccentricity: 0.0067, period: 0.61519726, model: '3d/venus.gltf' },
            { name: "Earth", semiMajorAxis: 4.004, eccentricity: 0.0167, period: 1.0, model: '3d/Earth.gltf' },
            { name: "Mars", semiMajorAxis: 5.689, eccentricity: 0.0934, period: 1.8808476, model: '3d/mars.gltf' },
            { name: "Jupiter", semiMajorAxis: 9.331, eccentricity: 0.0484, period: 11.862615, model: '3d/jupiter.gltf' },
            { name: "Saturn", semiMajorAxis: 18.403, eccentricity: 0.0565, period: 29.4571, model: '3d/saturn.gltf' },
            { name: "Uranus", semiMajorAxis: 36.728, eccentricity: 0.0463, period: 84.0205, model: '3d/uranus.gltf' },
            { name: "Neptune", semiMajorAxis: 57.918, eccentricity: 0.0097, period: 164.79, model: '3d/neptune.gltf' },
            { name: "Pluto", semiMajorAxis: 39.482, eccentricity: 0.2488, period: 247.68, model: '3d/pluto.gltf' }
        ];

        const planetMeshes = [];

        // إنشاء الكواكب
        planets.forEach(planet => {
            const orbit = createOrbit(planet.semiMajorAxis, planet.eccentricity);
            scene.add(orbit);

            const loader = new THREE.GLTFLoader();
            loader.load(planet.model, (gltf) => {
                const mesh = gltf.scene;
                mesh.scale.set(0.5, 0.5, 0.5); // ضبط الحجم
                mesh.position.set(planet.semiMajorAxis, 0, 0); // وضع الكوكب على مداره
                scene.add(mesh);
                console.log(`Loaded ${planet.name}`);
                planetMeshes.push({ mesh, angle: 0, semiMajorAxis: planet.semiMajorAxis, eccentricity: planet.eccentricity, period: planet.period });
            }, undefined, (error) => {
                console.error(`Error loading ${planet.name}:`, error);
            });
        });

        // إنشاء مدارات بيضاوية
        function createOrbit(semiMajorAxis, eccentricity) {
            const points = [];
            const segments = 128; // دقة عالية
            for (let i = 0; i <= segments; i++) {
                const angle = (i / segments) * Math.PI * 2;
                const radius = semiMajorAxis * (1 - eccentricity * Math.cos(angle));
                points.push(new THREE.Vector3(radius * Math.cos(angle), 0, radius * Math.sin(angle)));
            }
            const geometry = new THREE.BufferGeometry().setFromPoints(points);
            const material = new THREE.LineBasicMaterial({ color: 0x888888, opacity: 0.5, transparent: true });
            return new THREE.LineLoop(geometry, material);
        }

        const controls = new THREE.OrbitControls(camera, renderer.domElement);
        camera.position.set(0, 10, 20); // تحريك الكاميرا لتغطية مجال أكبر

        const gui = new dat.GUI();
        const params = {
            speed: 0.01,
            zoom: 20
        };
        gui.add(params, 'speed', 0, 1);
        gui.add(params, 'zoom', 1, 70).onChange((value) => {
            camera.position.z = value;
        });

        // دالة التحريك
        function animate() {
            requestAnimationFrame(animate);

            planetMeshes.forEach(planet => {
                if (planet.mesh) {
                    planet.angle += (Math.PI * 2 / (planet.period * 60)) * (params.speed || 0.01);
                    const radius = planet.semiMajorAxis * (1 - planet.eccentricity * Math.cos(planet.angle));
                    planet.mesh.position.x = radius * Math.cos(planet.angle);
                    planet.mesh.position.z = radius * Math.sin(planet.angle);
                }
            });

            controls.update();
            renderer.render(scene, camera);
        }
        animate();

        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
</body>
</html>
