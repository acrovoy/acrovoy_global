<?php

namespace App\Domain\Page\Services;

use App\Domain\Page\Actions\CreatePageAction;
use App\Domain\Page\Actions\DeletePageAction;
use App\Domain\Page\Actions\PublishPageAction;
use App\Domain\Page\Actions\UnpublishPageAction;
use App\Domain\Page\Actions\UpdatePageAction;
use App\Domain\Page\Models\Page;

class PageService
{
    public function __construct(
        protected CreatePageAction $createPageAction,
        protected UpdatePageAction $updatePageAction,
        protected DeletePageAction $deletePageAction,
        protected PublishPageAction $publishPageAction,
        protected UnpublishPageAction $unpublishPageAction,
    ) {
    }

    /**
     * Create page.
     */
    public function create(array $data): Page
    {
        return ($this->createPageAction)($data);
    }

    /**
     * Update page.
     */
    public function update(Page $page, array $data): Page
    {
        return ($this->updatePageAction)($page, $data);
    }

    /**
     * Delete page.
     */
    public function delete(Page $page): void
    {
        ($this->deletePageAction)($page);
    }

    /**
     * Publish page.
     */
    public function publish(Page $page): Page
    {
        return ($this->publishPageAction)($page);
    }

    /**
     * Unpublish page.
     */
    public function unpublish(Page $page): Page
    {
        return ($this->unpublishPageAction)($page);
    }

    /**
     * Find page by ID.
     */
    public function find(int $id): ?Page
    {
        return Page::with('translations')->find($id);
    }

    /**
     * Get page by slug.
     */
    public function findBySlug(string $slug): ?Page
    {
        return Page::with('translations')
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get all pages.
     */
    public function all()
    {
        return Page::with('translation')
            ->ordered()
            ->get();
    }

    /**
     * Paginate pages.
     */
    public function paginate(int $perPage = 20)
    {
        return Page::with('translation')
            ->ordered()
            ->paginate($perPage);
    }

    /**
     * Get published pages.
     */
    public function published()
    {
        return Page::with('translation')
            ->published()
            ->ordered()
            ->get();
    }
}