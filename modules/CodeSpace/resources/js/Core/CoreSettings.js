export const CoreSettings = {
	id: "core.settings",
	activate: (context) => {
		context.registerSetting({
			id: "editor.fontSize",
			label: "Font Size",
			type: "number",
			default: 14,
			min: 8,
			max: 32,
			category: "Editor",
			description: "Controls the font size in pixels.",
		});

		context.registerSetting({
			id: "editor.wordWrap",
			label: "Word Wrap",
			type: "boolean",
			default: false,
			category: "Editor",
			description: "Controls how lines should wrap.",
		});

		context.registerSetting({
			id: "editor.minimap.enabled",
			label: "Minimap",
			type: "boolean",
			default: true,
			category: "Editor",
			description: "Controls whether the minimap is shown.",
		});

		context.registerSetting({
			id: "editor.blockCursor",
			label: "Cursor Style",
			type: "select",
			default: "line",
			options: [
				{ value: "line", label: "Line" },
				{ value: "block", label: "Block" },
				{ value: "underline", label: "Underline" },
				{ value: "line-thin", label: "Line Thin" },
				{ value: "block-outline", label: "Block Outline" },
				{ value: "underline-thin", label: "Underline Thin" },
			],
			category: "Editor",
			description: "Controls the cursor style.",
		});

		context.registerSetting({
			id: "editor.tabSize",
			label: "Tab Size",
			type: "number",
			default: 4,
			min: 1,
			max: 8,
			category: "Editor",
			description: "The number of spaces a tab is equal to.",
		});

		context.registerSetting({
			id: "editor.renderWhitespace",
			label: "Render Whitespace",
			type: "select",
			default: "selection",
			options: [
				{ value: "none", label: "None" },
				{ value: "boundary", label: "Boundary" },
				{ value: "selection", label: "Selection" },
				{ value: "trailing", label: "Trailing" },
				{ value: "all", label: "All" },
			],
			category: "Editor",
			description: "Controls how the editor should render whitespace characters.",
		});

		context.registerSetting({
			id: "editor.smoothScrolling",
			label: "Smooth Scrolling",
			type: "boolean",
			default: true,
			category: "Editor",
			description: "Controls whether the editor will scroll using an animation.",
		});

		context.registerSetting({
			id: "ui.iconSize",
			label: "Icon Size",
			type: "number",
			default: 24,
			min: 16,
			max: 48,
			category: "UI",
			description: "Controls the size of icons in the Activity Bar.",
		});

		context.registerSetting({
			id: "ui.sidebarWidth",
			label: "Sidebar Width",
			type: "number",
			default: 250,
			min: 150,
			max: 500,
			category: "UI",
			description: "Initial width of the sidebar.",
		});
	},
};
