import { defineMiddleware } from 'astro/middleware'
import { verifyToken, getCookieName, type SessionUser } from './lib/auth'

export const onRequest = defineMiddleware(async (context, next) => {
	const cookieName = getCookieName()
	const token = context.cookies.get(cookieName)?.value
	const env = context.locals.runtime?.env

	if (token && env) {
		const user = await verifyToken(token, env)
		if (user) {
			context.locals.user = user
		}
	}

	return next()
})

declare global {
	namespace App {
		interface Locals {
			user: SessionUser | null
		}
	}
}
