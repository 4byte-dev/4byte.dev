import { slugify } from "@/Lib/Utils";
import { marked } from "marked";

export function createRenderer() {
	const renderer = new marked.Renderer();

	renderer.heading = ({ text, depth }) => {
		const id = slugify(text);

		return `
			<h${depth} id="${id}">
				<a href="#${id}" class="no-underline relative before:content-[''] before:absolute before:bottom-0 before:left-0 before:w-0 before:h-[2px] before:bg-foreground before:transition-all hover:before:w-full">${text}</a>
			</h${depth}>
		`;
	};

	return renderer;
}
