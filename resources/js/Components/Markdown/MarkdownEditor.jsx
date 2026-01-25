import { Crepe } from "@milkdown/crepe";
import { insert } from "@milkdown/kit/utils";
import { Milkdown, MilkdownProvider, useEditor } from "@milkdown/react";
import { clipboard } from "@milkdown/kit/plugin/clipboard";
import { useTranslation } from "react-i18next";
import "@milkdown/crepe/theme/common/style.css";

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
			},
		});
		crepe.editor.use(clipboard);
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
