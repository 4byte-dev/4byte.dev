import * as THREE from 'three'
import { OrbitControls } from 'three/addons/controls/OrbitControls.js'

export interface Vec3 {
	x: number
	y: number
	z: number
}

export interface ThemeColors {
	textColor: number
	gridColor: number
	axisColor: number
	bgCss: string
	darkText: string
	lightText: string
}

export function getThemeColors3D(): ThemeColors {
	const isDark = document.documentElement.classList.contains('dark')
	return {
		textColor: isDark ? 0xd4d4d8 : 0x52525b,
		gridColor: isDark ? 0x3f3f46 : 0xdde1e7,
		axisColor: isDark ? 0x71717a : 0x94a3b8,
		bgCss: isDark ? '#0c0c0e' : '#f8fafc',
		darkText: isDark ? '#e4e4e7' : '#27272a',
		lightText: isDark ? '#a1a1aa' : '#71717a',
	}
}

export function makeLabel(
	text: string,
	position: THREE.Vector3,
	colorHex: number,
	scaleW = 0.28,
	scaleH = 0.1,
	fontPx = 28,
): THREE.Sprite {
	const canvas = document.createElement('canvas')
	canvas.width = 256
	canvas.height = 64
	const ctx = canvas.getContext('2d')!
	ctx.clearRect(0, 0, 256, 64)
	ctx.fillStyle = `#${colorHex.toString(16).padStart(6, '0')}`
	ctx.font = `${fontPx}px monospace`
	ctx.textAlign = 'center'
	ctx.textBaseline = 'middle'
	ctx.fillText(text, 128, 32)
	const texture = new THREE.CanvasTexture(canvas)
	texture.needsUpdate = true
	const mat = new THREE.SpriteMaterial({ map: texture, transparent: true, depthTest: false })
	const sprite = new THREE.Sprite(mat)
	sprite.position.copy(position)
	sprite.scale.set(scaleW, scaleH, 1)
	return sprite
}

export function createBaseScene(): THREE.Scene {
	const scene = new THREE.Scene()
	scene.add(new THREE.AmbientLight(0xffffff, 0.7))
	const dLight = new THREE.DirectionalLight(0xffffff, 0.9)
	dLight.position.set(3, 5, 3)
	scene.add(dLight)
	const dLight2 = new THREE.DirectionalLight(0xffffff, 0.25)
	dLight2.position.set(-2, -1, -2)
	scene.add(dLight2)
	return scene
}

export function createCamera(
	width: number,
	height: number,
	position: THREE.Vector3,
	target: THREE.Vector3,
): THREE.PerspectiveCamera {
	const camera = new THREE.PerspectiveCamera(40, width / height, 0.01, 200)
	camera.position.copy(position)
	camera.lookAt(target)
	return camera
}

export function createRenderer(container: HTMLElement, width: number, height: number): THREE.WebGLRenderer {
	const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true })
	renderer.setSize(width, height)
	renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))
	renderer.setClearColor(0x000000, 0)
	renderer.domElement.style.cssText = 'position:absolute;top:0;left:0;z-index:1;border-radius:8px;'
	container.style.position = 'relative'
	container.appendChild(renderer.domElement)
	return renderer
}

export function createControls(
	camera: THREE.PerspectiveCamera,
	element: HTMLElement,
	target: THREE.Vector3,
	cubeSize: number,
): OrbitControls {
	const controls = new OrbitControls(camera, element)
	controls.enableDamping = true
	controls.dampingFactor = 0.06
	controls.rotateSpeed = 0.7
	controls.zoomSpeed = 1.2
	controls.minDistance = cubeSize * 0.5
	controls.maxDistance = cubeSize * 8
	controls.target.copy(target)
	controls.update()
	return controls
}

