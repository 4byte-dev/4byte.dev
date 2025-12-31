import { useState } from "react";
import { useEditorStore } from "@CodeSpace/Stores/EditorStore";
import { TEMPLATES } from "@CodeSpace/Data";
import { Save, Upload, LayoutTemplate } from "lucide-react";
import { Modal, ModalContent, ModalTitle, ModalDescription } from "@/Components/Ui/Modal";
import { Button } from "@/Components/Ui/Form/Button";
import { Separator } from "@/Components/Ui/Separator";
import { useTranslation } from "react-i18next";

export default function ProjectView() {
	const { loadTemplate, layout, setModal, setQuickOpen } = useEditorStore();
	const templateModalOpen = layout.modals.template;
	const setTemplateModalOpen = (val) => setModal("template", val);
	const [selectedTemplate, setSelectedTemplate] = useState(null);
	const [message, setMessage] = useState(null);
	const { t } = useTranslation();

	const handleTemplateClick = (key) => {
		setSelectedTemplate(key);
		setTemplateModalOpen(true);
	};

	const confirmTemplateLoad = () => {
		if (selectedTemplate) {
			loadTemplate(selectedTemplate);
			setTemplateModalOpen(false);
			setMessage({ type: "success", text: "Template loaded!" });
			setTimeout(() => setMessage(null), 3000);
		}
	};

	const triggerSave = () => setQuickOpen(true, "save");
	const triggerLoad = () => setQuickOpen(true, "load");

	return (
		<div className="flex flex-col h-full bg-[var(--ce-sideBar-background)] text-[var(--ce-sideBar-foreground)]">
			<div className="p-4 space-y-4 overflow-y-auto flex-1">
				<div className="space-y-3">
					<h3 className="text-xs font-semibold opacity-70 uppercase tracking-wider">
						{t("Storage")}
					</h3>

					<div className="flex gap-2">
						<Button onClick={triggerSave} className="flex-1 gap-2" size="sm">
							<Save size={16} />
							{t("Save")}
						</Button>

						<Button
							onClick={triggerLoad}
							variant="secondary"
							className="flex-1 gap-2"
							size="sm"
						>
							<Upload size={16} />
							{t("Load")}
						</Button>
					</div>

					<div className="text-[10px] opacity-50 text-center">
						{t("Managed via Command Palette")}
					</div>

					{message && (
						<div
							className={`text-xs p-2 rounded ${
								message.type === "success"
									? "bg-green-500/20 text-green-400"
									: message.type === "error"
										? "bg-red-500/20 text-red-400"
										: "bg-blue-500/20 text-blue-400"
							}`}
						>
							{message.text}
						</div>
					)}
				</div>

				<Separator className="bg-[var(--ce-sideBar-border)] opacity-50" />

				<div className="space-y-3">
					<h3 className="text-xs font-semibold opacity-70 uppercase tracking-wider flex items-center gap-2">
						<LayoutTemplate size={14} /> {t("Templates")}
					</h3>

					<div className="grid grid-cols-1 gap-2">
						{Object.entries(TEMPLATES).map(([key, tpl]) => (
							<Button
								key={key}
								onClick={() => handleTemplateClick(key)}
								variant="outline"
								className="justify-start h-auto py-2 px-3 border-[var(--ce-sideBar-border)] hover:bg-[var(--ce-list-hoverBackground)]"
								title={tpl.name}
							>
								<span className="truncate">{tpl.name}</span>
							</Button>
						))}
					</div>
				</div>
			</div>

			<Modal open={templateModalOpen} onOpenChange={setTemplateModalOpen}>
				<ModalContent className="bg-[var(--ce-editor-background)] text-[var(--ce-foreground)] border-[var(--ce-panel-border)] max-w-sm">
					<ModalTitle>{t("Load Template?")}</ModalTitle>
					<ModalDescription>
						{t("Are you sure you want to load")}
						<strong>{TEMPLATES[selectedTemplate]?.name}</strong>?
						<br />
						<span className="text-red-400 mt-1 block text-xs">
							{t("This will overwrite all current changes.")}
						</span>
					</ModalDescription>

					<div className="flex justify-end gap-2 mt-4">
						<Button
							variant="ghost"
							onClick={() => setTemplateModalOpen(false)}
							className="text-[var(--ce-foreground)] hover:bg-[var(--ce-list-hoverBackground)]"
						>
							{t("Cancel")}
						</Button>
						<Button
							onClick={confirmTemplateLoad}
							className="bg-red-600 hover:bg-red-700 text-white"
						>
							{t("Load Template")}
						</Button>
					</div>
				</ModalContent>
			</Modal>
		</div>
	);
}
