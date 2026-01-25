import { useEffect, useRef, useState } from "react";
import {
	Calendar,
	Share2,
	Bookmark,
	Edit,
	Tag,
	Hash,
	ThumbsUp,
	ThumbsDown,
	Check,
	Clock,
} from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { Button } from "@/Components/Ui/Form/Button";
import { Badge } from "@/Components/Ui/Badge";
import { Separator } from "@/Components/Ui/Separator";
import { UserProfileHover } from "@/Components/Common/UserProfileHover";
import { Link } from "@inertiajs/react";
import MarkdownRenderer from "@/Components/Common/MarkdownRenderer";
import Feed from "@/Components/Content/Feed";
import { useAuthStore } from "@/Stores/AuthStore";
import { Card, CardContent } from "@/Components/Ui/Card";
import { Trans, useTranslation } from "react-i18next";
import { Comments } from "@React/Components/Comments";
import { useMutation } from "@tanstack/react-query";
import ReactApi from "@React/Api";
import TableOfContents from "@Article/Components/TableOfContents";
import { calculateReadingTime } from "@Article/Utils";

export default function ArticlePage({ article }) {
	const [isLiked, setIsLiked] = useState(article.isLiked);
	const [isDisliked, setIsDisliked] = useState(article.isDisliked);
	const [likes, setLikes] = useState(Number(article.likes));
	const [dislikes, setDislikes] = useState(Number(article.dislikes));
	const [isSaved, setIsSaved] = useState(article.isSaved);

	const [isCopied, setIsCopied] = useState(false);
	const [isFeedVisible, setIsFeedVisible] = useState(false);
	const [isFeedLoading, setIsFeedLoading] = useState(false);
	const [isCommentsVisible, setIsCommentsVisible] = useState(false);
	const feedTriggerRef = useRef(null);
	const commentsTriggerRef = useRef(null);
	const authStore = useAuthStore();
	const { t } = useTranslation();

	const likeMutation = useMutation({
		mutationFn: () => ReactApi.like({ type: "article", slug: article.slug }),
		onMutate: async () => {
			const previousState = {
				isLiked,
				likes,
				isDisliked,
				dislikes,
			};

			setIsLiked((prev) => {
				if (prev) {
					setLikes((l) => l - 1);
					return false;
				}

				if (isDisliked) {
					setIsDisliked(false);
					setDislikes((d) => d - 1);
				}

				setLikes((l) => l + 1);
				return true;
			});

			return { previousState };
		},
		onError: (err, newTodo, context) => {
			if (context?.previousState) {
				setIsLiked(context.previousState.isLiked);
				setLikes(context.previousState.likes);
				setIsDisliked(context.previousState.isDisliked);
				setDislikes(context.previousState.dislikes);
			}
		},
	});

	const handleLike = () => {
		if (!authStore.isAuthenticated) return;
		likeMutation.mutate();
	};

	const dislikeMutation = useMutation({
		mutationFn: () => ReactApi.dislike({ type: "article", slug: article.slug }),
		onMutate: async () => {
			const previousState = {
				isLiked,
				likes,
				isDisliked,
				dislikes,
			};

			setIsDisliked((disliked) => {
				const willDislike = !disliked;

				if (willDislike) {
					if (isLiked) {
						setIsLiked(false);
						setLikes((l) => l - 1);
					}

					setDislikes((d) => d + 1);
				} else {
					setDislikes((d) => d - 1);
				}

				return willDislike;
			});

			return { previousState };
		},
		onError: (err, newTodo, context) => {
			if (context?.previousState) {
				setIsLiked(context.previousState.isLiked);
				setLikes(context.previousState.likes);
				setIsDisliked(context.previousState.isDisliked);
				setDislikes(context.previousState.dislikes);
			}
		},
	});

	const handleDislike = () => {
		if (!authStore.isAuthenticated) return;
		dislikeMutation.mutate();
	};

	const saveMutation = useMutation({
		mutationFn: () => ReactApi.save({ type: "article", slug: article.slug }),
		onMutate: async () => {
			const previousState = { isSaved };
			setIsSaved(!isSaved);
			return { previousState };
		},
		onError: (err, newTodo, context) => {
			if (context?.previousState) {
				setIsSaved(context.previousState.isSaved);
			}
		},
	});

	const handleSave = () => {
		if (!authStore.isAuthenticated) return;
		saveMutation.mutate();
	};

	const handleShare = () => {
		if (navigator.share) {
			navigator.share({
				url: route("article.view", { slug: article.slug }),
			});
		} else {
			navigator.clipboard.writeText(route("article.view", { slug: article.slug }));
			setIsCopied(true);
			setTimeout(() => {
				setIsCopied(false);
			}, 1500);
		}
	};

	useEffect(() => {
		if (!isCommentsVisible) return;

		const observer = new IntersectionObserver(
			([entry]) => {
				if (entry.isIntersecting) {
					setIsFeedVisible(true);
					setIsFeedLoading(false);
					observer.disconnect();
				}
			},
			{
				rootMargin: "50px",
			},
		);

		if (feedTriggerRef.current) {
			setIsFeedLoading(true);
			observer.observe(feedTriggerRef.current);
		}

		return () => {
			observer.disconnect();
		};
	}, [isCommentsVisible]);

	useEffect(() => {
		const observer = new IntersectionObserver(
			([intersect]) => {
				if (intersect.isIntersecting) {
					setIsCommentsVisible(true);
				}
			},
			{
				rootMargin: "50px",
			},
		);

		if (commentsTriggerRef.current) {
			observer.observe(commentsTriggerRef.current);
		}

		return () => {
			observer.disconnect();
		};
	}, []);

	return (
		<div className="container mx-auto px-4 py-8">
			<div className="max-w-4xl mx-auto relative">
				<article className="w-full">
					<div className="mb-8">
						<div className="flex items-center space-x-2 text-sm text-muted-foreground mb-4">
							{article.categories.slice(0, 3).map((category) => (
								<Link
									key={category.slug}
									href={route("category.view", { slug: category.slug })}
								>
									<Badge
										key={category.slug}
										variant="outline"
										className="text-xs p-1 px-2"
									>
										<Tag className="h-4 w-4 mr-1" />
										{category.name}
									</Badge>
								</Link>
							))}
							{article.tags.slice(0, 3).map((tag) => (
								<Link key={tag.slug} href={route("tag.view", { slug: tag.slug })}>
									<Badge variant="outline" className="text-xs p-1 px-2">
										<Hash className="h-4 w-4 mr-1" />
										{tag.name}
									</Badge>
								</Link>
							))}
						</div>

						<h1 className="text-4xl font-bold mb-6">{article.title}</h1>

						<div className="flex items-center sm:justify-between flex-col sm:flex-row gap-3">
							<div className="flex items-center space-x-4">
								<UserProfileHover username={article.user.username}>
									<div className="flex items-center space-x-3 cursor-pointer">
										<Avatar className="h-12 w-12">
											<AvatarImage
												src={article.user.avatar}
												alt={article.user.name}
											/>
											<AvatarFallback>
												{article.user.name
													.split(" ")
													.map((n) => n[0])
													.join("")}
											</AvatarFallback>
										</Avatar>
										<div>
											<p className="font-medium">{article.user.name}</p>
											<p className="text-sm text-muted-foreground">
												@{article.user.username}
											</p>
										</div>
									</div>
								</UserProfileHover>

								<div className="flex flex-col justify-center text-sm text-muted-foreground">
									<div className="flex items-center space-x-1">
										<Calendar className="h-4 w-4" />
										<span>
											{new Date(article.published_at).toLocaleDateString()}
										</span>
									</div>

									<div className="flex items-center space-x-1">
										<Clock className="h-4 w-4" />{" "}
										<span>
											<Trans
												i18nKey="read_time"
												values={{
													minute: calculateReadingTime(article.content),
												}}
											/>
										</span>
									</div>
								</div>
							</div>

							<div className="flex items-center space-x-2">
								{article.canUpdate && (
									<Button variant="outline" asChild size="sm">
										<Link
											className="flex"
											href={route("article.edit", {
												article: article.slug,
											})}
										>
											<Edit className="h-4 w-4" />
										</Link>
									</Button>
								)}
								<Button
									variant={isLiked ? "default" : "outline"}
									size="sm"
									disabled={!authStore.isAuthenticated}
									onClick={handleLike}
								>
									<ThumbsUp
										className={`h-4 w-4 mr-1 ${isLiked ? "fill-current" : ""}`}
									/>
									{likes}
								</Button>
								<Button
									variant={isDisliked ? "default" : "outline"}
									size="sm"
									disabled={!authStore.isAuthenticated}
									onClick={handleDislike}
								>
									<ThumbsDown
										className={`h-4 w-4 mr-1 ${isDisliked ? "fill-current" : ""}`}
									/>
									{dislikes}
								</Button>
								<Button
									variant={isSaved ? "default" : "outline"}
									size="sm"
									disabled={!authStore.isAuthenticated}
									onClick={handleSave}
								>
									<Bookmark
										className={`h-4 w-4 ${isSaved ? "fill-current" : ""}`}
									/>
								</Button>
								<Button variant="outline" size="sm" onClick={handleShare}>
									{isCopied ? (
										<Check className="h-4 w-4" />
									) : (
										<Share2 className="h-4 w-4" />
									)}
								</Button>
							</div>
						</div>
					</div>

					<Separator className="mb-8" />

					<MarkdownRenderer content={article.content} />

					{article.sources && article.sources.length > 0 && (
						<div className="mt-8">
							<h3 className="text-xl font-semibold mb-4 flex items-center">
								<Tag className="h-5 w-5 mr-2" />
								{t("Sources")}
							</h3>

							<Card>
								<CardContent className="p-4 space-y-4">
									{article.sources.map((source, index) => (
										<div
											key={index}
											className="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b last:border-0 pb-3 last:pb-0"
										>
											<div className="flex items-center space-x-3">
												<Bookmark className="h-5 w-5 text-muted-foreground" />
												<a
													href={source.url}
													target="_blank"
													rel="noopener noreferrer"
													className="text-primary hover:underline break-all"
												>
													{source.url}
												</a>
											</div>
											<div className="flex items-center text-sm text-muted-foreground mt-2 sm:mt-0">
												<Calendar className="h-4 w-4 mr-1" />
												{new Date(source.date).toLocaleDateString()}
											</div>
										</div>
									))}
								</CardContent>
							</Card>
						</div>
					)}

					{(!article.sources || article.sources.length == 0) && (
						<Separator className="mb-4 mt-8" />
					)}
				</article>
				<aside className="hidden xl:block absolute left-full top-0 h-full ml-12">
					<div className="sticky top-24 self-start w-[200px]">
						<TableOfContents markdown={article.content} />
					</div>
				</aside>
			</div>
			<div className="max-w-4xl mx-auto relative">
				<div ref={commentsTriggerRef} className="h-10"></div>

				{isCommentsVisible && (
					<Comments
						commentsCounts={article.comments}
						type="article"
						slug={article.slug}
					/>
				)}
			</div>
			<div className="mt-12">
				{isFeedLoading && (
					<div className="flex justify-center py-8">
						<div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
					</div>
				)}

				<div ref={feedTriggerRef} className="h-10"></div>

				{isFeedVisible && (
					<Feed hasNavigation hasSidebar filters={{ article: article.slug }} />
				)}
			</div>
		</div>
	);
}
