# Contributing to 4Byte.dev

Thank you for your interest in contributing to 4Byte.dev!

## How to Contribute

### Adding Articles

Articles are written in Markdown and placed in `src/content/articles/`.

#### Directory Structure

```
src/content/articles/
├── en/  # English articles
└── tr/  # Turkish articles
```

#### Frontmatter

Each article requires the following frontmatter:

```markdown
---
lang: 'tr' # 'tr' for Turkish, 'en' for English
slug: 'article-slug' # URL-friendly identifier
title: 'Article Title'
excerpt: 'Brief description of the article'
category: 'category-name'
tags: ['tag1', 'tag2']
author: '@author' # Author's github username
date: '2026-03-20'
views: 0
status: 'Published' # 'Published' or 'Draft'
---

Article content here...
```

#### Categories

You can check the available categories in the src/data/categories.json file.

#### Tags

You can check the available tags in the src/data/tags.json file, or you can add new one.

If you are adding new elements, don't forget to color-code and describe them in the PR workflow after data generation.

#### Writing Guidelines

1. **Be concise** - Explain concepts clearly without unnecessary fluff
2. **Use code examples** - Include practical examples where applicable
3. **Cross-reference** - Link to related articles
4. **Proofread** - Check spelling and grammar before submitting
5. **Update both languages** - When possible, provide articles in both Turkish and English

### Development

1. Fork the repository
2. Clone your fork: `git clone https://github.com/YOUR_USERNAME/4byte-astro.git`
3. Install dependencies: `pnpm install`
4. Create a branch: `git checkout -b feature/your-feature-name`
5. Make your changes
6. Run linting: `pnpm lint`
7. Format code: `pnpm format`
8. Commit your changes
9. Push to your fork
10. Open a Pull Request

### Code Style

- Follow existing code patterns in the project
- Use TypeScript for type safety
- Run `pnpm lint` and `pnpm format` before committing

## Reporting Issues

- Use the GitHub Issues tab
- Search for existing issues before creating new ones
- Provide clear reproduction steps for bugs

## Questions?

Feel free to open a discussion on GitHub if you have any questions.
