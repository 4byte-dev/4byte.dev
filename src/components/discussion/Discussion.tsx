import React, { useEffect, useRef } from 'react'
import { t } from '../../i18n/index.ts'

export interface DiscussionProps {
	slug: string
	labels: any
	lang: string
}

export default function Discussion({ labels, lang }: DiscussionProps) {
	const containerRef = useRef<HTMLDivElement>(null)

	useEffect(() => {
		if (!containerRef.current) return

		const script = document.createElement('script')
		script.src = 'https://giscus.app/client.js'
		script.setAttribute('data-repo', '4byte-dev/4byte.dev')
		script.setAttribute('data-repo-id', 'R_kgDORqu_NQ')
		script.setAttribute('data-category', 'Makale Yorumları')
		script.setAttribute('data-category-id', 'DIC_kwDORqu_Nc4C5G9p')
		script.setAttribute('data-mapping', 'pathname')
		script.setAttribute('data-strict', '0')
		script.setAttribute('data-reactions-enabled', '1')
		script.setAttribute('data-emit-metadata', '0')
		script.setAttribute('data-input-position', 'bottom')
		script.setAttribute('data-theme', 'preferred_color_scheme')
		script.setAttribute('data-lang', lang === 'tr' ? 'tr' : 'en')
		script.crossOrigin = 'anonymous'
		script.async = true

		containerRef.current.appendChild(script)
	}, [lang])

	return (
		<div className="mt-16 pt-8 border-t border-border dark:border-border-dark" id="discussion">
			<h2 className="text-2xl font-bold text-foreground dark:text-foreground-dark mb-6">
				{t(labels, 'discussion.title')}
			</h2>
			<div ref={containerRef} className="giscus-wrapper" />
		</div>
	)
}
