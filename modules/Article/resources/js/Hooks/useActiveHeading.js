import { useEffect, useState } from "react";

export function useActiveHeading(headingIds) {
	const [activeId, setActiveId] = useState("");

	useEffect(() => {
		const observer = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						setActiveId(entry.target.id);
					}
				});
			},
			{ rootMargin: "0% 0% -80% 0%" },
		);

		headingIds.forEach((id) => {
			const element = document.getElementById(id);
			if (element) {
				observer.observe(element);
			}
		});

		return () => {
			headingIds.forEach((id) => {
				const element = document.getElementById(id);
				if (element) {
					observer.unobserve(element);
				}
			});
		};
	}, [headingIds]);

	return activeId;
}
