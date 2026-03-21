import { defineConfig } from 'astro/config'
import UnoCSS from 'unocss/astro'
import icon from 'astro-icon'
import sitemap from '@astrojs/sitemap'
import partytown from '@astrojs/partytown'
import remarkMath from 'remark-math'
import rehypeKatex from 'rehype-katex'

export default defineConfig({
	site: 'https://4byte.dev',
	output: 'static',
	integrations: [
		UnoCSS(),
		icon(),
		sitemap(),
		partytown({
			config: {
				forward: ['dataLayer.push'],
			},
		}),
	],
	markdown: {
		remarkPlugins: [remarkMath],
		rehypePlugins: [rehypeKatex],
		shikiConfig: {
			themes: {
				dark: 'github-dark-dimmed',
				light: 'github-light',
			},
		},
		syntaxHighlight: {
			type: 'shiki',
			excludeLangs: ['mermaid'],
		},
	},
})
