import ApiService from "@/Services/ApiService";

export default {
	getProject: (slug) => {
		return ApiService.fetchJson(
			route("api.codespace.crud.get", { slug }),
			{},
			{
				method: "GET",
			},
		);
	},
	createProject: (data) => {
		return ApiService.fetchJson(route("api.codespace.crud.create"), data);
	},
	editProject: (slug, data) => {
		return ApiService.fetchJson(route("api.codespace.crud.edit", { slug }), data);
	},
	listProjects: () => {
		return ApiService.fetchJson(
			route("api.codespace.crud.list"),
			{},
			{
				method: "GET",
			},
		);
	},
};
