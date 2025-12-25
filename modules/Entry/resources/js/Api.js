import ApiService from "@/Services/ApiService";

export default {
	createEntry: (data) => {
		return ApiService.fetchJson(route("api.entry.crud.create"), data, {
			isMultipart: true,
		});
	},
};
