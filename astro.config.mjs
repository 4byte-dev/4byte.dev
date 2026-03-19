import { defineConfig } from 'astro/config'
import UnoCSS from 'unocss/astro'
import icon from 'astro-icon'
import sitemap from '@astrojs/sitemap'
import partytown from '@astrojs/partytown'

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
})
