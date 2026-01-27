import { useState, useEffect } from "react";
import * as React from "react";
import { ArrowLeft, Save, Upload, X, Plus, Settings, PanelRight } from "lucide-react"; // Yeni ikonlar eklendi
import { Button } from "@/Components/Ui/Form/Button";
import { Input } from "@/Components/Ui/Form/Input";
import { Label } from "@/Components/Ui/Form/Label";
import { Card, CardContent } from "@/Components/Ui/Card";
import { Switch } from "@/Components/Ui/Form/Switch";
import { useTranslation } from "react-i18next";
import { MultiSelect } from "@/Components/Ui/Form/MultiSelect";
import { useFieldArray, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { createArticleSchema } from "@Article/Validation";
import {
	Form,
	FormControl,
	FormDescription,
	FormField,
	FormItem,
	FormLabel,
	FormMessage,
} from "@/Components/Ui/Form/Form";
import { FormTextareaInput } from "@/Components/Ui/Form/FormTextareaInput";
import { FormMarkdownInput } from "@/Components/Ui/Form/FormMarkdownInput";
import { useQueryClient } from "@tanstack/react-query";
import CategoryApi from "@Category/Api";
import TagApi from "@Tag/Api";
import clsx from "clsx";
import { stripMarkdown } from "@Article/Utils";

export default function ArticleForm({
	initialValues,
	topTags,
	topCategories,
	onSubmit,
	isSubmitting,
	mode = "create",
	apiErrors = null,
}) {
	const { t } = useTranslation();
	const [imagePreview, setImagePreview] = useState(
		initialValues?.image ? initialValues.image : "",
	);
	const [newSourceUrl, setNewSourceUrl] = useState("");
	const [isSidebarOpen, setIsSidebarOpen] = useState(false);
	const pendingImages = React.useRef(new Map());
	const [isExcerptTouched, setIsExcerptTouched] = useState(
		!!initialValues?.excerpt && initialValues.excerpt.length > 0,
	);
	const hasExistingImage = !!initialValues?.image;

	const form = useForm({
		resolver: zodResolver(createArticleSchema(t, hasExistingImage)),
		defaultValues: {
			title: initialValues?.title || "",
			excerpt: initialValues?.excerpt || "",
			content: initialValues?.content || "",
			categories: initialValues?.categories || [],
			tags: initialValues?.tags || [],
			published: initialValues?.published || false,
			image: undefined,
			sources: initialValues?.sources || [],
		},
	});

	const queryClient = useQueryClient();

	const contentValue = form.watch("content");

	useEffect(() => {
		if (!isExcerptTouched && contentValue) {
			const plainText = stripMarkdown(contentValue);
			let newExcerpt = plainText.substring(0, 150);
			if (plainText.length > 150) {
				newExcerpt += "...";
			}
			form.setValue("excerpt", newExcerpt, {
				shouldDirty: true,
				shouldValidate: contentValue.length > 50,
			});
		}
	}, [contentValue, isExcerptTouched, form]);

	async function asyncSearchCategories(term, { page = 1, pageSize = 15 }) {
		if (!term) return { results: [], total: 0 };
		const data = await queryClient.fetchQuery({
			queryKey: [`categories-${term}-${page}-${pageSize}`],
			queryFn: () => CategoryApi.search(term, { page, per_page: pageSize }),
			staleTime: 5 * 60 * 1000,
		});
		return {
			results: data.data.map((item) => ({ value: item.slug, label: item.name })),
			total: data.total,
		};
	}

	async function asyncSearchTags(term, { page = 1, pageSize = 15 }) {
		if (!term) return { results: [], total: 0 };
		const data = await queryClient.fetchQuery({
			queryKey: [`tags-${term}-${page}-${pageSize}`],
			queryFn: () => TagApi.search(term, { page, per_page: pageSize }),
			staleTime: 5 * 60 * 1000,
		});
		return {
			results: data.data.map((item) => ({ value: item.slug, label: item.name })),
			total: data.total,
		};
	}

	useEffect(() => {
		if (apiErrors) {
			Object.keys(apiErrors).forEach((key) => {
				form.setError(key, { message: apiErrors[key][0] });
				if (["categories", "tags", "excerpt", "image"].includes(key)) {
					setIsSidebarOpen(true);
				}
			});
		}
	}, [apiErrors, form]);

	useEffect(() => {
		const sidebarFields = ["categories", "tags", "excerpt", "image", "published", "sources"];

		const hasSidebarErrors = Object.keys(form.formState.errors).some((key) =>
			sidebarFields.includes(key),
		);

		if (hasSidebarErrors && !isSidebarOpen) {
			setIsSidebarOpen(true);
		}
	}, [form.formState.errors]);

	const { fields, append, remove } = useFieldArray({
		control: form.control,
		name: "sources",
	});

	const handleOnImageUpload = (blobUrl, file) => {
		pendingImages.current.set(blobUrl, file);
		const currentCover = form.getValues("image");
		if (!currentCover && !imagePreview) {
			form.setValue("image", file, { shouldDirty: true, shouldValidate: true });
			setImagePreview(blobUrl);
		}
	};

	const handleFormSubmit = (data) => {
		let content = data.content || "";
		const contentImages = {};
		const blobRegex = /!\[.*?\]\((blob:.*?)\)/g;
		let match;

		while ((match = blobRegex.exec(content)) !== null) {
			const blobUrl = match[1];
			if (pendingImages.current.has(blobUrl)) {
				const file = pendingImages.current.get(blobUrl);
				const placeholder = `%img_${Date.now()}_${Math.random().toString(36).substr(2, 9)}%`;
				content = content.split(blobUrl).join(placeholder);
				contentImages[placeholder] = file;
			}
		}
		const finalData = { ...data, content };
		finalData.content_images = contentImages;
		onSubmit(finalData);
	};

	const handleAddSource = () => {
		if (!newSourceUrl) return;
		append({
			url: newSourceUrl,
			date: new Date().toISOString().split("T")[0],
		});
		setNewSourceUrl("");
	};

	return (
		<Form form={form} onSubmit={handleFormSubmit}>
			<div className="relative flex min-h-screen flex-col bg-background">
				<header className="sticky top-0 z-20 flex h-16 items-center justify-between border-b bg-background/95 px-6 backdrop-blur supports-[backdrop-filter]:bg-background/60">
					<div className="flex items-center gap-4">
						<Button
							variant="ghost"
							size="icon"
							onClick={() => window.history.back()}
							type="button"
						>
							<ArrowLeft className="h-5 w-5" />
						</Button>
						<div className="hidden md:block text-sm text-muted-foreground">
							{mode === "create"
								? form.watch("published")
									? t("Publishing new article")
									: t("Drafting new article")
								: t("Editing article")}
						</div>
					</div>

					<div className="flex items-center gap-2">
						<Button
							variant="outline"
							size="sm"
							type="button"
							onClick={() => setIsSidebarOpen(true)}
							className={isSidebarOpen ? "bg-accent" : ""}
						>
							<PanelRight className="h-4 w-4 mr-2" />
							{t("Details")}
						</Button>

						<Button type="submit" disabled={isSubmitting} size="sm">
							<Save className="h-4 w-4 mr-2" />
							{isSubmitting ? t("Saving...") : t("Publish")}
						</Button>
					</div>
				</header>

				<main className="flex-1 w-full max-w-6xl mx-auto py-10 px-6">
					<div className="space-y-6">
						<FormField
							control={form.control}
							name="title"
							render={({ field }) => (
								<FormItem>
									<FormControl>
										<input
											{...field}
											placeholder={t("Article Title")}
											className="w-full text-center bg-transparent text-4xl font-bold placeholder:text-muted-foreground/50 focus:outline-none border-none p-0"
											autoComplete="off"
										/>
									</FormControl>
									<FormMessage />
								</FormItem>
							)}
						/>

						<FormField
							control={form.control}
							name="content"
							render={({ field }) => (
								<FormMarkdownInput
									onImageUpload={handleOnImageUpload}
									field={field}
									className="min-h-[500px] border-none shadow-none focus-within:ring-0 px-0"
								/>
							)}
						/>
					</div>
				</main>

				{isSidebarOpen && (
					<div
						className="fixed inset-0 z-40 bg-background/80 backdrop-blur-sm"
						onClick={() => setIsSidebarOpen(false)}
					/>
				)}

				<div
					className={clsx(
						"fixed inset-y-0 right-0 z-50 w-full md:w-[450px] bg-background border-l shadow-2xl transition-transform duration-300 ease-in-out transform overflow-y-auto",
						isSidebarOpen ? "translate-x-0" : "translate-x-full",
					)}
				>
					<div className="flex flex-col h-full">
						<div className="flex items-center justify-between p-6 border-b">
							<h2 className="text-lg font-semibold flex items-center gap-2">
								<Settings className="w-5 h-5" />
								{t("Article Settings")}
							</h2>
							<Button
								variant="ghost"
								size="icon"
								onClick={() => setIsSidebarOpen(false)}
								type="button"
							>
								<X className="h-5 w-5" />
							</Button>
						</div>

						<div className="flex-1 p-6 space-y-8">
							<div className="space-y-4">
								<h3 className="text-sm font-medium text-muted-foreground uppercase tracking-wider">
									{t("Status")}
								</h3>
								<FormField
									control={form.control}
									name="published"
									render={({ field }) => (
										<Card className="border-dashed">
											<CardContent className="pt-6 flex items-center justify-between">
												<div className="space-y-0.5">
													<FormLabel>
														{t("Publish immediately")}
													</FormLabel>
													<FormDescription className="text-xs">
														{field.value
															? t("Public to everyone")
															: t("Draft mode")}
													</FormDescription>
												</div>
												<FormControl>
													<Switch
														checked={field.value}
														onCheckedChange={field.onChange}
													/>
												</FormControl>
											</CardContent>
										</Card>
									)}
								/>
							</div>

							<div className="space-y-4">
								<h3 className="text-sm font-medium text-muted-foreground uppercase tracking-wider">
									{t("Metadata")}
								</h3>
								<FormField
									control={form.control}
									name="excerpt"
									render={({ field }) => (
										<FormTextareaInput
											label={t("Excerpt")}
											placeholder={t(
												"Write a short description for SEO and cards...",
											)}
											field={{
												...field,
												onChange: (e) => {
													setIsExcerptTouched(true);
													field.onChange(e);
												},
											}}
											className="resize-none h-24"
										/>
									)}
								/>
							</div>

							<div className="space-y-4">
								<h3 className="text-sm font-medium text-muted-foreground uppercase tracking-wider">
									{t("Cover Image")}
								</h3>
								<FormField
									control={form.control}
									name="image"
									// eslint-disable-next-line no-unused-vars
									render={({ field: { value, onChange, ...fieldProps } }) => (
										<FormItem>
											<FormControl>
												<div className="group relative border-2 border-dashed border-muted-foreground/25 rounded-lg overflow-hidden hover:border-muted-foreground/50 transition-colors">
													{imagePreview ? (
														<div className="relative h-48 w-full">
															<img
																src={imagePreview}
																alt="Preview"
																className="w-full h-full object-cover"
															/>
															<div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
																<Label
																	htmlFor="sidebar-cover-input"
																	className="cursor-pointer text-white flex items-center gap-2 hover:underline"
																>
																	<Upload className="w-4 h-4" />{" "}
																	{t("Change")}
																</Label>
															</div>
														</div>
													) : (
														<div className="h-32 flex flex-col items-center justify-center text-center p-4">
															<Upload className="h-8 w-8 text-muted-foreground mb-2" />
															<p className="text-sm text-muted-foreground">
																{t("Upload cover image")}
															</p>
															<Label
																htmlFor="sidebar-cover-input"
																className="absolute inset-0 cursor-pointer"
															/>
														</div>
													)}

													<Input
														{...fieldProps}
														id="sidebar-cover-input"
														type="file"
														accept="image/*"
														className="hidden"
														onChange={(event) => {
															const file =
																event.target.files &&
																event.target.files[0];
															if (file) {
																onChange(file);
																setImagePreview(
																	URL.createObjectURL(file),
																);
															}
														}}
													/>
												</div>
											</FormControl>
											<FormMessage />
										</FormItem>
									)}
								/>
							</div>

							<div className="space-y-6">
								<h3 className="text-sm font-medium text-muted-foreground uppercase tracking-wider">
									{t("Taxonomy")}
								</h3>

								<FormField
									control={form.control}
									name="categories"
									render={({ field }) => (
										<FormItem>
											<FormLabel>{t("Categories")}</FormLabel>
											<FormControl>
												<MultiSelect
													options={topCategories.map((c) => ({
														label: c.name,
														value: c.slug,
													}))}
													onValueChange={field.onChange}
													defaultValue={field.value}
													placeholder={t("Select categories")}
													asyncSearch={asyncSearchCategories}
												/>
											</FormControl>
											<FormMessage />
										</FormItem>
									)}
								/>

								<FormField
									control={form.control}
									name="tags"
									render={({ field }) => (
										<FormItem>
											<FormLabel>{t("Tags")}</FormLabel>
											<FormControl>
												<MultiSelect
													options={topTags.map((c) => ({
														label: c.name,
														value: c.slug,
													}))}
													onValueChange={field.onChange}
													defaultValue={field.value}
													placeholder={t("Select tags")}
													asyncSearch={asyncSearchTags}
												/>
											</FormControl>
											<FormMessage />
										</FormItem>
									)}
								/>
							</div>

							<div className="space-y-4">
								<h3 className="text-sm font-medium text-muted-foreground uppercase tracking-wider">
									{t("Sources")}
								</h3>
								<div className="space-y-3">
									{fields.map((field, index) => (
										<div
											key={index}
											className="flex items-center gap-2 p-2 bg-muted/50 rounded-md text-sm group"
										>
											<div
												className="flex-1 truncate font-mono text-xs"
												title={field.url}
											>
												{field.url}
											</div>
											<Button
												variant="ghost"
												size="icon"
												onClick={() => remove(index)}
												className="h-6 w-6 text-muted-foreground hover:text-red-500"
												type="button"
											>
												<X className="h-3 w-3" />
											</Button>
										</div>
									))}

									<div className="flex gap-2">
										<Input
											value={newSourceUrl}
											onChange={(e) => setNewSourceUrl(e.target.value)}
											onKeyDown={(e) => {
												if (e.key === "Enter") {
													e.preventDefault();
													handleAddSource();
												}
											}}
											placeholder="https://..."
											className="h-8 text-sm"
										/>
										<Button
											size="sm"
											variant="secondary"
											onClick={handleAddSource}
											type="button"
											disabled={!newSourceUrl}
											className="h-8"
										>
											<Plus className="h-3 w-3" />
										</Button>
									</div>
								</div>
							</div>
						</div>

						<div className="p-6 border-t bg-muted/10">
							<Button
								className="w-full"
								type="button"
								onClick={() => setIsSidebarOpen(false)}
							>
								{t("Done")}
							</Button>
						</div>
					</div>
				</div>
			</div>
		</Form>
	);
}
