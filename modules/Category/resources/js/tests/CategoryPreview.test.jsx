/**
 * @vitest-environment jsdom
 */
import { describe, it, expect, vi, afterEach } from 'vitest';
import { render, screen, cleanup } from '@testing-library/react';
import { CategoryPreview } from '../Components/Preview/CategoryPreview';

afterEach(() => {
    cleanup();
});

vi.mock('@inertiajs/react', () => ({
    Link: ({ children, href }) => <a href={href}>{children}</a>,
}));

vi.mock('@/Components/Ui/Badge', () => ({
    Badge: ({ children }) => <span>{children}</span>,
}));

vi.mock('lucide-react', () => ({
    Tag: () => <span data-testid="icon-tag" />,
}));

global.route = vi.fn((name, params) => {
    if (name === 'category.view') return `/category/${params.slug}`;
    return '#';
});

describe('CategoryPreview', () => {
    const props = {
        name: 'Test Category',
        slug: 'test-category',
        total: 5,
    };

    it('renders category name and total count', () => {
        render(<CategoryPreview {...props} />);
        expect(screen.getByText('Test Category')).toBeDefined();
        expect(screen.getByText('5')).toBeDefined();
    });

    it('renders link with correct href', () => {
        const { container } = render(<CategoryPreview {...props} />);
        const link = container.querySelector('a');
        expect(link.getAttribute('href')).toBe('/category/test-category');
    });
});
