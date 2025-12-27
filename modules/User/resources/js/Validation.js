import * as z from "zod";
import Validation from "@/Data/Validation";

export const loginSchema = (t) =>
	z.object({
		email: z.string().email(t("Invalid email format")),
		password: z
			.string()
			.min(8, t("Password must be at least 8 characters"))
			.regex(Validation.password, t("These credentials do not match our records.")),
		rememberMe: z.boolean().optional(),
	});

export const registerSchema = (t) =>
	z
		.object({
			name: z.string().min(1, t("Full name is required")),
			email: z.string().email(t("Invalid email format")),
			username: z.string().regex(Validation.username, t("Invalid username format")),
			password: z
				.string()
				.min(8, t("Password must be at least 8 characters"))
				.regex(
					Validation.password,
					t(
						"Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.",
					),
				),
			password_confirmation: z.string().min(1, t("Password confirmation is required")),
			termsAccepted: z.literal(true, t("You must accept the Terms of Service to continue.")),
		})
		.refine((data) => data.password === data.password_confirmation, {
			message: t("Passwords do not match"),
			path: ["password_confirmation"],
		});

export const forgotPasswordSchema = (t) =>
	z.object({
		email: z.string().email(t("Invalid email format")),
	});

export const resetPasswordSchema = (t) =>
	z.object({
		token: z.string().min(1, t("Invalid credentials. Please try again.")),
		email: z.string().email(t("Invalid email format")),
		password: z
			.string()
			.min(8, t("Password must be at least 8 characters"))
			.regex(Validation.password, t("These credentials do not match our records.")),
		password_confirmation: z.refine((data) => data.password === data.confirmPassword, {
			message: t("Password confirmation is required"),
			path: ["confirm_password"],
		}),
	});

export const passwordConfirmationSchema = (t) =>
	z.object({
		password: z
			.string()
			.min(8, t("Password must be at least 8 characters"))
			.regex(Validation.password, t("These credentials do not match our records.")),
	});

export const accountSettingsSchema = (t) =>
	z.object({
		avatar: z.string().optional(),
		name: z.string().min(1, t("Full name is required")),
		username: z
			.string()
			.min(1, t("Username is required"))
			.regex(Validation.username, t("Invalid username format")),
		email: z.string().email(t("Invalid email format")),
	});

export const profileSettingsSchema = (t) =>
	z.object({
		bio: z.string().optional(),
		location: z.string().optional(),
		website: z.string().optional(),
		role: z.string().optional(),
		socials: z.array(z.string().url(t("Invalid URL"))).optional(),
		cover: z.string().optional(),
	});

export const securitySettingsSchema = (t) =>
	z
		.object({
			current_password: z
				.string()
				.min(8, t("Password must be at least 8 characters"))
				.regex(Validation.password, t("These credentials do not match our records.")),
			new_password: z
				.string()
				.min(8, t("Password must be at least 8 characters"))
				.regex(Validation.password, t("These credentials do not match our records.")),
			new_password_confirmation: z.string().min(1, t("Password confirmation is required")),
		})
		.refine((data) => data.new_password === data.new_password_confirmation, {
			message: t("Passwords do not match"),
			path: ["new_password_confirmation"],
		});
