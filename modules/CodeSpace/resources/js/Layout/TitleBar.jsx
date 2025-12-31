import { useEditorStore } from "@CodeSpace/Stores/EditorStore";
import {
	DropdownMenu,
	DropdownMenuContent,
	DropdownMenuItem,
	DropdownMenuSeparator,
	DropdownMenuTrigger,
} from "@/Components/Ui/Form/DropdownMenu";
import { Button } from "@/Components/Ui/Form/Button";
import { Menu, X } from "lucide-react";
import { useTranslation } from "react-i18next";
import { useSiteStore } from "@/Stores/SiteStore";

export default function TitleBar() {
	const { setQuickOpen, toggleSidebar, togglePanel, layout, activeFile, name } = useEditorStore();
	const siteStore = useSiteStore();
	const { t } = useTranslation();

	const triggerSave = () => setQuickOpen(true, "save");
	const triggerLoad = () => setQuickOpen(true, "load");
	const triggerTheme = () => setQuickOpen(true, "theme");
	const triggerCommandPalette = () => {
		setQuickOpen(true, "default");
	};

	return (
		<div className="h-9 flex items-center bg-[var(--ce-activityBar-background)] border-b border-[var(--ce-activityBar-border)] text-[var(--ce-activityBar-foreground)] px-2 select-none">
			<div className="mr-4 flex items-center">
				<div className="md:hidden mr-2 cursor-pointer" onClick={toggleSidebar}>
					<Menu size={16} />
				</div>
				<span className="font-bold text-sm tracking-wide">
					{siteStore.getLogo() || siteStore.title}
				</span>
			</div>

			<div className="flex items-center">
				<DropdownMenu>
					<DropdownMenuTrigger asChild>
						<Button
							variant="ghost"
							size="sm"
							className="h-7 px-3 text-xs hover:bg-[var(--ce-list-hoverBackground)] hover:text-[var(--ce-list-hoverForeground)] data-[state=open]:bg-[var(--ce-list-activeSelectionBackground)] data-[state=open]:text-[var(--ce-list-activeSelectionForeground)] border-none rounded-none focus-visible:ring-0 ring-offset-0"
						>
							{t("File")}
						</Button>
					</DropdownMenuTrigger>
					<DropdownMenuContent
						align="start"
						className="min-w-[200px] bg-[var(--ce-editor-background)] border-[var(--ce-widget-shadow)] text-[var(--ce-foreground)]"
					>
						<DropdownMenuItem
							onClick={() => useEditorStore.getState().createFile("file", "")}
						>
							{t("New File")}
						</DropdownMenuItem>
						<DropdownMenuItem
							onClick={() => useEditorStore.getState().createFile("folder", "")}
						>
							{t("New Folder")}
						</DropdownMenuItem>
						<DropdownMenuSeparator className="bg-[var(--ce-panel-border)]" />
						<DropdownMenuItem onClick={triggerLoad}>
							{t("Open Project...")}
						</DropdownMenuItem>
						<DropdownMenuSeparator className="bg-[var(--ce-panel-border)]" />
						<DropdownMenuItem onClick={triggerSave}>
							{t("Save Project")}
						</DropdownMenuItem>
					</DropdownMenuContent>
				</DropdownMenu>

				<DropdownMenu>
					<DropdownMenuTrigger asChild>
						<Button
							variant="ghost"
							size="sm"
							className="h-7 px-3 text-xs hover:bg-[var(--ce-list-hoverBackground)] hover:text-[var(--ce-list-hoverForeground)] data-[state=open]:bg-[var(--ce-list-activeSelectionBackground)] data-[state=open]:text-[var(--ce-list-activeSelectionForeground)] border-none rounded-none focus-visible:ring-0 ring-offset-0"
						>
							{t("Edit")}
						</Button>
					</DropdownMenuTrigger>
					<DropdownMenuContent
						align="start"
						className="min-w-[200px] bg-[var(--ce-editor-background)] border-[var(--ce-widget-shadow)] text-[var(--ce-foreground)]"
					>
						<DropdownMenuItem disabled>{t("Undo")}</DropdownMenuItem>
						<DropdownMenuItem disabled>{t("Redo")}</DropdownMenuItem>
						<DropdownMenuSeparator className="bg-[var(--ce-panel-border)]" />
						<DropdownMenuItem disabled>{t("Cut")}</DropdownMenuItem>
						<DropdownMenuItem disabled>{t("Copy")}</DropdownMenuItem>
						<DropdownMenuItem disabled>{t("Paste")}</DropdownMenuItem>
					</DropdownMenuContent>
				</DropdownMenu>

				<DropdownMenu>
					<DropdownMenuTrigger asChild>
						<Button
							variant="ghost"
							size="sm"
							className="h-7 px-3 text-xs hover:bg-[var(--ce-list-hoverBackground)] hover:text-[var(--ce-list-hoverForeground)] data-[state=open]:bg-[var(--ce-list-activeSelectionBackground)] data-[state=open]:text-[var(--ce-list-activeSelectionForeground)] border-none rounded-none focus-visible:ring-0 ring-offset-0"
						>
							{t("View")}
						</Button>
					</DropdownMenuTrigger>
					<DropdownMenuContent
						align="start"
						className="min-w-[200px] bg-[var(--ce-editor-background)] border-[var(--ce-widget-shadow)] text-[var(--ce-foreground)]"
					>
						<DropdownMenuItem onClick={triggerCommandPalette}>
							{t("Command Palette...")}
						</DropdownMenuItem>
						<DropdownMenuItem onClick={triggerTheme}>Themes...</DropdownMenuItem>
						<DropdownMenuSeparator className="bg-[var(--ce-panel-border)]" />
						<DropdownMenuItem
							onClick={() =>
								useEditorStore.getState().setActiveSidebarView("explorer")
							}
						>
							{t("Explorer")}
						</DropdownMenuItem>
						<DropdownMenuItem
							onClick={() =>
								useEditorStore.getState().setActiveSidebarView("project")
							}
						>
							{t("Project")}
						</DropdownMenuItem>
						<DropdownMenuItem
							onClick={() =>
								useEditorStore.getState().setActiveSidebarView("extensions")
							}
						>
							{t("Extensions")}
						</DropdownMenuItem>
						<DropdownMenuSeparator className="bg-[var(--ce-panel-border)]" />
						<DropdownMenuItem onClick={togglePanel}>
							{layout.panelVisible ? t("Close Panel") : t("Open Panel")}
						</DropdownMenuItem>
						<DropdownMenuItem onClick={toggleSidebar}>
							{layout.sidebarVisible ? t("Close Sidebar") : "Open Sidebar"}
						</DropdownMenuItem>
					</DropdownMenuContent>
				</DropdownMenu>

				<DropdownMenu>
					<DropdownMenuTrigger asChild>
						<Button
							variant="ghost"
							size="sm"
							className="h-7 px-3 text-xs hover:bg-[var(--ce-list-hoverBackground)] hover:text-[var(--ce-list-hoverForeground)] data-[state=open]:bg-[var(--ce-list-activeSelectionBackground)] data-[state=open]:text-[var(--ce-list-activeSelectionForeground)] border-none rounded-none focus-visible:ring-0 ring-offset-0"
						>
							{t("Help")}
						</Button>
					</DropdownMenuTrigger>
					<DropdownMenuContent
						align="start"
						className="min-w-[200px] bg-[var(--ce-editor-background)] border-[var(--ce-widget-shadow)] text-[var(--ce-foreground)]"
					>
						<DropdownMenuItem
							onClick={() =>
								window.open("https://github.com/4byte-dev/4byte.dev", "_blank")
							}
						>
							{t("GitHub Repository")}
						</DropdownMenuItem>
					</DropdownMenuContent>
				</DropdownMenu>
			</div>

			<div className="flex-1 text-center text-xs opacity-70">
				{name ? `${name} · ${activeFile}` : activeFile}
			</div>

			<div className="flex items-center">
				<Button
					variant="ghost"
					size="sm"
					className="h-9 w-12 rounded-none hover:bg-red-500 hover:text-white"
					onClick={() => (window.location.href = "/")}
					title="Close Editor"
				>
					<X size={16} />
				</Button>
			</div>
		</div>
	);
}
