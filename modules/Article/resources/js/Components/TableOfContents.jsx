import React, { useMemo } from "react";
import { cn, slugify } from "@/Lib/Utils";
import { useTranslation } from "react-i18next";
import { AlignLeft } from "lucide-react";
import { useActiveHeading } from "@Article/Hooks/useActiveHeading";

export default function TableOfContents({ markdown, className }) {
	const { t } = useTranslation();

	const headings = useMemo(() => {
		return [...markdown.matchAll(/^(#{2,3})\s+(.*)$/gm)].map((m) => {
			const text = m[2];
			return {
				level: m[1].length,
				text: text,
				id: slugify(text),
			};
		});
	}, [markdown]);

	const activeId = useActiveHeading(headings.map((h) => h.id));

	if (headings.length < 2) return null;

	return (
		<nav className={cn("space-y-2", className)}>
			<div className="flex items-center gap-2 mb-4 text-sm font-semibold text-foreground/90 uppercase tracking-wider">
				<AlignLeft className="w-4 h-4" />
				{t("On this page")}
			</div>

			<div className="relative">
				<div className="absolute left-0 top-0 bottom-0 w-px bg-border" />

				<ul className="flex flex-col space-y-3 text-sm">
					{headings.map((heading, index) => {
						const isActive = activeId === heading.id;

						return (
							<li
								key={index}
								className={cn(
									"relative pl-4 transition-colors duration-200",
									heading.level === 3 && "pl-8",
								)}
							>
								{isActive && (
									<div className="absolute left-0 top-0 bottom-0 w-[2px] bg-primary -ml-px z-10" />
								)}

								<a
									href={`#${heading.id}`}
									onClick={(e) => {
										e.preventDefault();
										document.getElementById(heading.id)?.scrollIntoView({
											behavior: "smooth",
											block: "start",
										});
									}}
									className={cn(
										"block leading-tight line-clamp-2 hover:text-foreground transition-colors break-words whitespace-normal",
										isActive
											? "font-medium text-foreground"
											: "text-muted-foreground/80",
									)}
								>
									{heading.text}
								</a>
							</li>
						);
					})}
				</ul>
			</div>
		</nav>
	);
}
