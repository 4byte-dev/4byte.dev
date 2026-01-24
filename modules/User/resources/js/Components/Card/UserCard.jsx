import { Avatar, AvatarImage, AvatarFallback } from "@/Components/Ui/Avatar";
import { useState } from "react";
import { Card, CardContent } from "@/Components/Ui/Card";
import { Trans, useTranslation } from "react-i18next";
import { useAuthStore } from "@/Stores/AuthStore";
import { Button } from "@/Components/Ui/Form/Button";
import { Settings, UserCheck, UserPlus } from "lucide-react";
import { Link } from "@inertiajs/react";
import { useMutation } from "@tanstack/react-query";
import ReactApi from "@React/Api";

export function UserCard(user) {
	const [isFollowing, setIsFollowing] = useState(false);
	const [followers, setFollowers] = useState(0);
	const authStore = useAuthStore();
	const { t } = useTranslation();

	const isOwnProfile = authStore.isAuthenticated && user.username === authStore.user.username;

	const followMutation = useMutation({
		mutationFn: () => ReactApi.follow({ type: "user", slug: user.username }),
		onMutate: () => {
			const previousState = {
				isFollowing,
				followers,
			};

			setIsFollowing(!isFollowing);
			setFollowers(isFollowing ? followers - 1 : followers + 1);

			return { previousState };
		},
		onError: (err, newTodo, context) => {
			if (context?.previousState) {
				setIsFollowing(context.previousState.isFollowing);
				setFollowers(context.previousState.followers);
			}
		},
	});

	const handleFollow = async () => {
		followMutation.mutate();
	};

	return (
		<Card className="mb-8">
			<CardContent className="px-2 py-2">
				<div className="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
					<div className="flex flex-col md:flex-row items-center md:items-center gap-1">
						<div className="bg-background rounded-full p-3 shadow-sm">
							<Avatar className="h-20 w-20">
								<AvatarImage src={user.avatar} alt={user.name} />
								<AvatarFallback className="text-2xl">
									{user.name
										.split(" ")
										.map((n) => n[0])
										.join("")}
								</AvatarFallback>
							</Avatar>
						</div>

						<div className="text-center md:text-left">
							<div className="flex flex-col md:flex-row md:items-center gap-1 md:gap-3">
								<h1 className="text-xl font-semibold">{user.name}</h1>
								<span className="text-muted-foreground text-sm">
									@{user.username}
								</span>
							</div>

							<div className="flex justify-center md:justify-start gap-6 mt-2 text-sm">
								<span>
									<Trans
										i18nKey="followers"
										values={{ count: followers.toLocaleString() }}
										components={{ strong: <strong /> }}
									/>
								</span>
								<span>
									<Trans
										i18nKey="followings"
										values={{ count: user.followings.toLocaleString() }}
										components={{ strong: <strong /> }}
									/>
								</span>
							</div>
						</div>
					</div>

					<div className="flex gap-3 justify-center md:justify-end">
						{!isOwnProfile && authStore.isAuthenticated && (
							<Button
								onClick={handleFollow}
								variant={isFollowing ? "outline" : "default"}
							>
								{isFollowing ? (
									<>
										<UserCheck className="h-4 w-4 mr-2" />
										{t("Following")}
									</>
								) : (
									<>
										<UserPlus className="h-4 w-4 mr-2" />
										{t("Follow")}
									</>
								)}
							</Button>
						)}

						<Button variant="outline" asChild>
							<Link
								className="flex items-center"
								href={route("user.view", { username: user.username })}
							>
								<Settings className="h-4 w-4 mr-2" />
								{t("View Profile")}
							</Link>
						</Button>
					</div>
				</div>
			</CardContent>
		</Card>
	);
}
