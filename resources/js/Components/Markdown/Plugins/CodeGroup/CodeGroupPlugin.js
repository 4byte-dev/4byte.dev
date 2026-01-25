import { CodeGroupExtension } from "./CodeGroupExtension";
import { initCodeGroups } from "./Lifecycle";

export const CodeGroupPlugin = {
	name: "CodeGroup",
	extension: CodeGroupExtension,
	lifecycle: initCodeGroups,
};
