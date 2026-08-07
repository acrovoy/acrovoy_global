<?php

namespace App\Domain\Page\Actions;

use App\Domain\Page\Models\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdatePageAction
{
    public function __invoke(Page $page, array $data): Page
    {
        return DB::transaction(function () use ($page, $data) {

            $slug = !empty($data['slug'])
                ? Str::slug($data['slug'])
                : Str::slug($data['translations']['en']['title']);

            $originalSlug = $slug;
            $counter = 1;

            while (
                Page::where('slug', $slug)
                    ->where('id', '!=', $page->id)
                    ->exists()
            ) {
                $slug = $originalSlug . '-' . $counter++;
            }

            $page->update([
                'slug'         => $slug,
                'template'     => $data['template'] ?? $page->template,
                'status'       => $data['status'] ?? $page->status,
                'sort_order'   => $data['sort_order'] ?? $page->sort_order,
                'updated_by'   => auth()->id(),
                'published_at' => ($data['status'] ?? $page->status) === 'published'
                    ? ($page->published_at ?? now())
                    : null,
            ]);

            foreach ($data['translations'] as $locale => $translation) {

                $page->translations()->updateOrCreate(
                    [
                        'locale' => $locale,
                    ],
                    [
                        'title'           => $translation['title'] ?? '',
                        'excerpt'         => $translation['excerpt'] ?? null,
                        'content'         => $translation['content'] ?? null,
                        'seo_title'       => $translation['seo_title'] ?? null,
                        'seo_description' => $translation['seo_description'] ?? null,
                        'seo_keywords'    => $translation['seo_keywords'] ?? null,
                    ]
                );
            }

            return $page->load('translations');
        });
    }
}