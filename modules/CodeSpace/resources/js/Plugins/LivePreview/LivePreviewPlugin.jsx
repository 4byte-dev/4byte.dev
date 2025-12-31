import React from "react";
import { pluginRegistry } from "@CodeSpace/Core/PluginRegistry";
import { Play, Globe } from "lucide-react";
import { useEditorStore } from "@CodeSpace/Stores/EditorStore";
import { usePreviewRunner } from "@CodeSpace/Plugins/LivePreview/usePreviewRunner";

function PreviewPane() {
	const iframeSrc = usePreviewRunner();
	return (
		<div className="flex flex-col h-full bg-white">
			<iframe
				title="preview"
				srcDoc={iframeSrc}
				className="flex-1 w-full border-none bg-white"
				sandbox="allow-scripts allow-modals allow-same-origin"
			/>
		</div>
	);
}

const LivePreviewPlugin = {
	id: "live-preview",
	name: "Live Preview",
	description: "Hosts a local server and previews your HTML/CSS/JS files in real-time.",
	version: "1.0.0",
	publisher: "4Byte",
	icon: <Globe size={24} />,
	activate: (context) => {
		const togglePreview = () => {
			const store = useEditorStore.getState();
			const activeFile = store.activeFile;

			if (store.layout.editorSplitVisible) {
				store.setEditorSplit(false, null);
				return;
			}

			if (!activeFile || !activeFile.endsWith(".html")) {
				return;
			}

			store.setEditorSplit(true, PreviewPane);
		};

		const closePreview = () => {
			useEditorStore.getState().setEditorSplit(false, null);
		};

		const refreshPreview = () => {
			const store = useEditorStore.getState();
			if (store.layout.editorSplitVisible) {
				store.setEditorSplit(false, null);
				setTimeout(() => {
					store.setEditorSplit(true, PreviewPane);
				}, 50);
			}
		};

		context.registerCommand("livePreview.start", togglePreview);
		context.registerCommand("livePreview.close", closePreview);
		context.registerCommand("livePreview.refresh", refreshPreview);

		context.registerEditorTitleItem({
			id: "live-preview.btn",
			title: "Toggle Preview",
			icon: <Play size={16} />,
			command: "livePreview.start",
			when: (ctx) => ctx.activeFile && ctx.activeFile.endsWith(".html"),
		});

		context.registerEditorAction({
			id: "live-preview.action.toggle",
			label: "Live Preview: Toggle",
			command: "livePreview.start",
			contextMenuGroupId: "navigation",
			contextMenuOrder: 1,
		});

		context.registerEditorAction({
			id: "live-preview.action.close",
			label: "Live Preview: Close",
			command: "livePreview.close",
		});

		context.registerEditorAction({
			id: "live-preview.action.refresh",
			label: "Live Preview: Refresh",
			command: "livePreview.refresh",
		});

		context.registerStatusBarItem({
			id: "live-preview.status",
			label: "Go Live",
			icon: <Play size={12} />,
			alignment: "right",
			command: "livePreview.start",
			priority: 100,
			className: "bg-blue-600 hover:bg-blue-500 font-bold",
			when: (ctx) => ctx.activeFile && ctx.activeFile.endsWith(".html"),
		});

		context.registerMenuItem({
			id: "live-preview.context",
			label: "Live Preview",
			icon: Play,
			command: "livePreview.start",
			context: "explorer",
			when: (ctx) => ctx.type === "file" && ctx.path.endsWith(".html"),
		});
	},
};

pluginRegistry.registerPlugin(LivePreviewPlugin);
