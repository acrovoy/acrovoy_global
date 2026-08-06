<?php

namespace App\Domain\Collection\Actions;

use App\Domain\Collection\Models\ProductCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateCollectionAction
{
    /**
     * Update Collection
     */
    public function __invoke(
        ProductCollection $collection,
        array $data
    ): ProductCollection {

        return DB::transaction(function () use (
            $collection,
            $data
        ) {

            /*
            |--------------------------------------------------------------------------
            | Update Collection
            |--------------------------------------------------------------------------
            */

            $collection->update([

                'slug' => $this->generateSlug(
                    $collection,
                    $data
                ),

                'subtitle' => $data['subtitle'] ?? null,

                'overview' => $data['overview'] ?? null,

                'ideal_for' => $data['ideal_for'] ?? null,

                'procurement_notes' => $data['procurement_notes'] ?? null,

                'type' => $data['type']
                    ?? $collection->type,

                'visibility' => $data['visibility']
                    ?? $collection->visibility,

                'is_featured' => isset($data['is_featured']),

                'sort_order' => $data['sort_order']
                    ?? $collection->sort_order,

                'published_at' => $data['published_at']
                    ?? $collection->published_at,

            ]);


            /*
            |--------------------------------------------------------------------------
            | Translations
            |--------------------------------------------------------------------------
            */

            foreach ($data['translations'] as $locale => $translation) {

                $collection
                    ->translations()
                    ->updateOrCreate(

                        [
                            'locale' => $locale,
                        ],

                        [
                            'title' => $translation['title'],

                            'description' => $translation['description'] ?? null,

                            'seo_title' => $translation['seo_title'] ?? null,

                            'seo_description' => $translation['seo_description'] ?? null,

                        ]

                    );

            }


            /*
            |--------------------------------------------------------------------------
            | Highlights
            |--------------------------------------------------------------------------
            */

            $collection->highlights()->delete();

            foreach ($data['highlights'] ?? [] as $index => $highlight) {

                $highlight = trim($highlight);

                if ($highlight === '') {
                    continue;
                }

                $collection->highlights()->create([

                    'title' => $highlight,

                    'sort_order' => $index,

                ]);

            }


            return $collection->load([
                'translations',
                'highlights',
            ]);

        });

    }

    /**
     * Generate unique slug
     */
    private function generateSlug(
        ProductCollection $collection,
        array $data
    ): string {

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

                ->whereKeyNot($collection->id)

                ->exists()

        ) {

            $slug = "{$originalSlug}-{$counter}";

            $counter++;

        }

        return $slug;

    }
}