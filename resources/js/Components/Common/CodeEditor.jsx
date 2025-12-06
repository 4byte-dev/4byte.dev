import React, { useState, useEffect, useRef, useMemo, useCallback } from "react";
import Editor from "@monaco-editor/react";
import { Tree } from "react-arborist";
import {
	Play,
	FileCode,
	FileJson,
	FileType,
	Trash2,
	FilePlus,
	X,
	Circle,
	FolderPlus,
	Save,
} from "lucide-react";
import clsx from "clsx";
import { INITIAL_FILES, TEMPLATES } from "@/Data/CodeEditor";
import { NodeRenderer } from "@/Components/Ui/CodeEditor/NodeRenderer";
import { ContextMenu } from "@/Components/Ui/CodeEditor/ContextMenu";
import { buildTreeData } from "@/Lib/TreeAdapter";
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from "../Ui/Form/DropdownMenu";
import { Button } from "@/Components/Ui/Form/Button";

const getIcon = (name) => {
	if (name.endsWith(".html")) return <FileCode size={14} className="text-orange-500" />;
	if (name.endsWith(".css")) return <FileType size={14} className="text-blue-400" />;
	if (name.endsWith(".js") || name.endsWith(".jsx"))
		return <FileJson size={14} className="text-yellow-400" />;
	return <FileCode size={14} className="text-gray-400" />;
};

