import React from "react";
import { pluginRegistry } from "@CodeSpace/Core/PluginRegistry";
import { useEditorStore } from "@CodeSpace/Stores/EditorStore";
import CodeSpaceApi from "@CodeSpace/Api";
import {
	Terminal,
	File,
	FileCode,
	FileJson,
	FileType,
	Palette,
	Save,
	Settings,
	ExternalLink,
} from "lucide-react";
import { router } from "@inertiajs/react";

export function initCoreCommands() {
	const getFileIcon = (name) => {
		if (name.endsWith(".html"))
			return React.createElement(FileCode, { className: "mr-2 h-4 w-4" });
		if (name.endsWith(".css"))
			return React.createElement(FileType, { className: "mr-2 h-4 w-4" });
		if (name.endsWith(".js") || name.endsWith(".jsx"))
			return React.createElement(FileJson, { className: "mr-2 h-4 w-4" });
		return React.createElement(File, { className: "mr-2 h-4 w-4" });
	};

	pluginRegistry.registerQuickOpenProvider({
		id: "core.files",
		mode: "default",
		getPlaceholder: () => "Search files...",
		getItems: (filter) => {
			if (filter.startsWith(">")) return [];
			const store = useEditorStore.getState();
			const files = store.files;
			return Object.keys(files)
				.filter((f) => !files[f].isDir && f.toLowerCase().includes(filter.toLowerCase()))
				.map((f) => ({
					id: f,
					label: f,
					icon: getFileIcon(f),
					value: f,
				}));
		},
		onSelect: (item) => {
			const store = useEditorStore.getState();
			store.openFile(item.value);
			store.setQuickOpen(false);
		},
	});

	pluginRegistry.registerQuickOpenProvider({
		id: "core.commands",
		mode: "default",
		prefix: ">",
		getPlaceholder: () => "Type a command...",
		getItems: (filter) => {
			const searchTerm = filter.substring(1).trim().toLowerCase();
			const actions = pluginRegistry.getEditorActions();
			const items = actions
				.filter((a) => a.label.toLowerCase().includes(searchTerm))
				.map((a) => ({
					id: a.id,
					label: a.label,
					icon: React.createElement(Terminal, { className: "mr-2 h-4 w-4" }),
					value: a.command,
				}));
			const hasThemeCommand = items.some((i) => i.id === "workbench.action.selectTheme");
			if (
				!hasThemeCommand &&
				("preferences: color theme".includes(searchTerm) || "theme".includes(searchTerm))
			) {
				items.unshift({
					id: "workbench.action.selectTheme",
					label: "Preferences: Color Theme",
					icon: React.createElement(Settings, { className: "mr-2 h-4 w-4" }),
					value: "workbench.action.selectTheme",
				});
			}
			return items;
		},
		onSelect: (item) => {
			const store = useEditorStore.getState();
			store.setQuickOpen(false);

			if (item.id === "workbench.action.selectTheme") {
				pluginRegistry.executeCommand("workbench.action.selectTheme");
			} else {
				pluginRegistry.executeCommand(item.value);
			}
		},
	});

	pluginRegistry.registerCommand(
		"workbench.action.selectTheme",
		() => {
			const store = useEditorStore.getState();
			store.setQuickOpen(true, "theme");
		},
		{ label: "Preferences: Color Theme" },
	);

	pluginRegistry.registerQuickOpenProvider({
		id: "core.themes",
		mode: "theme",
		getPlaceholder: () => "Select Color Theme",
		getItems: (filter) => {
			const themes = pluginRegistry.getThemes();
			return themes
				.filter((t) => t.label.toLowerCase().includes(filter.toLowerCase()))
				.map((t) => ({
					id: t.id,
					label: t.label,
					icon: React.createElement(Palette, { className: "mr-2 h-4 w-4" }),
					value: t.id,
				}));
		},
		onSelect: (item) => {
			const store = useEditorStore.getState();
			store.setTheme(item.value);
			store.setQuickOpen(false);
		},
	});

	pluginRegistry.registerQuickOpenProvider({
		id: "core.project.save",
		mode: "save",
		getPlaceholder: () => "Enter project name to save...",
		getItems: (filter) => {
			if (!filter.trim()) return [];
			return [
				{
					id: "save-action",
					label: `Save Project as "${filter}"`,
					icon: React.createElement(Save, { className: "mr-2 h-4 w-4" }),
					value: filter,
					alwaysShow: true,
				},
			];
		},
		onSelect: async (item) => {
			const store = useEditorStore.getState();

			try {
				if (store.slug) {
					CodeSpaceApi.editProject(store.slug, {
						files: store.files,
						name: item.value,
					}).then(() => {
						store.setName(item.value);
					});
				} else {
					CodeSpaceApi.createProject({
						files: store.files,
						name: item.value,
					}).then((response) => {
						router.replace(route("codespace.view", { slug: response.slug }));
					});
				}
			} catch (e) {
				console.error(e);
			}
			store.setQuickOpen(false);
		},
	});

	pluginRegistry.registerQuickOpenProvider({
		id: "core.project.load",
		mode: "load",
		getPlaceholder: () => "Search saved projects...",
		getItems: async (filter) => {
			try {
				const list = await CodeSpaceApi.listProjects();
				return list
					.filter((p) => (p.name || "").toLowerCase().includes(filter.toLowerCase()))
					.map((p) => ({
						id: p.slug,
						label: p.name,
						icon: React.createElement(FileCode, { className: "mr-2 h-4 w-4" }),
						description: new Date(p.updated_at).toLocaleDateString(),
						value: p.slug,
					}));
			} catch (e) {
				console.error(e);
				return [];
			}
		},
		onSelect: async (item) => {
			const store = useEditorStore.getState();
			try {
				router.visit(route("codespace.view", { slug: item.value }), {
					method: "get",
				});
			} catch (e) {
				console.error(e);
			}
			store.setQuickOpen(false);
		},
	});

	pluginRegistry.registerCommand(
		"workbench.action.files.save",
		() => {
			const store = useEditorStore.getState();
			if (store.activeFile) {
				store.saveFile();
			}
		},
		{ label: "File: Save" },
	);

	pluginRegistry.registerKeybinding({
		command: "workbench.action.files.save",
		key: "Ctrl+S",
		mac: "Cmd+S",
	});

	pluginRegistry.registerCommand(
		"workbench.action.project.save",
		() => {
			const store = useEditorStore.getState();
			store.setQuickOpen(true, "save");
		},
		{ label: "Project: Save Project..." },
	);

	pluginRegistry.registerCommand(
		"workbench.action.project.load",
		() => {
			const store = useEditorStore.getState();
			store.setQuickOpen(true, "load");
		},
		{ label: "Project: Load Project..." },
	);

	pluginRegistry.registerStatusBarItem({
		id: "core.lang",
		label: "Plain Text",
		alignment: "right",
		priority: 10,
	});
	pluginRegistry.registerStatusBarItem({
		id: "core.cursor",
		label: "Ln 1, Col 1",
		alignment: "right",
		priority: 20,
	});

	pluginRegistry.registerCommand(
		"workbench.action.quickOpen",
		() => {
			const store = useEditorStore.getState();
			store.setQuickOpen(true);
		},
		{ label: "Go to File..." },
	);

	pluginRegistry.registerKeybinding({
		command: "workbench.action.quickOpen",
		key: "Ctrl+P",
		mac: "Cmd+P",
	});

	pluginRegistry.registerCommand(
		"workbench.action.togglePanel",
		() => {
			const store = useEditorStore.getState();
			store.togglePanel();
		},
		{ label: "View: Toggle Panel" },
	);

	pluginRegistry.registerKeybinding({
		command: "workbench.action.togglePanel",
		key: "Ctrl+J",
		mac: "Cmd+J",
	});

	pluginRegistry.registerCommand(
		"workbench.action.files.close",
		() => {
			const store = useEditorStore.getState();
			const activeFile = store.activeFile;
			if (activeFile) {
				store.closeFile(activeFile);
			}
		},
		{ label: "File: Close Editor" },
	);

	pluginRegistry.registerKeybinding({
		command: "workbench.action.files.close",
		key: "Ctrl+W",
		mac: "Cmd+W",
	});

	pluginRegistry.registerCommand(
		"workbench.action.toggleSidebar",
		() => {
			const store = useEditorStore.getState();
			store.toggleSidebar();
		},
		{ label: "View: Toggle Sidebar" },
	);

	pluginRegistry.registerKeybinding({
		command: "workbench.action.toggleSidebar",
		key: "Ctrl+B",
		mac: "Cmd+B",
	});

	pluginRegistry.registerCommand(
		"workbench.action.files.newUntitledFile",
		() => {
			const store = useEditorStore.getState();
			store.createFile("file", "");
		},
		{ label: "File: New Untitled File" },
	);

	pluginRegistry.registerKeybinding({
		command: "workbench.action.files.newUntitledFile",
		key: "Ctrl+N",
		mac: "Cmd+N",
	});

	pluginRegistry.registerCommand(
		"workbench.view.explorer",
		() => {
			const store = useEditorStore.getState();
			store.setActiveSidebarView("explorer");
			if (!store.layout.sidebarVisible) store.toggleSidebar();
		},
		{ label: "View: Show Explorer" },
	);

	pluginRegistry.registerKeybinding({
		command: "workbench.view.explorer",
		key: "Ctrl+Shift+E",
		mac: "Cmd+Shift+E",
	});

	pluginRegistry.registerMenuItem({
		id: "explorer.openTerminal",
		label: "Open Terminal",
		icon: Terminal,
		command: "workbench.action.togglePanel",
		context: "explorer",
		when: (ctx) => ctx.type === "background",
	});

	pluginRegistry.registerEditorAction({
		id: "workbench.action.togglePanel",
		label: "Open Terminal",
		command: "workbench.action.togglePanel",
		contextMenuGroupId: "navigation",
		keybindings: [],
	});

	pluginRegistry.registerCommand(
		"codespace.openInNewTab",
		() => {
			const store = useEditorStore.getState();
			if (store.slug) {
				window.open(route("codespace.view", { slug: store.slug }), "_blank");
			}
		},
		{ label: "Open in New Tab" },
	);

	pluginRegistry.registerEditorTitleItem({
		id: "codespace.open-new-tab",
		label: "Open in New Tab",
		icon: React.createElement(ExternalLink, { size: 16 }),
		command: "codespace.openInNewTab",
		when: (ctx) => ctx.isEmbed,
	});
}
