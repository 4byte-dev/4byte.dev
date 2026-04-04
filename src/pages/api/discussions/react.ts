export const prerender = false

import type { APIRoute } from 'astro'
import { env } from 'cloudflare:workers'
import { verifyTokensCookie, getTokensCookieName } from '../../../lib/auth'

const GITHUB_GRAPHQL_API = 'https://api.github.com/graphql'

export const POST: APIRoute = async ({ request, cookies }) => {
	const tokensCookie = cookies.get(getTokensCookieName())
	if (!tokensCookie) {
		return new Response(JSON.stringify({ error: 'Unauthorized' }), { status: 401 })
	}

	const tokens = await verifyTokensCookie(tokensCookie.value, env as any)
	if (!tokens || !tokens.access_token) {
		return new Response(JSON.stringify({ error: 'Unauthorized' }), { status: 401 })
	}

	const body = await request.json().catch(() => ({}))
	const { subjectId, content, action } = body

	if (!subjectId || !content || !action) {
		return new Response(JSON.stringify({ error: 'Missing required fields' }), { status: 400 })
	}

	const getQuery = () => {
		if (action === 'add') {
			return `
				mutation($subjectId: ID!, $content: ReactionContent!) {
					addReaction(input: {subjectId: $subjectId, content: $content}) {
						subject { id }
					}
				}
			`
		} else {
			return `
				mutation($subjectId: ID!, $content: ReactionContent!) {
					removeReaction(input: {subjectId: $subjectId, content: $content}) {
						subject { id }
					}
				}
			`
		}
	}

	try {
		const response = await fetch(GITHUB_GRAPHQL_API, {
			method: 'POST',
			headers: {
				Authorization: `Bearer ${tokens.access_token}`,
				'Content-Type': 'application/json',
				'User-Agent': '4byte-dev',
			},
			body: JSON.stringify({
				query: getQuery(),
				variables: { subjectId, content },
			}),
		})

		const data = await response.json()

		if (data.errors) {
			console.error('GraphQL mutation errors:', data.errors)
			return new Response(JSON.stringify({ error: 'Failed to toggle reaction' }), { status: 400 })
		}

		return new Response(JSON.stringify({ success: true }), {
			status: 200,
			headers: { 'Content-Type': 'application/json' },
		})
	} catch (error) {
		console.error('API Error:', error)
		return new Response(JSON.stringify({ error: 'Internal server error' }), { status: 500 })
	}
}
