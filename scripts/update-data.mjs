#!/usr/bin/env node
import { promises as fs } from 'fs'
import path from 'path'
import matter from 'gray-matter'
import slugify from 'slugify'
import { SUPPORTED_LANGS } from '../src/config.mjs'

const readJSON = async (file) => JSON.parse(await fs.readFile(file, 'utf8'))
const writeJSON = async (file, data) => await fs.writeFile(file, JSON.stringify(data, null, 2) + '\n', 'utf8')

;(async () => {
	const __dirname = new URL('.', import.meta.url).pathname
	const repoRoot = path.resolve(__dirname, '..')

	for (const lang of SUPPORTED_LANGS) {
		const contentDir = path.join(repoRoot, 'src/content/articles', lang)
		const tagsPath = path.join(repoRoot, 'src/data', lang, 'tags.json')
		const categoriesPath = path.join(repoRoot, 'src/data', lang, 'categories.json')
		const articlesPath = path.join(repoRoot, 'src/data', lang, 'articles.json')

		const existingTags = (await readJSON(tagsPath)) ?? []
		const existingCategories = (await readJSON(categoriesPath)) ?? []

		const tags = [...existingTags]
		const categories = [...existingCategories]

		const tagSlugs = new Set(tags.map((t) => t.slug))
		const categorySlugs = new Set(categories.map((c) => c.slug))

		const addedTags = new Set()
		const addedCategories = new Set()

		let mdFiles = []
		try {
			mdFiles = (await fs.readdir(contentDir)).filter((f) => f.endsWith('.md') || f.endsWith('.mdx'))
		} catch {
			console.log(`No content directory for language '${lang}': ${contentDir}`)
			console.log('Skipping article generation.')
		}

		const articles = []

		for (const file of mdFiles) {
			const filePath = path.join(contentDir, file)
			const raw = await fs.readFile(filePath, 'utf8')
			const { data } = matter(raw)

			const title = data.title?.trim() ?? path.parse(file).name
			const slug = data.slug?.trim() ?? slugify(title, { lower: true })

			const fileTags = Array.isArray(data.tags) ? data.tags : []
			for (const tagName of fileTags) {
				const tagSlug = slugify(tagName, { lower: true })
				if (!tagSlugs.has(tagSlug)) {
					tags.push({
						id: String(tags.length + 1),
						name: tagName,
						slug: tagSlug,
						articleCount: 0,
						description: '',
					})
					tagSlugs.add(tagSlug)
					addedTags.add(tagName)
				}
				const existing = tags.find((t) => t.slug === tagSlug)
				if (existing) {
					existing.articleCount = (existing.articleCount ?? 0) + 1
				}
			}

			const catName = data.category?.trim()
			if (catName) {
				const catSlug = slugify(catName, { lower: true })
				if (!categorySlugs.has(catSlug)) {
					categories.push({
						id: String(categories.length + 1),
						name: catName,
						slug: catSlug,
						description: '',
						color: '',
						articleCount: 0,
					})
					categorySlugs.add(catSlug)
					addedCategories.add(catName)
				}
				const existing = categories.find((c) => c.slug === catSlug)
				if (existing) {
					existing.articleCount = (existing.articleCount ?? 0) + 1
				}
			}

			articles.push({ title, slug, category: data.category?.trim() ?? '' })
		}

		const filteredTags = tags.filter((t) => (t.articleCount ?? 0) > 0)
		const filteredCategories = categories.filter((c) => (c.articleCount ?? 0) > 0)

		const hasChanges = addedTags.size > 0 || addedCategories.size > 0

		if (hasChanges) {
			await writeJSON(tagsPath, filteredTags)
			await writeJSON(categoriesPath, filteredCategories)
		}

		const existingArticles = (await readJSON(articlesPath)) ?? []
		const articlesChanged = JSON.stringify(articles) !== JSON.stringify(existingArticles)
		if (articlesChanged) {
			await writeJSON(articlesPath, articles)
		}

		const didWrite = hasChanges || articlesChanged

		console.log(`Updated data for language: ${lang}`)
		console.log(`  Articles: ${articles.length} ${articlesChanged ? '(changed)' : '(unchanged)'}`)
		console.log(
			`  Categories: ${filteredCategories.length} ${addedCategories.size ? `(added: ${[...addedCategories].join(', ')})` : '(unchanged)'}`,
		)
		console.log(
			`  Tags: ${filteredTags.length} ${addedTags.size ? `(added: ${[...addedTags].join(', ')})` : '(unchanged)'}`,
		)

		if (didWrite && process.env.GITHUB_EVENT_PATH) {
			const event = JSON.parse(await fs.readFile(process.env.GITHUB_EVENT_PATH, 'utf8'))
			const prNumber = event.pull_request?.number
			if (prNumber) {
				const lines = []
				if (addedTags.size) {
					lines.push(`## New tags added for ${lang} (please add descriptions)`)
					for (const t of addedTags) lines.push(`- \`${t}\``)
					lines.push('')
				}
				if (addedCategories.size) {
					lines.push(`## New categories added for ${lang} (please add descriptions)`)
					for (const c of addedCategories) lines.push(`- \`${c}\``)
					lines.push('')
				}
				if (lines.length) {
					console.log('\nComment for PR:')
					console.log(lines.join('\n'))
				}
			}
		}
	}
})()
