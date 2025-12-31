import { useRef, useMemo, useState } from "react";
import { Tree } from "react-arborist";
import { FilePlus, FolderPlus } from "lucide-react";
import { useEditorStore } from "@CodeSpace/Stores/EditorStore";
import { NodeRenderer } from "@CodeSpace/Components/Parts/NodeRenderer";
import { buildTreeData } from "@CodeSpace/Lib/TreeAdapter";
import { INITIAL_FILES } from "@CodeSpace/Data";
import { ContextMenu } from "@CodeSpace/Components/Parts/ContextMenu";
import { ScrollArea } from "@/Components/Ui/ScrollArea";
import { getFileIcon } from "@CodeSpace/Lib/FileIcons";
import { useTranslation } from "react-i18next";

export default function FileExplorer() {
	const { openFile, files, renameFile, deleteFile, createFile } = useEditorStore();
	const [contextMenu, setContextMenu] = useState(null);
	const treeRef = useRef(null);
	const { t } = useTranslation();
	const treeData = useMemo(() => buildTreeData(files || INITIAL_FILES), [files]);

	const handleRename = ({ id, name }) => {
		renameFile(id, name);
	};

	const handleDelete = ({ ids }) => {
		deleteFile(ids[0]);
	};

	const handleContextMenu = (e, path, type) => {
		e.preventDefault();
		e.stopPropagation();
		setContextMenu({
			x: e.clientX,
			y: e.clientY,
			path: path || null,
			type: type || "background",
		});
	};

	const handleContainerContextMenu = (e) => {
		e.preventDefault();
		setContextMenu({ x: e.clientX, y: e.clientY, path: null, type: "background" });
	};

	const handleContextAction = (action) => {
		if (!contextMenu || !contextMenu.path) return;
		const { path } = contextMenu;

		if (action === "rename") {
			if (treeRef.current) treeRef.current.edit(path);
		}
		if (action === "delete") deleteFile(path);
		if (action === "new_file") createFile("file", path);
		if (action === "new_folder") createFile("folder", path);

		setContextMenu(null);
	};

	return (
		<div className="flex flex-col h-full bg-[var(--ce-sideBar-background)]">
			<div className="flex items-center justify-between px-4 py-2 text-[11px] font-bold text-[var(--ce-sideBarSectionHeader-foreground)] uppercase tracking-wider bg-[var(--ce-sideBarSectionHeader-background)]">
				<span>{t("Explorer")}</span>
				<div className="flex gap-1">
					<FilePlus
						size={14}
						className="hover:text-white cursor-pointer"
						onClick={() => createFile("file", "")}
					/>
					<FolderPlus
						size={14}
						className="hover:text-white cursor-pointer"
						onClick={() => createFile("folder", "")}
					/>
				</div>
			</div>
			<ScrollArea
				className="flex-1 pl-2 pt-2 text-[var(--ce-sideBar-foreground)]"
				onClick={() => setContextMenu(null)}
				onContextMenu={handleContainerContextMenu}
			>
				<Tree
					ref={treeRef}
					data={treeData}
					width={250}
					height={600}
					indent={12}
					rowHeight={24}
					paddingTop={5}
					paddingBottom={10}
					onActivate={(node) => openFile(node.id)}
					onRename={handleRename}
					onDelete={handleDelete}
					onContextMenu={handleContextMenu}
				>
					{(props) => <NodeRenderer {...props} getIcon={getFileIcon} />}
				</Tree>
			</ScrollArea>
			{contextMenu && (
				<ContextMenu
					x={contextMenu.x}
					y={contextMenu.y}
					handleAction={handleContextAction}
					onClose={() => setContextMenu(null)}
					context={{ path: contextMenu.path, type: contextMenu.type }}
				/>
			)}
		</div>
	);
}
