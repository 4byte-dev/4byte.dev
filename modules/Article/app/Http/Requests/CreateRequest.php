<?php

namespace Modules\Article\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Modules\Article\Models\Article;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isDraft = ! $this->boolean('published', false);

        $rules = [
            'title' => ['required', 'string', 'min:10'],
        ];

        $maxSize = config('article.max_file_size', 5 * 1024 * 1024);

        if (! $isDraft) {
            $rules = array_merge($rules, [
                'excerpt'          => ['required', 'string', 'min:100'],
                'content'          => ['required', 'string', 'min:500'],
                'categories'       => ['required', 'array', 'min:1', 'max:3'],
                'categories.*'     => ['string'],
                'tags'             => ['required', 'array', 'min:1', 'max:3'],
                'tags.*'           => ['string'],
                'image'            => ['required', 'file', 'image'],
                'sources'          => ['required', 'array', 'min:1'],
                'sources.*.url'    => ['required', 'string', 'url'],
                'sources.*.date'   => ['required', 'date'],
                'content_images'   => ['nullable', 'array'],
                'content_images.*' => ['image', "max:{$maxSize}"],
            ]);
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $isDraft = ! $this->boolean('published', false);

            /** @var Article $article */
            $article = $this->route('article');

            if (
                ! $isDraft &&
                ! $this->hasFile('image') &&
                ! $article?->hasMedia('cover')
            ) {
                $validator->addRules([
                    'image' => ['required'],
                ]);
            }
        });
    }

    public function createSlug(?int $ignoreId = null): string
    {
        $title    = $this->input('title');
        $baseSlug = Str::slug($title);
        $slug     = $baseSlug;
        $counter  = 1;

        while (
            Article::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
