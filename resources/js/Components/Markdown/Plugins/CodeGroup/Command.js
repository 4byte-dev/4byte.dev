import { $command } from "@milkdown/kit/utils";

export const codeGroupCommand = $command("InsertCodeGroup", () => {
	return () => (state, dispatch) => {
		const schema = state.schema;
		const nodeType = schema.nodes["code_group"];
		const codeBlock = schema.nodes["code_block"];

		if (!nodeType || !codeBlock) return false;

		const node = nodeType.create({ labels: ["JS", "CSS"], activeIndex: 0 }, [
			codeBlock.create(),
			codeBlock.create(),
		]);

		dispatch(state.tr.replaceSelectionWith(node));
		return true;
	};
});
