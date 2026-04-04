import { useState, useEffect } from 'react'
import type { Comment } from './types'
import CommentItem from './CommentItem'

export interface DiscussionProps {
	slug: string
}

export default function Discussion({ slug }: DiscussionProps) {
	const [comments, setComments] = useState<Comment[]>([])
	const [appData, setAppData] = useState<any>(null)
	const [newComment, setNewComment] = useState('')
	const [loading, setLoading] = useState(true)
	const [posting, setPosting] = useState(false)
	const [loggedIn, setLoggedIn] = useState(false)

	useEffect(() => {
		fetch('/api/auth/me')
			.then(res => res.json())
			.then(data => {
				setLoggedIn(!!data.user)
			})
			.catch(() => setLoggedIn(false))

		fetch(`/api/discussions/${slug}`)
			.then(res => res.json())
			.then(data => {
				if (data.discussion) {
					setAppData(data.discussion)
					setComments(data.discussion.comments.nodes)
				}
			})
			.finally(() => setLoading(false))
	}, [slug])

	const submitComment = async () => {
		if (!newComment.trim() || !appData) return

		setPosting(true)
		try {
			const res = await fetch('/api/discussions/comment', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ subjectId: appData.id, body: newComment })
			})
			const data = await res.json()
			if (data.comment) {
				setComments([...comments, data.comment])
				setNewComment('')
			}
		} catch (error) {
			console.error('Failed to post comment', error)
		} finally {
			setPosting(false)
		}
	}

	const totalComments = comments.length + comments.reduce((acc, c) => acc + (c.replies?.nodes.length || 0), 0)

	if (loading) {
		return <div className="mt-16 pt-8 text-center text-muted-foreground dark:text-muted-dark-foreground">Loading discussion...</div>
	}

	return (
		<div className="mt-16 pt-8 border-t border-border dark:border-border-dark" id="discussion">
			<div className="flex items-center justify-between mb-6">
				<h2 className="text-2xl font-bold text-foreground dark:text-foreground-dark flex items-center gap-2">
					Discussion
					<span className="bg-muted dark:bg-muted-dark text-muted-foreground dark:text-muted-dark-foreground text-sm py-0.5 px-2.5 rounded-full font-medium">
						{totalComments}
					</span>
				</h2>
				{!loggedIn && (
					<a href="/api/auth/login" className="text-sm text-primary dark:text-primary-dark hover:underline font-medium">
						Sign in with GitHub
					</a>
				)}
			</div>

			<div className="bg-card dark:bg-card-dark rounded-lg mb-8 border border-border dark:border-border-dark overflow-hidden flex flex-col">
				<div className="bg-muted/30 dark:bg-muted-dark/30 p-2 border-b border-border dark:border-border-dark flex items-center justify-between">
					<div className="flex space-x-2">
						<button className="px-3 py-1.5 text-sm font-medium border-b-2 border-primary dark:border-primary-dark text-foreground dark:text-foreground-dark">Write</button>
					</div>
				</div>
				<div className="p-3 bg-muted/10 dark:bg-muted-dark/10">
					<textarea
						disabled={!loggedIn}
						value={newComment}
						onChange={(e) => setNewComment(e.target.value)}
						className="w-full bg-background dark:bg-background-dark border border-border dark:border-border-dark rounded-md p-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary dark:focus:ring-primary-dark min-h-[100px] mb-3 text-foreground dark:text-foreground-dark disabled:opacity-50"
						placeholder={loggedIn ? "Leave a comment..." : "Sign in to leave a comment"}
					/>
					<div className="flex justify-between items-center">
						<div className="text-xs text-muted-foreground dark:text-muted-dark-foreground">
							Styling with Markdown is supported
						</div>
						<button
							onClick={submitComment}
							disabled={!loggedIn || !newComment.trim() || posting}
							className="bg-primary hover:bg-primary/90 dark:bg-primary-dark dark:hover:bg-primary-dark/90 text-primary-foreground dark:text-primary-dark-foreground px-4 py-2 rounded-md font-medium text-sm transition-colors cursor-pointer disabled:opacity-50"
						>
							{posting ? 'Posting...' : 'Comment'}
						</button>
					</div>
				</div>
			</div>

			<div className="space-y-4">
				{!appData ? (
					<div className="text-center py-12 border border-dashed border-border dark:border-border-dark rounded-lg text-muted-foreground dark:text-muted-dark-foreground">
						Discussion not found.
					</div>
				) : comments.length === 0 ? (
					<div className="text-center py-12 border border-dashed border-border dark:border-border-dark rounded-lg text-muted-foreground dark:text-muted-dark-foreground">
						No comments yet. Be the first to start the discussion!
					</div>
				) : (
					<div className="flex flex-col">
						{comments.map(comment => (
							<CommentItem key={comment.id} comment={comment} loggedIn={loggedIn} />
						))}
					</div>
				)}
			</div>
		</div>
	)
}
