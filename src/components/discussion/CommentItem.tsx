import React, { useState } from 'react'
import type { Comment } from './types'
import { t } from '../../i18n/index.ts'

export interface CommentItemProps {
	comment: Comment
	onReply?: (commentId: string) => void
	isReply?: boolean
	loggedIn?: boolean
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

export default function CommentItem({ comment: initialComment, onReply, isReply = false, loggedIn = false, labels }: CommentItemProps) {
	const [comment, setComment] = useState(initialComment)
	const [replyBoxOpen, setReplyBoxOpen] = useState(false)
	const [replyText, setReplyText] = useState('')
	const [replying, setReplying] = useState(false)

	const hasTokens = loggedIn

	const date = new Date(comment.createdAt).toLocaleDateString('en-US', {
		year: 'numeric',
		month: 'short',
		day: 'numeric',
	})

	const submitReply = async () => {
		if (!replyText.trim() || !hasTokens) return
		setReplying(true)
		try {
			const res = await fetch('/api/discussions/comment', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ subjectId: comment.id, body: replyText, isReply: true })
			})
			const data = await res.json()
			if (data.comment) {
				setComment({
					...comment,
					replies: {
						nodes: [...(comment.replies?.nodes || []), data.comment]
					}
				})
				setReplyText('')
				setReplyBoxOpen(false)
			}
		} catch (error) {
			console.error(error)
		} finally {
			setReplying(false)
		}
	}

	const toggleReaction = async (content: string, viewerHasReacted: boolean) => {
		if (!hasTokens) return
		const action = viewerHasReacted ? 'remove' : 'add'

		// Optimistic update
		const newReactions = comment.reactionGroups?.map(r => {
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

		setComment({ ...comment, reactionGroups: newReactions })

		try {
			await fetch('/api/discussions/react', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ subjectId: comment.id, content, action })
			})
		} catch (error) {
			// Revert on error
			setComment(initialComment)
		}
	}

	return (
		<div className={`flex gap-3 relative ${isReply ? 'mt-4' : 'mt-6'}`}>
			<a href={comment.author.url} target="_blank" rel="noreferrer" className="flex-shrink-0">
				<img
					src={comment.author.avatarUrl}
					alt={comment.author.login}
					className="w-10 h-10 rounded-full border border-border dark:border-border-dark bg-muted dark:bg-muted-dark object-cover"
				/>
			</a>

			<div className="flex-1 min-w-0">
				<div className="border border-border dark:border-border-dark rounded-md bg-card dark:bg-card-dark overflow-hidden">
					<div className="bg-muted/50 dark:bg-muted-dark/50 px-4 py-2 border-b border-border dark:border-border-dark flex items-center justify-between">
						<div className="flex items-center gap-2 text-sm">
							<a href={comment.author.url} target="_blank" rel="noreferrer" className="font-semibold text-foreground dark:text-foreground-dark hover:text-primary dark:hover:text-primary-dark transition-colors">
								{comment.author.login}
							</a>
							<span className="text-muted-foreground dark:text-muted-dark-foreground text-xs">
								{t(labels, 'discussion.on')} {date}
							</span>
						</div>
					</div>
					<div className="p-4 text-foreground dark:text-foreground-dark prose prose-sm dark:prose-invert max-w-none" dangerouslySetInnerHTML={{ __html: comment.bodyHTML }} />
				</div>

				<div className="mt-2 flex items-center gap-2 flex-wrap">
					{Object.keys(REACTION_EMOJIS).map(content => {
						const reaction = comment.reactionGroups?.find(r => r.content === content)
						const count = reaction?.users.totalCount || 0
						const userReacted = reaction?.viewerHasReacted || false

						if (count === 0 && !hasTokens) return null

						return (
							<button
								key={content}
								onClick={() => toggleReaction(content, userReacted)}
								disabled={!hasTokens}
								className={`flex items-center gap-1.5 px-2 py-1 rounded-full text-xs font-medium border transition-colors ${userReacted
									? 'bg-primary/10 border-primary/30 text-primary dark:bg-primary-dark/10 dark:border-primary-dark/30 dark:text-primary-dark'
									: 'bg-transparent border-border dark:border-border-dark text-muted-foreground dark:text-muted-dark-foreground hover:bg-muted dark:hover:bg-muted-dark'
									} ${count === 0 && 'opacity-50'}`}
							>
								<span>{REACTION_EMOJIS[content]}</span>
								{count > 0 && <span>{count}</span>}
							</button>
						)
					})}

					{hasTokens && !isReply && (
						<button
							onClick={() => setReplyBoxOpen(!replyBoxOpen)}
							className="flex items-center gap-1.5 px-2 py-1 rounded-full text-xs font-medium bg-transparent text-muted-foreground dark:text-muted-dark-foreground hover:text-foreground dark:hover:text-foreground-dark transition-colors ml-auto"
						>
							<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><polyline points="9 17 4 12 9 7"></polyline><path d="M20 18v-2a4 4 0 0 0-4-4H4"></path></svg>
							{t(labels, 'discussion.reply')}
						</button>
					)}
				</div>

				{replyBoxOpen && (
					<div className="mt-4 flex gap-3">
						<div className="flex-1 min-w-0">
							<textarea
								value={replyText}
								onChange={e => setReplyText(e.target.value)}
								className="w-full bg-background dark:bg-background-dark border border-border dark:border-border-dark rounded-md p-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary dark:focus:ring-primary-dark min-h-[80px] mb-2 text-foreground dark:text-foreground-dark"
								placeholder={t(labels, 'discussion.replyTo').replace('{user}', comment.author.login)}
							/>
							<div className="flex justify-end gap-2">
								<button
									onClick={() => setReplyBoxOpen(false)}
									className="px-3 py-1.5 rounded-md font-medium text-xs text-muted-foreground hover:bg-muted dark:text-muted-dark-foreground dark:hover:bg-muted-dark transition-colors"
								>
									{t(labels, 'discussion.cancel')}
								</button>
								<button
									onClick={submitReply}
									disabled={replying || !replyText.trim()}
									className="bg-primary dark:bg-primary-dark text-primary-foreground dark:text-primary-dark-foreground px-3 py-1.5 rounded-md font-medium text-xs transition-colors hover:brightness-110 disabled:opacity-50"
								>
									{replying ? t(labels, 'discussion.replying') : t(labels, 'discussion.reply')}
								</button>
							</div>
						</div>
					</div>
				)}

				{(comment.replies?.nodes.length || 0) > 0 && (
					<div className="mt-4 pl-4 border-l-2 border-border/50 dark:border-border-dark/50">
						{comment.replies!.nodes.map((reply) => (
							<CommentItem key={reply.id} comment={reply} isReply={true} loggedIn={loggedIn} labels={labels} />
						))}
					</div>
				)}
			</div>
		</div>
	)
}
