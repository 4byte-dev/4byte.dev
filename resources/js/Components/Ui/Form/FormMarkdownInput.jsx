import { MarkdownEditor } from "@/Components/MarkdownEditor/MarkdownEditor";
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
