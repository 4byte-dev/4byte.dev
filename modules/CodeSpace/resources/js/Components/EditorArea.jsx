import { useEffect, useState } from "react";
import Editor from "@monaco-editor/react";
import { useEditorStore } from "@CodeSpace/Stores/EditorStore";
import { X } from "lucide-react";
import clsx from "clsx";
import { usePluginRegistry } from "@CodeSpace/Core/PluginRegistry";
import { pluginRegistry } from "@CodeSpace/Core/PluginRegistry";
import { useTranslation } from "react-i18next";

function EditorTitle() {
	const titleItems = usePluginRegistry((reg) => reg.getEditorTitleItems());
	const { activeFile, isEmbed } = useEditorStore();

	const context = { activeFile, type: "editor", isEmbed };

	const visibleItems = titleItems.filter((item) => {
		if (item.when && !item.when(context)) return false;
		return true;
	});

	return (
		<div className="flex items-center">
			{visibleItems.map((item, idx) => (
				<button
					key={item.id || idx}
					onClick={() => item.command && pluginRegistry.executeCommand(item.command)}
					className="p-1 hover:bg-gray-700 rounded text-gray-400 hover:text-white ml-1"
					title={item.title}
				>
					{item.icon}
				</button>
			))}
		</div>
	);
}

