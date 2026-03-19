import js from '@eslint/js'
import astro from 'eslint-plugin-astro'
import globals from 'globals'

export default [
	{
		ignores: ['dist/**', '.astro/**', 'node_modules/**'],
	},
	{
		files: ['*.astro'],
		parser: 'astro-eslint-parser',
		parserOptions: {
			parser: '@typescript-eslint/parser',
		},
		extends: ['plugin:astro/recommended'],
	},
	{
		files: ['scripts/**/*.mjs', 'scripts/**/*.js'],
		languageOptions: {
			globals: {
				...globals.node,
			},
		},
	},
	js.configs.recommended,
]
