import { Input } from "@/Components/Ui/Form/Input";
import { $view } from "@milkdown/kit/utils";
import Api from "@Modules/CodeSpace/resources/js/Api";
import {
	ArrowRight,
	Code2,
	FileCode2,
	FolderOpen,
	Loader2,
	Search,
	Terminal,
	X,
} from "lucide-react";
import { useEffect, useState } from "react";
import { Trans, useTranslation } from "react-i18next";
import { codeSpaceSchema } from "./Schema";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { createRoot } from "react-dom/client";
import CodeSpacePage from "@Modules/CodeSpace/resources/js/Pages/CodeSpace/Detail";
import { ThemeProvider } from "@/Contexts/ThemeContext";

const ApiSelector = ({ onSelect, onCancel }) => {
	const [search, setSearch] = useState("");
	const [items, setItems] = useState([]);
	const [loading, setLoading] = useState(false);
	const [error, setError] = useState(null);
	const { t } = useTranslation();

	const fetchApiData = async () => {
		setLoading(true);
		setError(null);
		Api.listProjects()
			.then((data) => {
				const formattedItems = data.map((p) => ({
					id: p.id,
					slug: p.slug,
					label: p.name || "Untitled Project",
					description: new Date(p.updated_at).toLocaleDateString("tr-TR"),
				}));
				setItems(formattedItems);
				setLoading(false);
			})
			.catch((err) => {
				setError("Failed to load projects.");
				console.error(err);
			});
	};

	useEffect(() => {
		fetchApiData();
	}, []);

	const filteredItems = items.filter(
		(item) =>
			item.label.toLowerCase().includes(search.toLowerCase()) ||
			item.slug.toLowerCase().includes(search.toLowerCase()),
	);

	return (
		<div className="api-selector-wrapper w-full max-w-2xl !mx-auto !my-4">
			<div className="bg-card border border-border rounded-xl shadow-lg ring-1 ring-border/50 overflow-hidden">
				<div className="!px-5 !py-4 border-b border-border bg-muted/30 flex items-center justify-between">
					<div className="flex items-center gap-2.5">
						<div className="!p-2 bg-primary/10 rounded-lg text-primary">
							<Terminal className="w-5 h-5" />
						</div>
						<div>
							<h3 className="text-sm font-semibold text-foreground">
								{t("Embed CodeSpace")}
							</h3>
							<p className="text-xs text-muted-foreground">
								{t("Select a project to embed in your content")}
							</p>
						</div>
					</div>
				</div>

				<div className="!p-4 border-b border-border bg-card">
					<div className="relative">
						<Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
						<Input
							type="text"
							className="!pl-9 h-11 bg-muted/50 focus:bg-background transition-colors"
							placeholder={t("Search projects or enter slug...")}
							value={search}
							onChange={(e) => setSearch(e.target.value)}
							onKeyDown={(e) => {
								if (e.key === "Enter" && search) {
									onSelect(search);
								}
							}}
							autoFocus
						/>
					</div>
				</div>

				<div className="max-h-[320px] overflow-y-auto !p-2 space-y-1 bg-card scrollbar-thin scrollbar-thumb-border scrollbar-track-transparent">
					{loading ? (
						<div className="!py-12 flex flex-col items-center gap-3 text-muted-foreground">
							<Loader2 className="w-8 h-8 animate-spin text-primary/50" />
							<span className="text-sm">{t("Loading projects...")}</span>
						</div>
					) : error ? (
						<div className="!py-12 flex flex-col items-center gap-3 text-destructive">
							<X className="w-8 h-8 opacity-50" />
							<span className="text-sm font-medium">{error}</span>
						</div>
					) : filteredItems.length > 0 ? (
						filteredItems.map((item) => (
							<button
								key={item.id}
								onClick={() => onSelect(item.slug)}
								className="w-full flex items-center justify-between !p-3 rounded-lg
                                    hover:bg-accent hover:text-accent-foreground group transition-all duration-200
                                    border border-transparent hover:border-border/50"
							>
								<div className="flex items-center gap-3 text-left">
									<div className="!p-2 rounded-md bg-muted text-muted-foreground group-hover:bg-background group-hover:text-foreground transition-colors">
										<FileCode2 className="w-4 h-4" />
									</div>
									<div>
										<div className="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
											{item.label}
										</div>
										<div className="flex items-center gap-2 !mt-0.5">
											<code className="text-[10px] px-1.5 !py-0.5 rounded bg-muted font-mono text-muted-foreground">
												{item.slug}
											</code>
											<span className="text-[10px] text-muted-foreground/60">
												• {item.description}
											</span>
										</div>
									</div>
								</div>
								<ArrowRight className="w-4 h-4 text-muted-foreground/30 -translate-x-2 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all" />
							</button>
						))
					) : (
						<div className="!py-12 flex flex-col items-center gap-3 text-muted-foreground">
							<div className="!p-3 bg-muted rounded-full opacity-50">
								<FolderOpen className="w-8 h-8" />
							</div>
							<span className="text-sm">{t("No projects found")}</span>
							{search && (
								<button
									onClick={() => onSelect(search)}
									className="!mt-2 text-xs text-primary hover:underline font-medium flex items-center gap-1"
								>
									<Trans
										i18nKey="create_embed_with_slug"
										values={{ slug: search }}
										components={{
											code: (
												<code className="bg-muted !px-1 !py-0.5 rounded" />
											),
										}}
									/>
								</button>
							)}
						</div>
					)}
				</div>

				<div className="!px-5 !py-3 bg-muted/30 border-t border-border flex items-center justify-between text-xs text-muted-foreground">
					<div className="flex items-center gap-1.5">
						<kbd className="pointer-events-none inline-flex h-5 select-none items-center gap-1 rounded border bg-muted px-1.5 font-mono text-[10px] font-medium opacity-100">
							<span className="text-xs">↵</span>
						</kbd>
						<span>{t("to select")}</span>
					</div>

					{onCancel && (
						<button
							onClick={onCancel}
							className="text-muted-foreground hover:text-destructive transition-colors font-medium px-2 py-1 rounded hover:bg-destructive/10"
						>
							{t("Cancel Selection")}
						</button>
					)}
				</div>
			</div>
		</div>
	);
};

