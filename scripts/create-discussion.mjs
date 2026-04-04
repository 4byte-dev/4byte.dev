import * as fs from 'fs'
import * as path from 'path'
import { fileURLToPath } from 'url'
import matter from 'gray-matter'
import { SITE } from '../src/config.mjs'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

const GITHUB_GRAPHQL_API = 'https://api.github.com/graphql'

const OWNER = process.env.GITHUB_REPO_OWNER
const REPO_NAME = process.env.GITHUB_REPO_NAME
const TOKEN = process.env.GITHUB_TOKEN

if (!OWNER || !REPO_NAME || !TOKEN) {
	console.error('Missing required environment variables: GITHUB_REPO_OWNER, GITHUB_REPO_NAME, GITHUB_TOKEN')
	process.exit(1)
}

async function githubGraphQL(query, variables = {}) {
	const response = await fetch(GITHUB_GRAPHQL_API, {
		method: 'POST',
		headers: {
			Authorization: `Bearer ${TOKEN}`,
			'Content-Type': 'application/json',
			'User-Agent': '4byte-dev'
		},
		body: JSON.stringify({ query, variables })
	})
	const data = await response.json()
	if (data.errors) {
		console.error('GraphQL errors:', JSON.stringify(data.errors, null, 2))
		throw new Error('GraphQL query failed')
	}
	return data.data
}

async function getRepositoryAndCategory() {
	const query = `
		query($owner: String!, $name: String!) {
			repository(owner: $owner, name: $name) {
				id
				discussionCategories(first: 10) {
					nodes {
						id
						name
						slug
					}
				}
			}
		}
	`
	const data = await githubGraphQL(query, { owner: OWNER, name: REPO_NAME })
	if (!data.repository) throw new Error('Repository not found')

	const categoryName = SITE.discussionCategory || 'General'
	const category = data.repository.discussionCategories.nodes.find(
		c => c.name === categoryName || c.name.toLowerCase() === categoryName.toLowerCase() || c.slug === categoryName.toLowerCase().replace(/\s+/g, '-')
	) || data.repository.discussionCategories.nodes[0]

	return {
		repositoryId: data.repository.id,
		categoryId: category.id
	}
}

async function getExistingDiscussions() {
	const query = `
		query($searchQuery: String!) {
			search(query: $searchQuery, type: DISCUSSION, first: 100) {
				nodes {
					... on Discussion {
						title
					}
				}
			}
		}
	`
	const searchQuery = `repo:${OWNER}/${REPO_NAME} type:discussion`
	const data = await githubGraphQL(query, { searchQuery })
	return data.search.nodes.map(node => node.title)
}

async function createDiscussion(repositoryId, categoryId, title, body) {
	const query = `
		mutation($repositoryId: ID!, $categoryId: ID!, $title: String!, $body: String!) {
			createDiscussion(input: {
				repositoryId: $repositoryId,
				categoryId: $categoryId,
				title: $title,
				body: $body
			}) {
				discussion {
					id
					url
				}
			}
		}
	`
	await githubGraphQL(query, { repositoryId, categoryId, title, body })
}

async function main() {
	try {
		console.log('Fetching repository and discussion categories...')
		const { repositoryId, categoryId } = await getRepositoryAndCategory()

		console.log('Fetching existing discussions...')
		const existingTitles = await getExistingDiscussions()

		const articlesDir = path.join(__dirname, '../src/content/articles')

		function getFiles(dir) {
			const dirents = fs.readdirSync(dir, { withFileTypes: true });
			const files = dirents.map((dirent) => {
				const res = path.resolve(dir, dirent.name);
				return dirent.isDirectory() ? getFiles(res) : res;
			});
			return Array.prototype.concat(...files);
		}

		const files = getFiles(articlesDir)

		for (const filePath of files) {
			if (!filePath.endsWith('.mdx') && !filePath.endsWith('.md')) continue

			const content = fs.readFileSync(filePath, 'utf-8')
			const { data } = matter(content)

			if (data.status !== 'Published') continue

			const slug = data.slug || path.basename(filePath).replace(/\.mdx?$/, '')

			if (existingTitles.includes(slug)) {
				console.log(`Discussion already exists for: ${slug}`)
				continue
			}

			console.log(`Creating discussion for: ${slug}...`)
			const body = `This is the discussion thread for the article: **${data.title}**. \n\nRead it here: https://4byte.dev/articles/${slug}`
			await createDiscussion(repositoryId, categoryId, slug, body)
			console.log(`Created!`)
		}

		console.log('Done syncing discussions.')
	} catch (error) {
		console.error('Error:', error)
		process.exit(1)
	}
}

main()
