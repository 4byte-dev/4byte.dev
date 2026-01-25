export function AttachCopyButtons(t) {
	document.querySelectorAll("pre").forEach((pre) => {
		if (pre.querySelector(".copy-btn")) return;

		const btn = document.createElement("button");
		btn.className =
			"copy-btn absolute top-2 right-2 bg-gray-800 text-white text-xs px-2 py-1 rounded hover:bg-gray-700";
		btn.innerText = t("Copy");

		btn.onclick = () => {
			navigator.clipboard.writeText(pre.innerText);
			btn.innerText = t("Copied!");
			setTimeout(() => (btn.innerText = t("Copy")), 1500);
		};

		pre.style.position = "relative";
		pre.appendChild(btn);
	});
}
