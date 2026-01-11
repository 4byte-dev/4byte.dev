/**
 * @vitest-environment jsdom
 */
import { describe, it, expect, vi, afterEach } from 'vitest';
import { render, screen, cleanup } from '@testing-library/react';
import { TagPreview } from '../Components/Preview/TagPreview';

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
    Hash: () => <span data-testid="icon-hash" />,
}));

global.route = vi.fn((name, params) => {
    if (name === 'tag.view') {
        return `/tag/${params.slug}`;
    }
    return '#';
});

describe('TagPreview', () => {
    const props = {
        name: 'Test Tag',
        slug: 'test-tag',
        total: 10,
    };

    it('renders tag name and total count', () => {
        render(<TagPreview {...props} />);
        expect(screen.getByText('Test Tag')).toBeDefined();
        expect(screen.getByText('10')).toBeDefined();
    });

    it('renders link with correct href', () => {
        const { container } = render(<TagPreview {...props} />);
        const link = container.querySelector('a');
        expect(link.getAttribute('href')).toBe('/tag/test-tag');
    });
});
