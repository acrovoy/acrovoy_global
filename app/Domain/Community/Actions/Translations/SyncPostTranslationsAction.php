<?php

namespace App\Domain\Community\Actions\Translations;

use App\Domain\Community\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SyncPostTranslationsAction
{
    public function handle(Post $post, array $translations): void
    {
        if (empty($translations)) {
            throw ValidationException::withMessages([
                'translations' => 'At least one translation is required.',
            ]);
        }

        DB::transaction(function () use ($post, $translations) {
            foreach ($translations as $locale => $data) {
                $post->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title' => $data['title'],
                        'excerpt' => $data['excerpt'] ?? null,
                        'content' => $data['content'],
                        'seo_title' => $data['seo_title'] ?? null,
                        'seo_description' => $data['seo_description'] ?? null,
                    ]
                );
            }
        });
    }
}