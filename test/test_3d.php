<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>عرض وتحريك نموذج ثلاثي الأبعاد</title>
  <style>
    body {
      margin: 0;
      overflow: hidden;
    }
    #progress-bar {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: #ddd;
      width: 50%;
      height: 20px;
    }
    #progress-bar-inner {
      width: 0;
      height: 100%;
      background-color: #76c7c0;
    }
  </style>
</head>
<body>
  <div id="progress-bar">
    <div id="progress-bar-inner"></div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

  <script>
    // إعداد المشهد والكاميرا والمصباح
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer();
    renderer.setSize(window.innerWidth, window.innerHeight);
    document.body.appendChild(renderer.domElement);

    // إضافة مصباح للإضاءة
    const light = new THREE.DirectionalLight(0xffffff, 1);
    light.position.set(0, 1, 1).normalize();
    scene.add(light);

    // إعداد OrbitControls للتحكم في الكاميرا (تحريك، تكبير، تدوير)
    const controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true; // تمكين التخميد لتحسين سلاسة التحريك
    controls.dampingFactor = 0.05; // عامل التخميد
    controls.enableZoom = true; // تمكين التكبير والتصغير
    controls.enableRotate = true; // تمكين التدوير
    controls.enablePan = true; // تمكين التحريك الأفقي

    // تحميل النموذج ثلاثي الأبعاد
    const loader = new THREE.GLTFLoader();
    const progressBar = document.getElementById('progress-bar');
    const progressBarInner = document.getElementById('progress-bar-inner');

    loader.load(
      'http://localhost/nasa_legends/3d/Sun.glb', // تأكد من صحة الرابط
      function (gltf) {
        scene.add(gltf.scene); // إضافة النموذج إلى المشهد
        progressBar.style.display = 'none'; // إخفاء شريط التحميل عند الانتهاء
      },
      function (xhr) {
        const percentComplete = (xhr.loaded / xhr.total) * 100;
        progressBarInner.style.width = percentComplete + '%'; // تحديث شريط التحميل
      },
      function (error) {
        console.error('حدث خطأ أثناء تحميل النموذج:', error);
      }
    );

    camera.position.z = 5;

    // تحديث وعرض المشهد
    function animate() {
      requestAnimationFrame(animate);
      controls.update(); // تحديث أوامر التحكم
      renderer.render(scene, camera);
    }
    animate();

    // جعل العرض متجاوبًا مع تغيير حجم النافذة
    window.addEventListener('resize', () => {
      renderer.setSize(window.innerWidth, window.innerHeight);
      camera.aspect = window.innerWidth / window.innerHeight;
      camera.updateProjectionMatrix();
    });
  </script>
</body>
</html>
