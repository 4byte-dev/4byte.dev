import { defineConfig } from 'astro/config'
import UnoCSS from 'unocss/astro'
import icon from 'astro-icon'
import sitemap from '@astrojs/sitemap'
import partytown from '@astrojs/partytown'
import remarkMath from 'remark-math'
import rehypeKatex from 'rehype-katex'
import mdx from '@astrojs/mdx'
import { SITE } from './src/config.mjs'
import { getSitemapOptions } from './src/sitemap.mjs'

export default defineConfig({
	site: SITE.origin,
	output: 'static',
	integrations: [
		UnoCSS(),
		mdx(),
		icon(),
		sitemap(getSitemapOptions()),
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
