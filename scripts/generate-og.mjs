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

const DEFAULT_COLORS = { bg: '#1a1a2e', text: '#e2e8f0' }

async function makeSvg(title, color, isArticle) {
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
					background: `linear-gradient(135deg, ${bg} 0%, #0a0a1a 100%)`,
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
												type: 'div',
												props: {
													style: {
														width: '48px',
														height: '48px',
														borderRadius: '12px',
														background: '#22d3ee',
														display: 'flex',
														alignItems: 'center',
														justifyContent: 'center',
														color: '#0a0a1a',
														fontSize: '24px',
														fontWeight: '700',
														fontFamily: 'Inter',
													},
													children: '4',
												},
											},
											{
												type: 'span',
												props: {
													style: {
														fontSize: '28px',
														fontWeight: '700',
														color: '#f8fafc',
														fontFamily: 'Inter',
													},
													children: '4byte.dev',
												},
											},
										],
									},
								},
								{
									type: 'span',
									props: {
										style: {
											fontSize: '16px',
											color: '#94a3b8',
											fontWeight: '500',
											fontFamily: 'Inter',
										},
										children: isArticle ? 'Machine Learning & AI' : 'Explore Articles',
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
											color: '#e94560',
											fontWeight: '600',
											textTransform: 'uppercase',
											letterSpacing: '2px',
											fontFamily: 'Inter',
										},
										children: isArticle ? 'ARTICLE' : 'LEARN MACHINE LEARNING',
									},
								},
								{
									type: 'h1',
									props: {
										style: {
											fontSize: title.length > 60 ? '42px' : '56px',
											fontWeight: '700',
											color: '#f8fafc',
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
											color: '#94a3b8',
											lineHeight: 1.6,
											fontFamily: 'Inter',
											maxWidth: '800px',
										},
										children: isArticle
											? 'Clear explanations of machine learning concepts'
											: 'Neural networks, transformers, reinforcement learning & more',
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
									type: 'div',
									props: {
										style: {
											width: '40px',
											height: '40px',
											borderRadius: '50%',
											background: '#22d3ee',
											display: 'flex',
											alignItems: 'center',
											justifyContent: 'center',
											fontSize: '18px',
											fontWeight: '700',
											color: '#0a0a1a',
											fontFamily: 'Inter',
										},
										children: '4',
									},
								},
								{
									type: 'span',
									props: {
										style: { fontSize: '18px', color: '#94a3b8', fontFamily: 'Inter' },
										children: '4byte.dev — ML Concepts Explained',
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

const outDir = path.join(process.cwd(), 'public', 'og')
fs.mkdirSync(outDir, { recursive: true })

const dataDir = path.join(process.cwd(), 'src', 'data')
const categories = JSON.parse(fs.readFileSync(path.join(dataDir, 'categories.json'), 'utf8'))
const articles = JSON.parse(fs.readFileSync(path.join(dataDir, 'articles.json'), 'utf8'))

const categoryColorMap = {}
for (const cat of categories) {
	categoryColorMap[cat.name] = cat.color || null
}

const svgIndex = await makeSvg('4byte.dev', null, false)
const resvgIndex = new Resvg(svgIndex, { fitTo: { mode: 'width', value: 1200 } })
fs.writeFileSync(path.join(outDir, 'index.png'), Buffer.from(resvgIndex.render().asPng()))
console.log('Generated public/og/index.png')

if (articles.length === 0) {
	console.log('No articles found – skipping article OG images.')
} else {
	for (const { slug, title, category } of articles) {
		const color = categoryColorMap[category] || null
		const svgArticle = await makeSvg(title, color, true)
		const resvgArticle = new Resvg(svgArticle, { fitTo: { mode: 'width', value: 1200 } })
		fs.writeFileSync(path.join(outDir, `${slug}.png`), Buffer.from(resvgArticle.render().asPng()))
		console.log(`Generated public/og/${slug}.png`)
	}
}

console.log('All OG images generated.')
