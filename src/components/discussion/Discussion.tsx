import React, { useState, useEffect } from 'react'
import type { Comment, DiscussionData } from './types'
import { t } from '../../i18n/index.ts'
import CommentItem from './CommentItem'

export interface DiscussionProps {
	slug: string
	labels: any
}

const REACTION_EMOJIS: Record<string, string> = {
	THUMBS_UP: '👍',
	THUMBS_DOWN: '👎',
	LAUGH: '😄',
	HOORAY: '🎉',
	CONFUSED: '😕',
	HEART: '❤️',
	ROCKET: '🚀',
	EYES: '👀',
}

export default function Discussion({ slug, labels }: DiscussionProps) {
	const [comments, setComments] = useState<Comment[]>([])
	const [appData, setAppData] = useState<DiscussionData | null>(null)
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

	const toggleUpvote = async () => {
		if (!loggedIn || !appData) return
		const action = appData.viewerHasUpvoted ? 'remove' : 'add'

		setAppData({
			...appData,
			viewerHasUpvoted: !appData.viewerHasUpvoted,
			upvoteCount: appData.upvoteCount + (action === 'add' ? 1 : -1)
		})

		try {
			await fetch('/api/discussions/upvote', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ subjectId: appData.id, action })
			})
		} catch (error) { }
	}

	const toggleReaction = async (content: string, viewerHasReacted: boolean) => {
		if (!loggedIn || !appData) return
		const action = viewerHasReacted ? 'remove' : 'add'

		const newReactions = appData.reactionGroups?.map(r => {
			if (r.content === content) {
				return {
					...r,
					viewerHasReacted: !viewerHasReacted,
					users: { totalCount: r.users.totalCount + (viewerHasReacted ? -1 : 1) }
				}
			}
			return r
		}) || []

		const didExist = newReactions.find(r => r.content === content)
		if (!didExist && action === 'add') {
			newReactions.push({ content, viewerHasReacted: true, users: { totalCount: 1 } })
		}

		setAppData({ ...appData, reactionGroups: newReactions })

		try {
			await fetch('/api/discussions/react', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ subjectId: appData.id, content, action })
			})
		} catch (error) { }
	}

	const totalComments = comments.length + comments.reduce((acc, c) => acc + (c.replies?.nodes.length || 0), 0)

	if (loading) {
		return <div className="mt-16 pt-8 text-center text-muted-foreground dark:text-muted-dark-foreground">{t(labels, 'discussion.loading')}</div>
	}

	return (
		<div className="mt-16 pt-8 border-t border-border dark:border-border-dark" id="discussion">
			<div className="flex items-center justify-between mb-6">
				<h2 className="text-2xl font-bold text-foreground dark:text-foreground-dark flex items-center gap-2">
					{t(labels, 'discussion.title')}
					<span className="bg-muted dark:bg-muted-dark text-muted-foreground dark:text-muted-dark-foreground text-sm py-0.5 px-2.5 rounded-full font-medium">
						{totalComments}
					</span>
				</h2>
				{!loggedIn && (
					<a href="/api/auth/login" className="text-sm text-primary dark:text-primary-dark hover:underline font-medium">
						{t(labels, 'discussion.signIn')}
					</a>
				)}
			</div>

			{appData && (
				<div className="flex flex-wrap gap-2 items-center mb-6">
					<button
						onClick={toggleUpvote}
						disabled={!loggedIn}
						className={`flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium border transition-colors ${appData.viewerHasUpvoted
							? 'bg-primary/10 border-primary/30 text-primary dark:bg-primary-dark/10 dark:border-primary-dark/30 dark:text-primary-dark'
							: 'bg-transparent border-border dark:border-border-dark text-muted-foreground dark:text-muted-dark-foreground hover:bg-muted dark:hover:bg-muted-dark'
							}`}
					>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m18 15-6-6-6 6" /></svg>
						{appData.upvoteCount || 0} {t(labels, 'discussion.upvotes')}
					</button>

					<div className="w-px h-6 bg-border dark:bg-border-dark mx-2"></div>

					{Object.keys(REACTION_EMOJIS).map(content => {
						const reaction = appData.reactionGroups?.find(r => r.content === content)
						const count = reaction?.users.totalCount || 0
						const userReacted = reaction?.viewerHasReacted || false

						if (count === 0 && !loggedIn) return null

						return (
							<button
								key={content}
								onClick={() => toggleReaction(content, userReacted)}
								disabled={!loggedIn}
								className={`flex items-center gap-1.5 px-2 py-1 rounded-full text-sm font-medium border transition-colors ${userReacted
									? 'bg-primary/10 border-primary/30 text-primary dark:bg-primary-dark/10 dark:border-primary-dark/30 dark:text-primary-dark'
									: 'bg-transparent border-border dark:border-border-dark text-muted-foreground dark:text-muted-dark-foreground hover:bg-muted dark:hover:bg-muted-dark'
									} ${count === 0 && 'opacity-50'}`}
							>
								<span>{REACTION_EMOJIS[content]}</span>
								{count > 0 && <span>{count}</span>}
							</button>
						)
					})}
				</div>
			)}

			<div className="bg-card dark:bg-card-dark rounded-lg mb-8 border border-border dark:border-border-dark overflow-hidden flex flex-col">
				<div className="bg-muted/30 dark:bg-muted-dark/30 p-2 border-b border-border dark:border-border-dark flex items-center justify-between">
					<div className="flex space-x-2">
						<button className="px-3 py-1.5 text-sm font-medium border-b-2 border-primary dark:border-primary-dark text-foreground dark:text-foreground-dark">{t(labels, 'discussion.write')}</button>
					</div>
				</div>
				<div className="p-3 bg-muted/10 dark:bg-muted-dark/10">
					<textarea
						disabled={!loggedIn}
						value={newComment}
						onChange={(e) => setNewComment(e.target.value)}
						className="w-full bg-background dark:bg-background-dark border border-border dark:border-border-dark rounded-md p-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary dark:focus:ring-primary-dark min-h-[100px] mb-3 text-foreground dark:text-foreground-dark disabled:opacity-50"
						placeholder={loggedIn ? t(labels, 'discussion.leaveComment') : t(labels, 'discussion.signInToComment')}
					/>
					<div className="flex justify-between items-center">
						<div className="text-xs text-muted-foreground dark:text-muted-dark-foreground">
							{t(labels, 'discussion.markdownSupported')}
						</div>
						<button
							onClick={submitComment}
							disabled={!loggedIn || !newComment.trim() || posting}
							className="bg-primary hover:bg-primary/90 dark:bg-primary-dark dark:hover:bg-primary-dark/90 text-primary-foreground dark:text-primary-dark-foreground px-4 py-2 rounded-md font-medium text-sm transition-colors cursor-pointer disabled:opacity-50"
						>
							{posting ? t(labels, 'discussion.posting') : t(labels, 'discussion.comment')}
						</button>
					</div>
				</div>
			</div>

			<div className="space-y-4">
				{!appData ? (
					<div className="text-center py-12 border border-dashed border-border dark:border-border-dark rounded-lg text-muted-foreground dark:text-muted-dark-foreground">
						{t(labels, 'discussion.notFound')}
					</div>
				) : comments.length === 0 ? (
					<div className="text-center py-12 border border-dashed border-border dark:border-border-dark rounded-lg text-muted-foreground dark:text-muted-dark-foreground">
						{t(labels, 'discussion.noComments')}
					</div>
				) : (
					<div className="flex flex-col">
						{comments.map(comment => (
							<CommentItem key={comment.id} comment={comment} loggedIn={loggedIn} labels={labels} />
						))}
					</div>
				)}
			</div>
		</div>
	)
}