export default function CodeEditor({ isEmbed = false, defaultOpenFiles = ["index.html"] }) {
	const [files, setFiles] = useState(INITIAL_FILES);
	const [buffers, setBuffers] = useState({});
	const [activeFile, setActiveFile] = useState(defaultOpenFiles[0] || null);
	const [openFiles, setOpenFiles] = useState(defaultOpenFiles);
	const [contextMenu, setContextMenu] = useState(null);
	const [consoleLogs, setConsoleLogs] = useState([]);
	const [iframeSrc, setIframeSrc] = useState("");

	const treeRef = useRef(null);

	const treeData = useMemo(() => buildTreeData(files), [files]);

	const loadTemplate = (templateKey) => {
		const template = TEMPLATES[templateKey];
		if (!template) return;

		if (confirm("Unsaved changes will be lost. Load template?")) {
			setFiles(template.files);
			const firstFile = Object.keys(template.files)[0];
			setOpenFiles([firstFile]);
			setActiveFile(firstFile);
			setBuffers({});
		}
	};

	const handleRename = (oldPath, newName) => {
		const parts = oldPath.split("/");
		parts.pop();
		const parentPath = parts.join("/");
		const newPath = parentPath ? `${parentPath}/${newName}` : newName;

		if (oldPath === newPath) return;
		if (files[newPath]) {
			alert("File already exists!");
			return;
		}

		performMoveOrRename(oldPath, newPath);
	};

	const handleMove = ({ dragIds, parentId }) => {
		const oldPath = dragIds[0];
		const fileName = oldPath.split("/").pop();
		const newPath = parentId ? `${parentId}/${fileName}` : fileName;

		if (oldPath === newPath) return;
		if (files[newPath]) return;

		performMoveOrRename(oldPath, newPath);
	};

	const performMoveOrRename = (oldPath, newPath) => {
		const newFiles = {};
		const newBuffers = {};

		Object.keys(files).forEach((path) => {
			if (path === oldPath) {
				newFiles[newPath] = { ...files[path], name: newPath.split("/").pop() };
				if (buffers[path]) newBuffers[newPath] = buffers[path];
			} else if (path.startsWith(oldPath + "/")) {
				const suffix = path.substring(oldPath.length);
				const targetPath = newPath + suffix;
				newFiles[targetPath] = { ...files[path], name: targetPath.split("/").pop() };
				if (buffers[path]) newBuffers[targetPath] = buffers[path];
			} else {
				newFiles[path] = files[path];
				if (buffers[path]) newBuffers[path] = buffers[path];
			}
		});

		setFiles(newFiles);
		setBuffers((prev) => {
			const next = { ...prev };
			Object.keys(prev).forEach((k) => {
				if (k.startsWith(oldPath)) delete next[k];
			});
			return { ...next, ...newBuffers };
		});

		setOpenFiles((prev) =>
			prev.map((p) => {
				if (p === oldPath) return newPath;
				if (p.startsWith(oldPath + "/")) return newPath + p.substring(oldPath.length);
				return p;
			}),
		);

		if (activeFile === oldPath) setActiveFile(newPath);
		else if (activeFile?.startsWith(oldPath + "/"))
			setActiveFile(newPath + activeFile.substring(oldPath.length));
	};

	const handleDelete = (path) => {
		if (!path) return;

		if (!confirm(`Delete '${path}'?`)) return;

		const newFiles = { ...files };
		Object.keys(files).forEach((k) => {
			if (k === path || k.startsWith(path + "/")) delete newFiles[k];
		});
		setFiles(newFiles);
		setOpenFiles((prev) => prev.filter((p) => p !== path && !p.startsWith(path + "/")));
		if (activeFile === path || activeFile?.startsWith(path + "/")) setActiveFile(null);
	};

	const handleCreate = (type) => {
		const base = contextMenu?.path
			? contextMenu.type === "folder"
				? contextMenu.path
				: contextMenu.path.split("/").slice(0, -1).join("/")
			: "";

		let tempName = type === "new_folder" ? "New Folder" : "untitled";
		let counter = 1;

		while (files[base ? `${base}/${tempName}` : tempName]) {
			tempName = `${type === "new_folder" ? "New Folder" : "untitled"}-${counter}`;
			counter++;
		}

		const newPath = base ? `${base}/${tempName}` : tempName;

		if (type === "new_file") {
			setFiles((prev) => ({
				...prev,
				[newPath]: { name: tempName, language: "plaintext", content: "" },
			}));
		} else {
			const keepPath = `${newPath}/.gitkeep`;
			setFiles((prev) => ({
				...prev,
				[keepPath]: { name: ".gitkeep", language: "plaintext", content: "" },
			}));
		}

		treeRef.current.edit(newPath);
	};

	const handleContextMenu = (e, path, type) => {
		e.preventDefault();
		setContextMenu({ x: e.clientX, y: e.clientY, path, type });
	};

	const handleContextAction = (action) => {
		if (!contextMenu || !contextMenu.path) return;

		const { path } = contextMenu;

		if (action === "rename") {
			if (treeRef.current) treeRef.current.edit(path);
		}
		if (action === "delete") handleDelete(path);
		if (action === "new_file") handleCreate("new_file");
		if (action === "new_folder") handleCreate("new_folder");

		setContextMenu(null);
	};

	const handleOpenFile = (path) => {
		if (!files[path] || files[path]?.type === "folder" || path.endsWith(".gitkeep")) return;
		if (!openFiles.includes(path)) setOpenFiles([...openFiles, path]);
		setActiveFile(path);
	};

	const saveFile = useCallback(() => {
		if (!activeFile || buffers[activeFile] === undefined) return;
		setFiles((prev) => ({
			...prev,
			[activeFile]: { ...prev[activeFile], content: buffers[activeFile] },
		}));
		setBuffers((prev) => {
			const n = { ...prev };
			delete n[activeFile];
			return n;
		});
	}, [activeFile, buffers]);

	useEffect(() => {
		const entry = files["index.html"];
		if (!entry) return;
		let html = entry.content;

		html = html.replace(/<link[^>]+href=["'](.*?)["'][^>]*>/g, (match, path) => {
			const file = files[path.replace(/^\.\//, "")];
			return file ? `<style>${file.content}</style>` : match;
		});

		const processJS = (content) =>
			content.replace(/from\s+['"](.*?)['"]/g, (_, p) => {
				const key = Object.keys(files).find((k) => k.endsWith(p.replace(/^\.\//, "")));
				if (key) {
					return `from "${URL.createObjectURL(new Blob([files[key].content], { type: "application/javascript" }))}"`;
				}
				return `from "${p}"`;
			});

		html = html.replace(/<script[^>]+src=["'](.*?)["'][^>]*><\/script>/g, (match, p) => {
			const key = p.replace(/^\.\//, "");
			const file = files[key];
			if (!file) return match;

			if (key.endsWith(".jsx")) {
				return `<script type="text/babel" data-type="module">${processJS(file.content)}</script>`;
			}

			return `<script type="module">${processJS(file.content)}</script>`;
		});

		const proxy = `<script>
	const _log = console.log;
	console.log = (...args) => { window.parent.postMessage({type:'console', args}, '*'); _log(...args); }
</script>`;
		setIframeSrc(html + proxy);
	}, [files]);

	useEffect(() => {
		const handler = (e) => e.data.type === "console" && setConsoleLogs((p) => [...p, e.data]);
		window.addEventListener("message", handler);
		return () => window.removeEventListener("message", handler);
	}, []);

	useEffect(() => {
		const handleKey = (e) => {
			if ((e.ctrlKey || e.metaKey) && e.key === "s") {
				e.preventDefault();
				saveFile();
			}
		};
		window.addEventListener("keydown", handleKey);
		return () => window.removeEventListener("keydown", handleKey);
	}, [saveFile]);

	const activeContent = activeFile ? (buffers[activeFile] ?? files[activeFile]?.content) : "";
	const isDirty = (path) => buffers[path] !== undefined && buffers[path] !== files[path]?.content;

	return (
		<div
			className={clsx(
				"flex flex-col bg-[#1e1e1e] text-gray-300 border border-gray-700 rounded-lg shadow-2xl overflow-hidden font-sans select-none",
				isEmbed ? "h-[400px] max-h-[100vh] w-full" : "h-[700px] w-full",
			)}
			onClick={() => setContextMenu(null)}
		>
			{!isEmbed && (
				<div className="flex items-center justify-between px-3 py-2 bg-[#2d2d2d] border-b border-black/20">
					<div className="flex items-center gap-2">
						<span className="ml-3 text-xs text-gray-400 font-medium">VS Code Pro</span>
						<div className="relative relative z-50">
							<DropdownMenu>
								<DropdownMenuTrigger asChild>
									<Button
										variant="ghost"
										className="text-xs px-2 py-0.5 text-gray-300 hover:bg-transparent"
									>
										File
									</Button>
								</DropdownMenuTrigger>
								<DropdownMenuContent align="middle" className="w-48 z-50 p-0">
									<div className="bg-[#252526] border border-[#454545] shadow-xl p-1 rounded flex flex-col">
										<div className="px-3 py-1 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
											Start New
										</div>

										{Object.keys(TEMPLATES).map((key) => (
											<button
												key={key}
												onClick={() => loadTemplate(key)}
												className="text-left px-4 py-1.5 text-xs text-gray-300 hover:bg-[#094771] hover:text-white flex items-center gap-2"
											>
												<FileCode size={12} />
												{TEMPLATES[key].name}
											</button>
										))}

										<div className="h-[1px] bg-[#454545] my-1 mx-2" />

										<button
											onClick={saveFile}
											className="text-left px-4 py-1.5 text-xs text-gray-300 hover:bg-[#094771] hover:text-white flex items-center gap-2"
										>
											<Save size={12} /> Save Project
										</button>
									</div>
								</DropdownMenuContent>
							</DropdownMenu>
						</div>
					</div>
					<div className="flex gap-2 text-xs">
						<Button
							variant="ghost"
							onClick={saveFile}
							className="flex items-center gap-1 hover:bg-transparent p-1 text-blue-400 hover:text-blue-300"
						>
							<Save size={12} /> Save
						</Button>
						<Button
							variant="ghost"
							onClick={() => setFiles({ ...files })}
							className="flex items-center gap-1 hover:bg-transparent p-1 text-green-500 hover:text-green-400"
						>
							<Play size={12} /> Run
						</Button>
					</div>
				</div>
			)}

			<div className="flex flex-1 overflow-hidden">
				{!isEmbed && (
					<div
						className="w-64 bg-[#252526] flex flex-col border-r border-black/20"
						onContextMenu={(e) => handleContextMenu(e, "", "folder")}
					>
						<div className="flex items-center justify-between px-4 py-2 text-[11px] font-bold text-gray-400 uppercase tracking-wider bg-[#252526]">
							<span>Explorer</span>
							<div className="flex gap-1">
								<FilePlus
									size={14}
									className="hover:text-white cursor-pointer"
									onClick={(e) => {
										e.stopPropagation();
										handleCreate("new_file");
									}}
								/>
								<FolderPlus
									size={14}
									className="hover:text-white cursor-pointer"
									onClick={(e) => {
										e.stopPropagation();
										handleCreate("new_folder");
									}}
								/>
							</div>
						</div>

						<div className="flex-1 overflow-hidden pl-2 pt-2">
							<Tree
								ref={treeRef}
								data={treeData}
								width={250}
								height={600}
								indent={12}
								rowHeight={24}
								paddingTop={5}
								paddingBottom={10}
								onActivate={(node) => handleOpenFile(node.id)}
								onRename={({ id, name }) => handleRename(id, name)}
								onDelete={({ ids }) => handleDelete(ids[0])}
								onMove={handleMove}
								onContextMenu={handleContextMenu}
							>
								{(props) => <NodeRenderer {...props} getIcon={getIcon} />}
							</Tree>
						</div>
					</div>
				)}

				<div className="flex-1 flex flex-col min-w-0 bg-[#1e1e1e]">
					<div className="flex bg-[#252526] overflow-x-auto h-9 border-b border-black/20 hide-scrollbar">
						{openFiles.map((path) => (
							<div
								key={path}
								onClick={() => setActiveFile(path)}
								className={clsx(
									"flex items-center gap-2 px-3 min-w-[120px] max-w-[200px] text-xs cursor-pointer border-r border-[#1e1e1e] group relative",
									activeFile === path
										? "bg-[#1e1e1e] text-white"
										: "text-gray-400 hover:bg-[#2a2d2e]",
								)}
							>
								{activeFile === path && (
									<div className="absolute top-0 left-0 w-full h-[1px] bg-blue-500" />
								)}
								<span className="truncate flex-1 flex items-center gap-1.5">
									{getIcon(path)} {path.split("/").pop()}
								</span>
								<div
									onClick={(e) => {
										e.stopPropagation();
										const n = openFiles.filter((f) => f !== path);
										setOpenFiles(n);
										if (activeFile === path) setActiveFile(n[n.length - 1]);
									}}
									className={clsx(
										"p-0.5 hover:bg-gray-700 rounded",
										activeFile !== path && "opacity-0 group-hover:opacity-100",
									)}
								>
									{isDirty(path) ? (
										<Circle size={8} className="text-white fill-white" />
									) : (
										<X size={12} />
									)}
								</div>
							</div>
						))}
					</div>

					<div
						className={clsx(
							"flex-1 flex min-h-0",
							isEmbed ? "flex-col lg:flex-row" : "flex-row",
						)}
					>
						<div
							className={clsx(
								"relative border-r border-black/20",
								isEmbed ? "flex-1 min-h-[50%] lg:min-h-0 lg:w-1/2" : "flex-1",
							)}
						>
							{activeFile ? (
								<Editor
									height="100%"
									theme="vs-dark"
									path={activeFile}
									defaultLanguage={files[activeFile]?.language}
									value={activeContent}
									onChange={(val) =>
										setBuffers((prev) => ({ ...prev, [activeFile]: val }))
									}
									options={{
										minimap: { enabled: false },
										fontSize: 13,
										fontFamily: "'JetBrains Mono', Consolas, monospace",
										padding: { top: 16 },
									}}
								/>
							) : (
								<div className="h-full flex items-center justify-center text-gray-500 text-sm">
									Select a file
								</div>
							)}
						</div>

						<div
							className={clsx(
								"flex flex-col bg-white",
								isEmbed ? "lg:w-1/2 min-h-[50%] lg:min-h-0" : "w-[40%]",
							)}
						>
							<iframe
								title="p"
								srcDoc={iframeSrc}
								className="flex-1 w-full border-none"
								sandbox="allow-scripts allow-modals allow-same-origin"
							/>
							<div className="h-32 bg-[#1e1e1e] border-t border-gray-700 flex flex-col">
								<div className="px-2 py-1 bg-[#252526] text-[10px] uppercase font-bold text-gray-400 flex justify-between">
									<span>Console</span>{" "}
									<Trash2
										size={12}
										className="cursor-pointer hover:text-white"
										onClick={() => setConsoleLogs([])}
									/>
								</div>
								<div className="flex-1 p-2 font-mono text-xs overflow-auto text-gray-300">
									{consoleLogs.map((l, i) => (
										<div
											key={i}
											className="border-b border-white/10 pb-0.5 mb-0.5"
										>
											<span className="text-blue-500">›</span>{" "}
											{l.args.join(" ")}
										</div>
									))}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			{contextMenu && (
				<ContextMenu
					x={contextMenu.x}
					y={contextMenu.y}
					type={contextMenu.type}
					handleAction={handleContextAction}
					onClose={() => setContextMenu(null)}
				/>
			)}
		</div>
	);
}
