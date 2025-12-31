export const transformDataToTree = (files) => {
	const root = [];
	const map = {};

	Object.keys(files)
		.sort()
		.forEach((path) => {
			const parts = path.split("/");
			const name = parts[parts.length - 1];
			const isFile = !!files[path];

			const parentId = parts.length > 1 ? parts.slice(0, -1).join("/") : null;

			const node = {
				id: path,
				name: name,
				children: isFile ? undefined : [],
				type: isFile ? "file" : "folder",
			};

			map[path] = node;

			if (parentId === null) {
				root.push(node);
			} else {
				if (map[parentId]) {
					map[parentId].children.push(node);
				}
			}
		});

	return root;
};

export const buildTreeData = (files) => {
	const root = [];
	const paths = Object.keys(files).sort();
	const nodes = {};

	const folderPaths = new Set();
	paths.forEach((path) => {
		const parts = path.split("/");
		let current = "";
		for (let i = 0; i < parts.length - 1; i++) {
			current = current ? `${current}/${parts[i]}` : parts[i];
			folderPaths.add(current);
		}
	});

	folderPaths.forEach((path) => {
		const name = path.split("/").pop();
		nodes[path] = { id: path, name, children: [], type: "folder" };
	});

	paths.forEach((path) => {
		const name = path.split("/").pop();
		nodes[path] = { id: path, name, children: undefined, type: "file" };
	});

	Object.values(nodes).forEach((node) => {
		const parts = node.id.split("/");
		if (parts.length === 1) {
			root.push(node);
		} else {
			const parentPath = parts.slice(0, -1).join("/");
			if (nodes[parentPath]) {
				nodes[parentPath].children.push(node);
			} else {
				root.push(node);
			}
		}
	});

	const sortNodes = (list) => {
		list.sort((a, b) => {
			if (a.type === b.type) return a.name.localeCompare(b.name);
			return a.type === "folder" ? -1 : 1;
		});
		list.forEach((n) => {
			if (n.children) sortNodes(n.children);
		});
	};
	sortNodes(root);
	return root;
};
