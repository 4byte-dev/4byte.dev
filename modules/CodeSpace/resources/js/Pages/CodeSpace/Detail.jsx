import CodeSpace from "@CodeSpace/Components/CodeSpace";
import Api from "../../Api";
import { useEffect, useState } from "react";

export default function CodeSpacePage({ slug, codeSpace, embed }) {
	const [name, setName] = useState(codeSpace?.name);
	const [files, setFiles] = useState(codeSpace?.files);

	useEffect(() => {
		if (!embed) return;

		Api.getProject(slug).then((response) => {
			setName(response.name);
			setFiles(response.files);
		});
	}, []);

	return <CodeSpace name={name} files={files} slug={slug} embed={embed} />;
}
