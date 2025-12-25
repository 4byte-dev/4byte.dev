import ApiService from "@/Services/ApiService";

export default {
	login: (data) => {
		return ApiService.fetchJson(route("api.auth.login"), data);
	},
	register: (data) => {
		return ApiService.fetchJson(route("api.auth.register"), data);
	},
	forgotPassword: (data) => {
		return ApiService.fetchJson(route("api.auth.forgot-password"), data);
	},
	resetPassword: (data) => {
		return ApiService.fetchJson(route("api.auth.reset-password.request"), data);
	},
	logout: () => {
		return ApiService.fetchJson(route("api.auth.logout"));
	},

	preview: (data) => {
		return ApiService.fetchJson(route("api.user.preview", data), {}, { method: "GET" });
	},
	updateAccount: (data) => {
		return ApiService.fetchJson(route("api.user.settings.account"), data, {
			isMultipart: true,
		});
	},
	updateProfile: (data) => {
		return ApiService.fetchJson(route("api.user.settings.profile"), data, {
			isMultipart: true,
		});
	},
	changePassword: (data) => {
		return ApiService.fetchJson(route("api.user.settings.password"), data);
	},
	deleteAccount: (data) => {
		return ApiService.fetchJson(route("api.user.settings.delete-account"), data);
	},
	logOutOtherBrowserSessions: (data) => {
		return ApiService.fetchJson(route("api.user.settings.logout-other-sessions"), data);
	},
	resendVerify: () => {
		return ApiService.fetchJson(route("api.user.verification.resend"));
	},
};
