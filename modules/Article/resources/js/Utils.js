export const stripMarkdown = (markdown) => {
	if (!markdown) return "";

	let text = markdown;

	text = text.replace(/<br\s*\/?>/gi, " ");

	text = text.replace(/```[\s\S]*?```/g, "");
	text = text.replace(/`([^`]+)`/g, "$1");

	text = text.replace(/<[^>]*>/g, "");

	text = text
		.replace(/!\[.*?\]\(.*?\)/g, "")
		.replace(/\[([^\]]+)\]\([^)]+\)/g, "$1")
		.replace(/^#{1,6}\s+/gm, "")
		.replace(/(\*\*|__)(.*?)\1/g, "$2")
		.replace(/(\*|_)(.*?)\1/g, "$2")
		.replace(/^\s*>\s+/gm, "")
		.replace(/^\s*[-*+]\s+/gm, "")
		.replace(/^\s*\d+\.\s+/gm, "")
		.replace(/~{2}(.*?)~{2}/g, "$1");

	return text
		.replace(/[<>]/g, "")
		.replace(/\n/g, " ")
		.replace(/\s+/g, " ")
		.trim();
};

export function calculateReadingTime(markdown) {
	if (!markdown) return 0;

	const WORDS_PER_MINUTE = 225;
	const CODE_WORDS_PER_MINUTE = 150;

	let content = markdown;
	let totalTimeInSeconds = 0;

	const codeBlockRegex = /```[\s\S]*?```/g;
	const codeBlocks = content.match(codeBlockRegex) || [];

	content = content.replace(codeBlockRegex, "");

	codeBlocks.forEach((block) => {
		const codeWords = block.split(/\s+/g).length;
		totalTimeInSeconds += (codeWords / CODE_WORDS_PER_MINUTE) * 60;
	});

	const imageRegex = /!\[.*?\]\(.*?\)|<img.*?>/g;
	const images = content.match(imageRegex) || [];

	content = content.replace(imageRegex, "");

	let imageSecs = 0;
	let seconds = 12;
	if (images.length > 0) {
		for (let i = 0; i < images.length; i++) {
			imageSecs += seconds;
			if (seconds > 3) {
				seconds -= 1;
			}
		}
	}
	totalTimeInSeconds += imageSecs;

	const cleanText = content
		.replace(/#+\s/g, "")
		.replace(/(\*\*|__)(.*?)\1/g, "$2")
		.replace(/(\*|_)(.*?)\1/g, "$2")
		.replace(/\[([^\]]+)\]\([^)]+\)/g, "$1")
		.replace(/>\s/g, "")
		.trim();

	const wordCount = cleanText.split(/\s+/g).filter((w) => w !== "").length;
	totalTimeInSeconds += (wordCount / WORDS_PER_MINUTE) * 60;

	const readingTime = Math.ceil(totalTimeInSeconds / 60);

	return readingTime > 0 ? readingTime : 1;
}
