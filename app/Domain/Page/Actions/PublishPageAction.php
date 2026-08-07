<?php

namespace App\Domain\Page\Actions;

use App\Domain\Page\Models\Page;
use Illuminate\Support\Facades\DB;

class PublishPageAction
{
    public function __invoke(Page $page): Page
    {
        DB::transaction(function () use ($page) {

            $page->update([
                'status'       => 'published',
                'published_at' => $page->published_at ?? now(),
                'updated_by'   => auth()->id(),
            ]);

        });

        return $page->fresh('translations');
    }
}