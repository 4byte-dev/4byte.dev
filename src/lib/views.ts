import type { CloudflareEnv } from '../env'

export async function getArticleViews(slug: string, env: CloudflareEnv): Promise<number> {
	const count = await env.ARTICLE_VIEWS.get(slug)
	return count ? parseInt(count, 10) : 0
}

export async function incrementArticleViews(slug: string, env: CloudflareEnv): Promise<number> {
	const current = await getArticleViews(slug, env)
	const newCount = current + 1
	await env.ARTICLE_VIEWS.put(slug, newCount.toString())
	return newCount
}
