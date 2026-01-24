import { useState } from "react";
import { useTranslation } from "react-i18next";
import { router } from "@inertiajs/react";
import { useMutation } from "@tanstack/react-query";
import ArticleApi from "@Article/Api";
import ArticleForm from "../../Components/Form/ArticleForm";

export default function EditArticlePage({ topTags, topCategories, article, slug }) {
	const { t } = useTranslation();
	const [apiErrors, setApiErrors] = useState(null);

	const updateMutation = useMutation({
		mutationFn: (data) => {
			const payload = {
				title: data.title,
				excerpt: data.excerpt,
				content: data.content,
				published: data.published,
			};

			if (data.image) {
				payload.image = data.image;
			}

			if (data.categories && data.categories.length > 0) {
				data.categories.forEach((cat, index) => {
					payload[`categories[${index}]`] = cat;
				});
			}
			if (data.tags && data.tags.length > 0) {
				data.tags.forEach((tag, index) => {
					payload[`tags[${index}]`] = tag;
				});
			}
			if (data.sources && data.sources.length > 0) {
				data.sources.forEach((source, index) => {
					payload[`sources[${index}][url]`] = source.url;
					payload[`sources[${index}][date]`] = source.date;
				});
			}
			if (data.content_images && Object.keys(data.content_images).length > 0) {
				Object.entries(data.content_images).forEach(([key, value]) => {
					payload[`content_images[${key}]`] = value;
				});
			}

			return ArticleApi.editArticle(slug, payload);
		},
		onSuccess: (response) => {
			if (response.published) {
				router.visit(route("article.view", { slug: response.slug }), {
					method: "get",
				});
			} else {
				if (response.slug !== slug) {
					router.visit(route("article.edit", { article: response.slug }), {
						method: "get",
					});
				}
			}
		},
		onError: (error) => {
			if (error?.errors) {
				setApiErrors(error.errors);
			} else {
				setApiErrors({ title: [t("Invalid credentials. Please try again.")] });
			}
		},
	});

	const onSubmit = (data) => {
		setApiErrors(null);
		updateMutation.mutate(data);
	};

	return (
		<div className="container mx-auto px-4 py-8">
			<ArticleForm
				initialValues={article}
				topTags={topTags}
				topCategories={topCategories}
				onSubmit={onSubmit}
				isSubmitting={updateMutation.isPending}
				apiErrors={apiErrors}
				mode="edit"
			/>
		</div>
	);
}
