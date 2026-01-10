import ApiService from "@/Services/ApiService";

export default {
	createArticle: (data) => {
		return ApiService.fetchJson(route("api.article.store"), data, {
			isMultipart: true,
		});
	},
	editArticle: (slug, data) => {
		return ApiService.fetchJson(route("api.article.update", { slug }), data, {
			method: "PUT",
			isMultipart: true
		});
	},
};
