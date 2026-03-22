#!/usr/bin/env node
import { promises as fs } from 'fs'
import path from 'path'
import matter from 'gray-matter'
import { SUPPORTED_LANGS, LANGUAGES } from '../src/config.mjs'

const RESERVED_SLUGS = {
	tr: new Set(['articles', 'categories', 'tags', '404']),
	en: new Set(['en', 'en/articles', 'en/categories', 'en/tags', 'en/404']),
}

let hasErrors = false

const __dirname = new URL('.', import.meta.url).pathname
const repoRoot = path.resolve(__dirname, '..')

;(async () => {
	for (const lang of SUPPORTED_LANGS) {
		const contentDir = path.join(repoRoot, 'src/content/articles', lang)
		console.log(`Checking slugs for language: ${lang}`)

		let mdFiles
		try {
			mdFiles = (await fs.readdir(contentDir)).filter((f) => f.endsWith('.md') || f.endsWith('.mdx'))
		} catch {
			console.log(`  No content directory for language '${lang}': ${contentDir}`)
			console.log(`  Skipping.`)
			continue
		}

		const slugMap = new Map()
		const reserved = RESERVED_SLUGS[lang] || new Set()
		const langPrefix = LANGUAGES[lang]?.prefix || ''

		for (const file of mdFiles) {
			const filePath = path.join(contentDir, file)
			const raw = await fs.readFile(filePath, 'utf8')
			const { data } = matter(raw)

			const slug = data.slug?.trim()
			if (!slug) {
				console.log(`  ✗ ${file} — MISSING slug field in frontmatter`)
				hasErrors = true
				continue
			}

			if (reserved.has(slug) || (langPrefix && slug.startsWith(langPrefix.slice(1) + '/'))) {
				console.log(`  ✗ ${file} — RESERVED slug '${slug}' (conflicts with static route)`)
				hasErrors = true
				continue
			}

			if (slugMap.has(slug)) {
				const original = slugMap.get(slug)
				console.log(`  ✗ ${file} — DUPLICATE slug '${slug}' (already used by ${original})`)
				hasErrors = true
			} else {
				slugMap.set(slug, file)
				console.log(`  ✓ ${slug} — slug OK`)
			}
		}
	}

	if (hasErrors) {
		console.log('\nFound slug errors. Exiting with code 1.')
		process.exit(1)
	} else {
		console.log('\nAll slugs are valid.')
		process.exit(0)
	}
})()
