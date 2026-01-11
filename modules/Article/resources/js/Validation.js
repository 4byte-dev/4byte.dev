import * as z from "zod";

export const createArticleSchema = (t) => {
	const baseSchema = z.object({
		title: z
			.string()
			.trim()
			.min(1, t("Title is required"))
			.min(10, t("Title must be at least 10 characters")),
		sources: z.array(
			z.object({
				url: z.url(),
				date: z.string().refine((val) => !isNaN(Date.parse(val))),
			}),
		),
	});

	const draftSchema = baseSchema.extend({
		published: z.literal(false),
		excerpt: z.string().trim().default(""),
		content: z.string().trim().default(""),
		categories: z.array(z.string()).default([]),
		tags: z.array(z.string()).default([]),
		image: z.any().nullable().optional(),
	});

	const publishedSchema = baseSchema.extend({
		published: z.literal(true),
		excerpt: z
			.string()
			.trim()
			.min(1, t("Excerpt is required"))
			.min(100, t("Excerpt must be at least 100 characters")),
		content: z
			.string()
			.trim()
			.min(1, t("Content is required"))
			.min(500, t("Content must be at least 500 characters")),
		categories: z
			.array(z.string())
			.min(1, t("Select at least 1 category"))
			.max(3, t("You can select up to 3 categories")),
		tags: z
			.array(z.string())
			.min(1, t("Select at least 1 tag"))
			.max(3, t("You can select up to 3 tags")),
		image: z.any().refine((val) => !!val, t("Cover image is required")),
	});

	return z.preprocess(
		(data) => {
			if (
				typeof data === "object" &&
				data !== null &&
				data.published === undefined
			) {
				return { ...data, published: false };
			}
			return data;
		},
		z.discriminatedUnion("published", [draftSchema, publishedSchema]),
	);
};
