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
	reactionGroups: ReactionGroup[]
	replies?: {
		nodes: Comment[]
	}
}

export interface DiscussionData {
	id: string
	title: string
	url: string
	upvoteCount: number
	viewerHasUpvoted: boolean
	reactionGroups: ReactionGroup[]
	comments: { nodes: Comment[] }
}