export default function EditorArea() {
	const {
		openFiles,
		activeFile,
		files,
		closeFile,
		setActiveFile,
		updateBuffer,
		buffers,
		layout,
		theme,
		settings,
	} = useEditorStore();
	const [editorInstance, setEditorInstance] = useState(null);
	const [monacoInstance, setMonacoInstance] = useState(null);
	const { t } = useTranslation();

	const fontSize = settings["editor.fontSize"] || 13;
	const wordWrap = settings["editor.wordWrap"] ? "on" : "off";
	const minimapEnabled =
		settings["editor.minimap.enabled"] !== undefined
			? settings["editor.minimap.enabled"]
			: false;
	const cursorStyle = settings["editor.blockCursor"] || "line";
	const tabSize = settings["editor.tabSize"] || 4;
	const renderWhitespace = settings["editor.renderWhitespace"] || "selection";
	const smoothScrolling =
		settings["editor.smoothScrolling"] !== undefined
			? settings["editor.smoothScrolling"]
			: true;

	useEffect(() => {
		if (!editorInstance) return;

		editorInstance.updateOptions({
			fontSize,
			wordWrap,
			minimap: { enabled: minimapEnabled },
			cursorStyle,
			tabSize,
			renderWhitespace,
			smoothScrolling,
			fontFamily: "'JetBrains Mono', Consolas, monospace",
			padding: { top: 16 },
		});

		const actions = pluginRegistry.getEditorActions();
		const disposables = actions.map((action) => {
			return editorInstance.addAction({
				id: action.id,
				label: action.label,
				keybindings: action.keybindings,
				contextMenuGroupId: action.contextMenuGroupId,
				contextMenuOrder: action.contextMenuOrder,
				run: () => {
					if (action.command) pluginRegistry.executeCommand(action.command);
				},
			});
		});

		return () => {
			disposables.forEach((d) => d.dispose());
		};
	}, [
		editorInstance,
		fontSize,
		wordWrap,
		minimapEnabled,
		cursorStyle,
		tabSize,
		renderWhitespace,
		smoothScrolling,
	]);

	useEffect(() => {
		if (!monacoInstance || !theme) return;

		const themeDef =
			pluginRegistry.getThemes().find((t) => t.id === theme) ||
			pluginRegistry.getThemes().find((t) => t.id === "theme-defaults-dark");

		if (themeDef) {
			const base =
				themeDef.type === "light" ? "vs" : themeDef.type === "hc" ? "hc-black" : "vs-dark";

			monacoInstance.editor.defineTheme("dynamic-theme", {
				base: base,
				inherit: true,
				rules: [],
				colors: {
					"editor.background": themeDef.colors["--ce-editor-background"],
					"editor.foreground": themeDef.colors["--ce-editor-foreground"],
					"editorLineNumber.foreground":
						themeDef.colors["--ce-editorLineNumber-foreground"],
					"editorLineNumber.activeForeground":
						themeDef.colors["--ce-editorLineNumber-activeForeground"],
					"editorCursor.foreground": themeDef.colors["--ce-focus-border"],
				},
			});
			monacoInstance.editor.setTheme("dynamic-theme");
		}
	}, [theme, monacoInstance]);

	if (openFiles.length === 0) {
		return (
			<div className="h-full flex items-center justify-center text-[var(--ce-editor-foreground)] text-sm bg-[var(--ce-editor-background)]">
				<div className="text-center">
					<div>{t("No open files")}</div>
					<div className="text-xs text-muted-foreground">
						{t("Click on a file in the file explorer to open it or CTRL + P")}
					</div>
				</div>
			</div>
		);
	}

	const activeContent = activeFile ? (buffers[activeFile] ?? files[activeFile]?.content) : "";

	return (
		<div className="flex flex-col h-full min-w-0">
			<div className="flex items-center justify-between bg-[var(--ce-editorGroupHeader-tabsBackground)] h-9 border-b border-[var(--ce-editorGroup-border)] pr-2">
				<div className="flex overflow-x-auto h-full hide-scrollbar flex-1">
					{openFiles.map((path) => (
						<div
							key={path}
							onClick={() => setActiveFile(path)}
							className={clsx(
								"flex items-center gap-2 px-3 min-w-[120px] max-w-[200px] text-xs cursor-pointer border-r border-[var(--ce-tab-border)] group relative",
								activeFile === path
									? "bg-[var(--ce-tab-activeBackground)] text-[var(--ce-tab-activeForeground)]"
									: "bg-[var(--ce-tab-inactiveBackground)] text-[var(--ce-tab-inactiveForeground)]",
							)}
						>
							{activeFile === path && (
								<div className="absolute top-0 left-0 w-full h-[1px] bg-[var(--ce-tab-activeBorderTop)]" />
							)}
							<span className="truncate flex-1 flex items-center gap-1.5">
								{path.split("/").pop()}
							</span>
							<div
								onClick={(e) => {
									e.stopPropagation();
									closeFile(path);
								}}
								className={clsx(
									"p-0.5 hover:bg-gray-700 rounded flex items-center justify-center w-5 h-5",
									activeFile === path || buffers[path] !== undefined
										? "opacity-100"
										: "opacity-0 group-hover:opacity-100",
								)}
							>
								{buffers[path] !== undefined ? (
									<div className="w-2 h-2 bg-white rounded-full group-hover:hidden" />
								) : null}
								<X
									size={12}
									className={clsx(
										buffers[path] !== undefined && "hidden group-hover:block",
									)}
								/>
							</div>
						</div>
					))}
				</div>
				<EditorTitle />
			</div>

			<div className="flex-1 flex min-h-0 bg-[var(--ce-editor-background)]">
				<div
					className={clsx(
						"relative h-full",
						layout.editorSplitVisible
							? "w-1/2 border-r border-[var(--ce-editorGroup-border)]"
							: "w-full",
					)}
				>
					{activeFile && (
						<Editor
							height="100%"
							theme="vs-dark"
							path={activeFile}
							defaultLanguage={files[activeFile]?.language || "plaintext"}
							value={activeContent}
							onChange={(val) => updateBuffer(activeFile, val)}
							onMount={(editor, monaco) => {
								setEditorInstance(editor);
								setMonacoInstance(monaco);

								const pos = editor.getPosition();
								pluginRegistry.updateStatusBarItem("core.cursor", {
									label: `Ln ${pos.lineNumber}, Col ${pos.column}`,
								});

								editor.onDidChangeCursorPosition((e) => {
									const { lineNumber, column } = e.position;
									pluginRegistry.updateStatusBarItem("core.cursor", {
										label: `Ln ${lineNumber}, Col ${column}`,
									});
								});

								editor.addCommand(monaco.KeyCode.F1, () => {
									const store = useEditorStore.getState();
									store.setQuickOpen(true);
								});
							}}
							options={{
								minimap: { enabled: minimapEnabled },
								fontSize,
								wordWrap,
								cursorStyle,
								tabSize,
								renderWhitespace,
								smoothScrolling,
								fontFamily: "'JetBrains Mono', Consolas, monospace",
								padding: { top: 16 },
								contextmenu: true,
								automaticLayout: true,
							}}
						/>
					)}
				</div>

				{layout.editorSplitVisible && layout.editorSplitContent && (
					<div className="w-1/2 flex flex-col bg-white">
						<layout.editorSplitContent />
					</div>
				)}
			</div>
		</div>
	);
}
