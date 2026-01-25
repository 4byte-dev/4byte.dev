export function initCodeGroups() {
	document.querySelectorAll(".code-group").forEach((group) => {
		const tabs = group.querySelectorAll(".code-tab");
		const contents = group.querySelectorAll(".tab-content");

		tabs.forEach((tab) => {
			tab.addEventListener("click", () => {
				const index = tab.getAttribute("data-id");

				tabs.forEach((t) =>
					t.classList.remove("bg-background", "text-foreground", "shadow-sm"),
				);
				contents.forEach((c) => c.classList.add("hidden"));

				tab.classList.add("bg-background", "text-foreground", "shadow-sm");
				group.querySelector(`.tab-content[data-id="${index}"]`).classList.remove("hidden");
			});
		});
	});
}
