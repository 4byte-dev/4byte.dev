export const prerender = false

import type { APIRoute } from 'astro'
import { getCookieName, getTokensCookieName } from '../../../lib/auth'

export const GET: APIRoute = async ({ cookies, redirect }) => {
	cookies.delete(getCookieName(), { path: '/' })
	cookies.delete(getTokensCookieName(), { path: '/' })

	return redirect('/')
}
