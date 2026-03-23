import { readFileSync, readdirSync } from 'fs'
import { fileURLToPath } from 'url'
import { dirname, join } from 'path'
import { SITE, LANGUAGES, DEFAULT_LANG, SUPPORTED_LANGS } from './config.mjs'

const __filename = fileURLToPath(import.meta.url)
const __dirname = dirname(__filename)

function getArticleDates() {
	const dates = {}
	for (const lang of SUPPORTED_LANGS) {
		const dir = join(__dirname, 'content/articles', lang)
		try {
			const files = readdirSync(dir).filter((f) => f.endsWith('.mdx'))
			for (const file of files) {
				const content = readFileSync(join(dir, file), 'utf-8')
				const match = content.match(/^---\n([\s\S]*?)\n---/)
				if (match) {
					const frontmatter = match[1]
					const slugMatch = frontmatter.match(/slug:\s*['"](.+?)['"]/)
					const dateMatch = frontmatter.match(/date:\s*['"](.+?)['"]/)
					if (slugMatch && dateMatch) {
						dates[slugMatch[1]] = dateMatch[1]
					}
				}
			}
		} catch {
			// directory doesn't exist or is empty
		}
	}
	return dates
}

function getCustomPages() {
	const pages = []
	for (const lang of SUPPORTED_LANGS) {
		const prefix = LANGUAGES[lang].prefix
		try {
			const categories = JSON.parse(readFileSync(join(__dirname, `data/${lang}/categories.json`), 'utf-8'))
			for (const c of categories) {
				pages.push(`${SITE.origin}${prefix}/categories/${c.slug}/`)
			}
		} catch {
			// file doesn't exist or is empty
		}
		try {
			const tags = JSON.parse(readFileSync(join(__dirname, `data/${lang}/tags.json`), 'utf-8'))
			for (const t of tags) {
				pages.push(`${SITE.origin}${prefix}/tags/${t.slug}/`)
			}
		} catch {
			// file doesn't exist or is empty
		}
	}
	return pages
}

const articleDates = getArticleDates()
const customPages = getCustomPages()

const i18n = {
	defaultLocale: DEFAULT_LANG,
	locales: Object.fromEntries(SUPPORTED_LANGS.map((lang) => [lang, LANGUAGES[lang].ogLocale.replace('_', '-')])),
}

export function getSitemapOptions() {
	return {
		filter: (page) => !page.includes('/404'),
		i18n,
		customPages,
		serialize: (item) => {
			const articleMatch = item.url.match(/\/articles\/([^/]+)\//)
			if (articleMatch) {
				const slug = articleMatch[1]
				if (articleDates[slug]) {
					item.lastmod = articleDates[slug]
				}
				item.changefreq = 'weekly'
				item.priority = 0.8
				return item
			}

			if (/\/(categories|tags)\/[^/]+\/$/.test(item.url)) {
				item.changefreq = 'monthly'
				item.priority = 0.6
				return item
			}

			item.changefreq = 'weekly'
			item.priority = 0.5
			return item
		},
	}
}
