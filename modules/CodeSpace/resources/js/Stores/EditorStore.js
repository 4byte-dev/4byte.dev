import { create } from "zustand";
import { INITIAL_FILES, TEMPLATES } from "@CodeSpace/Data";

export const useEditorStore = create((set) => ({
	layout: {
		sidebarVisible: true,
		activityBarVisible: true,
		panelVisible: true,
		statusBarVisible: true,
		activeSiderbarView: "explorer",
		editorSplitVisible: false,
		editorSplitContent: null,
		quickOpenVisible: false,
		quickOpenMode: "default", // 'default' | 'theme' | 'save' | 'load'
		modals: {
			save: false,
			load: false,
			template: false,
		},
	},

	files: INITIAL_FILES,
	buffers: {},

	name: null,
	slug: null,
	isEmbed: false,

	activeFile: "index.html",
	openFiles: ["index.html"],
	consoleLogs: [],
	theme: localStorage.getItem("ce-theme") || "theme-defaults-4byte",

	toggleSidebar: () =>
		set((state) => ({
			layout: { ...state.layout, sidebarVisible: !state.layout.sidebarVisible },
		})),

	togglePanel: (force) =>
		set((state) => ({
			layout: {
				...state.layout,
				panelVisible: force !== undefined ? force : !state.layout.panelVisible,
			},
		})),

	setActiveSidebarView: (viewId) =>
		set((state) => ({
			layout: { ...state.layout, activeSiderbarView: viewId, sidebarVisible: true },
		})),

	setEditorSplit: (visible, content) =>
		set((state) => ({
			layout: { ...state.layout, editorSplitVisible: visible, editorSplitContent: content },
		})),

	setQuickOpen: (visible, mode = "default") =>
		set((state) => ({
			layout: {
				...state.layout,
				quickOpenVisible: visible,
				quickOpenMode: visible ? mode : "default",
			},
		})),

	setModal: (modalName, visible) =>
		set((state) => ({
			layout: {
				...state.layout,
				modals: { ...state.layout.modals, [modalName]: visible },
			},
		})),

	setName: (name) => set({ name }),
	setSlug: (slug) => set({ slug }),
	setEmbed: (isEmbed) => set({ isEmbed }),

	setActiveFile: (filePath) => set({ activeFile: filePath }),

	setConsoleLogs: (logs) => set({ consoleLogs: logs }),
	addConsoleLog: (log) => set((state) => ({ consoleLogs: [...state.consoleLogs, log] })),
	clearConsole: () => set({ consoleLogs: [] }),

	setTheme: (themeId) => {
		localStorage.setItem("ce-theme", themeId);
		set({ theme: themeId });
	},

	openFile: (filePath) =>
		set((state) => {
			if (!state.files[filePath] || state.files[filePath].isDir) return {};
			if (!state.openFiles.includes(filePath)) {
				return { openFiles: [...state.openFiles, filePath], activeFile: filePath };
			}
			return { activeFile: filePath };
		}),

	closeFile: (filePath) =>
		set((state) => {
			const newOpenFiles = state.openFiles.filter((f) => f !== filePath);
			let newActiveFile = state.activeFile;
			if (state.activeFile === filePath) {
				newActiveFile = newOpenFiles[newOpenFiles.length - 1] || null;
			}
			return { openFiles: newOpenFiles, activeFile: newActiveFile };
		}),

	updateBuffer: (filePath, content) =>
		set((state) => ({
			buffers: { ...state.buffers, [filePath]: content },
		})),

	saveFile: () =>
		set((state) => {
			const { activeFile, buffers, files } = state;
			if (!activeFile || buffers[activeFile] === undefined) return {};

			return {
				files: {
					...files,
					[activeFile]: { ...files[activeFile], content: buffers[activeFile] },
				},
				buffers: (() => {
					const newBuffers = { ...buffers };
					delete newBuffers[activeFile];
					return newBuffers;
				})(),
			};
		}),

	setFiles: (files) =>
		set({
			files,
			buffers: {},
			openFiles: Object.keys(files).slice(0, 1),
			activeFile: Object.keys(files)[0],
		}),

	setOpenFiles: (files) => set({ openFiles: files }),

	loadTemplate: (templateKey) => {
		const template = TEMPLATES[templateKey];
		if (template) {
			set({
				files: template.files,
				buffers: {},
				openFiles: [Object.keys(template.files)[0]],
				activeFile: Object.keys(template.files)[0],
			});
		}
	},

	renameFile: (oldPath, newName) =>
		set((state) => {
			const parts = oldPath.split("/");
			parts.pop();
			const parentPath = parts.join("/");
			const newPath = parentPath ? `${parentPath}/${newName}` : newName;
			if (oldPath === newPath) return {};
			if (state.files[newPath]) {
				alert("File already exists!");
				return {};
			}
			const newFiles = {};
			const newBuffers = {};
			Object.keys(state.files).forEach((path) => {
				if (path === oldPath) {
					newFiles[newPath] = { ...state.files[path], name: newPath.split("/").pop() };
					if (state.buffers[path]) newBuffers[newPath] = state.buffers[path];
				} else if (path.startsWith(oldPath + "/")) {
					const suffix = path.substring(oldPath.length);
					const targetPath = newPath + suffix;
					newFiles[targetPath] = {
						...state.files[path],
						name: targetPath.split("/").pop(),
					};
					if (state.buffers[path]) newBuffers[targetPath] = state.buffers[path];
				} else {
					newFiles[path] = state.files[path];
					if (state.buffers[path]) newBuffers[path] = state.buffers[path];
				}
			});
			const newOpenFiles = state.openFiles.map((p) => {
				if (p === oldPath) return newPath;
				if (p.startsWith(oldPath + "/")) return newPath + p.substring(oldPath.length);
				return p;
			});
			let newActiveFile = state.activeFile;
			if (state.activeFile === oldPath) newActiveFile = newPath;
			else if (state.activeFile?.startsWith(oldPath + "/"))
				newActiveFile = newPath + state.activeFile.substring(oldPath.length);
			return {
				files: newFiles,
				buffers: newBuffers,
				openFiles: newOpenFiles,
				activeFile: newActiveFile,
			};
		}),

	deleteFile: (path) =>
		set((state) => {
			const newFiles = { ...state.files };
			Object.keys(state.files).forEach((k) => {
				if (k === path || k.startsWith(path + "/")) delete newFiles[k];
			});
			const newOpenFiles = state.openFiles.filter(
				(p) => p !== path && !p.startsWith(path + "/"),
			);
			let newActiveFile = state.activeFile;
			if (newActiveFile === path || newActiveFile?.startsWith(path + "/"))
				newActiveFile = null;
			return { files: newFiles, openFiles: newOpenFiles, activeFile: newActiveFile };
		}),

	createFile: (type, parentPath) =>
		set((state) => {
			const base = parentPath || "";
			let tempName = type === "folder" ? "New Folder" : "untitled";
			let counter = 1;
			while (state.files[base ? `${base}/${tempName}` : tempName]) {
				tempName = `${type === "folder" ? "New Folder" : "untitled"}-${counter}`;
				counter++;
			}
			const newPath = base ? `${base}/${tempName}` : tempName;
			const newFiles = { ...state.files };
			if (type === "file")
				newFiles[newPath] = { name: tempName, language: "plaintext", content: "" };
			else
				newFiles[`${newPath}/.gitkeep`] = {
					name: ".gitkeep",
					language: "plaintext",
					content: "",
				};
			return { files: newFiles };
		}),

	settings: {},

	loadSettings: (defaultSettings = []) => {
		let savedSettings = {};
		try {
			const saved = localStorage.getItem("ce-settings");
			if (saved) savedSettings = JSON.parse(saved);
		} catch (e) {
			console.error("Failed to load settings", e);
		}

		const newSettings = {};
		defaultSettings.forEach((S) => {
			newSettings[S.id] = savedSettings[S.id] !== undefined ? savedSettings[S.id] : S.default;
		});

		Object.keys(savedSettings).forEach((k) => {
			if (newSettings[k] === undefined) newSettings[k] = savedSettings[k];
		});

		set({ settings: newSettings });
	},

	setSetting: (id, value) =>
		set((state) => {
			const newSettings = { ...state.settings, [id]: value };
			localStorage.setItem("ce-settings", JSON.stringify(newSettings));
			return { settings: newSettings };
		}),
}));
