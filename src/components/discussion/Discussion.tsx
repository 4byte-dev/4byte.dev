import React, { useEffect, useRef } from 'react'

export interface DiscussionProps {
	lang: string
}

export default function Discussion({ lang }: DiscussionProps) {
	const containerRef = useRef<HTMLDivElement>(null)

	const getGiscusTheme = () => {
		return document.documentElement.classList.contains('dark') ? 'dark_dimmed' : 'light_dimmed'
	}

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
		script.setAttribute('data-theme', getGiscusTheme())
		script.setAttribute('data-lang', lang === 'tr' ? 'tr' : 'en')
		script.crossOrigin = 'anonymous'
		script.async = true

		containerRef.current.appendChild(script)

		const observer = new MutationObserver(() => {
			const iframe = document.querySelector('iframe.giscus-frame') as HTMLIFrameElement
			if (iframe) {
				iframe.contentWindow?.postMessage(
					{ giscus: { setConfig: { theme: getGiscusTheme() } } },
					'https://giscus.app'
				)
			}
		})
		observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] })

		return () => observer.disconnect()
	}, [lang])

	return (
		<div className="mt-16 pt-8 border-t border-border dark:border-border-dark" id="discussion">
			<div ref={containerRef} className="giscus-wrapper" />
		</div>
	)
}
