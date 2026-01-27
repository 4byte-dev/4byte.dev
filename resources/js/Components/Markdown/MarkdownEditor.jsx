import { Crepe } from "@milkdown/crepe";
import { insert } from "@milkdown/kit/utils";
import { Milkdown, MilkdownProvider, useEditor } from "@milkdown/react";
import { clipboard } from "@milkdown/kit/plugin/clipboard";
import { useTranslation } from "react-i18next";
import "@milkdown/crepe/theme/common/style.css";
import { $remark } from "@milkdown/kit/utils";
import directive from "remark-directive";
import { CodeGroupPlugin } from "./Plugins/CodeGroup/CodeGroupPlugin";
import { CodeSpacePlugin } from "./Plugins/CodeSpace/CodeSpacePlugin";

const plugins = [CodeGroupPlugin, CodeSpacePlugin];
const remarkDirective = $remark("remarkDirective", () => directive);

const CrepeEditor = ({ value, onChange, onImageUpload }) => {
	const { t } = useTranslation();

	useEditor((root) => {
		const crepe = new Crepe({
			root,
			defaultValue: value || "",
			featureConfigs: {
				[Crepe.Feature.Placeholder]: {
					text: t("Write your article content here... (Markdown supported)"),
				},
				[Crepe.Feature.ImageBlock]: {
					onUpload: async (file) => {
						const blobUrl = URL.createObjectURL(file);
						onImageUpload?.(blobUrl, file);
						return blobUrl;
					},
				},
				[Crepe.Feature.BlockEdit]: {
					buildMenu: (groupBuilder) => {
						plugins.forEach((plugin) => {
							plugin.configureMenu?.(groupBuilder);
						});
					},
				},
			},
		});
		crepe.editor.use(remarkDirective);
		crepe.editor.use(clipboard);
		plugins.forEach((plugin) => {
			crepe.editor.use(plugin.feature);
		});
		crepe.on((listener) => {
			listener.mounted(() => {
				root.addEventListener("paste", (event) => {
					const items = event.clipboardData.items;
					for (const item of items) {
						if (item.type.indexOf("image") === 0) {
							event.preventDefault();
							const file = item.getAsFile();
							const blobUrl = URL.createObjectURL(file);
							onImageUpload?.(blobUrl, file);
							crepe.editor.action(insert(`![](${blobUrl})\n`));
							return;
						}
					}
				});
			});
			listener.markdownUpdated(() => {
				onChange?.(crepe.getMarkdown());
			});
		});
		return crepe;
	});

	return <Milkdown />;
};

export const MarkdownEditor = (props) => {
	return (
		<MilkdownProvider>
			<CrepeEditor {...props} />
		</MilkdownProvider>
	);
};
