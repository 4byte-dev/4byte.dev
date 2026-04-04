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
	const { subjectId, body: commentBody } = body

	if (!subjectId || !commentBody) {
		return new Response(JSON.stringify({ error: 'Missing required fields' }), { status: 400 })
	}

	const query = `
		mutation($subjectId: ID!, $body: String!) {
			addDiscussionComment(input: {discussionId: $subjectId, body: $body}) {
				comment {
					id
					author {
						login
						avatarUrl
						url
					}
					createdAt
					bodyHTML
					reactionGroups {
						content
						viewerHasReacted
						users { totalCount }
					}
					replies(first: 0) {
						nodes { id }
					}
				}
			}
		}
	`

	const replyQuery = `
		mutation($subjectId: ID!, $body: String!) {
			addDiscussionReply(input: {replyToId: $subjectId, body: $body}) {
				reply {
					id
					author {
						login
						avatarUrl
						url
					}
					createdAt
					bodyHTML
					reactionGroups {
						content
						viewerHasReacted
						users { totalCount }
					}
				}
			}
		}
	`

	const isReply = body.isReply === true
	const targetQuery = isReply ? replyQuery : query

	try {
		const response = await fetch(GITHUB_GRAPHQL_API, {
			method: 'POST',
			headers: {
				Authorization: `Bearer ${tokens.access_token}`,
				'Content-Type': 'application/json',
				'User-Agent': '4byte-dev',
			},
			body: JSON.stringify({
				query: targetQuery,
				variables: { subjectId, body: commentBody },
			}),
		})

		const data = await response.json()

		if (data.errors) {
			console.error('GraphQL mutation errors:', data.errors)
			return new Response(JSON.stringify({ error: 'Failed to post comment' }), { status: 400 })
		}

		const newComment = isReply ? data.data.addDiscussionReply.reply : data.data.addDiscussionComment.comment

		return new Response(JSON.stringify({ comment: newComment }), {
			status: 200,
			headers: { 'Content-Type': 'application/json' },
		})
	} catch (error) {
		console.error('API Error:', error)
		return new Response(JSON.stringify({ error: 'Internal server error' }), { status: 500 })
	}
}
