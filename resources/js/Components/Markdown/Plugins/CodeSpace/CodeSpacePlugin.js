import { commandsCtx } from "@milkdown/kit/core";
import { codeSpaceCommand } from "./Command";
import { codeSpaceExtension } from "./Extension";
import { replaceCodeSpace } from "./Replace";
import { codeSpaceSchema } from "./Schema";
import { codeSpaceView } from "./View";

export const CodeSpacePlugin = {
	name: "CodeSpace",
	extension: codeSpaceExtension,
	replace: replaceCodeSpace,
	feature: [codeSpaceSchema, codeSpaceView, codeSpaceCommand],
	configureMenu: (builder) => {
		builder.getGroup("advanced").addItem(codeSpaceCommand.key, {
			label: "Code Space",
			key: codeSpaceCommand.key,
			onRun: (ctx) => {
				ctx.get(commandsCtx).call(codeSpaceCommand.key, {});
			},
		});
	},
};
