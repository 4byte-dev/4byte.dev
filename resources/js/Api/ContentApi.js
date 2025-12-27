import ApiService from "@/Services/ApiService";

export default {
	search: (query) => {
		return ApiService.fetchJson(
			route("api.search") + "?q=" + encodeURIComponent(query),
			{},
			{ method: "GET" },
		);
	},

	feedData: () => {
		return ApiService.fetchJson(route("api.feed.data"), {}, { method: "GET" });
	},
};
