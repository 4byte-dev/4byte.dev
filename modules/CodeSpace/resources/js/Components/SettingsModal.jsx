import { useEffect, useState } from "react";
import { useEditorStore } from "@CodeSpace/Stores/EditorStore";
import { usePluginRegistry } from "@CodeSpace/Core/PluginRegistry";
import {
	Dialog,
	DialogContent,
	DialogDescription,
	DialogHeader,
	DialogTitle,
} from "@/Components/Ui/Dialog";
import { Input } from "@/Components/Ui/Form/Input";
import { Label } from "@/Components/Ui/Form/Label";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/Components/Ui/Tabs";
import { Switch } from "@/Components/Ui/Form/Switch";
import { useTranslation } from "react-i18next";

export default function SettingsModal() {
	const { layout, setModal, settings, setSetting, loadSettings } = useEditorStore();
	const isOpen = layout.modals.settings || false;
	const { t } = useTranslation();
	const [activeTab, setActiveTab] = useState(t("Editor"));

	const registrySettings = usePluginRegistry((reg) => reg.getSettings());
	const categories = Array.from(new Set(registrySettings.map((s) => s.category || t("General"))));

	useEffect(() => {
		if (Object.keys(settings).length === 0 && registrySettings.length > 0) {
			loadSettings(registrySettings);
		}
	}, [registrySettings.length]);

	const renderSettingInput = (def) => {
		const val = settings[def.id] !== undefined ? settings[def.id] : def.default;

		switch (def.type) {
			case "boolean":
				return (
					<div className="flex items-center space-x-2">
						<Switch
							id={def.id}
							checked={val}
							onCheckedChange={(checked) => setSetting(def.id, checked)}
						/>
						<Label htmlFor={def.id} className="cursor-pointer">
							{val ? t("Enabled") : t("Disabled")}
						</Label>
					</div>
				);
			case "number":
				return (
					<Input
						type="number"
						value={val === "" || (typeof val === "number" && isNaN(val)) ? "" : val}
						onChange={(e) => {
							const v = e.target.value;
							if (v === "") setSetting(def.id, "");
							else setSetting(def.id, parseFloat(v));
						}}
						min={def.min}
						max={def.max}
						className="w-32 bg-[var(--ce-input-background)] border-[var(--ce-input-border)] text-[var(--ce-input-foreground)]"
					/>
				);
			case "select":
				return (
					<select
						value={val || def.default}
						onChange={(e) => setSetting(def.id, e.target.value)}
						className="bg-[var(--ce-input-background)] border-[var(--ce-input-border)] text-[var(--ce-input-foreground)] h-9 rounded-md px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-[var(--ce-focus-border)] disabled:cursor-not-allowed disabled:opacity-50"
					>
						{def.options &&
							def.options.map((opt) => (
								<option key={opt.value} value={opt.value}>
									{opt.label}
								</option>
							))}
					</select>
				);
			default:
				return (
					<Input
						type="text"
						value={val || ""}
						onChange={(e) => setSetting(def.id, e.target.value)}
						className="bg-[var(--ce-input-background)] border-[var(--ce-input-border)] text-[var(--ce-input-foreground)]"
					/>
				);
		}
	};

	return (
		<Dialog open={isOpen} onOpenChange={(open) => setModal("settings", open)}>
			<DialogContent className="sm:max-w-[600px] h-[500px] flex flex-col bg-[var(--ce-editor-background)] border-[var(--ce-widget-shadow)] text-[var(--ce-foreground)] p-0 gap-0 overflow-hidden">
				<DialogHeader className="p-4 border-b border-[var(--ce-panel-border)] bg-[var(--ce-panel-background)]">
					<DialogTitle>{t("Settings")}</DialogTitle>
					<DialogDescription className="text-[var(--ce-descriptionForeground)]">
						{t("Manage editor preferences. Changes are saved automatically.")}
					</DialogDescription>
				</DialogHeader>

				<div className="flex flex-1 overflow-hidden">
					<Tabs
						value={activeTab}
						onValueChange={setActiveTab}
						orientation="vertical"
						className="flex flex-1 w-full"
					>
						<div className="w-1/4 border-r border-[var(--ce-panel-border)] bg-[var(--ce-sidebar-background)] overflow-y-auto p-2">
							<TabsList className="flex flex-col h-auto bg-transparent gap-1 w-full justify-start space-y-1">
								{categories.map((cat) => (
									<TabsTrigger
										key={cat}
										value={cat}
										className="w-full justify-start px-3 py-1.5 data-[state=active]:bg-[var(--ce-list-activeSelectionBackground)] data-[state=active]:text-[var(--ce-list-activeSelectionForeground)] font-normal text-sm"
									>
										{cat}
									</TabsTrigger>
								))}
							</TabsList>
						</div>

						<div className="flex-1 w-3/4 p-6 overflow-y-auto bg-[var(--ce-editor-background)]">
							{categories.map((cat) => (
								<TabsContent key={cat} value={cat} className="mt-0 space-y-6">
									{registrySettings
										.filter((s) => (s.category || "General") === cat)
										.map((setting) => (
											<div
												key={setting.id}
												className="flex flex-col space-y-2"
											>
												<Label
													htmlFor={setting.id}
													className="text-base font-medium"
												>
													{setting.label}
												</Label>
												{setting.description && (
													<p className="text-sm text-muted-foreground opacity-70 mb-2">
														{setting.description}
													</p>
												)}
												{renderSettingInput(setting)}
											</div>
										))}
								</TabsContent>
							))}
						</div>
					</Tabs>
				</div>
			</DialogContent>
		</Dialog>
	);
}
