import { ChevronDown, ChevronRight, Folder, FolderOpen } from "lucide-react";
import clsx from "clsx";
import { Input } from "@/Components/Ui/Form/Input";

export function NodeRenderer({ getIcon, node, style, dragHandle, tree }) {
	const Icon =
		node.data.type === "folder"
			? node.isOpen
				? FolderOpen
				: Folder
			: () => getIcon(node.data.name);

	return (
		<div
			style={style}
			ref={dragHandle}
			onClick={() => (node.isInternal ? node.toggle() : node.select())}
			onContextMenu={(e) => {
				e.preventDefault();
				e.stopPropagation();
				node.select();
				tree.props.onContextMenu(e, node.id, node.data.type);
			}}
			className={clsx(
				"flex items-center gap-1.5 px-2 cursor-pointer select-none text-[13px] border-l-2 hover:bg-[#2a2d2e] transition-colors group",
				node.isSelected
					? "bg-[#37373d] text-white border-blue-500"
					: "border-transparent text-gray-400",
			)}
		>
			<span
				className="w-4 flex justify-center shrink-0 opacity-70"
				onClick={(e) => {
					e.stopPropagation();
					node.toggle();
				}}
			>
				{node.isInternal &&
					(node.isOpen ? <ChevronDown size={12} /> : <ChevronRight size={12} />)}
			</span>

			<Icon size={14} className={node.data.type === "folder" ? "text-blue-400" : ""} />

			{node.isEditing ? (
				<Input
					type="text"
					defaultValue={node.data.name}
					onClick={(e) => e.stopPropagation()}
					onFocus={(e) => e.currentTarget.select()}
					onBlur={() => node.reset()}
					onKeyDown={(e) => {
						if (e.key === "Enter") node.submit(e.currentTarget.value);
						if (e.key === "Escape") node.reset();
					}}
					autoFocus
					className="bg-[#3c3c3c] text-white border border-blue-500 focus-visible:ring-0 focus-visible:ring-offset-0 outline-none px-1 py-0.5 ml-1 w-full rounded-sm h-5 text-xs z-50"
				/>
			) : (
				<span className="truncate ml-1">{node.data.name}</span>
			)}
		</div>
	);
}
