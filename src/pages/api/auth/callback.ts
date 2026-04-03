export const prerender = false

import type { APIRoute } from 'astro'
import {
	createToken,
	createTokensCookie,
	getCookieName,
	getTokensCookieName,
	getCookieOptions,
	getTokensCookieOptions,
	type SessionUser,
	type GitHubTokens,
} from '../../../lib/auth'

export const GET: APIRoute = async ({ url, redirect, cookies }) => {
	const clientId = import.meta.env.GITHUB_CLIENT_ID
	const clientSecret = import.meta.env.GITHUB_CLIENT_SECRET
	const callbackUrl = import.meta.env.GITHUB_CALLBACK_URL

	if (!clientId || !clientSecret || !callbackUrl) {
		return new Response('GitHub OAuth not configured', { status: 500 })
	}

	const code = url.searchParams.get('code')
	const state = url.searchParams.get('state')

	if (!code) {
		return redirect('/')
	}

	try {
		const tokenResponse = await fetch('https://github.com/login/oauth/access_token', {
			method: 'POST',
			headers: {
				Accept: 'application/json',
				'Content-Type': 'application/json',
				'User-Agent': '4byte-dev',
			},
			body: JSON.stringify({
				client_id: clientId,
				client_secret: clientSecret,
				code,
			}),
		})

		const tokenData = await tokenResponse.json()
		const accessToken = tokenData.access_token
		const refreshToken = tokenData.refresh_token

		if (!accessToken) {
			return redirect('/')
		}

		const userResponse = await fetch('https://api.github.com/user', {
			headers: {
				Authorization: `Bearer ${accessToken}`,
				Accept: 'application/vnd.github.v3+json',
				'User-Agent': '4byte-dev',
			},
		})

		const githubUser = await userResponse.json()

		const sessionUser: SessionUser = {
			login: githubUser.login,
			avatar_url: githubUser.avatar_url,
			name: githubUser.name,
		}

		const userToken = await createToken(sessionUser)
		cookies.set(getCookieName(), userToken, getCookieOptions())

		if (refreshToken) {
			const tokens: GitHubTokens = {
				access_token: accessToken,
				refresh_token: refreshToken,
				expires_at: Date.now() + (tokenData.expires_in || 3600) * 1000,
				scope: tokenData.scope || '',
			}
			const tokensCookie = await createTokensCookie(tokens)
			cookies.set(getTokensCookieName(), tokensCookie, getTokensCookieOptions())
		}

		return redirect('/')
	} catch (error) {
		console.error('OAuth callback error:', error)
		return redirect('/')
	}
}
