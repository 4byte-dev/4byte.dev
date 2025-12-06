export const INITIAL_FILES = {
	"index.html": {
		name: "index.html",
		language: "html",
		content: `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>VS Code Lite</title>
  <link rel="stylesheet" href="src/styles.css">
</head>
<body>
  <div id="app"></div>
  <script type="module" src="src/main.js"></script>
</body>
</html>`,
	},
	"src/styles.css": {
		name: "styles.css",
		language: "css",
		content: `body { 
  font-family: 'Inter', sans-serif; 
  background: #1e1e1e; 
  color: #d4d4d4; 
  display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; 
}
h1 { color: #4fc1ff; font-weight: 300; }`,
	},
	"src/main.js": {
		name: "main.js",
		language: "javascript",
		content: `const app = document.getElementById('app');
app.innerHTML = '<h1>Hello World</h1>';`,
	},
};

export const TEMPLATES = {
	vanilla: {
		name: "Vanilla JS Starter",
		files: INITIAL_FILES,
	},

	"react-tailwind": {
		name: "React + Tailwind",
		files: {
			"index.html": {
				name: "index.html",
				language: "html",
				content: `<!DOCTYPE html>
<html>
<head>
  <title>React App</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
  
  <script type="importmap">
  {
    "imports": {
      "react": "https://esm.sh/react@18.2.0",
      "react-dom/client": "https://esm.sh/react-dom@18.2.0/client"
    }
  }
  </script>
</head>
<body class="bg-slate-900 text-white">
  <div id="root"></div>
  <script src="main.jsx"></script> 
</body>
</html>`,
			},
			"main.jsx": {
				name: "main.jsx",
				language: "javascript",
				content: `import React, { useState } from 'react';
import { createRoot } from 'react-dom/client';

const App = () => {
  const [count, setCount] = useState(0);

  return (
    <div className="flex flex-col items-center justify-center min-h-screen">
      <div className="bg-slate-800 p-8 rounded-xl shadow-2xl text-center border border-slate-700">
        <h1 className="text-4xl font-bold bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent mb-4">
          React + Tailwind
        </h1>
        <p className="text-slate-400 mb-6">Running in Browser!</p>
        
        <div className="text-6xl font-mono mb-6">{count}</div>
        
        <div className="flex gap-4 justify-center">
          <button 
            onClick={() => setCount(c => c - 1)}
            className="px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg transition"
          >
            Decrease
          </button>
          <button 
            onClick={() => setCount(c => c + 1)}
            className="px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded-lg transition shadow-lg shadow-blue-500/30"
          >
            Increase
          </button>
        </div>
      </div>
    </div>
  );
};

const root = createRoot(document.getElementById('root'));
root.render(<App />);`,
			},
		},
	},

	vue: {
		name: "Vue 3 Application",
		files: {
			"index.html": {
				name: "index.html",
				language: "html",
				content: `<!DOCTYPE html>
<html>
<head>
  <title>Vue 3</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #222; color: #fff; display: flex; justify-content: center; height: 100vh; padding-top: 50px; }
    button { cursor: pointer; }
  </style>
  <script type="importmap">
  {
    "imports": {
      "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js"
    }
  }
  </script>
</head>
<body>
  <div id="app"></div>
  <script src="main.js"></script>
</body>
</html>`,
			},
			"main.js": {
				name: "main.js",
				language: "javascript",
				content: `import { createApp, ref } from 'vue';

const App = {
  template: \`
    <div style="text-align: center; border: 1px solid #444; padding: 2rem; border-radius: 10px; background: #333;">
      <h1 style="color: #42b883;">Vue 3 Counter</h1>
      <h2 style="font-size: 3rem; margin: 20px 0;">{{ count }}</h2>
      <button @click="increment" style="background: #42b883; border: none; padding: 10px 20px; color: #222; font-weight: bold; border-radius: 4px;">
        Increment
      </button>
    </div>
  \`,
  setup() {
    const count = ref(0);
    const increment = () => count.value++;
    return { count, increment };
  }
};

createApp(App).mount('#app');`,
			},
		},
	},

	bootstrap: {
		name: "Bootstrap 5 Starter",
		files: {
			"index.html": {
				name: "index.html",
				language: "html",
				content: `<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <title>Hello, world!</title>
  </head>
  <body>
    <h1>Hello, world!</h1>
    <button type="button" class="btn btn-primary" id="hi">Say Hi !</button>

    <div class="alert alert-success" role="alert" id="alert-div" style="display:none;">
      Hello there!
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="script.js"></script>
  </body>
</html>`,
			},
			"script.js": {
				name: "script.js",
				language: "javascript",
				content: `$( document ).ready(function() {
 $( "#hi" ).click(function() {
    $("#alert-div").toggle();
 });
});`,
			},
			"styles.css": {
				name: "styles.css",
				language: "css",
				content: `body{
   padding: 25px;
}

h1 {
 padding-bottom: 15px;
}

.alert {
 margin-top: 25px;
}`,
			},
		},
	},

	threejs: {
		name: "Three.js 3D Scene",
		files: {
			"index.html": {
				name: "index.html",
				language: "html",
				content: `<!DOCTYPE html>
<html>
<head>
  <title>Three.js Starter</title>
  <style>body { margin: 0; overflow: hidden; background: #000; }</style>
  <script type="importmap">
    {
      "imports": {
        "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
        "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
      }
    }
  </script>
</head>
<body>
  <script type="module" src="main.js"></script>
</body>
</html>`,
			},
			"main.js": {
				name: "main.js",
				language: "javascript",
				content: `import * as THREE from 'three';

const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
const renderer = new THREE.WebGLRenderer({ antialias: true });

renderer.setSize(window.innerWidth, window.innerHeight);
document.body.appendChild(renderer.domElement);

// Create a Cube with neon material
const geometry = new THREE.BoxGeometry(1, 1, 1);
const material = new THREE.MeshBasicMaterial({ 
  color: 0x00ff88, 
  wireframe: true 
});
const cube = new THREE.Mesh(geometry, material);
scene.add(cube);

camera.position.z = 3;

// Animation Loop
function animate() {
  requestAnimationFrame(animate);
  
  cube.rotation.x += 0.01;
  cube.rotation.y += 0.01;
  
  // Pulse effect
  const scale = 1 + Math.sin(Date.now() * 0.002) * 0.2;
  cube.scale.set(scale, scale, scale);
  
  renderer.render(scene, camera);
}

// Handle window resize
window.addEventListener('resize', () => {
  camera.aspect = window.innerWidth / window.innerHeight;
  camera.updateProjectionMatrix();
  renderer.setSize(window.innerWidth, window.innerHeight);
});

animate();`,
			},
		},
	},
};
