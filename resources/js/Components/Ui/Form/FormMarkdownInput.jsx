import { MarkdownEditor } from "@/Components/Markdown/MarkdownEditor";
import { FormControl, FormItem, FormLabel, FormMessage } from "./Form";

export function FormMarkdownInput({ label, field, ...props }) {
	return (
		<FormItem>
			<FormLabel>{label}</FormLabel>
			<FormControl>
				<MarkdownEditor value={field.value} onChange={field.onChange} {...props} />
			</FormControl>
			<FormMessage />
		</FormItem>
	);
}
