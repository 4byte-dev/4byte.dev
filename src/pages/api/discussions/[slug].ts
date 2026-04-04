export const prerender = false

import type { APIRoute } from 'astro'
import { env } from 'cloudflare:workers'

const GITHUB_GRAPHQL_API = 'https://api.github.com/graphql'

export const GET: APIRoute = async ({ params }) => {
	const slug = params.slug
	if (!slug) {
		return new Response(JSON.stringify({ error: 'Slug required' }), { status: 400 })
	}

	const owner = env.GITHUB_REPO_OWNER as string
	const repoName = env.GITHUB_REPO_NAME as string
	const token = env.GITHUB_TOKEN as string

	if (!owner || !repoName) {
		return new Response(JSON.stringify({ error: 'GitHub repository not configured' }), { status: 500 })
	}

	const searchQuery = `repo:${owner}/${repoName} "${slug}" in:title type:discussion`

	const query = `
		query($searchQuery: String!, $owner: String!, $repoName: String!) {
			search(query: $searchQuery, type: DISCUSSION, first: 1) {
				nodes {
					...DiscussionFields
				}
			}
			repository(owner: $owner, name: $repoName) {
				discussions(first: 50, orderBy: {field: CREATED_AT, direction: DESC}) {
					nodes {
						...DiscussionFields
					}
				}
			}
		}

		fragment DiscussionFields on Discussion {
			id
			title
			url
			upvoteCount
			viewerHasUpvoted
			reactionGroups {
				content
				viewerHasReacted
				users {
					totalCount
				}
			}
			comments(first: 100) {
				totalCount
				nodes {
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
						users {
							totalCount
						}
					}
					replies(first: 50) {
						nodes {
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
								users {
									totalCount
								}
							}
						}
					}
				}
			}
		}
	`

	try {
		const response = await fetch(GITHUB_GRAPHQL_API, {
			method: 'POST',
			headers: {
				Authorization: `Bearer ${token}`,
				'Content-Type': 'application/json',
				'User-Agent': '4byte-dev',
			},
			body: JSON.stringify({
				query,
				variables: { searchQuery, owner, repoName },
			}),
		})

		const data = await response.json()

		if (data.errors) {
			console.error('GraphQL errors:', data.errors)
			return new Response(JSON.stringify({ error: 'Failed to fetch discussion' }), { status: 500 })
		}

		let discussion = data.data.search.nodes[0]

		if (!discussion && data.data.repository?.discussions) {
			discussion = data.data.repository.discussions.nodes.find((n: any) => n.title === slug)
		}

		return new Response(JSON.stringify({ discussion: discussion || null }), {
			status: 200,
			headers: { 'Content-Type': 'application/json' },
		})
	} catch (error) {
		console.error('API Error:', error)
		return new Response(JSON.stringify({ error: 'Internal server error' }), { status: 500 })
	}
}
