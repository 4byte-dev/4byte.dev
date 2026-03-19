import { LANGUAGES, DEFAULT_LANG, SUPPORTED_LANGS, getAlternatePath } from '../helper.mjs'

let labelsCache = {
	tr: null as Record<string, unknown> | null,
	en: null as Record<string, unknown> | null,
}

export async function loadLabels(lang: string): Promise<Record<string, unknown>> {
	const normalizedLang = SUPPORTED_LANGS.includes(lang) ? lang : DEFAULT_LANG
	if (labelsCache[normalizedLang as keyof typeof labelsCache]) {
		return labelsCache[normalizedLang as keyof typeof labelsCache]!
	}
	try {
		const labels = await import(`../data/${normalizedLang}/labels.json`)
		labelsCache[normalizedLang as keyof typeof labelsCache] = labels.default as Record<string, unknown>
		return labelsCache[normalizedLang as keyof typeof labelsCache]!
	} catch {
		return {}
	}
}

export function t(labels: Record<string, unknown>, path: string): string {
	const keys = path.split('.')
	let value: unknown = labels
	for (const key of keys) {
		if (value && typeof value === 'object' && key in value) {
			value = (value as Record<string, unknown>)[key]
		} else {
			return path
		}
	}
	return typeof value === 'string' ? value : path
}

export function getLangFromUrl(url: URL): string {
	const [, lang] = url.pathname.split('/')
	if (SUPPORTED_LANGS.includes(lang)) return lang
	return DEFAULT_LANG
}

export function getOtherLang(currentLang: string): string {
	return SUPPORTED_LANGS.find((l) => l !== currentLang) || DEFAULT_LANG
}

export function getHreflangLinks(currentPath: string, currentLang: string): Array<{ lang: string; href: string }> {
	const links: Array<{ lang: string; href: string }> = []
	for (const lang of SUPPORTED_LANGS) {
		const href = getAlternatePath(lang, currentPath)
		links.push({ lang: LANGUAGES[lang as keyof typeof LANGUAGES].code, href })
	}
	return links
}

export { LANGUAGES, DEFAULT_LANG, SUPPORTED_LANGS }

export function formatDate(date: string | Date): string {
	if (!date) return ''
	const d = typeof date === 'string' ? new Date(date) : date
	if (isNaN(d.getTime())) return ''
	return d.toISOString().split('T')[0]
}