export function createAxisArrows(
	cube: number,
	theme: ThemeColors,
	scene: THREE.Scene,
	labels: { x: string; y: string; z: string },
	tickLabels: { x: string[]; y: string[]; z: string[] },
): void {
	const arrowLen = cube + 0.15
	const headLen = 0.08
	const headWid = 0.04

	const xDir = new THREE.Vector3(1, 0, 0)
	const yDir = new THREE.Vector3(0, 1, 0)
	const zDir = new THREE.Vector3(0, 0, 1)

	scene.add(new THREE.ArrowHelper(xDir, new THREE.Vector3(0, 0, 0), arrowLen, theme.axisColor, headLen, headWid))
	scene.add(new THREE.ArrowHelper(yDir, new THREE.Vector3(0, 0, 0), arrowLen, theme.axisColor, headLen, headWid))
	scene.add(new THREE.ArrowHelper(zDir, new THREE.Vector3(0, 0, 0), arrowLen, theme.axisColor, headLen, headWid))

	const tickOffset = cube * 0.02

	scene.add(
		makeLabel(
			labels.x,
			new THREE.Vector3(cube / 2, -tickOffset * 2.5, cube + tickOffset * 2),
			theme.textColor,
			0.3,
			0.1,
			30,
		),
	)
	scene.add(
		makeLabel(
			labels.y,
			new THREE.Vector3(-tickOffset * 3, cube / 2, cube + tickOffset * 2),
			theme.textColor,
			0.3,
			0.1,
			30,
		),
	)
	scene.add(
		makeLabel(
			labels.z,
			new THREE.Vector3(-tickOffset * 3, -tickOffset * 2.5, cube / 2),
			theme.textColor,
			0.3,
			0.1,
			30,
		),
	)

	for (let i = 0; i < tickLabels.x.length; i++) {
		const t = i / (tickLabels.x.length - 1)
		const pos = t * cube
		scene.add(
			makeLabel(
				tickLabels.x[i],
				new THREE.Vector3(pos, -tickOffset, cube + tickOffset),
				theme.textColor,
				0.2,
				0.08,
				24,
			),
		)
	}
	for (let i = 0; i < tickLabels.y.length; i++) {
		const t = i / (tickLabels.y.length - 1)
		const pos = t * cube
		scene.add(
			makeLabel(
				tickLabels.y[i],
				new THREE.Vector3(-tickOffset * 1.5, pos, cube + tickOffset),
				theme.textColor,
				0.2,
				0.08,
				24,
			),
		)
	}
	for (let i = 0; i < tickLabels.z.length; i++) {
		const t = i / (tickLabels.z.length - 1)
		const pos = t * cube
		scene.add(
			makeLabel(
				tickLabels.z[i],
				new THREE.Vector3(-tickOffset * 1.5, -tickOffset, pos),
				theme.textColor,
				0.2,
				0.08,
				24,
			),
		)
	}
}

export function createGridLines(cube: number, theme: ThemeColors, scene: THREE.Scene, divisions = 6): void {
	function makeFaceGrid(origin: THREE.Vector3, uDir: THREE.Vector3, vDir: THREE.Vector3): void {
		const points: THREE.Vector3[] = []
		for (let i = 0; i <= divisions; i++) {
			const t = (i / divisions) * cube
			points.push(origin.clone().addScaledVector(vDir, t))
			points.push(origin.clone().addScaledVector(uDir, cube).addScaledVector(vDir, t))
			points.push(origin.clone().addScaledVector(uDir, t))
			points.push(origin.clone().addScaledVector(uDir, t).addScaledVector(vDir, cube))
		}
		const geo = new THREE.BufferGeometry().setFromPoints(points)
		const mat = new THREE.LineBasicMaterial({ color: theme.gridColor, transparent: true, opacity: 0.35 })
		scene.add(new THREE.LineSegments(geo, mat))
	}

	makeFaceGrid(new THREE.Vector3(0, 0, 0), new THREE.Vector3(1, 0, 0), new THREE.Vector3(0, 0, 1))
	makeFaceGrid(new THREE.Vector3(0, 0, 0), new THREE.Vector3(1, 0, 0), new THREE.Vector3(0, 1, 0))
	makeFaceGrid(new THREE.Vector3(0, 0, 0), new THREE.Vector3(0, 0, 1), new THREE.Vector3(0, 1, 0))
}

export function renderPoint(
	scene: THREE.Scene,
	position: THREE.Vector3,
	color: number,
	size = 8,
	opacity = 1,
): THREE.Mesh {
	const geo = new THREE.SphereGeometry(size * 0.005, 20, 20)
	const mat = new THREE.MeshStandardMaterial({
		color,
		transparent: opacity < 1,
		opacity,
		roughness: 0.35,
		metalness: 0.15,
	})
	const mesh = new THREE.Mesh(geo, mat)
	mesh.position.copy(position)
	scene.add(mesh)
	return mesh
}

export function renderVector(
	scene: THREE.Scene,
	origin: THREE.Vector3,
	direction: THREE.Vector3,
	color: number,
	length?: number,
): THREE.ArrowHelper {
	const len = length ?? direction.length()
	const arrow = new THREE.ArrowHelper(direction.clone().normalize(), origin, len, color, len * 0.15, len * 0.06)
	scene.add(arrow)
	return arrow
}

export function renderLine(
	scene: THREE.Scene,
	from: THREE.Vector3,
	to: THREE.Vector3,
	color: number,
	dashed = false,
): THREE.Line | THREE.LineSegments {
	if (dashed) {
		const geo = new THREE.BufferGeometry().setFromPoints([from, to])
		const mat = new THREE.LineDashedMaterial({
			color,
			dashSize: 0.05,
			gapSize: 0.03,
			transparent: true,
			opacity: 0.8,
		})
		const line = new THREE.Line(geo, mat)
		line.computeLineDistances()
		scene.add(line)
		return line
	}
	const geo = new THREE.BufferGeometry().setFromPoints([from, to])
	const mat = new THREE.LineBasicMaterial({ color, transparent: true, opacity: 0.8 })
	const line = new THREE.Line(geo, mat)
	scene.add(line)
	return line
}

