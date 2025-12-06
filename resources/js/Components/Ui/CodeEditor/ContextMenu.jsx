import { Edit, Trash2, FilePlus, FolderPlus } from "lucide-react";
import { useEffect, useRef, Fragment } from "react";

export function ContextMenu({ x, y, handleAction, onClose }) {
	const menuRef = useRef(null);
	useEffect(() => {
		const clickOutside = (e) => {
			if (menuRef.current && !menuRef.current.contains(e.target)) onClose();
		};
		document.addEventListener("mousedown", clickOutside);
		return () => document.removeEventListener("mousedown", clickOutside);
	}, [onClose]);

	const items = [
		{ label: "Rename", icon: Edit, action: "rename", show: true },
		{ label: "Delete", icon: Trash2, action: "delete", show: true, separator: true },
		{ label: "New File", icon: FilePlus, action: "new_file", show: true },
		{ label: "New Folder", icon: FolderPlus, action: "new_folder", show: true },
	];

	return (
		<div
			ref={menuRef}
			className="fixed z-50 bg-[#252526] border border-[#454545] shadow-2xl rounded py-1 min-w-[160px]"
			style={{ top: y, left: x }}
		>
			{items
				.filter((i) => i.show)
				.map((item, idx) => (
					<Fragment key={idx}>
						<div
							onClick={() => {
								handleAction(item.action);
								onClose();
							}}
							className="flex items-center gap-2 px-3 py-1.5 text-xs text-gray-300 hover:bg-[#094771] hover:text-white cursor-pointer"
						>
							<item.icon size={13} />
							<span>{item.label}</span>
						</div>
						{item.separator && <div className="h-[1px] bg-[#454545] my-1 mx-2" />}
					</Fragment>
				))}
		</div>
	);
}
