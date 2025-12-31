import { useEditorStore } from "@CodeSpace/Stores/EditorStore";
import { usePluginRegistry } from "@CodeSpace/Core/PluginRegistry";
import clsx from "clsx";
import { pluginRegistry } from "@CodeSpace/Core/PluginRegistry";

export default function StatusBar() {
	const { layout, activeFile } = useEditorStore();
	const statusItems = usePluginRegistry((reg) => reg.getStatusBarItems());

	if (!layout.statusBarVisible) return null;

	const context = { activeFile, type: "editor" };

	const visibleItems = statusItems.filter((item) => {
		if (item.when && !item.when(context)) return false;
		return true;
	});

	const leftItems = visibleItems.filter((i) => i.alignment === "left");
	const rightItems = visibleItems.filter((i) => i.alignment === "right");

	const handleClick = (item) => {
		if (item.command) {
			pluginRegistry.executeCommand(item.command);
		}
	};

	return (
		<div className="h-6 bg-[var(--ce-statusBar-background)] text-[var(--ce-statusBar-foreground)] border-t border-[var(--ce-statusBar-border)] flex items-center px-3 text-xs justify-between select-none">
			<div className="flex items-center gap-4">
				{leftItems.map((item, idx) => (
					<div
						key={item.id || idx}
						className={clsx(
							"flex items-center gap-1 cursor-pointer hover:bg-white/10 px-1 rounded",
							item.className,
						)}
						onClick={() => handleClick(item)}
					>
						{item.icon && <span className="mr-1">{item.icon}</span>}
						<span>{item.label}</span>
					</div>
				))}
			</div>
			<div className="flex items-center gap-4">
				{rightItems.map((item, idx) => (
					<div
						key={item.id || idx}
						className={clsx(
							"flex items-center gap-1 cursor-pointer hover:bg-white/10 px-1 rounded",
							item.className,
						)}
						onClick={() => handleClick(item)}
					>
						{item.icon && <span className="mr-1">{item.icon}</span>}
						<span>{item.label}</span>
					</div>
				))}
			</div>
		</div>
	);
}
