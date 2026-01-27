import { $nodeSchema } from "@milkdown/kit/utils";

export const codeGroupSchema = $nodeSchema("code_group", () => ({
	name: "code_group",
	inline: false,
	content: "code_block+",
	group: "block",
	selectable: true,
	draggable: true,
	defining: true,
	isolating: true,
	attrs: {
		labels: { default: [] },
		activeIndex: { default: 0 },
	},
	parseMarkdown: {
		match: (node) => node.type === "containerDirective" && node.name === "code-group",
		runner: (state, node, type) => {
			const labels = node.attributes?.labels
				? node.attributes.labels.split(",").map((s) => s.trim())
				: ["Code"];

			state.openNode(type, { labels, activeIndex: 0 });
			state.next(node.children);
			state.closeNode();
		},
	},
	toMarkdown: {
		match: (node) => node.type.name === "code_group",
		runner: (state, node) => {
			state.openNode("containerDirective", undefined, {
				name: "code-group",
				attributes: {
					labels: node.attrs.labels.join(", "),
				},
			});

			state.next(node.content);

			state.closeNode();
		},
	},
	parseDOM: [
		{
			tag: 'div[data-type="code-group"]',
			getAttrs: (dom) => ({
				labels: dom.dataset.labels ? dom.dataset.labels.split(",") : [],
				activeIndex: parseInt(dom.dataset.activeIndex || "0", 10),
			}),
		},
	],
	toDOM: (node) => [
		"div",
		{
			"data-type": "code-group",
			"data-labels": node.attrs.labels.join(","),
			"data-active-index": node.attrs.activeIndex,
		},
		0,
	],
}));
