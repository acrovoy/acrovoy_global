<?php

namespace App\Http\Controllers;

use App\Domain\Collection\Services\ProductCollectionService;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Domain\Collection\Models\ProductCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductCollectionController extends Controller
{
    public function __construct(
        protected ProductCollectionService $collections
    ) {
    }

    /**
     * Collections list
     */
    public function index(): View
{
    $collections = $this->collections
        ->myCollections()
        ->with([
            'translations',
            'cover',
        ])
        ->latest()
        ->paginate(20);

    return view(
        'dashboard.supplier.collections.index',
        compact('collections')
    );
}

    /**
     * Create form
     */
    public function create(): View
    {
        return view(
            'dashboard.supplier.collections.create'
        );
    }

    /**
     * Store
     */
    public function store(Request $request): RedirectResponse
{
    $collection = $this->collections->create($request->all());

    return redirect()
        ->route('supplier.collections.edit', $collection)
        ->with('success', __('Collection created successfully.'));
}

    /**
     * Show
     */
    public function show(
        ProductCollection $collection
    ): View {

        $collection->load([
            'translations',
            'products',
            'cover',
        ]);

        return view(
            'dashboard.supplier.collections.show',
            compact('collection')
        );
    }

    /**
     * Edit
     */
    public function edit(
        ProductCollection $collection
    ): View {

        $collection->load([
            'translations',
            'products',
            'cover',
        ]);

        return view(
            'dashboard.supplier.collections.edit',
            compact('collection')
        );
    }

    /**
     * Update
     */
    public function update(
        Request $request,
        ProductCollection $collection
    ): RedirectResponse {

        $this->collections->update(
            $collection,
            $request->validated()
        );

        return back()->with(
            'success',
            __('Collection updated successfully.')
        );
    }

    /**
     * Delete
     */
    public function destroy(
        ProductCollection $collection
    ): RedirectResponse {

        $this->collections->delete(
            $collection
        );

        return redirect()
            ->route('supplier.collections.index')
            ->with(
                'success',
                __('Collection deleted successfully.')
            );
    }
}