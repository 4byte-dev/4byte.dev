export const codeGroupExtension = {
	name: "codegroup",
	level: "block",
	start(src) {
		return src.match(/::: code-group/)?.index;
	},
	tokenizer(src) {
		const rule = /^:::code-group(?:\{[^}]*labels\s*=\s*"([^"]+)"[^}]*\})?\n([\s\S]*?)\n:::/;

		const match = rule.exec(src);
		if (!match) return;

		const labels = match[1] ? match[1].split(",").map((l) => l.trim()) : [];

		const content = match[2];
		const blocks = [];

		const codeRule = /```(\w+)?\n([\s\S]*?)```/g;
		let codeMatch;

		while ((codeMatch = codeRule.exec(content)) !== null) {
			blocks.push({
				lang: codeMatch[1] || "",
				code: codeMatch[2],
			});
		}

		return {
			type: "codegroup",
			raw: match[0],
			labels,
			blocks,
			tokens: [],
		};
	},
	renderer(token) {
		const labels =
			token.labels.length > 0
				? token.labels
				: token.blocks.map((block) => block.lang || "text");

		const tabs = labels
			.map(
				(label, i) =>
					`<button 
                    type="button" 
                    class="code-tab inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50
                        ${i === 0 ? "bg-background text-foreground shadow-sm" : "text-muted-foreground hover:text-foreground"}"
                    data-id="${i}">
                    ${label}
                </button>`,
			)
			.join("");

		const blocks = token.blocks
			.map((block, i) => {
				const lang = block.lang || "";
				const code = this.parser.parse([
					{
						type: "code",
						raw: "```" + lang + "\n" + block.code + "\n```",
						lang: lang,
						text: block.code,
					},
				]);

				return `
                <div class="tab-content ${i === 0 ? "block" : "hidden"} ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                    data-id="${i}">
                    ${code}
                </div>`;
			})
			.join("");

		return `
            <div class="code-group w-full">
              <div role="tablist" class="flex h-10 items-center rounded-md bg-muted p-1 gap-1">
                ${tabs}
              </div>
              <div class="code-blocks -mt-5">${blocks}</div>
            </div>
          `;
	},
};
