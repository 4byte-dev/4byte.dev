import ReactApi from "@React/Api";
import { Card, CardContent } from "@/Components/Ui/Card";
import { Button } from "@/Components/Ui/Form/Button";
import { Form, FormField } from "@/Components/Ui/Form/Form";
import { FormInput } from "@/Components/Ui/Form/FormInput";
import { FormTextareaInput } from "@/Components/Ui/Form/FormTextareaInput";
import { commentSubmitSchema } from "@React/Validation";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { useForm } from "react-hook-form";
import { useTranslation } from "react-i18next";
import { useAuthStore } from "@/Stores/AuthStore";

export function CommentSubmitForm({ type, slug, parent = null, onSuccess }) {
	const { t } = useTranslation();
	const authStore = useAuthStore();

	const commentSubmitForm = useForm({
		resolver: zodResolver(commentSubmitSchema(t)),
		defaultValues: {
			content: "",
			parent,
		},
	});

	const commentSubmitMutation = useMutation({
		mutationFn: (data) => ReactApi.submitComment({ type, slug }, data),
		onSuccess: () => {
			onSuccess({
				id: 0,
				content: commentSubmitForm.getValues("content"),
				parent,
				user: {
					id: 0,
					name: authStore.user.name,
					username: authStore.user.username,
					avatar: authStore.user.avatar,
				},
				published_at: new Date().toISOString(),
				replies: 0,
				likes: 0,
				isLiked: false,
			});
			commentSubmitForm.reset({
				content: "",
				parent,
			});
		},
		onError: (error) => {
			if (error?.errors) {
				Object.keys(error.errors).forEach((key) => {
					commentSubmitForm.setError(key, { message: error.errors[key][0] });
				});
			} else {
				commentSubmitForm.setError("content", {
					message: t("System error. Please try again."),
				});
			}
		},
	});

	const onCommentSubmit = (data) => {
		commentSubmitMutation.mutate(data);
	};

	return (
		<Form form={commentSubmitForm} onSubmit={onCommentSubmit}>
			<Card className="mb-6">
				<CardContent className="p-4">
					{parent && (
						<FormField
							control={commentSubmitForm.control}
							name="parent"
							render={({ field }) => (
								<FormInput field={field} disabled type="hidden" />
							)}
						/>
					)}

					<FormField
						control={commentSubmitForm.control}
						name="content"
						render={({ field }) => (
							<FormTextareaInput
								placeholder={t("Share your thoughts...")}
								field={field}
								className="mb-4"
							/>
						)}
					/>

					<div className="flex justify-end">
						<Button type="submit" disabled={commentSubmitMutation.isPending}>
							{t("Post Comment")}
						</Button>
					</div>
				</CardContent>
			</Card>
		</Form>
	);
}
