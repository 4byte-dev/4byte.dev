import { ArticleCard } from "@Article/Components/Card/ArticleCard";
import { DraftCard } from "@Article/Components/Card/DraftCard";
import { CommentCard } from "@React/Components/Card/CommentCard";
import { CourseCard } from "@Course//Components/Cards/CourseCard";
import { EntryCard } from "@Entry/Components/Card/EntryCard";
import { UserCard } from "@User/Components/Card/UserCard";

export function ContentCard({ ...props }) {
	const { type } = props;

	switch (type) {
		case "article":
			return <ArticleCard {...props} />;
		case "draft":
			return <DraftCard {...props} />;
		case "entry":
			return <EntryCard {...props} />;
		case "comment":
			return <CommentCard {...props} />;
		case "user":
			return <UserCard {...props} />;
		case "course":
			return <CourseCard {...props} />;
		default:
			return null;
	}
}
