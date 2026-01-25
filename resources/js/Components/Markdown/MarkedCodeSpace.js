export function markedCodeSpace(markedInstance) {
	markedInstance.use({
		extensions: [
			{
				name: "codespace",
				level: "block",
				start(src) {
					return src.match(/\[codespace\s+/)?.index;
				},
				tokenizer(src) {
					const rule = /^\[codespace\s+([^\]]+)\]/;
					const match = rule.exec(src);
					if (!match) return;

					const rawAttrs = match[1];
					const props = {};

					const attrRegex = /(\w+)="([^"]*)"/g;
					let attrMatch;
					while ((attrMatch = attrRegex.exec(rawAttrs)) !== null) {
						props[attrMatch[1]] = attrMatch[2];
					}

					return {
						type: "codespace",
						raw: match[0],
						props,
					};
				},
				renderer(token) {
					const attrs = Object.entries(token.props)
						.map(([k, v]) => `data-${k}="${v}"`)
						.join(" ");

					return `<div data-codespace ${attrs}></div>`;
				},
			},
		],
	});
}
