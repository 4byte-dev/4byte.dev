import { Resvg } from '@resvg/resvg-js'
import satori from 'satori'
import fs from 'fs'
import path from 'path'

const fontRegular = await fetch(
	'https://fonts.gstatic.com/s/inter/v20/UcCO3FwrK3iLTeHuS_nVMrMxCp50SjIw2boKoduKmMEVuLyfMZg.ttf',
).then((res) => res.arrayBuffer())

const fontBold = await fetch(
	'https://fonts.gstatic.com/s/inter/v20/UcCO3FwrK3iLTeHuS_nVMrMxCp50SjIw2boKoduKmMEVuFuYMZg.ttf',
).then((res) => res.arrayBuffer())

const repoRoot = path.join(process.cwd())
const logoDark = fs.readFileSync(path.join(repoRoot, 'src/assets/logo-dark.png')).toString('base64')

const DEFAULT_COLORS = { bg: '#18181b', text: '#e5e7eb' }

const LANG_TEXTS = {
	tr: {
		explore: 'Makaleleri Keşfet',
		category: 'KATEGORİ',
		article: 'MAKALE',
		tagline: 'Sinir ağları, transformerlar, pekiştirmeli öğrenme ve daha fazlası',
		description: 'Makine öğrenimi kavramlarının net açıklamaları',
		suffix: 'ML Kavramları Açıklandı',
	},
	en: {
		explore: 'Explore Articles',
		category: 'CATEGORY',
		article: 'ARTICLE',
		tagline: 'Neural networks, transformers, reinforcement learning & more',
		description: 'Clear explanations of machine learning concepts',
		suffix: 'ML Concepts Explained',
	},
}

async function makeSvg(title, color, isArticle, lang = 'en', logo) {
	const texts = LANG_TEXTS[lang] || LANG_TEXTS.en
	const bg = color || DEFAULT_COLORS.bg
	const svg = await satori(
		{
			type: 'div',
			props: {
				style: {
					width: '1200px',
					height: '630px',
					display: 'flex',
					flexDirection: 'column',
					justifyContent: 'space-between',
					padding: '60px 80px',
					background: `linear-gradient(180deg, ${bg} 0%, #0d0d0d 100%)`,
					fontFamily: 'Inter',
				},
				children: [
					{
						type: 'div',
						props: {
							style: { display: 'flex', alignItems: 'center', justifyContent: 'space-between' },
							children: [
								{
									type: 'div',
									props: {
										style: { display: 'flex', alignItems: 'center', gap: '16px' },
										children: [
											{
												type: 'img',
												props: {
													src: `data:image/png;base64,${logo}`,
													height: 48
												},
											}
										],
									},
								},
								{
									type: 'span',
									props: {
										style: {
											fontSize: '16px',
											color: '#8b949e',
											fontWeight: '500',
											fontFamily: 'Inter',
										},
										children: isArticle ? 'Machine Learning & AI' : texts.explore,
									},
								},
							],
						},
					},
					{
						type: 'div',
						props: {
							style: { display: 'flex', flexDirection: 'column', gap: '24px' },
							children: [
								{
									type: 'span',
									props: {
										style: {
											fontSize: '14px',
											color: '#388bfd',
											fontWeight: '600',
											textTransform: 'uppercase',
											letterSpacing: '2px',
											fontFamily: 'Inter',
										},
										children: isArticle ? texts.article : texts.category,
									},
								},
								{
									type: 'h1',
									props: {
										style: {
											fontSize: title.length > 60 ? '42px' : '56px',
											fontWeight: '700',
											color: '#e5e7eb',
											lineHeight: 1.2,
											fontFamily: 'Inter',
											maxWidth: '900px',
										},
										children: title,
									},
								},
								{
									type: 'p',
									props: {
										style: {
											fontSize: '20px',
											color: '#8b949e',
											lineHeight: 1.6,
											fontFamily: 'Inter',
											maxWidth: '800px',
										},
										children: isArticle ? texts.description : texts.tagline,
									},
								},
							],
						},
					},
					{
						type: 'div',
						props: {
							style: { display: 'flex', alignItems: 'center', gap: '12px' },
							children: [
								{
									type: 'img',
									props: {
										src: `data:image/png;base64,${logo}`,
										height: 48
									},
								},
								{
									type: 'span',
									props: {
										style: { fontSize: '18px', color: '#8b949e', fontFamily: 'Inter' },
										children: texts.suffix,
									},
								},
							],
						},
					},
				],
			},
		},
		{
			width: 1200,
			height: 630,
			fonts: [
				{ name: 'Inter', data: fontRegular, weight: 400, style: 'normal' },
				{ name: 'Inter', data: fontBold, weight: 700, style: 'normal' },
			],
		},
	)
	return svg
}

const outDir = path.join(repoRoot, 'public', 'og')
fs.mkdirSync(outDir, { recursive: true })

const SUPPORTED_LANGS = ['tr', 'en']

for (const lang of SUPPORTED_LANGS) {
	const langOutDir = path.join(outDir, lang)
	fs.mkdirSync(langOutDir, { recursive: true })

	const dataDir = path.join(repoRoot, 'src', 'data', lang)
	let categories = []
	let articles = []

	try {
		categories = JSON.parse(fs.readFileSync(path.join(dataDir, 'categories.json'), 'utf8'))
		articles = JSON.parse(fs.readFileSync(path.join(dataDir, 'articles.json'), 'utf8'))
	} catch {
		console.log(`No data found for language: ${lang}`)
	}

	const categoryColorMap = {}
	for (const cat of categories) {
		categoryColorMap[cat.name] = cat.color || null
	}

	const svgIndex = await makeSvg('4byte.dev', null, false, lang, logoDark)
	const resvgIndex = new Resvg(svgIndex, { fitTo: { mode: 'width', value: 1200 } })
	fs.writeFileSync(path.join(langOutDir, 'index.png'), Buffer.from(resvgIndex.render().asPng()))
	console.log(`Generated public/og/${lang}/index.png`)

	if (articles.length === 0) {
		console.log(`No articles for ${lang} – skipping article OG images.`)
	} else {
		for (const { slug, title, category } of articles) {
			const color = categoryColorMap[category] || null
			const svgArticle = await makeSvg(title, color, true, lang, logoDark)
			const resvgArticle = new Resvg(svgArticle, { fitTo: { mode: 'width', value: 1200 } })
			fs.writeFileSync(path.join(langOutDir, `${slug}.png`), Buffer.from(resvgArticle.render().asPng()))
			console.log(`Generated public/og/${lang}/${slug}.png`)
		}
	}
}

console.log('All OG images generated.')
