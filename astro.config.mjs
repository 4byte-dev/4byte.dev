import { defineConfig } from 'astro/config'
import UnoCSS from 'unocss/astro'
import icon from 'astro-icon'
import sitemap from '@astrojs/sitemap'
import partytown from '@astrojs/partytown'
import cloudflare from '@astrojs/cloudflare'

export default defineConfig({
	site: 'https://4byte.dev',
	output: 'static',
	adapter: cloudflare(),
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
