import { pluginRegistry } from "@CodeSpace/Core/PluginRegistry";

export function initCoreThemes() {
	pluginRegistry.registerTheme({
		id: "theme-defaults-dark",
		label: "Dark Modern",
		type: "dark",
		colors: {
			"--ce-focus-border": "#007acc",
			"--ce-foreground": "#cccccc",
			"--ce-widget-shadow": "0 0 8px 2px rgba(0, 0, 0, 0.6)",

			"--ce-activityBar-background": "#333333",
			"--ce-activityBar-foreground": "#ffffff",
			"--ce-activityBar-inactiveForeground": "#858585",
			"--ce-activityBar-border": "#252526",
			"--ce-activityBar-activeBorder": "#007acc",
			"--ce-activityBarBadge-background": "#007acc",
			"--ce-activityBarBadge-foreground": "#ffffff",

			"--ce-sideBar-background": "#252526",
			"--ce-sideBar-foreground": "#cccccc",
			"--ce-sideBar-border": "#252526",
			"--ce-sideBarTitle-foreground": "#bbbbbb",
			"--ce-sideBarSectionHeader-background": "#333333",
			"--ce-sideBarSectionHeader-foreground": "#cccccc",

			"--ce-list-activeSelectionBackground": "#37373d",
			"--ce-list-activeSelectionForeground": "#ffffff",
			"--ce-list-hoverBackground": "#2a2d2e",
			"--ce-list-hoverForeground": "#cccccc",

			"--ce-editorGroupHeader-tabsBackground": "#252526",
			"--ce-editorGroup-border": "#444444",
			"--ce-tab-activeBackground": "#1e1e1e",
			"--ce-tab-activeForeground": "#ffffff",
			"--ce-tab-activeBorderTop": "#007acc",
			"--ce-tab-inactiveBackground": "#2d2d2d",
			"--ce-tab-inactiveForeground": "#969696",
			"--ce-tab-border": "#252526",

			"--ce-editor-background": "#1e1e1e",
			"--ce-editor-foreground": "#d4d4d4",
			"--ce-editorLineNumber-foreground": "#858585",
			"--ce-editorLineNumber-activeForeground": "#c6c6c6",

			"--ce-statusBar-background": "#007acc",
			"--ce-statusBar-foreground": "#ffffff",
			"--ce-statusBarItem-hoverBackground": "rgba(255, 255, 255, 0.12)",
			"--ce-statusBar-border": "#007acc",

			"--ce-panel-background": "#1e1e1e",
			"--ce-panel-border": "#80808059",
			"--ce-panelTitle-activeForeground": "#e7e7e7",
			"--ce-panelTitle-inactiveForeground": "#e7e7e799",
			"--ce-panelTitle-activeBorder": "#e7e7e7",

			"--ce-input-background": "#3c3c3c",
			"--ce-input-foreground": "#cccccc",
			"--ce-input-placeholderForeground": "#a6a6a6",
			"--ce-input-border": "#3c3c3c",
		},
	});

	pluginRegistry.registerTheme({
		id: "theme-defaults-light",
		label: "Light Modern",
		type: "light",
		colors: {
			"--ce-focus-border": "#0090f1",
			"--ce-foreground": "#616161",
			"--ce-widget-shadow": "0 0 8px 2px rgba(0, 0, 0, 0.2)",

			"--ce-activityBar-background": "#2c2c2c",
			"--ce-activityBar-foreground": "#ffffff",
			"--ce-activityBar-inactiveForeground": "rgba(255,255,255,0.4)",
			"--ce-activityBar-border": "#2c2c2c",
			"--ce-activityBar-activeBorder": "#ffffff",
			"--ce-activityBarBadge-background": "#007acc",
			"--ce-activityBarBadge-foreground": "#ffffff",

			"--ce-sideBar-background": "#f3f3f3",
			"--ce-sideBar-foreground": "#616161",
			"--ce-sideBar-border": "#e5e5e5",
			"--ce-sideBarTitle-foreground": "#6f6f6f",
			"--ce-sideBarSectionHeader-background": "#e5e5e5",
			"--ce-sideBarSectionHeader-foreground": "#3b3b3b",

			"--ce-list-activeSelectionBackground": "#0060c0",
			"--ce-list-activeSelectionForeground": "#ffffff",
			"--ce-list-hoverBackground": "#e8e8e8",
			"--ce-list-hoverForeground": "#616161",

			"--ce-editorGroupHeader-tabsBackground": "#f3f3f3",
			"--ce-editorGroup-border": "#e5e5e5",
			"--ce-tab-activeBackground": "#ffffff",
			"--ce-tab-activeForeground": "#333333",
			"--ce-tab-activeBorderTop": "#0090f1",
			"--ce-tab-inactiveBackground": "#ececec",
			"--ce-tab-inactiveForeground": "rgba(51, 51, 51, 0.7)",
			"--ce-tab-border": "#e5e5e5",

			"--ce-editor-background": "#ffffff",
			"--ce-editor-foreground": "#333333",
			"--ce-editorLineNumber-foreground": "#237893",
			"--ce-editorLineNumber-activeForeground": "#0b216f",

			"--ce-statusBar-background": "#007acc",
			"--ce-statusBar-foreground": "#ffffff",
			"--ce-statusBarItem-hoverBackground": "rgba(255, 255, 255, 0.12)",
			"--ce-statusBar-border": "#007acc",

			"--ce-panel-background": "#ffffff",
			"--ce-panel-border": "#80808059",
			"--ce-panelTitle-activeForeground": "#424242",
			"--ce-panelTitle-inactiveForeground": "#42424299",
			"--ce-panelTitle-activeBorder": "#424242",

			"--ce-input-background": "#ffffff",
			"--ce-input-foreground": "#616161",
			"--ce-input-placeholderForeground": "#767676",
			"--ce-input-border": "#cecece",
		},
	});

	pluginRegistry.registerTheme({
		id: "theme-defaults-4byte",
		label: "4Byte",
		type: "dark",
		colors: {
			"--ce-focus-border": "#fafafa",
			"--ce-foreground": "#fafafa",
			"--ce-widget-shadow": "0 0 8px 2px rgba(0, 0, 0, 0.6)",

			"--ce-activityBar-background": "#0a0a0a",
			"--ce-activityBar-foreground": "#fafafa",
			"--ce-activityBar-inactiveForeground": "#858585",
			"--ce-activityBar-border": "#262626",
			"--ce-activityBar-activeBorder": "#fafafa",
			"--ce-activityBarBadge-background": "#fafafa",
			"--ce-activityBarBadge-foreground": "#0a0a0a",

			"--ce-sideBar-background": "#0a0a0a",
			"--ce-sideBar-foreground": "#fafafa",
			"--ce-sideBar-border": "#262626",
			"--ce-sideBarTitle-foreground": "#bbbbbb",
			"--ce-sideBarSectionHeader-background": "#0a0a0a",
			"--ce-sideBarSectionHeader-foreground": "#fafafa",

			"--ce-list-activeSelectionBackground": "#262626",
			"--ce-list-activeSelectionForeground": "#fafafa",
			"--ce-list-hoverBackground": "#262626",
			"--ce-list-hoverForeground": "#fafafa",

			"--ce-editorGroupHeader-tabsBackground": "#0a0a0a",
			"--ce-editorGroup-border": "#262626",
			"--ce-tab-activeBackground": "#0a0a0a",
			"--ce-tab-activeForeground": "#fafafa",
			"--ce-tab-activeBorderTop": "#fafafa",
			"--ce-tab-inactiveBackground": "#0a0a0a",
			"--ce-tab-inactiveForeground": "#969696",
			"--ce-tab-border": "#262626",

			"--ce-editor-background": "#0a0a0a",
			"--ce-editor-foreground": "#fafafa",
			"--ce-editorLineNumber-foreground": "#858585",
			"--ce-editorLineNumber-activeForeground": "#c6c6c6",

			"--ce-statusBar-background": "#0a0a0a",
			"--ce-statusBar-foreground": "#fafafa",
			"--ce-statusBarItem-hoverBackground": "rgba(255, 255, 255, 0.12)",
			"--ce-statusBar-border": "#262626",

			"--ce-panel-background": "#0a0a0a",
			"--ce-panel-border": "#262626",
			"--ce-panelTitle-activeForeground": "#fafafa",
			"--ce-panelTitle-inactiveForeground": "#e7e7e799",
			"--ce-panelTitle-activeBorder": "#fafafa",

			"--ce-input-background": "#0a0a0a",
			"--ce-input-foreground": "#fafafa",
			"--ce-input-placeholderForeground": "#a6a6a6",
			"--ce-input-border": "#262626",
		},
	});
}
