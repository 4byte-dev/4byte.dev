export interface User {
	login: string
	avatarUrl: string
	url: string
}

export interface ReactionGroup {
	content: string
	users: { totalCount: number }
	viewerHasReacted: boolean
}

export interface Comment {
	id: string
	author: User
	createdAt: string
	bodyHTML: string
	reactions: {
		nodes: ReactionGroup[]
	}
	replies?: {
		nodes: Comment[]
	}
}
