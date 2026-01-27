import { FileCode2, Plus, Terminal, X } from "lucide-react";
import { createRoot } from "react-dom/client";
import { codeGroupSchema } from "./Schema";
import { $view } from "@milkdown/kit/utils";

export const codeGroupView = $view(codeGroupSchema.node, () => {
	return (initialNode, view, getPos) => {
		const dom = document.createElement("div");
		dom.className =
			"!my-6 relative w-full rounded-xl border border-border bg-card text-card-foreground shadow-sm overflow-hidden group/codegroup";

		const headerContainer = document.createElement("div");
		headerContainer.contentEditable = "false";
		headerContainer.className = "border-b border-border bg-muted/30";
		dom.appendChild(headerContainer);

		const contentDOM = document.createElement("div");
		contentDOM.className = "!p-0 !m-0";
		dom.appendChild(contentDOM);

		const updateVisibility = (index) => {
			Array.from(contentDOM.children).forEach((child, i) => {
				if (child instanceof HTMLElement) {
					child.style.display = i === index ? "" : "none";
					child.style.borderRadius = "0";
					child.style.margin = "0";
				}
			});
		};

		const CodeGroupHeader = ({ labels, activeIndex }) => {
			const setAttr = (attr, value) => {
				const pos = getPos();
				if (pos == null) return;
				view.dispatch(view.state.tr.setNodeAttribute(pos, attr, value));
			};

			const updateLabel = (index, newLabel) => {
				const newLabels = [...labels];
				newLabels[index] = newLabel;
				setAttr("labels", newLabels);
			};

			const removeTab = (e, index) => {
				e.stopPropagation();
				const pos = getPos();
				if (pos == null || labels.length <= 1) return;

				const currentNode = view.state.doc.nodeAt(pos);
				const newLabels = labels.filter((_, i) => i !== index);

				let nextActiveIndex = activeIndex;
				if (activeIndex >= index && activeIndex > 0) {
					nextActiveIndex = activeIndex - 1;
				}

				let tr = view.state.tr;

				tr = tr.setNodeAttribute(pos, "labels", newLabels);
				tr = tr.setNodeAttribute(pos, "activeIndex", nextActiveIndex);

				let childPos = pos + 1;
				for (let i = 0; i < index; i++) {
					childPos += currentNode.child(i).nodeSize;
				}

				const targetNode = currentNode.child(index);
				tr = tr.delete(childPos, childPos + targetNode.nodeSize);

				view.dispatch(tr);
			};

			const addCodeBlock = (e) => {
				e.preventDefault();
				const pos = getPos();
				if (pos == null) return;

				const currentNode = view.state.doc.nodeAt(pos);
				const currentLabels = currentNode.attrs.labels;
				const newLabel = `Tab ${currentLabels.length + 1}`;
				const newLabels = [...currentLabels, newLabel];

				let tr = view.state.tr.setNodeAttribute(pos, "labels", newLabels);
				tr = tr.setNodeAttribute(pos, "activeIndex", newLabels.length - 1);

				const codeBlockNode = view.state.schema.nodes.code_block.create();
				tr = tr.insert(pos + currentNode.nodeSize - 1, codeBlockNode);
				view.dispatch(tr);
			};

			return (
				<div className="flex items-center justify-between !px-2 !py-2">
					<div className="flex items-center gap-1 overflow-x-auto scrollbar-none">
						<div className="mr-2 p-1.5 rounded-md bg-primary/10 text-primary">
							<Terminal className="w-4 h-4" />
						</div>
						{labels.map((label, i) => (
							<div
								key={i}
								onClick={(e) => {
									e.stopPropagation();
									setAttr("activeIndex", i);
								}}
								className={`
                                    group/tab relative flex items-center gap-2 !px-3 !py-1.5 rounded-md text-sm font-medium transition-all cursor-pointer border border-transparent
                                    ${
										i === activeIndex
											? "bg-background text-foreground shadow-sm ring-1 ring-black/5 dark:ring-white/5"
											: "text-muted-foreground hover:bg-muted hover:text-foreground"
									}
                                `}
							>
								<div className="flex items-center gap-2">
									{i === activeIndex && (
										<FileCode2 className="w-3.5 h-3.5 opacity-50" />
									)}
									<input
										className="bg-transparent border-none outline-none text-inherit font-inherit w-[min-content] min-w-[30px] !p-0 focus:ring-0 cursor-text"
										value={label}
										onChange={(e) => updateLabel(i, e.target.value)}
										style={{ width: `${Math.max(label.length, 4)}ch` }}
									/>
								</div>

								{labels.length > 1 && (
									<button
										onClick={(e) => removeTab(e, i)}
										type="button"
										className={`
                                            !ml-1 p-0.5 rounded-md opacity-0 group-hover/tab:opacity-100 transition-all
                                            hover:bg-destructive/10 hover:text-destructive
                                            ${i === activeIndex ? "opacity-0" : ""}
                                        `}
									>
										<X className="w-3 h-3" />
									</button>
								)}
							</div>
						))}
					</div>
					<button
						onClick={addCodeBlock}
						className="flex items-center justify-center !p-1.5 rounded-md hover:bg-accent text-muted-foreground hover:text-accent-foreground transition-colors"
						title="Add Tab"
						type="button"
					>
						<Plus className="w-4 h-4" />
					</button>
				</div>
			);
		};

		const root = createRoot(headerContainer);

		root.render(
			<CodeGroupHeader
				labels={initialNode.attrs.labels}
				activeIndex={initialNode.attrs.activeIndex}
			/>,
		);
		setTimeout(() => updateVisibility(initialNode.attrs.activeIndex), 0);

		return {
			dom,
			contentDOM,
			ignoreMutation: (mutation) => {
				return !dom.contains(mutation.target) || headerContainer.contains(mutation.target);
			},
			update(updatedNode) {
				if (updatedNode.type.name !== "code_group") return false;

				root.render(
					<CodeGroupHeader
						labels={updatedNode.attrs.labels}
						activeIndex={updatedNode.attrs.activeIndex}
					/>,
				);
				updateVisibility(updatedNode.attrs.activeIndex);
				return true;
			},
			destroy() {
				root.unmount();
			},
		};
	};
});
