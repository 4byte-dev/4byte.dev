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

	return text.replace(/\n/g, " ").replace(/\s+/g, " ").trim();
};
