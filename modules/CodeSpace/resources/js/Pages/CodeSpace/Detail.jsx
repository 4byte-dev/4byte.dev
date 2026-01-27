import CodeSpace from "@CodeSpace/Components/CodeSpace";
import CodeSpaceState from "@CodeSpace/Components/CodeSpaceState";
import Api from "@CodeSpace/Api";
import { useEffect, useState } from "react";
import { useMutation } from "@tanstack/react-query";

export default function CodeSpacePage({ slug, codeSpace, embed }) {
	const [name, setName] = useState(codeSpace?.name);
	const [files, setFiles] = useState(codeSpace?.files);

	const projectMutation = useMutation({
		mutationFn: () => Api.getProject(slug),
		onSuccess: (response) => {
			setName(response.name);
			setFiles(response.files);
		},
	});

	useEffect(() => {
		if (!embed) return;
		projectMutation.mutate();
	}, [embed, slug]);

	if (projectMutation.isPending) {
		return <CodeSpaceState type="loading" embed={embed} />;
	}

	if (projectMutation.isError) {
		const status = projectMutation.error?.response?.status;
		const is404 = status === 404;

		return <CodeSpaceState type={is404 ? "404" : "error"} embed={embed} />;
	}

	return <CodeSpace name={name} files={files} slug={slug} embed={embed} consoleOpen={!embed} />;
}
