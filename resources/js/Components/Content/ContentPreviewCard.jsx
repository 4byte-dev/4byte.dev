import { ArticlePreview } from "@Article/Components/Preview/ArticlePreview";
import { UserPreview } from "@User/Components/Preview/UserPreview";
import { TagPreview } from "@Tag/Components/Preview/TagPreview";
import { CategoryPreview } from "@Category/Components/Preview/CategoryPreview";
import { CoursePreview } from "@Course/Components/Preview/CoursePreview";

export function ContentPreviewCard({ item }) {
	const { type } = item;

	switch (type) {
		case "article":
			return <ArticlePreview {...item} />;
		case "user":
			return <UserPreview {...item} />;
		case "tag":
			return <TagPreview {...item} />;
		case "category":
			return <CategoryPreview {...item} />;
		case "course":
			return <CoursePreview {...item} />;

		default:
			break;
	}
}
