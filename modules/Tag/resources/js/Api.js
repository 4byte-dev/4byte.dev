import ApiService from "@/Services/ApiService";

export default {
	search: (query, { page = 1, pageSize = 15 }) => {
		return ApiService.fetchJson(route("tag.search"), { query, page, per_page: pageSize }, {
			method: "GET",
		});
	},
};
