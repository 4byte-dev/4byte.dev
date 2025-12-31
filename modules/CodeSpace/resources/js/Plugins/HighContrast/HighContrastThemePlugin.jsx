import { pluginRegistry } from "@CodeSpace/Core/PluginRegistry";
import { Eye } from "lucide-react";
import { useEditorStore } from "@CodeSpace/Stores/EditorStore";

const HighContrastThemePlugin = {
	id: "theme-high-contrast",
	name: "High Contrast Theme",
	description: "A high contrast theme for better visibility.",
	version: "1.0.0",
	publisher: "4Byte",
	icon: <Eye size={24} />,
	activate: (context) => {
		context.registerTheme({
			id: "theme-hc-black",
			label: "High Contrast Black",
			type: "hc",
			colors: {
				"--ce-focus-border": "#f38518",
				"--ce-foreground": "#ffffff",
				"--ce-widget-shadow": "none",

				"--ce-activityBar-background": "#000000",
				"--ce-activityBar-foreground": "#ffffff",
				"--ce-activityBar-inactiveForeground": "#ffffff",
				"--ce-activityBar-border": "#6FC3DF",
				"--ce-activityBar-activeBorder": "#f38518",
				"--ce-activityBarBadge-background": "#6FC3DF",
				"--ce-activityBarBadge-foreground": "#000000",

				"--ce-sideBar-background": "#000000",
				"--ce-sideBar-foreground": "#ffffff",
				"--ce-sideBar-border": "#6FC3DF",
				"--ce-sideBarTitle-foreground": "#ffffff",
				"--ce-sideBarSectionHeader-background": "#000000",
				"--ce-sideBarSectionHeader-foreground": "#ffffff",

				"--ce-list-activeSelectionBackground": "#000000",
				"--ce-list-activeSelectionForeground": "#f38518",
				"--ce-list-hoverBackground": "#ffffff22",
				"--ce-list-hoverForeground": "#ffffff",

				"--ce-editorGroupHeader-tabsBackground": "#000000",
				"--ce-editorGroup-border": "#6FC3DF",
				"--ce-tab-activeBackground": "#000000",
				"--ce-tab-activeForeground": "#ffffff",
				"--ce-tab-activeBorderTop": "#f38518",
				"--ce-tab-inactiveBackground": "#000000",
				"--ce-tab-inactiveForeground": "#ffffff",
				"--ce-tab-border": "#6FC3DF",

				"--ce-editor-background": "#000000",
				"--ce-editor-foreground": "#ffffff",
				"--ce-editorLineNumber-foreground": "#ffffff",
				"--ce-editorLineNumber-activeForeground": "#f38518",

				"--ce-statusBar-background": "#000000",
				"--ce-statusBar-foreground": "#ffffff",
				"--ce-statusBarItem-hoverBackground": "rgba(255, 255, 255, 0.2)",
				"--ce-statusBar-border": "#6FC3DF",

				"--ce-panel-background": "#000000",
				"--ce-panel-border": "#6FC3DF",
				"--ce-panelTitle-activeForeground": "#ffffff",
				"--ce-panelTitle-inactiveForeground": "#ffffff99",
				"--ce-panelTitle-activeBorder": "#f38518",

				"--ce-input-background": "#000000",
				"--ce-input-foreground": "#ffffff",
				"--ce-input-placeholderForeground": "#ffffff99",
				"--ce-input-border": "#6FC3DF",
			},
		});

		context.registerCommand("theme.hc.apply", () => {
			const store = useEditorStore.getState();
			store.setTheme("theme-hc-black");
		});
	},
};

pluginRegistry.registerPlugin(HighContrastThemePlugin);
