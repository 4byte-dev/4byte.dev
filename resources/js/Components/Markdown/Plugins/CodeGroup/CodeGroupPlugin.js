import { commandsCtx } from "@milkdown/kit/core";
import { codeGroupExtension } from "./Extension";
import { codeGroupCommand } from "./Command";
import { initCodeGroups } from "./Lifecycle";
import { codeGroupSchema } from "./Schema";
import { codeGroupView } from "./View";

export const CodeGroupPlugin = {
	name: "CodeGroup",
	extension: codeGroupExtension,
	lifecycle: initCodeGroups,
	feature: [codeGroupSchema, codeGroupView, codeGroupCommand],
	configureMenu: (builder) => {
		builder.getGroup("advanced").addItem(codeGroupCommand.key, {
			label: "Code Group",
			key: codeGroupCommand.key,
			onRun: (ctx) => {
				ctx.get(commandsCtx).call(codeGroupCommand.key, {});
			},
		});
	},
};
