import MDEditor from "@uiw/react-md-editor";
import rehypeSanitize, { defaultSchema } from "rehype-sanitize";

export function MarkdownEditor({ onChange, value, ...props }) {
	return (
		<div className="container">
			<MDEditor
				value={value}
				onChange={onChange}
				className={"!bg-background"}
				previewOptions={{
					rehypePlugins: [
						[
							rehypeSanitize,
							{
								...defaultSchema,
								attributes: {
									...defaultSchema.attributes,
									img: [...(defaultSchema.attributes.img || []), "src", "alt"],
								},
								protocols: {
									...defaultSchema.protocols,
									src: ["http", "https", "data", "blob"],
								},
							},
						],
					],
				}}
				height={"800px"}
				{...props}
			/>
		</div>
	);
}