export function renderPlane(
	scene: THREE.Scene,
	center: THREE.Vector3,
	normal: THREE.Vector3,
	size: number,
	color: number,
	opacity = 0.35,
): THREE.Mesh {
	const geo = new THREE.PlaneGeometry(size, size)
	const mat = new THREE.MeshStandardMaterial({
		color,
		transparent: true,
		opacity,
		side: THREE.DoubleSide,
		roughness: 0.6,
		metalness: 0.1,
	})
	const mesh = new THREE.Mesh(geo, mat)
	mesh.position.copy(center)

	const up = new THREE.Vector3(0, 1, 0)
	const norm = normal.clone().normalize()
	if (Math.abs(norm.dot(up)) > 0.999) {
		mesh.rotation.x = norm.y > 0 ? -Math.PI / 2 : Math.PI / 2
	} else {
		mesh.lookAt(center.clone().add(norm))
	}
	scene.add(mesh)
	return mesh
}

export function projectPointOnPlane(
	point: THREE.Vector3,
	planeNormal: THREE.Vector3,
	planePoint: THREE.Vector3,
): THREE.Vector3 {
	const n = planeNormal.clone().normalize()
	const diff = point.clone().sub(planePoint)
	const dist = diff.dot(n)
	return point.clone().sub(n.clone().multiplyScalar(dist))
}

export function computeResidual(
	point: THREE.Vector3,
	planeNormal: THREE.Vector3,
	planePoint: THREE.Vector3,
): { from: THREE.Vector3; to: THREE.Vector3 } {
	return {
		from: point.clone(),
		to: projectPointOnPlane(point, planeNormal, planePoint),
	}
}

export function setupZoomControls(
	container: HTMLElement,
	camera: THREE.PerspectiveCamera,
	controls: OrbitControls,
	defaultEye: THREE.Vector3,
	center: THREE.Vector3,
): void {
	const duration = 300
	const zoomFactor = 0.75

	container.querySelectorAll('.chart-btn').forEach((btn) => {
		btn.addEventListener('click', () => {
			if ((btn as HTMLElement).classList.contains('zoom-in')) {
				const targetPos = camera.position
					.clone()
					.sub(controls.target)
					.normalize()
					.multiplyScalar(camera.position.distanceTo(controls.target) * zoomFactor)
					.add(controls.target)
				const startPos = camera.position.clone()
				const zoomStart = performance.now()
				function zoomInAnim() {
					const p = Math.min((performance.now() - zoomStart) / duration, 1)
					const e = 1 - Math.pow(1 - p, 3)
					camera.position.lerpVectors(startPos, targetPos, e)
					controls.update()
					if (p < 1) requestAnimationFrame(zoomInAnim)
				}
				zoomInAnim()
			} else if ((btn as HTMLElement).classList.contains('zoom-out')) {
				const targetPos = camera.position
					.clone()
					.sub(controls.target)
					.normalize()
					.multiplyScalar(camera.position.distanceTo(controls.target) / zoomFactor)
					.add(controls.target)
				const startPos = camera.position.clone()
				const zoomStart = performance.now()
				function zoomOutAnim() {
					const p = Math.min((performance.now() - zoomStart) / duration, 1)
					const e = 1 - Math.pow(1 - p, 3)
					camera.position.lerpVectors(startPos, targetPos, e)
					controls.update()
					if (p < 1) requestAnimationFrame(zoomOutAnim)
				}
				zoomOutAnim()
			} else {
				const resetStart = performance.now()
				const fromPos = camera.position.clone()
				const toPos = defaultEye.clone()
				function resetAnim() {
					const p = Math.min((performance.now() - resetStart) / 400, 1)
					const e = 1 - Math.pow(1 - p, 3)
					camera.position.lerpVectors(fromPos, toPos, e)
					controls.target.copy(center)
					controls.update()
					if (p < 1) requestAnimationFrame(resetAnim)
				}
				resetAnim()
			}
		})
	})
}

export function createControlsHTML(): string {
	return `<div class="chart-controls mt-9" style="display: flex; gap: 8px; justify-content: flex-end; position: relative; z-index: 10;">
		<button class="chart-btn zoom-in" title="Zoom In" style="background: oklch(0.9 0 0); border: 1px solid oklch(0.85 0 0); border-radius: 4px; padding: 4px 8px; cursor: pointer; font-size: 12px; color: oklch(0.2 0 0);">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
		</button>
		<button class="chart-btn zoom-out" title="Zoom Out" style="background: oklch(0.9 0 0); border: 1px solid oklch(0.85 0 0); border-radius: 4px; padding: 4px 8px; cursor: pointer; font-size: 12px; color: oklch(0.2 0 0);">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
		</button>
		<button class="chart-btn reset" title="Reset View" style="background: oklch(0.9 0 0); border: 1px solid oklch(0.85 0 0); border-radius: 4px; padding: 4px 8px; cursor: pointer; font-size: 12px; color: oklch(0.2 0 0);">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
		</button>
	</div>`
}
