class EventEmitter {
	constructor() {
		this.listeners = new Set();
	}
	subscribe(listener) {
		this.listeners.add(listener);
		return () => this.listeners.delete(listener);
	}
	emit(data) {
		this.listeners.forEach((l) => l(data));
	}
}

class PluginRegistry {
	constructor() {
		this.plugins = new Map();

		this.commands = new Map();
		this.editorTitle = []; // { id, icon, command, title }
		this.statusBar = []; // { id, label, command, alignment, priority }
		this.menus = []; // { id, label, command, context }
		this.editorActions = []; // { id, label, command, keybindings, contextMenuGroupId }
		this.keybindings = []; // { command, key, mac }
		this.views = []; // { id, name, component, location }
		this.views = []; // { id, name, component, location }
		this.themes = []; // { id, label, type, colors }
		this.settings = []; // { id, label, type, default, category, description, min, max, options }
		this.quickOpenProviders = []; // { id, mode ('default'|'other'), prefix, getItems: (filter) => [], onSelect, ... }

		this.onRegistryChange = new EventEmitter();

		this.plugins = new Map(); // id -> pluginDefinition
		this.pluginStates = new Map(); // id -> boolean (enabled/disabled)
		this.pluginResources = new Map(); // id -> { commands: [], views: [], ... }

		this._loadState();
	}

	_loadState() {
		try {
			const saved = localStorage.getItem("code-editor-plugins-state");
			if (saved) {
				const parsed = JSON.parse(saved);
				Object.keys(parsed).forEach((k) => this.pluginStates.set(k, parsed[k]));
			}
		} catch (e) {
			console.error("Failed to load plugin state", e);
		}
	}

	_saveState() {
		const obj = {};
		this.pluginStates.forEach((v, k) => (obj[k] = v));
		localStorage.setItem("code-editor-plugins-state", JSON.stringify(obj));
	}

	_trackResource(pluginId, type, id) {
		if (!pluginId) return;
		if (!this.pluginResources.has(pluginId)) {
			this.pluginResources.set(pluginId, {
				commands: [],
				views: [],
				menus: [],
				editorTitle: [],
				statusBar: [],
				editorActions: [],
				keybindings: [],
				themes: [],
				quickOpenProviders: [],
				settings: [],
			});
		}
		const resources = this.pluginResources.get(pluginId);
		if (resources[type]) resources[type].push(id);
	}

	registerPlugin(plugin) {
		if (this.plugins.has(plugin.id)) return;
		console.log(`[PluginRegistry] Registering ${plugin.id}`);
		this.plugins.set(plugin.id, plugin);

		if (!this.pluginStates.has(plugin.id)) {
			this.pluginStates.set(plugin.id, true);
			this._saveState();
		}

		if (this.isPluginEnabled(plugin.id)) {
			this._activatePlugin(plugin);
		}
	}

	isPluginEnabled(id) {
		return this.pluginStates.get(id) === true;
	}

	enablePlugin(id) {
		const plugin = this.plugins.get(id);
		if (!plugin || this.isPluginEnabled(id)) return;

		this.pluginStates.set(id, true);
		this._saveState();
		this._activatePlugin(plugin);
		this.onRegistryChange.emit("plugins");
	}

	disablePlugin(id) {
		if (!this.isPluginEnabled(id)) return;

		this.pluginStates.set(id, false);
		this._saveState();
		this._deactivatePlugin(id);
		this.onRegistryChange.emit("plugins");
	}

	_activatePlugin(plugin) {
		if (!plugin.activate) return;

		console.log(`[PluginRegistry] Activating ${plugin.id}`);

		const context = {
			registerCommand: (id, handler, metadata) => {
				this.registerCommand(id, handler, metadata);
				this._trackResource(plugin.id, "commands", id);
			},
			registerEditorTitleItem: (item) => {
				this.registerEditorTitleItem(item);
				this._trackResource(plugin.id, "editorTitle", item);
			},
			registerStatusBarItem: (item) => {
				this.registerStatusBarItem(item);
				this._trackResource(plugin.id, "statusBar", item);
			},
			registerMenuItem: (item) => {
				this.registerMenuItem(item);
				this._trackResource(plugin.id, "menus", item);
			},
			registerEditorAction: (item) => {
				this.registerEditorAction(item);
				this._trackResource(plugin.id, "editorActions", item);
			},
			registerKeybinding: (item) => {
				this.registerKeybinding(item);
				this._trackResource(plugin.id, "keybindings", item);
			},
			registerView: (view) => {
				this.registerView(view);
				this._trackResource(plugin.id, "views", view);
			},
			registerTheme: (theme) => {
				this.registerTheme(theme);
				this._trackResource(plugin.id, "themes", theme);
			},
			registerQuickOpenProvider: (provider) => {
				this.registerQuickOpenProvider(provider);
				this._trackResource(plugin.id, "quickOpenProviders", provider);
			},
			registerSetting: (setting) => {
				this.registerSetting(setting);
				this._trackResource(plugin.id, "settings", setting);
			},
		};

		try {
			plugin.activate(context);
		} catch (e) {
			console.error(`Error activating plugin ${plugin.id}:`, e);
		}
	}

