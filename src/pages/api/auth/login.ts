export const prerender = false

import type { APIRoute } from 'astro'
import { env } from 'cloudflare:workers'

export const GET: APIRoute = async ({ redirect }) => {
	const clientId = env.GITHUB_CLIENT_ID
	const clientSecret = env.GITHUB_CLIENT_SECRET
	const callbackUrl = env.GITHUB_CALLBACK_URL

	if (!clientId || !clientSecret || !callbackUrl) {
		return new Response('GitHub OAuth not configured', { status: 500 })
	}

	const state = crypto.randomUUID()
	const scope = 'read:user,repo'

	const githubAuthUrl = new URL('https://github.com/login/oauth/authorize')
	githubAuthUrl.searchParams.set('client_id', clientId)
	githubAuthUrl.searchParams.set('redirect_uri', callbackUrl)
	githubAuthUrl.searchParams.set('scope', scope)
	githubAuthUrl.searchParams.set('state', state)

	return redirect(githubAuthUrl.toString(), 302)
}
