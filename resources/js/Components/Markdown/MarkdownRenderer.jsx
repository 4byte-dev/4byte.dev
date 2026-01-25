import { useEffect, useMemo } from "react";
import parse from "html-react-parser";
import { useTranslation } from "react-i18next";
import { createMarked } from "./Core/CreateMarked";
import { sanitize } from "./Core/Sanitize";
import { HighlightCode } from "./Lifecycles/Highlight";
import { AttachCopyButtons } from "./Lifecycles/CopyButton";
import { CodeSpacePlugin } from "./Plugins/CodeSpace/CodeSpacePlugin";
import { CodeGroupPlugin } from "./Plugins/CodeGroup/CodeGroupPlugin";
import { EmojiPlugin } from "./Plugins/Emoji/EmojiPlugin";

export default function MarkdownRenderer({ content }) {
	const { t } = useTranslation();
	const marked = useMemo(() => createMarked([CodeGroupPlugin, CodeSpacePlugin, EmojiPlugin]), []);
	const html = useMemo(() => sanitize(marked(content)), [content]);

	useEffect(() => {
		HighlightCode();
		AttachCopyButtons(t);
		CodeGroupPlugin.lifecycle();
	}, [content]);

	return (
		<div className="prose dark:prose-invert max-w-none">
			{parse(html, {
				replace(domNode) {
					return CodeSpacePlugin.replace(domNode);
				},
			})}
		</div>
	);
}
