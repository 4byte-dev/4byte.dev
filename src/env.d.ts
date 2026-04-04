export interface CloudflareEnv {
	JWT_SECRET: string
	GITHUB_CLIENT_ID: string
	GITHUB_CLIENT_SECRET: string
	GITHUB_CALLBACK_URL: string
	DEV?: string
	ARTICLE_VIEWS: KVNamespace
}

export {}
