<?php

namespace App\Domain\Page\Actions;

use App\Domain\Page\Models\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreatePageAction
{
    public function __invoke(array $data): Page
    {
        return DB::transaction(function () use ($data) {

            $slug = !empty($data['slug'])
                ? Str::slug($data['slug'])
                : Str::slug($data['translations']['en']['title']);

            $originalSlug = $slug;
            $counter = 1;

            while (Page::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            $page = Page::create([
                'slug'         => $slug,
                'template'     => $data['template'] ?? 'default',
                'status'       => $data['status'] ?? 'draft',
                'sort_order'   => $data['sort_order'] ?? 0,
                'created_by'   => auth()->id(),
                'updated_by'   => auth()->id(),
                'published_at' => ($data['status'] ?? 'draft') === 'published'
                    ? now()
                    : null,
            ]);

            foreach ($data['translations'] as $locale => $translation) {

            

                $page->translations()->create([
                    
                    'locale'          => $locale,
                    'title'           => $translation['title'] ?? '',
                    'excerpt'         => $translation['excerpt'] ?? null,
                    'content'         => $translation['content'] ?? null,
                    'seo_title'       => $translation['seo_title'] ?? null,
                    'seo_description' => $translation['seo_description'] ?? null,
                    'seo_keywords'    => $translation['seo_keywords'] ?? null,
                ]);

         



            }

            return $page->load('translations');
        });
    }
}