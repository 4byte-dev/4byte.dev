import { $nodeSchema } from "@milkdown/kit/utils";

export const codeSpaceSchema = $nodeSchema("code_space", () => ({
	name: "code_space",
	group: "block",
	atom: true,
	isolating: true,
	attrs: {
		slug: { default: "" },
	},
	parseMarkdown: {
		match: (node) => node.type === "leafDirective" && node.name === "code-space",
		runner: (state, node, type) => {
			state.addNode(type, {
				slug: (node.attributes && node.attributes.slug) || "",
			});
		},
	},
	toMarkdown: {
		match: (node) => node.type.name === "code_space",
		runner: (state, node) => {
			state.addNode("leafDirective", undefined, "code-space", {
				name: "code-space",
				attributes: {
					slug: node.attrs.slug || "",
				},
			});
		},
	},
	parseDOM: [
		{
			tag: 'div[data-type="code-space"]',
			getAttrs: (dom) => ({
				slug: dom.dataset.slug,
			}),
		},
	],
	toDOM: (node) => [
		"div",
		{
			"data-type": "code-space",
			"data-slug": node.attrs.slug,
		},
	],
}));
