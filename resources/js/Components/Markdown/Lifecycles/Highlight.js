import hljs from "highlight.js";

export function HighlightCode() {
	document.querySelectorAll("pre code").forEach((block) => {
		if (!block.dataset.highlighted) {
			hljs.highlightElement(block);
		}
	});
}
