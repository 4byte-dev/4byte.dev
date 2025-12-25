import ApiService from "@/Services/ApiService";

export default {
	createArticle: (data) => {
		return ApiService.fetchJson(route("api.article.crud.create"), data, {
			isMultipart: true,
		});
	},
	editArticle: (slug, data) => {
		return ApiService.fetchJson(route("api.article.crud.edit", { slug }), data, {
			isMultipart: true,
		});
	},
};
