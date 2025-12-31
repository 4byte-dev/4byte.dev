import { useEditorStore } from "@CodeSpace/Stores/EditorStore";
import { Trash2, X } from "lucide-react";
import clsx from "clsx";
import { Button } from "@/Components/Ui/Form/Button";
import { ScrollArea } from "@/Components/Ui/ScrollArea";
import { useTranslation } from "react-i18next";

export default function ConsolePanel() {
	const { consoleLogs, layout, togglePanel, setConsoleLogs } = useEditorStore();
	const { t } = useTranslation();

	if (!layout.panelVisible) return null;

	const formatTime = (ts) => {
		try {
			return new Date(ts || Date.now()).toLocaleTimeString();
		} catch (e) {
			console.error(e);
			return "";
		}
	};

	return (
		<div className="h-64 flex flex-col bg-[var(--ce-panel-background)] border-t border-[var(--ce-panel-border)] text-[var(--ce-foreground)] shadow-xl">
			<div className="flex items-center px-4 h-9 border-b border-[var(--ce-panel-border)] select-none">
				<div className="flex items-center gap-6 text-xs text-[var(--ce-panelTitle-inactiveForeground)]">
					<div className="text-[var(--ce-panelTitle-activeForeground)] border-b-2 border-[var(--ce-panelTitle-activeBorder)] uppercase font-bold px-1">
						{t("Console")}
					</div>
				</div>
				<div className="ml-auto flex items-center gap-2">
					<Button
						variant="ghost"
						size="icon"
						onClick={() => setConsoleLogs([])}
						title="Clear"
						className="h-6 w-6 text-[var(--ce-foreground)] opacity-60 hover:opacity-100 hover:bg-[var(--ce-list-hoverBackground)]"
					>
						<Trash2 size={14} />
					</Button>
					<Button
						variant="ghost"
						size="icon"
						onClick={() => togglePanel(false)}
						className="h-6 w-6 text-[var(--ce-foreground)] opacity-60 hover:opacity-100 hover:bg-[var(--ce-list-hoverBackground)]"
					>
						<X size={14} />
					</Button>
				</div>
			</div>

			<ScrollArea className="flex-1 font-mono text-xs">
				<div className="p-4 space-y-1">
					{consoleLogs.length === 0 && (
						<div className="italic opacity-50">{t("No logs yet...")}</div>
					)}
					{consoleLogs.map((log, i) => (
						<div
							key={i}
							className={clsx("border-b border-[var(--ce-panel-border)] pb-0.5", {
								"text-red-400": log.level === "error",
								"text-yellow-400": log.level === "warn",
								"text-blue-400": log.level === "info",
								"text-[var(--ce-foreground)] opacity-90":
									log.level === "log" || !log.level,
							})}
						>
							<span className="opacity-50 mr-2">[{formatTime(log.timestamp)}]</span>
							<span className="text-blue-500 mr-2">›</span>
							{Array.isArray(log.message)
								? log.message
										.map((arg) =>
											typeof arg === "object"
												? JSON.stringify(arg)
												: String(arg),
										)
										.join(" ")
								: typeof log.message === "object"
									? JSON.stringify(log.message)
									: String(log.message)}
						</div>
					))}
				</div>
			</ScrollArea>
		</div>
	);
}
