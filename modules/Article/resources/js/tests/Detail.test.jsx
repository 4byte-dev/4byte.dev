/**
 * @vitest-environment jsdom
 */
import { describe, it, expect, vi, afterEach } from 'vitest';
import { render, screen, cleanup } from '@testing-library/react';
import ArticlePage from '../Pages/Article/Detail';

afterEach(() => {
    cleanup();
});

vi.mock('react-i18next', () => ({
    useTranslation: () => ({ t: (key) => key }),
}));
vi.mock('@/Stores/AuthStore', () => ({
    useAuthStore: () => ({ isAuthenticated: true, user: { id: 1 } }),
}));
vi.mock('@/Hooks/useToast', () => ({
    toast: vi.fn(),
}));
vi.mock('@tanstack/react-query', () => ({
    useMutation: () => ({ mutate: vi.fn() }),
}));
vi.mock('@inertiajs/react', () => ({
    Link: ({ children, href }) => <a href={href}>{children}</a>,
}));

global.route = vi.fn(() => '#');

global.IntersectionObserver = class IntersectionObserver {
    constructor() { }
    observe() { return null; }
    disconnect() { return null; }
    unobserve() { return null; }
};

vi.mock('@/Components/Ui/Avatar', () => ({
    Avatar: ({ children }) => <div>{children}</div>,
    AvatarFallback: ({ children }) => <div>{children}</div>,
    AvatarImage: ({ src }) => <img src={src} />,
}));
vi.mock('@/Components/Ui/Form/Button', () => ({
    Button: ({ children, onClick }) => <button onClick={onClick}>{children}</button>,
}));
vi.mock('@/Components/Ui/Badge', () => ({
    Badge: ({ children }) => <span>{children}</span>,
}));
vi.mock('@/Components/Ui/Separator', () => ({
    Separator: () => <hr />,
}));
vi.mock('@/Components/Common/UserProfileHover', () => ({
    UserProfileHover: ({ children }) => <div>{children}</div>,
}));
vi.mock('@/Components/Common/MarkdownRenderer', () => ({
    default: ({ content }) => <div>{content}</div>,
}));
vi.mock('@/Components/Content/Feed', () => ({
    default: () => <div data-testid="feed" />,
}));
vi.mock('@React/Components/Comments', () => ({
    Comments: () => <div data-testid="comments" />,
}));
vi.mock('@Article/Components/TableOfContents', () => ({
    default: () => <div data-testid="toc" />,
}));

vi.mock('lucide-react', () => ({
    Calendar: () => <span />,
    Share2: () => <span />,
    Bookmark: () => <span />,
    Edit: () => <span />,
    Tag: () => <span />,
    Hash: () => <span />,
    ThumbsUp: () => <span />,
    ThumbsDown: () => <span />,
    Check: () => <span />,
}));

describe('ArticlePage', () => {
    const article = {
        title: 'Test Article',
        slug: 'test-article',
        content: 'Content',
        likes: 10,
        dislikes: 2,
        isLiked: false,
        isDisliked: false,
        isSaved: false,
        published_at: '2023-01-01',
        user: { name: 'John Doe', username: 'johndoe', avatar: 'avatar.jpg' },
        categories: [{ name: 'Tech', slug: 'tech' }],
        tags: [{ name: 'React', slug: 'react' }],
        canUpdate: false,
        sources: [],
    };

    it('renders article content correctly', () => {
        render(<ArticlePage article={article} />);
        expect(screen.getByText('Test Article')).toBeDefined();
        expect(screen.getByText('John Doe')).toBeDefined();
        expect(screen.getByText('Content')).toBeDefined();
    });

    it('renders likes count', () => {
        render(<ArticlePage article={article} />);
        expect(screen.getByText('10')).toBeDefined();
    });
});
