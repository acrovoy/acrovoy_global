<?php

namespace App\Http\Controllers\Admin\Content;

use App\Domain\Page\Models\Page;
use App\Domain\Page\Services\PageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Services\Language\LanguageService;

class PageController extends Controller
{
    public function __construct(
        protected PageService $pages,
        private LanguageService $languages
    ) {
    }

    /**
     * Pages list.
     */
    public function index(): View
    {
        return view('dashboard.admin.content.pages.index', [
            'pages' => $this->pages->paginate(),
        ]);
    }

    /**
     * Create page form.
     */
    public function create(): View
    {
        return view('dashboard.admin.content.pages.create', [
        'locales' => $this->languages->activeLocales(),
    ]);
    }

    /**
     * Store page.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->pages->create($request->all());

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page created successfully.');
    }

    /**
     * Edit page form.
     */
    public function edit(Page $page): View
    {
        $page->load('translations');

    return view('dashboard.admin.content.pages.edit', [
        'page' => $page,
        'locales' => $this->languages->activeLocales(),
    ]);
    }

    /**
     * Update page.
     */
    public function update(Request $request, Page $page): RedirectResponse
    {
        $this->pages->update($page, $request->all());

        return redirect()
            ->route('admin.pages.edit', $page)
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Delete page.
     */
    public function destroy(Page $page): RedirectResponse
    {
        $this->pages->delete($page);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    /**
     * Publish page.
     */
    public function publish(Page $page): RedirectResponse
    {
        $this->pages->publish($page);

        return back()->with('success', 'Page published.');
    }

    /**
     * Unpublish page.
     */
    public function unpublish(Page $page): RedirectResponse
    {
        $this->pages->unpublish($page);

        return back()->with('success', 'Page moved to draft.');
    }

    public function upload(Request $request)
{
    $request->validate([
        'upload' => 'required|image|max:2048',
    ]);

    $path = $request->file('upload')->store('pages', 'public');

    return response()->json([
        'uploaded' => true,
        'url' => asset('storage/' . $path)
    ]);
}

public function show(Page $page)
{
    abort_if($page->status !== 'published', 404);

    $page->load('translations');

  

    return view('pages.show', compact('page'));
}


}