	_deactivatePlugin(id) {
		console.log(`[PluginRegistry] Deactivating ${id}`);
		const resources = this.pluginResources.get(id);
		if (!resources) return;

		if (resources.commands) resources.commands.forEach((cmdId) => this.commands.delete(cmdId));

		if (resources.editorTitle) {
			this.editorTitle = this.editorTitle.filter(
				(item) => !resources.editorTitle.includes(item),
			);
			this.onRegistryChange.emit("editorTitle");
		}

		if (resources.statusBar) {
			this.statusBar = this.statusBar.filter((item) => !resources.statusBar.includes(item));
			this.onRegistryChange.emit("statusBar");
		}

		if (resources.menus) {
			this.menus = this.menus.filter((item) => !resources.menus.includes(item));
			this.onRegistryChange.emit("menus");
		}

		if (resources.editorActions) {
			this.editorActions = this.editorActions.filter(
				(item) => !resources.editorActions.includes(item),
			);
			this.onRegistryChange.emit("editorActions");
		}

		if (resources.keybindings) {
			this.keybindings = this.keybindings.filter(
				(item) => !resources.keybindings.includes(item),
			);
		}

		if (resources.views) {
			this.views = this.views.filter((item) => !resources.views.includes(item));
			this.onRegistryChange.emit("views");
		}

		if (resources.themes) {
			this.themes = this.themes.filter((item) => !resources.themes.includes(item));
			this.onRegistryChange.emit("themes");
		}

		if (resources.quickOpenProviders) {
			this.quickOpenProviders = this.quickOpenProviders.filter(
				(item) => !resources.quickOpenProviders.includes(item),
			);
			this.onRegistryChange.emit("quickOpenProviders");
		}

		if (resources.settings) {
			this.settings = this.settings.filter((item) => !resources.settings.includes(item));
			this.onRegistryChange.emit("settings");
		}

		this.pluginResources.delete(id);
	}

	registerCommand(id, handler, metadata = {}) {
		this.commands.set(id, handler);

		if (metadata && metadata.label) {
			this.registerEditorAction({
				id: id,
				label: metadata.label,
				command: id,
				// description:
				// keybindings:
			});
		}
	}

	registerEditorTitleItem(item) {
		const index = this.editorTitle.findIndex((i) => i.id === item.id);
		if (index !== -1) {
			this.editorTitle[index] = item;
		} else {
			this.editorTitle.push(item);
		}
		this.onRegistryChange.emit("editorTitle");
	}

	registerStatusBarItem(item) {
		const index = this.statusBar.findIndex((i) => i.id === item.id);
		if (index !== -1) {
			this.statusBar[index] = item;
		} else {
			this.statusBar.push(item);
		}
		this.onRegistryChange.emit("statusBar");
	}

	updateStatusBarItem(id, updates) {
		const item = this.statusBar.find((i) => i.id === id);
		if (item) {
			Object.assign(item, updates);
			this.onRegistryChange.emit("statusBar");
		}
	}

	registerMenuItem(item) {
		const index = this.menus.findIndex((i) => i.id === item.id);
		if (index !== -1) {
			this.menus[index] = item;
		} else {
			this.menus.push(item);
		}
		this.onRegistryChange.emit("menus");
	}

	registerEditorAction(item) {
		const index = this.editorActions.findIndex((i) => i.id === item.id);
		if (index !== -1) {
			this.editorActions[index] = item;
		} else {
			this.editorActions.push(item);
		}
		this.onRegistryChange.emit("editorActions");
	}

	registerKeybinding(item) {
		this.keybindings.push(item);
	}

	registerView(view) {
		this.views.push(view);
		this.onRegistryChange.emit("views");
	}

	registerTheme(theme) {
		const index = this.themes.findIndex((t) => t.id === theme.id);
		if (index !== -1) {
			this.themes[index] = theme;
		} else {
			this.themes.push(theme);
		}
		this.onRegistryChange.emit("themes");
	}

	registerQuickOpenProvider(provider) {
		const index = this.quickOpenProviders.findIndex((p) => p.id === provider.id);
		if (index !== -1) {
			this.quickOpenProviders[index] = provider;
		} else {
			this.quickOpenProviders.push(provider);
		}
		this.onRegistryChange.emit("quickOpenProviders");
	}

	registerSetting(setting) {
		const index = this.settings.findIndex((s) => s.id === setting.id);
		if (index !== -1) {
			this.settings[index] = setting;
		} else {
			this.settings.push(setting);
		}
		this.onRegistryChange.emit("settings");
	}

	executeCommand(id, ...args) {
		if (this.commands.has(id)) {
			return this.commands.get(id)(...args);
		}
		console.warn(`Command '${id}' not found`);
	}

	getEditorTitleItems() {
		return [...this.editorTitle];
	}
	getStatusBarItems() {
		return [...this.statusBar].sort((a, b) => (b.priority || 0) - (a.priority || 0));
	}
	getMenuItems() {
		return [...this.menus];
	}
	getEditorActions() {
		return [...this.editorActions];
	}
	getKeybindings() {
		return [...this.keybindings];
	}
	getViews(location = "sidebar") {
		return this.views.filter((v) => v.location === location);
	}
	getThemes() {
		return [...this.themes];
	}
	getQuickOpenProviders() {
		return this.quickOpenProviders;
	}
	getAllPlugins() {
		return Array.from(this.plugins.values());
	}
	getSettings() {
		return [...this.settings];
	}

	subscribe(listener) {
		return this.onRegistryChange.subscribe(listener);
	}
}

export const pluginRegistry = new PluginRegistry();

import { useState, useEffect } from "react";

export function usePluginRegistry(selector) {
	const [, setTick] = useState(0);
	useEffect(() => {
		return pluginRegistry.subscribe(() => setTick((t) => t + 1));
	}, []);

	return selector(pluginRegistry);
}
