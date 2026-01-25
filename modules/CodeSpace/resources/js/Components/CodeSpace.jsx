import Workbench from "@CodeSpace/Layout/Workbench";
import EditorArea from "@CodeSpace/Components/EditorArea";
import { useEditorStore } from "@CodeSpace/Stores/EditorStore";
import { useEffect } from "react";

export default function CodeSpace({
	files,
	name,
	slug,
	embed = false,
	openFiles = null,
	consoleOpen = false,
	height = null,
}) {
	const setFiles = useEditorStore((s) => s.setFiles);
	const setActiveFile = useEditorStore((s) => s.setActiveFile);
	const setName = useEditorStore((s) => s.setName);
	const setSlug = useEditorStore((s) => s.setSlug);
	const setEmbed = useEditorStore((s) => s.setEmbed);
	const setOpenFiles = useEditorStore((s) => s.setOpenFiles);
	const togglePanel = useEditorStore((s) => s.togglePanel);

	useEffect(() => {
		setEmbed(embed);
	}, [embed, setEmbed]);

	useEffect(() => {
		if (!files) return;
		setFiles(files);

		if (openFiles) {
			setOpenFiles(openFiles);
			if (openFiles.length > 0) {
				setActiveFile(openFiles[0]);
			}
		} else if (openFiles === null) {
			const allFiles = Object.keys(files).filter((k) => !files[k].isDir);
			setOpenFiles(allFiles);
			if (allFiles.length > 0) setActiveFile(allFiles[0]);
		}
	}, [files, setFiles, setActiveFile, name, setName, openFiles, setOpenFiles]);

	useEffect(() => {
		togglePanel(consoleOpen);
	}, [consoleOpen, togglePanel]);

	useEffect(() => {
		if (!name) return;
		setName(name);
	}, [name, setName]);

	useEffect(() => {
		if (!slug) return;
		setSlug(slug);
	}, [slug, setSlug]);

	return (
		<Workbench embed={embed} height={height}>
			<EditorArea />
		</Workbench>
	);
}
