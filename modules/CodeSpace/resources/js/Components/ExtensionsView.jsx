import { useState } from "react";
import { usePluginRegistry } from "@CodeSpace/Core/PluginRegistry";
import { MoreHorizontal } from "lucide-react";
import { Input } from "@/Components/Ui/Form/Input";
import { Button } from "@/Components/Ui/Form/Button";
import { ScrollArea } from "@/Components/Ui/ScrollArea";
import { useTranslation } from "react-i18next";

export default function ExtensionsView() {
	const plugins = usePluginRegistry((reg) => reg.getAllPlugins());
	const [search, setSearch] = useState("");
	const { t } = useTranslation();

	const filteredPlugins = plugins.filter(
		(p) =>
			(p.name && p.name.toLowerCase().includes(search.toLowerCase())) ||
			(p.description && p.description.toLowerCase().includes(search.toLowerCase())),
	);

	return (
		<div className="flex flex-col h-full bg-[var(--ce-sideBar-background)] text-[var(--ce-sideBar-foreground)]">
			<div className="px-5 py-2.5 flex items-center justify-between text-[11px] font-medium uppercase tracking-wider text-[var(--ce-sideBarSectionHeader-foreground)] bg-[var(--ce-sideBarSectionHeader-background)]">
				<span>{t("Extensions")}</span>
				<MoreHorizontal size={16} className="cursor-pointer hover:text-white" />
			</div>

			<div className="px-4 pb-2">
				<Input
					placeholder={t("Search Extensions...")}
					value={search}
					onChange={(e) => setSearch(e.target.value)}
					className="bg-[var(--ce-input-background)] border-[var(--ce-input-border)] focus-visible:ring-1 focus-visible:ring-[var(--ce-focus-border)] text-[var(--ce-input-foreground)] h-8 text-xs"
				/>
			</div>

			{/* List */}
			<ScrollArea className="flex-1 [&>[data-radix-scroll-area-viewport]>div]:!block">
				<div className="pb-4">
					{filteredPlugins.map((plugin) => (
						<ExtensionItem key={plugin.id} plugin={plugin} />
					))}

					{filteredPlugins.length === 0 && (
						<div className="p-4 text-center text-sm text-gray-500">
							{t("No extensions found.")}
						</div>
					)}
				</div>
			</ScrollArea>
		</div>
	);
}

function ExtensionItem({ plugin }) {
	const isEnabled = usePluginRegistry((reg) => reg.isPluginEnabled(plugin.id));
	const reg = usePluginRegistry((reg) => reg);
	const { t } = useTranslation();

	const handleToggle = (e) => {
		e.stopPropagation();
		if (isEnabled) {
			reg.disablePlugin(plugin.id);
		} else {
			reg.enablePlugin(plugin.id);
		}
	};

	return (
		<div className="flex gap-3 p-3 hover:bg-[var(--ce-list-hoverBackground)] cursor-pointer group border-l-2 border-transparent hover:border-gray-500">
			<div className="w-10 h-10 bg-[#333] flex items-center justify-center shrink-0">
				{plugin.icon ? (
					plugin.icon
				) : (
					<div className="text-xs font-bold">{plugin.name.substring(0, 2)}</div>
				)}
			</div>

			<div className="flex-1 min-w-0">
				<div className="flex justify-between items-start">
					<div className="font-bold text-sm text-white truncate pr-2">{plugin.name}</div>
				</div>

				<div className="text-xs text-gray-500 truncate mb-1">{plugin.description}</div>

				<div className="flex items-center justify-between mt-1.5">
					<div className="text-[10px] text-gray-500 flex items-center gap-1">
						<span className="hover:text-blue-400 hover:underline">
							{plugin.publisher}
						</span>
						<span>v{plugin.version}</span>
					</div>

					<Button
						onClick={handleToggle}
						variant={isEnabled ? "secondary" : "default"}
						size="sm"
						className={
							isEnabled
								? "h-5 text-[10px] px-2 bg-[var(--ce-sideBar-background)] text-[var(--ce-sideBar-foreground)] hover:bg-gray-700"
								: "h-5 text-[10px] px-2 bg-[#007fd4] hover:bg-[#006bb3] text-white"
						}
					>
						{isEnabled ? t("Disable") : t("Enable")}
					</Button>
				</div>
			</div>
		</div>
	);
}
