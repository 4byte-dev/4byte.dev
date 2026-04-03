import * as jose from 'jose'

const getSecret = () => {
	const secret = import.meta.env.JWT_SECRET
	if (!secret) {
		throw new Error('JWT_SECRET is not set')
	}
	return new TextEncoder().encode(secret)
}

const getTokenSecret = () => {
	const secret = import.meta.env.JWT_SECRET
	if (!secret) {
		throw new Error('JWT_SECRET is not set')
	}
	return new TextEncoder().encode(secret + '_tokens')
}

const COOKIE_NAME = 'auth_session'
const TOKEN_COOKIE_NAME = 'auth_tokens'

export interface SessionUser {
	login: string
	avatar_url: string
	name: string | null
}

export interface GitHubTokens {
	access_token: string
	refresh_token: string
	expires_at: number
	scope: string
}

export async function createToken(user: SessionUser): Promise<string> {
	const secret = getSecret()
	return await new jose.SignJWT({ ...user })
		.setProtectedHeader({ alg: 'HS256' })
		.setIssuedAt()
		.setExpirationTime('7d')
		.sign(secret)
}

export async function verifyToken(token: string): Promise<SessionUser | null> {
	try {
		const secret = getSecret()
		const { payload } = await jose.jwtVerify(token, secret)
		return payload as unknown as SessionUser
	} catch {
		return null
	}
}

export async function createTokensCookie(tokens: GitHubTokens): Promise<string> {
	const secret = getTokenSecret()
	return await new jose.SignJWT({ ...tokens })
		.setProtectedHeader({ alg: 'HS256' })
		.setIssuedAt()
		.setExpirationTime('90d')
		.sign(secret)
}

export async function verifyTokensCookie(token: string): Promise<GitHubTokens | null> {
	try {
		const secret = getTokenSecret()
		const { payload } = await jose.jwtVerify(token, secret)
		return payload as unknown as GitHubTokens
	} catch {
		return null
	}
}

export async function refreshAccessToken(refreshToken: string): Promise<GitHubTokens | null> {
	const clientId = import.meta.env.GITHUB_CLIENT_ID
	const clientSecret = import.meta.env.GITHUB_CLIENT_SECRET

	if (!clientId || !clientSecret) {
		return null
	}

	try {
		const response = await fetch('https://github.com/login/oauth/access_token', {
			method: 'POST',
			headers: {
				Accept: 'application/json',
				'Content-Type': 'application/json',
				'User-Agent': '4byte-dev',
			},
			body: JSON.stringify({
				client_id: clientId,
				client_secret: clientSecret,
				grant_type: 'refresh_token',
				refresh_token: refreshToken,
			}),
		})

		if (!response.ok) {
			return null
		}

		const data = await response.json()

		if (!data.access_token) {
			return null
		}

		return {
			access_token: data.access_token,
			refresh_token: data.refresh_token || refreshToken,
			expires_at: Date.now() + (data.expires_in || 3600) * 1000,
			scope: data.scope || '',
		}
	} catch {
		return null
	}
}

export function getCookieName(): string {
	return COOKIE_NAME
}

export function getTokensCookieName(): string {
	return TOKEN_COOKIE_NAME
}

export function getCookieOptions() {
	const isDev = import.meta.env.DEV
	return {
		httpOnly: true,
		secure: !isDev,
		sameSite: 'lax' as const,
		path: '/',
		maxAge: 60 * 60 * 24 * 7,
	}
}

export function getTokensCookieOptions() {
	const isDev = import.meta.env.DEV
	return {
		httpOnly: true,
		secure: !isDev,
		sameSite: 'lax' as const,
		path: '/',
		maxAge: 60 * 60 * 24 * 90,
	}
}
