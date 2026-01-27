import React from "react";
import { useTranslation } from "react-i18next";

export default function CodeSpaceState({ type, message, embed }) {
	const isError = type === "error" || type === "404";
	const { t } = useTranslation();

	const containerStyles = embed
		? "w-full min-h-[400px] h-full relative"
		: "fixed inset-0 w-screen h-screen z-50";

	return (
		<div
			className={`${containerStyles} flex flex-col items-center justify-center bg-[var(--ce-editor-background)] text-[var(--ce-foreground)] font-sans p-6`}
		>
			<style>
				{`
                @keyframes shimmer {
                    0% { opacity: 0.3; width: 30%; }
                    50% { opacity: 0.8; width: 80%; }
                    100% { opacity: 0.3; width: 30%; }
                }
                .ghost-line {
                    height: 12px;
                    background-color: var(--ce-sidebar-foreground);
                    opacity: 0.2;
                    margin-bottom: 8px;
                    border-radius: 4px;
                    animation: shimmer 2s infinite ease-in-out;
                }
                `}
			</style>

			<div className="w-full max-w-xl !p-8 border border-[var(--ce-activity-bar-border)] rounded-lg bg-[var(--ce-sidebar-background)] shadow-2xl relative overflow-hidden">
				<div className="absolute top-0 left-0 w-full h-8 bg-[var(--ce-title-bar-background)] flex items-center !px-3 !space-x-2 border-b border-[var(--ce-activity-bar-border)]">
					<div className="w-3 h-3 rounded-full bg-red-500/50"></div>
					<div className="w-3 h-3 rounded-full bg-yellow-500/50"></div>
					<div className="w-3 h-3 rounded-full bg-green-500/50"></div>
				</div>

				<div className="!mt-8 flex flex-col items-center text-center">
					{type === "loading" && (
						<div className="w-full space-y-3 px-4 py-6">
							<div className="flex space-x-2 items-center !mb-6 opacity-50 text-xs font-mono">
								<span className="text-[var(--ce-tab-active-foreground)]">&gt;</span>
								<span>{t("initializing_environment...")}</span>
							</div>

							<div className="flex flex-col items-start w-full font-mono text-sm">
								<div
									className="ghost-line"
									style={{ width: "40%", animationDelay: "0.1s" }}
								></div>
								<div
									className="ghost-line"
									style={{ width: "70%", animationDelay: "0.2s" }}
								></div>
								<div
									className="ghost-line"
									style={{ width: "55%", animationDelay: "0.3s" }}
								></div>
								<div className="h-2"></div>
								<div
									className="ghost-line"
									style={{ width: "30%", animationDelay: "0.4s" }}
								></div>
								<div className="h-2"></div>
								<div
									className="ghost-line"
									style={{ width: "80%", animationDelay: "0.5s" }}
								></div>
								<div
									className="ghost-line"
									style={{ width: "60%", animationDelay: "0.6s" }}
								></div>
							</div>
						</div>
					)}

					{isError && (
						<div className="py-6 flex flex-col items-center animate-in fade-in zoom-in duration-300">
							<div className="w-16 h-16 mb-4 rounded-full bg-[var(--ce-element-hover-background)] flex items-center justify-center text-[var(--ce-error-foreground)]">
								<svg
									xmlns="http://www.w3.org/2000/svg"
									fill="none"
									viewBox="0 0 24 24"
									strokeWidth={1.5}
									stroke="currentColor"
									className="w-8 h-8"
								>
									<path
										strokeLinecap="round"
										strokeLinejoin="round"
										d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"
									/>
								</svg>
							</div>

							<h3 className="text-xl font-semibold !mb-2 text-[var(--ce-foreground)]">
								{type === "404" ? t("CodeSpace Not Found") : t("Connection Error")}
							</h3>
							<p className="text-[var(--ce-description-foreground)] text-sm max-w-xs mx-auto !mb-6">
								{message ||
									(type === "404"
										? t(
												"The requested CodeSpace environment could not be located.",
											)
										: t(
												"Unable to establish a connection to the remote environment.",
											))}
							</p>

							{type === "404" && (
								<div className="px-4 py-2 text-xs font-mono bg-[var(--ce-editor-background)] rounded border border-[var(--ce-input-border)] text-[var(--ce-description-foreground)]">
									{t("Error: 404_RESOURCE_NOT_FOUND")}
								</div>
							)}
						</div>
					)}
				</div>
			</div>

			<div className="!mt-6 text-xs text-[var(--ce-description-foreground)] opacity-50 font-mono">
				{type === "loading" ? t("Compiling resources...") : t("System Halted")}
			</div>
		</div>
	);
}
