import { marked } from "marked";
import { createRenderer } from "./Renderer";

export function createMarked(plugins) {
	const renderer = createRenderer();

	marked.setOptions({ renderer });

	plugins.forEach((plugin) => {
		if (plugin.extension) {
			marked.use({
				extensions: [plugin.extension],
			});
		}
	});

	return marked;
}
