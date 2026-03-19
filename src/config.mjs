const CONFIG = {
	name: '4byte.dev',
	origin: 'https://4byte.dev',

	title: '4byte.dev — Machine Learning & AI Concepts Explained',
	description:
		'Clear, concise explanations of machine learning and AI concepts. From neural networks to reinforcement learning — learn the fundamentals that power modern AI.',
	defaultImage: '/og-default.png',

	googleAnalyticsId: 'G-LCW5GXVKS6',
	googleSiteVerificationId: 'orcPxI47GSa-cRvY11tUe6iGg2IO_RPvnA1q95iEM3M',

	author: {
		name: '4Byte.dev',
		url: 'https://4byte.dev',
	},

	social: {
		twitter: '@4bytedev',
	},
}

export const SITE = CONFIG

export const LANGUAGES = {
	tr: {
		code: 'tr',
		name: 'Türkçe',
		dir: 'ltr',
		prefix: '',
		ogLocale: 'tr_TR',
	},
	en: {
		code: 'en',
		name: 'English',
		dir: 'ltr',
		prefix: '/en',
		ogLocale: 'en_US',
	},
}

export const DEFAULT_LANG = 'tr'
export const SUPPORTED_LANGS = Object.keys(LANGUAGES)
