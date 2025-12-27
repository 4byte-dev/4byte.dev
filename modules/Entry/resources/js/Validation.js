import * as z from "zod";

export const createEntrySchema = (t) =>
	z
		.object({
			content: z.string().default(""),
			media: z.array(z.any()).default([]),
		})
		.superRefine((data, ctx) => {
			const hasMedia = data.media.length > 0;
			const contentLen = data.content.trim().length;

			if (!hasMedia) {
				if (contentLen < 50) {
					ctx.addIssue({
						code: z.ZodIssueCode.custom,
						message: t("Content must be at least 50 characters"),
						path: ["content"],
					});
				}
				if (contentLen > 350) {
					ctx.addIssue({
						code: z.ZodIssueCode.custom,
						message: t("Content must be at most 350 characters"),
						path: ["content"],
					});
				}
			}

			if (data.media.length > 10) {
				ctx.addIssue({
					code: z.ZodIssueCode.custom,
					message: t("You can upload up to 10 media files"),
					path: ["media"],
				});
			}

			if (contentLen === 0 && !hasMedia) {
				ctx.addIssue({
					code: z.ZodIssueCode.custom,
					message: t("You must provide either content or at least one media"),
					path: ["content"],
				});
			}
		});
