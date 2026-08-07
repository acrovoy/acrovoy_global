<?php

namespace App\Domain\Page\Actions;

use App\Domain\Page\Models\Page;
use Illuminate\Support\Facades\DB;

class UnpublishPageAction
{
    public function __invoke(Page $page): Page
    {
        DB::transaction(function () use ($page) {

            $page->update([
                'status'     => 'draft',
                'updated_by' => auth()->id(),
            ]);

        });

        return $page->fresh('translations');
    }
}