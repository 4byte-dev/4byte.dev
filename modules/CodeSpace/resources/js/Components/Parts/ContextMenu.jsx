import { Edit, Trash2, FilePlus, FolderPlus } from "lucide-react";
import { useEffect, useRef, Fragment } from "react";
import { pluginRegistry, usePluginRegistry } from "@CodeSpace/Core/PluginRegistry";
import { useTranslation } from "react-i18next";

export function ContextMenu({ x, y, handleAction, onClose, context }) {
	const menuRef = useRef(null);
	const { t } = useTranslation();
	const registryItems = usePluginRegistry((reg) => reg.getMenuItems());
	useEffect(() => {
		const clickOutside = (e) => {
			if (menuRef.current && !menuRef.current.contains(e.target)) onClose();
		};
		document.addEventListener("mousedown", clickOutside);
		return () => document.removeEventListener("mousedown", clickOutside);
	}, [onClose]);

	const items = [
		{ label: t("Rename"), icon: Edit, action: "rename", show: true },
		{ label: t("Delete"), icon: Trash2, action: "delete", show: true, separator: true },
		{ label: t("New File"), icon: FilePlus, action: "new_file", show: true },
		{ label: t("New Folder"), icon: FolderPlus, action: "new_folder", show: true },
	];

	const pluginItems = registryItems.filter((item) => {
		if (item.context !== "explorer") return false;
		if (item.when && !item.when(context)) return false;
		return true;
	});

	const allItems = [...items, ...pluginItems];

	return (
		<div
			ref={menuRef}
			className="fixed z-50 bg-[#252526] border border-[#454545] shadow-2xl rounded py-1 min-w-[160px]"
			style={{ top: y, left: x }}
		>
			{allItems
				.filter((i) => i.show !== false)
				.map((item, idx) => (
					<Fragment key={idx}>
						<div
							onClick={() => {
								if (item.action) handleAction(item.action);
								if (item.command) pluginRegistry.executeCommand(item.command);
								onClose();
							}}
							className="flex items-center gap-2 px-3 py-1.5 text-xs text-gray-300 hover:bg-[#094771] hover:text-white cursor-pointer"
						>
							{item.icon && <item.icon size={13} />}
							<span>{item.label}</span>
						</div>
						{item.separator && <div className="h-[1px] bg-[#454545] my-1 mx-2" />}
					</Fragment>
				))}
		</div>
	);
}
