# 4Byte.dev

> Clear, concise explanations of machine learning and AI concepts for practitioners.

## About

4Byte.dev is a technical documentation website focused on machine learning and AI concepts. It provides well-structured, easy-to-understand explanations of ML architectures, algorithms, and implementation patterns.

## Tech Stack

- **Framework**: [Astro](https://astro.build) v6
- **Styling**: [UnoCSS](https://unocss.dev) (Tailwind-compatible)
- **Icons**: [Lucide](https://lucide.dev)
- **Languages**: TypeScript + Astro components

## Quick Start

```bash
# Install dependencies
pnpm install

# Start development server
pnpm dev

# Build for production
pnpm build

# Preview production build
pnpm preview
```

## Scripts

| Command              | Description               |
| -------------------- | ------------------------- |
| `pnpm dev`           | Start local dev server    |
| `pnpm build`         | Build for production      |
| `pnpm preview`       | Preview production build  |
| `pnpm generate:og`   | Generate OG images        |
| `pnpm generate:data` | Update data files         |
| `pnpm lint`          | Run ESLint                |
| `pnpm lint:fix`      | Fix ESLint errors         |
| `pnpm format`        | Format code with Prettier |
| `pnpm format:check`  | Check code formatting     |

## Features

- **Multi-language Support**: Turkish (default) and English
- **Dark/Light Mode**: System preference detection
- **Search**: Client-side search functionality
- **SEO Optimized**: Sitemap, RSS, Open Graph, Schema.org
- **Responsive Design**: Mobile-friendly layout
- **Static Generation**: Fast performance

## Project Structure

```
src/
├── components/     # Astro components
├── content/        # Markdown articles (en/, tr/)
├── data/           # JSON data files
├── i18n/           # Internationalization utilities
├── layouts/        # Page layouts
├── pages/          # Route pages
└── styles/         # Global CSS
```
