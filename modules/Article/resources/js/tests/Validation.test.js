
import { describe, it, expect } from 'vitest';
import { createArticleSchema } from '../Validation';

const t = (key) => key;

describe('Article Validation', () => {
    describe('Draft State (published: false)', () => {
        const schema = createArticleSchema(t);

        it('should validate valid draft', () => {
            const result = schema.safeParse({
                title: 'Valid Title Here',
                published: false,
                sources: [],
            });
            expect(result.success).toBe(true);
        });

        it('should require title', () => {
            const result = schema.safeParse({
                title: '',
                published: false,
                sources: [],
            });
            expect(result.success).toBe(false);
            expect(result.error.issues[0].message).toBe('Title is required');
        });

        it('should require title min length 10', () => {
            const result = schema.safeParse({
                title: 'Short',
                published: false,
                sources: [],
            });
            expect(result.success).toBe(false);
            expect(result.error.issues[0].message).toBe('Title must be at least 10 characters');
        });

        it('should allow empty content and excerpt in draft', () => {
            const result = schema.safeParse({
                title: 'Valid Title Here',
                excerpt: '',
                content: '',
                published: false,
                sources: [],
            });
            expect(result.success).toBe(true);
        });
        it('should allow empty image in draft', () => {
            const result = schema.safeParse({
                title: 'Valid Title Here',
                image: null,
                published: false,
                sources: [],
            });
            expect(result.success).toBe(true);
        });
    });

    describe('Published State (published: true)', () => {
        const schema = createArticleSchema(t);

        it('should validate valid published article', () => {
            const result = schema.safeParse({
                title: 'Valid Title Here',
                excerpt: 'This is a long enough excerpt to pass validation. It needs to be at least 100 characters so I am typing some more text here to ensure it passes the length check.',
                content: 'This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. ',
                categories: ['Tech'],
                tags: ['News'],
                image: 'some-image-url',
                published: true,
                sources: [],
            });
            if (!result.success) {
                console.log(result.error.issues);
            }
            expect(result.success).toBe(true);
        });

        it('should fail if excerpt is too short', () => {
            const result = schema.safeParse({
                title: 'Valid Title Here',
                excerpt: 'Short excerpt',
                content: 'This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. ',
                categories: ['Tech'],
                tags: ['News'],
                image: 'some-image-url',
                published: true,
                sources: [],
            });
            expect(result.success).toBe(false);
            expect(result.error.issues.some(i => i.path.includes('excerpt'))).toBe(true);
        });

        it('should fail if content is too short', () => {
            const result = schema.safeParse({
                title: 'Valid Title Here',
                excerpt: 'This is a long enough excerpt to pass validation. It needs to be at least 100 characters so I am typing some more text here to ensure it passes the length check.',
                content: 'Short content',
                categories: ['Tech'],
                tags: ['News'],
                image: 'some-image-url',
                published: true,
                sources: [],
            });
            expect(result.success).toBe(false);
            expect(result.error.issues.some(i => i.path.includes('content'))).toBe(true);
        });

        it('should fail if categories are missing', () => {
            const result = schema.safeParse({
                title: 'Valid Title Here',
                excerpt: 'This is a long enough excerpt to pass validation. It needs to be at least 100 characters so I am typing some more text here to ensure it passes the length check.',
                content: 'This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. ',
                categories: [],
                tags: ['News'],
                image: 'some-image-url',
                published: true,
                sources: [],
            });
            expect(result.success).toBe(false);
            expect(result.error.issues.some(i => i.path.includes('categories'))).toBe(true);
        });

        it('should fail if tags are missing', () => {
            const result = schema.safeParse({
                title: 'Valid Title Here',
                excerpt: 'This is a long enough excerpt to pass validation. It needs to be at least 100 characters so I am typing some more text here to ensure it passes the length check.',
                content: 'This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. ',
                categories: ['Tech'],
                tags: [],
                image: 'some-image-url',
                published: true,
                sources: [],
            });
            expect(result.success).toBe(false);
            expect(result.error.issues.some(i => i.path.includes('tags'))).toBe(true);
        });

        it('should fail if image is missing', () => {
            const result = schema.safeParse({
                title: 'Valid Title Here',
                excerpt: 'This is a long enough excerpt to pass validation. It needs to be at least 100 characters so I am typing some more text here to ensure it passes the length check.',
                content: 'This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. This is a long enough content to pass validation. It needs to be at least 500 characters so I will copy paste this a few times. ',
                categories: ['Tech'],
                tags: ['News'],
                image: null,
                published: true,
                sources: [],
            });
            expect(result.success).toBe(false);
            expect(result.error.issues.some(i => i.path.includes('image'))).toBe(true);
        });
    });
});
