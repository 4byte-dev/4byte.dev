import { useEffect, useState, useMemo } from "react";
import { useEditorStore } from "@CodeSpace/Stores/EditorStore";
import {
	CommandDialog,
	CommandInput,
	CommandList,
	CommandEmpty,
	CommandGroup,
	CommandItem,
} from "@/Components/Ui/Command";
import { usePluginRegistry } from "@CodeSpace/Core/PluginRegistry";
import { useTranslation } from "react-i18next";

export default function QuickOpen() {
	const { layout, setQuickOpen } = useEditorStore();
	const open = layout.quickOpenVisible;
	const mode = layout.quickOpenMode || "default";
	const [inputValue, setInputValue] = useState("");
	const [loading, setLoading] = useState(false);
	const [providersData, setProvidersData] = useState([]);
	const providers = usePluginRegistry((reg) => reg.getQuickOpenProviders());
	const { t } = useTranslation();

	const activeProviders = useMemo(() => {
		return providers.filter((p) => p.mode === mode);
	}, [providers, mode]);

	useEffect(() => {
		if (!open) {
			setInputValue("");
			setProvidersData([]);
		} else {
			setInputValue("");
		}
	}, [open, mode]);

	useEffect(() => {
		if (!open) return;

		let isMounted = true;

		async function fetchData() {
			setLoading(true);
			const promises = activeProviders.map(async (provider) => {
				try {
					const items = await provider.getItems(inputValue);
					return { providerId: provider.id, items };
				} catch (e) {
					console.error(`Provider ${provider.id} failed`, e);
					return { providerId: provider.id, items: [] };
				}
			});

			const results = await Promise.all(promises);
			if (isMounted) {
				setProvidersData(results);
				setLoading(false);
			}
		}

		fetchData();

		return () => {
			isMounted = false;
		};
	}, [open, inputValue, activeProviders]);

	useEffect(() => {
		const down = (e) => {
			if (e.key === "p" && (e.metaKey || e.ctrlKey)) {
				e.preventDefault();
				setQuickOpen(!open);
			}
			if (e.key === "P" && (e.metaKey || e.ctrlKey) && e.shiftKey) {
				e.preventDefault();
				setQuickOpen(!open);
				setInputValue(">");
			}
		};
		document.addEventListener("keydown", down);
		return () => document.removeEventListener("keydown", down);
	}, [open, setQuickOpen]);

	const placeholder = useMemo(() => {
		if (activeProviders.length === 1) {
			return activeProviders[0].getPlaceholder
				? activeProviders[0].getPlaceholder()
				: t("Type...");
		}
		if (inputValue.startsWith(">")) return t("Type a command...");
		return t("Type a command or search files...");
	}, [activeProviders, inputValue]);

	const handleSelect = (item, provider) => {
		if (provider.onSelect) {
			provider.onSelect(item);
		} else {
			console.warn(`Provider ${provider.id} has no onSelect handler`);
			setQuickOpen(false);
		}
	};

	return (
		<CommandDialog open={open} onOpenChange={setQuickOpen} shouldFilter={false}>
			<CommandInput
				placeholder={placeholder}
				value={inputValue}
				onValueChange={setInputValue}
			/>
			<CommandList>
				{!loading && providersData.every((d) => d.items.length === 0) && (
					<CommandEmpty>{t("No results found.")}</CommandEmpty>
				)}

				{loading && <CommandItem disabled>{t("Loading...")}</CommandItem>}

				{!loading &&
					providersData.map((data) => {
						const provider = activeProviders.find((p) => p.id === data.providerId);
						if (!provider || data.items.length === 0) return null;

						return (
							<CommandGroup
								key={provider.id}
								heading={
									activeProviders.length > 1
										? provider.id.split(".").pop().toUpperCase()
										: undefined
								}
							>
								{data.items.map((item) => (
									<CommandItem
										key={item.id}
										onSelect={() => handleSelect(item, provider)}
										value={item.id}
									>
										{item.icon}
										<div className="flex flex-col">
											<span>{item.label}</span>
											{item.description && (
												<span className="text-xs opacity-50">
													{item.description}
												</span>
											)}
										</div>
									</CommandItem>
								))}
							</CommandGroup>
						);
					})}
			</CommandList>
		</CommandDialog>
	);
}
