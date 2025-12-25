import ApiService from "@/Services/ApiService";

export default {
	getNotifications: () => {
		return ApiService.fetchJson(
			route("api.notification.list"),
			{},
			{
				method: "GET",
			},
		);
	},
	readNotification: (data) => {
		return ApiService.fetchJson(route("api.notification.read"), data);
	},
	readNotifications: () => {
		return ApiService.fetchJson(route("api.notification.read-all"));
	},
};
