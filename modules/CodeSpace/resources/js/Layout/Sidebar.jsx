import { useEditorStore } from "@CodeSpace/Stores/EditorStore";
import { usePluginRegistry } from "@CodeSpace/Core/PluginRegistry";
import FileExplorer from "@CodeSpace/Components/FileExplorer";
import ExtensionsView from "@CodeSpace/Components/ExtensionsView";
import ProjectView from "@CodeSpace/Components/ProjectView";
import { useTranslation } from "react-i18next";

export default function Sidebar() {
	const { layout } = useEditorStore();
	const pluginViews = usePluginRegistry((reg) => reg.getViews("sidebar"));
	const { t } = useTranslation();

	if (!layout.sidebarVisible) return null;

	const activeViewId = layout.activeSiderbarView;
	let Component = null;

	if (activeViewId === "explorer") {
		Component = FileExplorer;
	} else if (activeViewId === "project") {
		Component = ProjectView;
	} else if (activeViewId === "extensions") {
		Component = ExtensionsView;
	} else {
		const pluginView = pluginViews.find((v) => v.id === activeViewId);
		if (pluginView) {
			Component = pluginView.component;
		}
	}

	return (
		<div className="w-64 bg-[var(--ce-sideBar-background)] flex flex-col border-r border-[var(--ce-sideBar-border)] text-[var(--ce-sideBar-foreground)] absolute md:static z-20 h-full shadow-xl md:shadow-none">
			{Component ? (
				<div className="flex-1 overflow-hidden h-full flex flex-col">
					<Component />
				</div>
			) : (
				<div className="p-4 text-gray-500 text-sm">{t("No View Selected")}</div>
			)}
		</div>
	);
}
