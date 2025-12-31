import { useState } from "react";
import { Calendar, Share2, Check } from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { Button } from "@/Components/Ui/Form/Button";
import { Separator } from "@/Components/Ui/Separator";
import { UserProfileHover } from "@/Components/Common/UserProfileHover";
import MarkdownRenderer from "@/Components/Common/MarkdownRenderer";

export default function PagePage({ page }) {
	const [isCopied, setIsCopied] = useState(false);

	const handleShare = () => {
		if (navigator.share) {
			navigator.share({
				title: page.title,
				text: page.excerpt,
				url: window.location.href,
			});
		} else {
			navigator.clipboard.writeText(window.location.href);
			setIsCopied(true);
			setTimeout(() => {
				setIsCopied(false);
			}, 1500);
		}
	};

	return (
		<div className="container mx-auto px-4 py-8">
			<div className="max-w-4xl mx-auto">
				{/* Page Header */}
				<div className="mb-8">
					<h1 className="text-4xl font-bold mb-6">{page.title}</h1>

					<div className="flex items-center justify-between">
						<div className="flex items-center space-x-4">
							<UserProfileHover username={page.user.username}>
								<div className="flex items-center space-x-3 cursor-pointer">
									<Avatar className="h-12 w-12">
										<AvatarImage
											src={page.user.avatar.image}
											alt={page.user.name}
										/>
										<AvatarFallback>
											{page.user.name
												.split(" ")
												.map((n) => n[0])
												.join("")}
										</AvatarFallback>
									</Avatar>
									<div>
										<p className="font-medium">{page.user.name}</p>
										<p className="text-sm text-muted-foreground">
											@{page.user.username}
										</p>
									</div>
								</div>
							</UserProfileHover>

							<div className="flex items-center space-x-4 text-sm text-muted-foreground">
								<div className="flex items-center space-x-1">
									<Calendar className="h-4 w-4" />
									<span>{new Date(page.published_at).toLocaleDateString()}</span>
								</div>
							</div>
						</div>

						<div className="flex items-center space-x-2">
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

				<MarkdownRenderer content={page.content} />
			</div>
		</div>
	);
}
