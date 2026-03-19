import { LANGUAGES, DEFAULT_LANG, SUPPORTED_LANGS } from './config.mjs'

export { LANGUAGES, DEFAULT_LANG, SUPPORTED_LANGS }

export function getLangFromUrl(url) {
	const [, lang] = url.pathname.split('/')
	if (SUPPORTED_LANGS.includes(lang)) return lang
	return DEFAULT_LANG
}

export function getCanonicalPath(path) {
	if (path.startsWith('/en')) {
		return path.replace(/^\/en/, '') || '/'
	}
	return path || '/'
}

export function getAlternatePath(lang, path) {
	const langConfig = LANGUAGES[lang]
	if (!langConfig) return path
	const canonical = getCanonicalPath(path)
	return langConfig.prefix + canonical
}

export function getOtherLang(lang) {
	return SUPPORTED_LANGS.find((l) => l !== lang) || DEFAULT_LANG
}

export function getLangDataPath(lang) {
	return `../data/${lang}`
}
