/**
 * @vitest-environment jsdom
 */
import { describe, it, expect, vi } from 'vitest';
import { render, screen, fireEvent } from '@testing-library/react';
import ArticleForm from '../Components/Form/ArticleForm';

vi.mock('react-i18next', () => ({
    useTranslation: () => ({ t: (key) => key }),
}));

vi.mock('lucide-react', () => ({
    ArrowLeft: () => <span data-testid="icon-arrow-left" />,
    Save: () => <span data-testid="icon-save" />,
    Upload: () => <span data-testid="icon-upload" />,
    X: () => <span data-testid="icon-x" />,
    Plus: () => <span data-testid="icon-plus" />,
}));

vi.mock('@/Components/Ui/Form/Button', () => ({
    Button: ({ children, onClick, type }) => <button onClick={onClick} type={type}>{children}</button>,
}));
vi.mock('@/Components/Ui/Form/Input', () => ({
    Input: (props) => <input {...props} />,
}));
vi.mock('@/Components/Ui/Form/Label', () => ({
    Label: ({ children }) => <label>{children}</label>,
}));
vi.mock('@/Components/Ui/Card', () => ({
    Card: ({ children }) => <div>{children}</div>,
    CardContent: ({ children }) => <div>{children}</div>,
    CardHeader: ({ children }) => <div>{children}</div>,
    CardTitle: ({ children }) => <h3>{children}</h3>,
}));
vi.mock('@/Components/Ui/Form/Switch', () => ({
    Switch: ({ checked, onCheckedChange }) => (
        <input type="checkbox" checked={checked} onChange={(e) => onCheckedChange(e.target.checked)} />
    ),
}));
vi.mock('@/Components/Ui/Form/MultiSelect', () => ({
    MultiSelect: () => <div data-testid="multi-select" />,
}));
vi.mock('@/Components/Ui/Form/Form', () => {
    return {
        Form: ({ children, onSubmit, form }) => <form onSubmit={form.handleSubmit(onSubmit)}>{children}</form>,
        FormControl: ({ children }) => <div>{children}</div>,
        FormDescription: ({ children }) => <p>{children}</p>,
        FormField: ({ render, control, name }) => {
            return render({ field: { name, value: control._defaultValues[name], onChange: vi.fn(), onBlur: vi.fn() } });
        },
        FormItem: ({ children }) => <div>{children}</div>,
        FormLabel: ({ children }) => <label>{children}</label>,
        FormMessage: () => <span />,
    };
});

vi.mock('@Article/Validation', () => ({
    createArticleSchema: () => ({
        parse: vi.fn(),
    })
}));
vi.mock('@hookform/resolvers/zod', () => ({
    zodResolver: () => async () => ({ values: {}, errors: {} }),
}));

vi.mock('@/Components/Ui/Form/FormInput', () => ({
    FormInput: ({ label, placeholder }) => <div><label>{label}</label><input placeholder={placeholder} /></div>,
}));

vi.mock('@/Components/Ui/Form/FormTextareaInput', () => ({
    FormTextareaInput: ({ label, placeholder }) => <div><label>{label}</label><textarea placeholder={placeholder} /></div>,
}));
vi.mock('@/Components/Ui/Form/FormMarkdownInput', () => ({
    FormMarkdownInput: ({ placeholder }) => <div><textarea placeholder={placeholder} /></div>,
}));


describe('ArticleForm', () => {
    const mockSubmit = vi.fn();
    const defaultProps = {
        topTags: [],
        topCategories: [],
        onSubmit: mockSubmit,
        isSubmitting: false,
    };

    it('renders correctly', () => {
        render(<ArticleForm {...defaultProps} />);
        expect(screen.getByText('Create New Article')).toBeDefined();
        expect(screen.getByText('Title *')).toBeDefined();
    });

    it('renders edit mode correctly', () => {
        render(<ArticleForm {...defaultProps} mode="edit" initialValues={{ title: 'Existing' }} />);
        expect(screen.getByText('Edit Article')).toBeDefined();
    });
});