export const codeSpaceView = $view(codeSpaceSchema.node, () => {
	return (node, view, getPos) => {
		const dom = document.createElement("div");
		dom.className = "my-6 relative w-full group/codespace";
		dom.contentEditable = "false";

		const root = createRoot(dom);
		const queryClient = new QueryClient();

		const NodeViewComponent = ({ initialSlug }) => {
			const [slug, setSlug] = useState(initialSlug);
			const [isSelecting, setIsSelecting] = useState(!initialSlug);
			const { t } = useTranslation();

			const updateNodeSlug = (newSlug) => {
				const pos = getPos();
				if (pos == null) return;

				const tr = view.state.tr.setNodeAttribute(pos, "slug", newSlug);
				view.dispatch(tr);

				setSlug(newSlug);
				setIsSelecting(false);
			};

			if (!isSelecting && slug) {
				return (
					<div className="relative rounded-xl overflow-hidden border border-border shadow-sm transition-all duration-300 hover:shadow-md bg-card">
						<button
							onClick={() => setIsSelecting(true)}
							className="absolute bottom-3 right-3 opacity-0 group-hover/codespace:opacity-100 transition-all duration-200
                                bg-background/80 hover:bg-background backdrop-blur-sm
                                text-muted-foreground hover:text-foreground
                                border border-border shadow-sm rounded-md px-3 py-1.5 text-xs font-medium flex items-center gap-2 z-10"
						>
							<Code2 className="w-3.5 h-3.5" />
							{t("Change Project")}
						</button>
						<CodeSpacePage embed slug={slug} />
					</div>
				);
			}

			return (
				<ApiSelector
					onSelect={updateNodeSlug}
					onCancel={slug ? () => setIsSelecting(false) : undefined}
				/>
			);
		};

		root.render(
			<ThemeProvider>
				<QueryClientProvider client={queryClient}>
					<NodeViewComponent initialSlug={node.attrs.slug} />
				</QueryClientProvider>
			</ThemeProvider>,
		);

		return {
			dom,
			update: (updatedNode) => {
				if (updatedNode.type.name !== "code_space") return false;
				return true;
			},
			destroy: () => {
				root.unmount();
			},
			ignoreMutation: (mutation) => {
				return dom.contains(mutation.target) || mutation.type === "selection";
			},
			stopEvent: (event) => {
				const target = event.target;
				if (target.tagName === "INPUT" || target.tagName === "TEXTAREA") {
					return true;
				}

				if (target.closest(".api-selector-wrapper") || target.closest("button")) {
					return true;
				}

				return false;
			},
		};
	};
});
