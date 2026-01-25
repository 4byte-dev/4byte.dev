import { CodeSpaceExtension } from "./CodeSpaceExtension";
import { replaceCodeSpace } from "./Replace";

export const CodeSpacePlugin = {
	name: "CodeSpace",
	extension: CodeSpaceExtension,
	replace: replaceCodeSpace,
};
