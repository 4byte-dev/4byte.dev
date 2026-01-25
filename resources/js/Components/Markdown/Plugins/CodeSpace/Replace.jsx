import CodeSpacePage from "@Modules/CodeSpace/resources/js/Pages/CodeSpace/Detail";

export function replaceCodeSpace(domNode) {
	if (
		domNode.type === "tag" &&
		domNode.name === "div" &&
		domNode.attribs?.["data-codespace"] !== undefined
	) {
		const props = {};
		Object.entries(domNode.attribs).forEach(([key, value]) => {
			if (key.startsWith("data-") && key !== "data-codespace") {
				props[key.replace("data-", "")] = value;
			}
		});

		return <CodeSpacePage embed {...props} />;
	}
}
