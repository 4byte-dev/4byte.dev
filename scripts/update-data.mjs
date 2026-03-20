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

		const tags = (await readJSON(tagsPath)) ?? []
		const categories = (await readJSON(categoriesPath)) ?? []

		tags.forEach((t) => (t.articleCount = 0))
		categories.forEach((c) => (c.articleCount = 0))

		const addedTags = new Set()
		const addedCategories = new Set()

		let mdFiles = []
		try {
			mdFiles = (await fs.readdir(contentDir)).filter((f) => f.endsWith('.md'))
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
				const existing = tags.find((t) => t.name === tagName)
				if (existing) {
					existing.articleCount = (existing.articleCount ?? 0) + 1
				} else {
					tags.push({
						id: String(tags.length + 1),
						name: tagName,
						slug: slugify(tagName, { lower: true }),
						articleCount: 1,
						description: '',
					})
					addedTags.add(tagName)
				}
			}

			const catName = data.category?.trim()
			if (catName) {
				const existing = categories.find((c) => c.name === catName)
				if (existing) {
					existing.articleCount = (existing.articleCount ?? 0) + 1
				} else {
					categories.push({
						id: String(categories.length + 1),
						name: catName,
						slug: slugify(catName, { lower: true }),
						description: '',
						color: '',
						articleCount: 1,
					})
					addedCategories.add(catName)
				}
			}

			articles.push({ title, slug, category: data.category?.trim() ?? '' })
		}

		const filteredTags = tags.filter((t) => (t.articleCount ?? 0) > 0)
		const filteredCategories = categories.filter((c) => (c.articleCount ?? 0) > 0)

		await writeJSON(tagsPath, filteredTags)
		await writeJSON(categoriesPath, filteredCategories)
		await writeJSON(articlesPath, articles)

		console.log(`Updated data for language: ${lang}`)
		console.log(`  Articles: ${articles.length}`)
		console.log(`  Categories: ${filteredCategories.length}`)
		console.log(`  Tags: ${filteredTags.length}`)

		if (process.env.GITHUB_EVENT_PATH) {
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
					const comment = lines.join('\n')
					const { execFileSync } = await import('child_process')
					execFileSync('gh', ['pr', 'comment', String(prNumber), '--body', comment])
				}
			}
		}
	}
})()
