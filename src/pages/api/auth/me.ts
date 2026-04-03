export const prerender = false

import type { APIRoute } from 'astro'
import {
	verifyToken,
	verifyTokensCookie,
	refreshAccessToken,
	getCookieName,
	getTokensCookieName,
	createTokensCookie,
	getTokensCookieOptions,
} from '../../../lib/auth'

export const GET: APIRoute = async ({ cookies }) => {
	const sessionToken = cookies.get(getCookieName())?.value
	const tokensToken = cookies.get(getTokensCookieName())?.value

	if (!sessionToken || !tokensToken) {
		return new Response(JSON.stringify({ user: null, accessToken: null }), {
			status: 200,
			headers: { 'Content-Type': 'application/json' },
		})
	}

	const user = await verifyToken(sessionToken)
	if (!user) {
		return new Response(JSON.stringify({ user: null, accessToken: null }), {
			status: 200,
			headers: { 'Content-Type': 'application/json' },
		})
	}

	let tokens = await verifyTokensCookie(tokensToken)

	if (tokens) {
		const isExpired = Date.now() >= tokens.expires_at
		const shouldRefresh = tokens.refresh_token && isExpired

		if (shouldRefresh) {
			const newTokens = await refreshAccessToken(tokens.refresh_token)
			if (newTokens) {
				tokens = newTokens
				const newTokensCookie = await createTokensCookie(newTokens)
				cookies.set(getTokensCookieName(), newTokensCookie, getTokensCookieOptions())
			}
		}
	}

	return new Response(
		JSON.stringify({
			user,
			accessToken: tokens?.access_token || null,
		}),
		{
			status: 200,
			headers: { 'Content-Type': 'application/json' },
		},
	)
}
