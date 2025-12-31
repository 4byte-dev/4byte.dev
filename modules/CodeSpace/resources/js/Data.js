export const INITIAL_FILES = {
	"index.html": {
		name: "index.html",
		language: "html",
		content: `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CodeSpace</title>
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
	phaser: {
		name: "Phaser.js Game Starter",
		files: {
			"index.html": {
				name: "index.html",
				language: "html",
				content: `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Phaser Game</title>
  <style>
    body {
      margin: 0;
      background: #0f172a;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    canvas {
      border-radius: 12px;
      box-shadow: 0 0 40px rgba(0,0,0,.6);
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/phaser@3.80.1/dist/phaser.js"></script>
</head>
<body>
  <script src="main.js"></script>
</body>
</html>`,
			},

			"main.js": {
				name: "main.js",
				language: "javascript",
				content: `const config = {
  type: Phaser.AUTO,
  width: 800,
  height: 450,
  backgroundColor: "#020617",
  physics: {
    default: "arcade",
    arcade: {
      gravity: { y: 600 },
      debug: false
    }
  },
  scene: {
    preload,
    create,
    update
  }
};

const game = new Phaser.Game(config);

let player;
let cursors;
let platforms;

function preload() {
  this.load.image("ground", "https://labs.phaser.io/assets/sprites/platform.png");
  this.load.image("player", "https://labs.phaser.io/assets/sprites/phaser-dude.png");
}

function create() {
  platforms = this.physics.add.staticGroup();

  platforms.create(400, 430, "ground").setScale(2).refreshBody();

  player = this.physics.add.sprite(400, 300, "player");
  player.setBounce(0.2);
  player.setCollideWorldBounds(true);

  this.physics.add.collider(player, platforms);

  cursors = this.input.keyboard.createCursorKeys();

  this.add.text(20, 20, "Phaser.js Starter", {
    fontSize: "20px",
    color: "#38bdf8"
  });
}

function update() {
  if (cursors.left.isDown) {
    player.setVelocityX(-200);
  } else if (cursors.right.isDown) {
    player.setVelocityX(200);
  } else {
    player.setVelocityX(0);
  }

  if (cursors.up.isDown && player.body.touching.down) {
    player.setVelocityY(-420);
  }
}`,
			},
		},
	},
	"canvas-physics": {
		name: "Canvas Physics Demo",
		files: {
			"index.html": {
				name: "index.html",
				language: "html",
				content: `<!DOCTYPE html>
<html>
<head>
  <title>Canvas Physics</title>
  <style>
    body { margin: 0; background: #020617; }
    canvas { display: block; }
  </style>
</head>
<body>
  <canvas id="c"></canvas>
  <script src="main.js"></script>
</body>
</html>`,
			},
			"main.js": {
				name: "main.js",
				language: "javascript",
				content: `const canvas = document.getElementById("c");
const ctx = canvas.getContext("2d");

canvas.width = innerWidth;
canvas.height = innerHeight;

const gravity = 0.5;
const balls = [];

class Ball {
  constructor(x, y) {
    this.x = x;
    this.y = y;
    this.vy = 0;
    this.r = 12;
  }
  update() {
    this.vy += gravity;
    this.y += this.vy;
    if (this.y + this.r > canvas.height) {
      this.y = canvas.height - this.r;
      this.vy *= -0.8;
    }
  }
  draw() {
    ctx.beginPath();
    ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
    ctx.fillStyle = "#38bdf8";
    ctx.fill();
  }
}

canvas.addEventListener("click", e => {
  balls.push(new Ball(e.clientX, e.clientY));
});

function loop() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  balls.forEach(b => {
    b.update();
    b.draw();
  });
  requestAnimationFrame(loop);
}

loop();`,
			},
		},
	},
	"pathfinding-astar": {
		name: "Pathfinding Visualizer (A*)",
		files: {
			"index.html": {
				name: "index.html",
				language: "html",
				content: `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>A* Pathfinding</title>
  <style>
    body {
      margin: 0;
      background: #020617;
      color: #e5e7eb;
      font-family: monospace;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    canvas {
      border: 1px solid #334155;
      margin-top: 10px;
    }
    .info {
      margin-top: 10px;
      opacity: .8;
    }
  </style>
</head>
<body>
  <h2>A* Pathfinding Visualizer</h2>
  <canvas id="c" width="600" height="600"></canvas>
  <div class="info">
    Click: toggle wall | Space: start A*
  </div>
  <script src="main.js"></script>
</body>
</html>`,
			},

			"main.js": {
				name: "main.js",
				language: "javascript",
				content: `const canvas = document.getElementById("c");
const ctx = canvas.getContext("2d");

const SIZE = 25;
const COLS = canvas.width / SIZE;
const ROWS = canvas.height / SIZE;

let grid = [];
let openSet = [];
let closedSet = [];
let path = [];

let start, end;
let running = false;

/* ---------- Utils ---------- */
const heuristic = (a, b) =>
  Math.abs(a.x - b.x) + Math.abs(a.y - b.y);

/* ---------- Node ---------- */
class Node {
  constructor(x, y) {
    this.x = x;
    this.y = y;
    this.wall = false;

    this.g = 0;
    this.h = 0;
    this.f = 0;
    this.parent = null;
  }

  draw(color) {
    ctx.fillStyle = color;
    ctx.fillRect(
      this.x * SIZE,
      this.y * SIZE,
      SIZE - 1,
      SIZE - 1
    );
  }
}

/* ---------- Grid ---------- */
function init() {
  grid = [];
  for (let y = 0; y < ROWS; y++) {
    const row = [];
    for (let x = 0; x < COLS; x++) {
      row.push(new Node(x, y));
    }
    grid.push(row);
  }

  start = grid[2][2];
  end = grid[ROWS - 3][COLS - 3];

  openSet = [start];
  closedSet = [];
  path = [];
  running = false;
}

init();

/* ---------- Neighbors ---------- */
function neighbors(node) {
  const res = [];
  const dirs = [
    [1,0],[-1,0],[0,1],[0,-1]
  ];
  for (const [dx, dy] of dirs) {
    const x = node.x + dx;
    const y = node.y + dy;
    if (grid[y] && grid[y][x]) {
      res.push(grid[y][x]);
    }
  }
  return res;
}

/* ---------- A* Step ---------- */
function step() {
  if (openSet.length === 0) {
    running = false;
    return;
  }

  let current = openSet.reduce((a,b) =>
    a.f < b.f ? a : b
  );

  if (current === end) {
    path = [];
    let temp = current;
    while (temp) {
      path.push(temp);
      temp = temp.parent;
    }
    running = false;
    return;
  }

  openSet = openSet.filter(n => n !== current);
  closedSet.push(current);

  for (const n of neighbors(current)) {
    if (n.wall || closedSet.includes(n)) continue;

    const tentative = current.g + 1;
    let better = false;

    if (!openSet.includes(n)) {
      openSet.push(n);
      better = true;
    } else if (tentative < n.g) {
      better = true;
    }

    if (better) {
      n.g = tentative;
      n.h = heuristic(n, end);
      n.f = n.g + n.h;
      n.parent = current;
    }
  }
}

/* ---------- Draw ---------- */
function draw() {
  ctx.clearRect(0,0,canvas.width,canvas.height);

  for (const row of grid) {
    for (const n of row) {
      if (n.wall) n.draw("#334155");
      else n.draw("#020617");
    }
  }

  closedSet.forEach(n => n.draw("#7c2d12")); // red
  openSet.forEach(n => n.draw("#1d4ed8"));   // blue
  path.forEach(n => n.draw("#22c55e"));      // green

  start.draw("#facc15");
  end.draw("#ef4444");
}

/* ---------- Loop ---------- */
function loop() {
  if (running) step();
  draw();
  requestAnimationFrame(loop);
}
loop();

/* ---------- Input ---------- */
canvas.addEventListener("click", e => {
  if (running) return;
  const x = Math.floor(e.offsetX / SIZE);
  const y = Math.floor(e.offsetY / SIZE);
  const n = grid[y][x];
  if (n !== start && n !== end) n.wall = !n.wall;
});

window.addEventListener("keydown", e => {
  if (e.code === "Space") {
    init();
    running = true;
  }
});`,
			},
		},
	},
	"brainjs-visualizer": {
		name: "Brain.js Neural Network Visualizer",
		files: {
			"index.html": {
				name: "index.html",
				language: "html",
				content: `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Brain.js Visualizer</title>
  <style>
    body {
      margin: 0;
      background: #020617;
      color: #e5e7eb;
      font-family: monospace;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    canvas {
      border: 1px solid #334155;
      margin-top: 10px;
      cursor: crosshair;
    }
    .controls {
      margin-top: 10px;
      display: flex;
      gap: 10px;
    }
    button {
      background: #1e293b;
      border: 1px solid #334155;
      color: #e5e7eb;
      padding: 6px 12px;
      cursor: pointer;
    }
    button.active {
      background: #2563eb;
    }
  </style>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/brain.js/2.0.0-beta.1/brain-browser.js"></script>
</head>
<body>
  <h2>Brain.js – Live Neural Network</h2>

  <canvas id="c" width="500" height="500"></canvas>

  <div class="controls">
    <button id="pos" class="active">Positive</button>
    <button id="neg">Negative</button>
    <button id="train">Train</button>
    <button id="reset">Reset</button>
  </div>

  <script src="main.js"></script>
</body>
</html>`,
			},

			"main.js": {
				name: "main.js",
				language: "javascript",
				content: `const canvas = document.getElementById("c");
const ctx = canvas.getContext("2d");

const SIZE = canvas.width;
let mode = "pos";

const data = [];

const net = new brain.NeuralNetwork({
  hiddenLayers: [8, 8]
});

/* ---------- UI ---------- */
document.getElementById("pos").onclick = () => setMode("pos");
document.getElementById("neg").onclick = () => setMode("neg");
document.getElementById("train").onclick = train;
document.getElementById("reset").onclick = reset;

function setMode(m) {
  mode = m;
  document.getElementById("pos").classList.toggle("active", m === "pos");
  document.getElementById("neg").classList.toggle("active", m === "neg");
}

/* ---------- Input ---------- */
canvas.addEventListener("click", e => {
  const x = e.offsetX / SIZE;
  const y = e.offsetY / SIZE;

  data.push({
    input: { x, y },
    output: { value: mode === "pos" ? 1 : 0 }
  });

  draw();
});

/* ---------- Train ---------- */
function train() {
  if (data.length < 2) return;

  net.train(data, {
    iterations: 1000,
    learningRate: 0.3,
    log: true
  });

  drawDecision();
  draw();
}

/* ---------- Draw ---------- */
function draw() {
  ctx.clearRect(0,0,SIZE,SIZE);

  for (const d of data) {
    ctx.beginPath();
    ctx.arc(
      d.input.x * SIZE,
      d.input.y * SIZE,
      5,
      0,
      Math.PI * 2
    );
    ctx.fillStyle = d.output.value ? "#22c55e" : "#ef4444";
    ctx.fill();
  }
}

function drawDecision() {
  const step = 8;
  for (let x = 0; x < SIZE; x += step) {
    for (let y = 0; y < SIZE; y += step) {
      const out = net.run({
        x: x / SIZE,
        y: y / SIZE
      }).value;

      ctx.fillStyle = \`rgba(37, 99, 235, \${out * 0.4})\`;
      ctx.fillRect(x, y, step, step);
    }
  }
}

/* ---------- Reset ---------- */
function reset() {
  data.length = 0;
  ctx.clearRect(0,0,SIZE,SIZE);
}`,
			},
		},
	},
};
