export const prerender = false

import type { APIRoute } from 'astro'

export const GET: APIRoute = async ({ redirect, locals }) => {
	const env = locals.runtime.env
	const clientId = env.GITHUB_CLIENT_ID
	const clientSecret = env.GITHUB_CLIENT_SECRET
	const callbackUrl = env.GITHUB_CALLBACK_URL

	if (!clientId || !clientSecret || !callbackUrl) {
		return new Response('GitHub OAuth not configured', { status: 500 })
	}

	const state = crypto.randomUUID()
	const scope = 'read:user'

	const githubAuthUrl = new URL('https://github.com/login/oauth/authorize')
	githubAuthUrl.searchParams.set('client_id', clientId)
	githubAuthUrl.searchParams.set('redirect_uri', callbackUrl)
	githubAuthUrl.searchParams.set('scope', scope)
	githubAuthUrl.searchParams.set('state', state)

	return redirect(githubAuthUrl.toString(), 302)
}
