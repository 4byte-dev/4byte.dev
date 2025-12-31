import React, { useEffect } from "react";
import { useEditorStore } from "@CodeSpace/Stores/EditorStore";
import ActivityBar from "@CodeSpace/Layout/ActivityBar";
import Sidebar from "@CodeSpace/Layout/Sidebar";
import StatusBar from "@CodeSpace/Layout/StatusBar";
import TitleBar from "@CodeSpace/Layout/TitleBar";
import ConsolePanel from "@CodeSpace/Components/Panel/ConsolePanel";
import { pluginRegistry } from "@CodeSpace/Core/PluginRegistry";
import { useKeybindings } from "@CodeSpace/Hooks/useKeybindings";
import { initCoreCommands } from "@CodeSpace/Core/CoreCommands";
import QuickOpen from "@CodeSpace/Components/QuickOpen";
import { initCoreThemes } from "@CodeSpace/Core/CoreThemes";
import { CoreSettings } from "@CodeSpace/Core/CoreSettings";
import SettingsModal from "@CodeSpace/Components/SettingsModal";
import { getFileLabel } from "@CodeSpace/Lib/FileIcons";

import.meta.glob("../Plugins/**/*.jsx", { eager: true });
initCoreCommands();
initCoreThemes();
pluginRegistry.registerPlugin(CoreSettings);
pluginRegistry.enablePlugin(CoreSettings.id);

export default function Workbench({ children }) {
	useKeybindings();
	const { activeFile, files, theme } = useEditorStore();

	useEffect(() => {
		const themes = pluginRegistry.getThemes();
		const activeTheme =
			themes.find((t) => t.id === theme) ||
			themes.find((t) => t.id === "theme-defaults-dark");

		if (activeTheme && activeTheme.colors) {
			const root = document.documentElement;
			Object.entries(activeTheme.colors).forEach(([key, value]) => {
				root.style.setProperty(key, value);
			});
		}
	}, [theme]);

	useEffect(() => {
		if (!activeFile) {
			pluginRegistry.updateStatusBarItem("core.lang", { label: "" });
			return;
		}

		const label = getFileLabel(activeFile);

		pluginRegistry.updateStatusBarItem("core.lang", { label });
	}, [activeFile, files]);

	return (
		<div className="fixed inset-0 z-50 flex flex-col w-screen h-screen bg-[var(--ce-editor-background)] text-[var(--ce-foreground)] overflow-hidden font-sans">
			<TitleBar />
			<div className="flex-1 flex overflow-hidden relative">
				<ActivityBar />
				<Sidebar />
				<div className="flex-1 flex flex-col min-w-0 bg-[var(--ce-editor-background)]">
					<div className="flex-1 overflow-hidden relative">{children}</div>
					<ConsolePanel />
				</div>
			</div>
			<StatusBar />
			<QuickOpen />
			<SettingsModal />
		</div>
	);
}
