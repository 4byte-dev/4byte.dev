import Emoji from "@/Data/Emoji";

export const EmojiExtension = {
	name: "emoji",
	level: "inline",
	start(src) {
		return src.match(/:/)?.index;
	},
	tokenizer(src) {
		const matchResult = /^:([\w-]+):/.exec(src);

		if (matchResult) {
			const shortcode = matchResult[0].slice(1, -1);

			const emoji = Emoji[shortcode];
			if (emoji) {
				return {
					type: "emoji",
					raw: matchResult[0],
					emoji,
					shortcode,
				};
			}
		}
	},
	renderer({ emoji, shortcode }) {
		return `<span class="custom-emoji" title=":${shortcode}:">${emoji}</span>`;
	},
};
