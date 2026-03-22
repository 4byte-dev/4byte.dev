export interface GitHubUser {
	login: string
	avatar_url: string
	html_url: string
	name: string | null
	bio: string | null
	company: string | null
	blog: string
	public_repos: number
	followers: number
	following: number
}

export async function fetchGitHubUser(username: string): Promise<GitHubUser | null> {
	const cleanUsername = username.startsWith('@') ? username.slice(1) : username

	try {
		const response = await fetch(`https://api.github.com/users/${cleanUsername}`, {
			headers: {
				Accept: 'application/vnd.github.v3+json',
			},
		})

		if (!response.ok) {
			console.error(`GitHub API error: ${response.status} for user ${cleanUsername}`)
			return null
		}

		return await response.json()
	} catch (error) {
		console.error(`Failed to fetch GitHub user ${cleanUsername}:`, error)
		return null
	}
}
