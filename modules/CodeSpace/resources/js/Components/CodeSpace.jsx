import Workbench from "@CodeSpace/Layout/Workbench";
import EditorArea from "@CodeSpace/Components/EditorArea";
import { useEditorStore } from "@CodeSpace/Stores/EditorStore";
import { useEffect } from "react";

export default function CodeSpace({ files, name, slug }) {
	const setFiles = useEditorStore((s) => s.setFiles);
	const setActiveFile = useEditorStore((s) => s.setActiveFile);
	const setName = useEditorStore((s) => s.setName);
	const setSlug = useEditorStore((s) => s.setSlug);

	useEffect(() => {
		if (!files) return;
		setFiles(files);

		const firstKey = Object.keys(files)[0];
		if (firstKey) {
			setActiveFile(firstKey);
		}
	}, [files, setFiles, setActiveFile, name, setName]);

	useEffect(() => {
		if (!name) return;
		setName(name);
	}, [name, setName]);

	useEffect(() => {
		if (!slug) return;
		setSlug(slug);
	}, [slug, setSlug]);

	return (
		<Workbench>
			<EditorArea />
		</Workbench>
	);
}
