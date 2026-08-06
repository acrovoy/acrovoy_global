<?php

namespace App\Domain\Collection\Actions;

use App\Domain\Collection\Models\ProductCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateCollectionAction
{
    /**
     * Create Collection
     */
    public function __invoke(array $data): ProductCollection
    {
        return DB::transaction(function () use ($data) {

            $collection = ProductCollection::create([

                'owner_type'   => $data['owner_type'],
                'owner_id'     => $data['owner_id'],

                'slug'         => $this->generateSlug($data),

                'type'         => $data['type'] ?? 'platform',

                'visibility'   => $data['visibility'] ?? 'public',

                'is_featured'  => $data['is_featured'] ?? false,

                'sort_order'   => $data['sort_order'] ?? 0,

                'published_at' => $data['published_at'] ?? null,

            ]);

            $translations = [];

            foreach ($data['translations'] as $locale => $translation) {

                $translations[] = [

                    'locale'          => $locale,

                    'title'           => $translation['title'],

                    'description'     => $translation['description'] ?? null,

                    'seo_title'       => $translation['seo_title'] ?? null,

                    'seo_description' => $translation['seo_description'] ?? null,

                ];

            }

            $collection
                ->translations()
                ->createMany($translations);

            return $collection->load('translations');

        });
    }

    /**
     * Generate unique slug
     */
    private function generateSlug(array $data): string
    {
        $slug = Str::slug($data['slug'] ?? '');

        if (blank($slug)) {

            $translation = $data['translations']['en']
                ?? reset($data['translations']);

            $slug = Str::slug(
                $translation['title'] ?? 'collection'
            );

        }

        $originalSlug = $slug;

        $counter = 1;

        while (
            ProductCollection::query()
                ->where('slug', $slug)
                ->exists()
        ) {

            $slug = "{$originalSlug}-{$counter}";

            $counter++;

        }

        return $slug;
    }
}