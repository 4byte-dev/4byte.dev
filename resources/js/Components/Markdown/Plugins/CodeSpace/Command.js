import { $command } from "@milkdown/kit/utils";

export const codeSpaceCommand = $command("InsertCodeSpace", () => {
	return () => (state, dispatch) => {
		const nodeType = state.schema.nodes["code_space"];
		if (!nodeType) return false;

		const node = nodeType.create({ slug: "" });
		dispatch(state.tr.replaceSelectionWith(node));
		return true;
	};
});
