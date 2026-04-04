export const prerender = false

import type { APIRoute } from 'astro'
import { env } from 'cloudflare:workers'
import { getArticleViews, incrementArticleViews } from '../../../lib/views'

export const GET: APIRoute = async ({ params }) => {
	const slug = params.slug
	if (!slug) {
		return new Response(JSON.stringify({ error: 'Slug required' }), {
			status: 400,
			headers: { 'Content-Type': 'application/json' },
		})
	}

	const views = await getArticleViews(slug, env as any)
	return new Response(JSON.stringify({ slug, views }), {
		status: 200,
		headers: { 'Content-Type': 'application/json' },
	})
}

export const POST: APIRoute = async ({ params, cookies }) => {
	const slug = params.slug
	if (!slug) {
		return new Response(JSON.stringify({ error: 'Slug required' }), {
			status: 400,
			headers: { 'Content-Type': 'application/json' },
		})
	}

	const cookieName = `view_tracked_${slug}`
	const existingCookie = cookies.get(cookieName)

	if (existingCookie) {
		const views = await getArticleViews(slug, env as any)
		return new Response(JSON.stringify({ slug, views }), {
			status: 200,
			headers: { 'Content-Type': 'application/json' },
		})
	}

	const views = await incrementArticleViews(slug, env as any)

	cookies.set(cookieName, '1', {
		path: '/',
		maxAge: 60 * 60 * 24 * 30,
		httpOnly: false,
		sameSite: 'lax',
	})

	return new Response(JSON.stringify({ slug, views }), {
		status: 200,
		headers: { 'Content-Type': 'application/json' },
	})
}
