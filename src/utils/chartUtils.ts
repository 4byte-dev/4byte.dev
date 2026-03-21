export interface DataPoint {
	label: string
	value: number
	color?: string
}

export interface ChartThemeColors {
	textColor: string
	gridColor: string
	axisColor: string
}

export function getThemeColors(): ChartThemeColors {
	const isDark = document.documentElement.classList.contains('dark')

	return {
		textColor: isDark ? '#e5e7eb' : '#1e293b',
		gridColor: isDark ? '#3f3f46' : '#cbd5e1',
		axisColor: isDark ? '#52525b' : '#94a3b8',
	}
}

export function getDefaultColors(): string[] {
	return ['#3b82f6', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316']
}

export function getColor(index: number, colors?: string[], color?: string): string {
	if (color) return color
	return (colors || getDefaultColors())[index % (colors?.length || 8)]
}

export function createTooltip(container: HTMLElement): HTMLElement {
	const tooltip = document.createElement('div')
	tooltip.className = 'chart-tooltip'
	tooltip.style.cssText = `
		position: absolute;
		pointer-events: none;
		background: oklch(0.15 0 0);
		color: oklch(0.95 0 0 0);
		padding: 8px 12px;
		border-radius: 6px;
		font-size: 13px;
		font-weight: 500;
		opacity: 0;
		transition: opacity 0.15s ease;
		box-shadow: 0 4px 12px rgba(0,0,0,0.3);
		z-index: 1000;
		white-space: nowrap;
	`
	container.style.position = 'relative'
	container.appendChild(tooltip)
	return tooltip
}

export function showTooltip(tooltip: HTMLElement, x: number, y: number, content: string, containerRect: DOMRect) {
	tooltip.innerHTML = content
	tooltip.style.opacity = '1'

	const tooltipRect = tooltip.getBoundingClientRect()
	let left = x + 12
	let top = y - 12

	if (left + tooltipRect.width > containerRect.width) {
		left = x - tooltipRect.width - 12
	}
	if (top < 0) {
		top = y + 12
	}

	tooltip.style.left = `${left}px`
	tooltip.style.top = `${top}px`
}

export function hideTooltip(tooltip: HTMLElement) {
	tooltip.style.opacity = '0'
}
