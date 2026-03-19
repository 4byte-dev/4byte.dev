import { defineCollection, z } from 'astro:content'
import { glob } from 'astro/loaders'

const articles = defineCollection({
	loader: glob({ pattern: '**/*{.md,.mdx}', base: './src/content/articles' }),
	schema: z.object({
		lang: z.enum(['tr', 'en']).default('tr'),
		slug: z.string(),
		title: z.string(),
		excerpt: z.string(),
		category: z.string(),
		tags: z.array(z.string()),
		author: z.string(),
		date: z.union([z.string(), z.date()]),
		views: z.number().default(0),
		status: z.enum(['Published', 'Draft']).default('Published'),
	}),
})

export const collections = { articles }
