
import { describe, it, expect, vi, beforeEach } from 'vitest';
import Api from '../Api';
import ApiService from "@/Services/ApiService";

global.route = vi.fn((name, params) => {
    if (name === 'api.article.store') return '/api/article';
    if (name === 'api.article.update') return `/api/article/${params.slug}`;
    return name;
});

vi.mock("@/Services/ApiService", () => ({
    default: {
        fetchJson: vi.fn(),
    },
}));

describe('Article API', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('createArticle calls ApiService.fetchJson correctly', () => {
        const data = { title: 'Test' };
        Api.createArticle(data);

        expect(ApiService.fetchJson).toHaveBeenCalledWith(
            '/api/article',
            data,
            { isMultipart: true }
        );
    });

    it('editArticle calls ApiService.fetchJson correctly', () => {
        const slug = 'test-slug';
        const data = { title: 'Updated' };
        Api.editArticle(slug, data);

        expect(ApiService.fetchJson).toHaveBeenCalledWith(
            `/api/article/${slug}`,
            data,
            { method: 'PUT', isMultipart: true }
        );
    });
});
