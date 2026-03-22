export function onHydration(init: () => void): void {
	init()
	document.addEventListener('astro:after-swap', init)
}
