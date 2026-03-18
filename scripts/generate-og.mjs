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

const CATEGORY_COLORS = {
	fundamentals: { bg: '#1a1a2e', text: '#e2e8f0' },
	'deep-learning': { bg: '#0f3460', text: '#e94560' },
	nlp: { bg: '#1b1b2f', text: '#a855f7' },
	'computer-vision': { bg: '#0d1b2a', text: '#22d3ee' },
	'reinforcement-learning': { bg: '#1a0a2e', text: '#4ade80' },
}

async function makeSvg(title, catSlug, isArticle) {
	const colors = CATEGORY_COLORS[catSlug] || CATEGORY_COLORS['fundamentals']
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
					background: `linear-gradient(135deg, ${colors.bg} 0%, #0a0a1a 100%)`,
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

const svgIndex = await makeSvg('4byte.dev', 'fundamentals', false)
const resvgIndex = new Resvg(svgIndex, { fitTo: { mode: 'width', value: 1200 } })
fs.writeFileSync(path.join(outDir, 'index.png'), Buffer.from(resvgIndex.render().asPng()))
console.log('Generated dist/og/index.png')

const articles = [
	{ slug: 'backpropagation', title: 'Backpropagation', cat: 'deep-learning' },
	{ slug: 'cnn-image-classification', title: 'CNN for Image Classification', cat: 'computer-vision' },
	{ slug: 'diffusion-models', title: 'Diffusion Models', cat: 'deep-learning' },
	{ slug: 'linear-regression', title: 'Linear Regression', cat: 'fundamentals' },
	{ slug: 'loss-functions', title: 'Loss Functions', cat: 'fundamentals' },
	{ slug: 'q-learning', title: 'Q-Learning', cat: 'reinforcement-learning' },
	{ slug: 'rnn-sequence-modeling', title: 'RNN for Sequence Modeling', cat: 'nlp' },
	{ slug: 'transformer-architectures', title: 'Transformer Architectures', cat: 'nlp' },
	{ slug: 'word-embeddings', title: 'Word Embeddings', cat: 'nlp' },
]

for (const { slug, title, cat } of articles) {
	const svgArticle = await makeSvg(title, cat, true)
	const resvgArticle = new Resvg(svgArticle, { fitTo: { mode: 'width', value: 1200 } })
	fs.writeFileSync(path.join(outDir, `${slug}.png`), Buffer.from(resvgArticle.render().asPng()))
	console.log(`Generated dist/og/${slug}.png`)
}

console.log('All OG images generated.')
