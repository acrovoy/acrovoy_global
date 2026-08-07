<?php

namespace App\Domain\Page\Actions;

use App\Domain\Page\Models\Page;
use Illuminate\Support\Facades\DB;

class DeletePageAction
{
    public function __invoke(Page $page): void
    {
        DB::transaction(function () use ($page) {
            $page->delete();
        });
    }
}