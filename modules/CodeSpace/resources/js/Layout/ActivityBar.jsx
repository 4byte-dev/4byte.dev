import { useEditorStore } from "@CodeSpace/Stores/EditorStore";
import { usePluginRegistry } from "@CodeSpace/Core/PluginRegistry";
import { useAuthStore } from "@/Stores/AuthStore";
import { useModalStore } from "@/Stores/ModalStore";
import clsx from "clsx";
import { Settings, Files, Blocks, FolderOpen, User } from "lucide-react";
import {
	DropdownMenu,
	DropdownMenuContent,
	DropdownMenuItem,
	DropdownMenuSeparator,
	DropdownMenuTrigger,
} from "@/Components/Ui/Form/DropdownMenu";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { useTranslation } from "react-i18next";

export default function ActivityBar() {
	const views = usePluginRegistry((reg) => reg.getViews("sidebar"));
	const { layout, setActiveSidebarView } = useEditorStore();
	const authStore = useAuthStore();
	const modalStore = useModalStore();
	const { t } = useTranslation();

	const isAuthenticated = authStore.isAuthenticated;
	const user = authStore.user;

	const SidebarIcon = ({ id, icon, title }) => (
		<div
			onClick={() => setActiveSidebarView(id)}
			className={clsx(
				"p-3 cursor-pointer text-[var(--ce-activityBar-inactiveForeground)] hover:text-[var(--ce-activityBar-foreground)] relative",
				layout.activeSiderbarView === id &&
					layout.sidebarVisible &&
					"text-[var(--ce-activityBar-activeForeground)]",
			)}
			title={title}
		>
			{layout.activeSiderbarView === id && layout.sidebarVisible && (
				<div className="absolute left-0 top-0 bottom-0 w-[2px] bg-[var(--ce-activityBar-activeBorder)]" />
			)}
			{icon}
		</div>
	);

	return (
		<div className="w-12 bg-[var(--ce-activityBar-background)] flex flex-col items-center py-2 flex-shrink-0 border-r border-[var(--ce-activityBar-border)]">
			<SidebarIcon
				id="explorer"
				icon={<Files size={24} strokeWidth={1.5} />}
				title={t("Explorer")}
			/>
			<SidebarIcon
				id="project"
				icon={<FolderOpen size={24} strokeWidth={1.5} />}
				title={t("Project & Templates")}
			/>

			<SidebarIcon
				id="extensions"
				icon={<Blocks size={24} strokeWidth={1.5} />}
				title={t("Extensions")}
			/>

			{views.map((view) => (
				<SidebarIcon key={view.id} id={view.id} icon={view.icon} title={view.name} />
			))}

			<div className="flex-1" />

			{isAuthenticated ? (
				<DropdownMenu>
					<DropdownMenuTrigger asChild>
						<div
							className="p-3 text-[var(--ce-activityBar-inactiveForeground)] hover:text-[var(--ce-activityBar-foreground)] cursor-pointer"
							title="User Account"
						>
							{user.avatar ? (
								<Avatar className="h-6 w-6">
									<AvatarImage src={user.avatar} />
									<AvatarFallback>
										{user.name?.charAt(0).toUpperCase() || "U"}
									</AvatarFallback>
								</Avatar>
							) : (
								<User size={24} strokeWidth={1.5} />
							)}
						</div>
					</DropdownMenuTrigger>
					<DropdownMenuContent
						side="right"
						align="end"
						sideOffset={10}
						className="w-56 bg-[var(--ce-editor-background)] border border-[var(--ce-panel-border)] text-[var(--ce-foreground)]"
					>
						<div className="flex items-center gap-3 p-2">
							<Avatar className="h-8 w-8">
								<AvatarImage src={user.avatar} />
								<AvatarFallback>
									{user.name?.charAt(0).toUpperCase() || "U"}
								</AvatarFallback>
							</Avatar>
							<div className="flex flex-col space-y-0.5">
								<p className="text-sm font-medium leading-none">{user.name}</p>
								<p className="text-xs text-muted-foreground opacity-70">
									@{user.username}
								</p>
							</div>
						</div>
						<DropdownMenuSeparator className="bg-[var(--ce-panel-border)]" />
						<DropdownMenuItem
							onClick={() =>
								window.open(
									route("user.view", { username: user.username }),
									"_blank",
								)
							}
						>
							{t("Profile")}
						</DropdownMenuItem>
						<DropdownMenuItem
							onClick={() => window.open(route("user.settings.view"), "_blank")}
						>
							{t("Settings")}
						</DropdownMenuItem>
					</DropdownMenuContent>
				</DropdownMenu>
			) : (
				<div
					className="p-3 text-[var(--ce-activityBar-inactiveForeground)] hover:text-[var(--ce-activityBar-foreground)] cursor-pointer"
					title="Login"
					onClick={() => modalStore.open("login")}
				>
					<User size={24} strokeWidth={1.5} />
				</div>
			)}

			<div
				className="p-3 text-[var(--ce-activityBar-inactiveForeground)] hover:text-[var(--ce-activityBar-foreground)] cursor-pointer"
				title="Settings"
				onClick={() => useEditorStore.getState().setModal("settings", true)}
			>
				<Settings size={24} strokeWidth={1.5} />
			</div>
		</div>
	);
}
