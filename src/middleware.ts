import { defineMiddleware } from 'astro/middleware'
import { verifyToken, getCookieName, type SessionUser } from './lib/auth'

export const onRequest = defineMiddleware(async (context, next) => {
	const cookieName = getCookieName()
	const token = context.cookies.get(cookieName)?.value

	if (token) {
		const user = await verifyToken(token)
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
