import { useEffect } from "react";
import { pluginRegistry } from "@CodeSpace/Core/PluginRegistry";

function matchKeybinding(binding, event) {
	const isMac = navigator.platform.toUpperCase().indexOf("MAC") >= 0;
	const keyCombo = isMac && binding.mac ? binding.mac : binding.key;

	if (!keyCombo) return false;

	const eventKey = event.key.toUpperCase();

	const wantCtrl = keyCombo.toLowerCase().includes("ctrl");
	const wantCmd =
		keyCombo.toLowerCase().includes("cmd") || keyCombo.toLowerCase().includes("meta");
	const wantShift = keyCombo.toLowerCase().includes("shift");
	const wantAlt = keyCombo.toLowerCase().includes("alt");

	const hasCtrl = event.ctrlKey;
	const hasCmd = event.metaKey;
	const hasShift = event.shiftKey;
	const hasAlt = event.altKey;

	if (wantCtrl !== hasCtrl) return false;
	if (wantCmd !== hasCmd) return false;
	if (wantShift !== hasShift) return false;
	if (wantAlt !== hasAlt) return false;

	const parts = keyCombo.split("+");
	const mainKey = parts[parts.length - 1].toUpperCase();

	return mainKey === eventKey;
}

export function useKeybindings() {
	useEffect(() => {
		const handleKeyDown = (e) => {
			const bindings = pluginRegistry.getKeybindings();

			for (const binding of bindings) {
				if (matchKeybinding(binding, e)) {
					e.preventDefault();
					if (binding.command) {
						console.log(`[Keybinding] Executing ${binding.command}`);
						pluginRegistry.executeCommand(binding.command);
					}
					return;
				}
			}
		};

		window.addEventListener("keydown", handleKeyDown);
		return () => window.removeEventListener("keydown", handleKeyDown);
	}, []);
}
