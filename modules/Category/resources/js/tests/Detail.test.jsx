/**
 * @vitest-environment jsdom
 */
import { describe, it, expect, vi, afterEach } from 'vitest';
import { render, screen, cleanup, fireEvent } from '@testing-library/react';
import CategoryDetailPage from '../Pages/Category/Detail';

afterEach(() => {
    cleanup();
});

vi.mock('react-i18next', () => ({
    useTranslation: () => ({ t: (key) => key }),
}));

const mockMutate = vi.fn();
vi.mock('@tanstack/react-query', () => ({
    useMutation: () => ({
        mutate: () => {
            mockMutate();
        }
    }),
}));

vi.mock('@inertiajs/react', () => ({
    Link: ({ children, href }) => <a href={href}>{children}</a>,
}));

vi.mock('@/Stores/AuthStore', () => ({
    useAuthStore: () => ({ isAuthenticated: true }),
}));

vi.mock('@/Hooks/useToast', () => ({
    toast: vi.fn(),
}));

vi.mock('@React/Api', () => ({
    default: {
        follow: vi.fn(),
    },
}));

vi.mock('@/Components/Ui/Form/Button', () => ({
    Button: ({ children, onClick }) => <button onClick={onClick}>{children}</button>,
}));

vi.mock('@/Components/Ui/Card', () => ({
    Card: ({ children }) => <div>{children}</div>,
    CardContent: ({ children }) => <div>{children}</div>,
}));

vi.mock('@/Components/Ui/Badge', () => ({
    Badge: ({ children }) => <span>{children}</span>,
}));

vi.mock('@/Components/Content/Feed', () => ({
    default: () => <div data-testid="feed" />,
}));

vi.mock('lucide-react', () => ({
    UserCheck: () => <span />,
    UserPlus: () => <span />,
    Tag: () => <span />,
}));

global.route = vi.fn((name, params) => {
    if (name === 'tag.view') return `/tag/${params.slug}`;
    return '#';
});

describe('CategoryDetailPage', () => {
    const defaultProps = {
        category: {
            name: 'React',
            slug: 'react',
            followers: 100,
            isFollowing: false,
        },
        profile: {
            color: '#61dafb',
            description: 'A JavaScript library for building user interfaces',
        },
        articles: 50,
        news: 10,
        tags: [
            { name: 'JavaScript', slug: 'javascript' },
            { name: 'Frontend', slug: 'frontend' },
        ],
    };

    it('renders category information correctly', () => {
        render(<CategoryDetailPage {...defaultProps} />);
        expect(screen.getByText('React')).toBeDefined();
        expect(screen.getByText('A JavaScript library for building user interfaces')).toBeDefined();
    });

    it('renders statistics cards', () => {
        render(<CategoryDetailPage {...defaultProps} />);
        expect(screen.getByText('50')).toBeDefined();
        expect(screen.getByText('10')).toBeDefined();
        expect(screen.getByText('60')).toBeDefined();
        expect(screen.getByText('100')).toBeDefined();
    });

    it('renders related tags', () => {
        render(<CategoryDetailPage {...defaultProps} />);
        expect(screen.getByText('JavaScript')).toBeDefined();
        expect(screen.getByText('Frontend')).toBeDefined();
    });

    it('renders Follow button when not following', () => {
        render(<CategoryDetailPage {...defaultProps} />);
        expect(screen.getByText('Follow')).toBeDefined();
    });

    it('renders Following button when following', () => {
        const props = {
            ...defaultProps,
            category: { ...defaultProps.category, isFollowing: true },
        };
        render(<CategoryDetailPage {...props} />);
        expect(screen.getByText('Following')).toBeDefined();
    });

    it('calls mutation on follow button click', () => {
        render(<CategoryDetailPage {...defaultProps} />);
        const button = screen.getByText('Follow');
        fireEvent.click(button);
        expect(mockMutate).toHaveBeenCalled();
    });
});
