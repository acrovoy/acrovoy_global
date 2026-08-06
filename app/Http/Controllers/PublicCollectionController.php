<?php

namespace App\Http\Controllers;

use App\Domain\Collection\Models\ProductCollection;

class PublicCollectionController extends Controller
{

public function index()
{
    $featured = ProductCollection::query()
        ->whereNotNull('published_at')
        ->where('is_featured', true)
        ->with([
            'translation',
            'cover',
        ])
        ->withCount('products')
        ->latest('published_at')
        ->first();

    $collections = ProductCollection::query()
        ->whereNotNull('published_at')
        ->when($featured, fn ($q) => $q->whereKeyNot($featured->id))
        ->with([
            'translation',
            'cover',
        ])
        ->withCount('products')
        ->latest('published_at')
        ->paginate(12);

    return view('collection.index', compact(
        'featured',
        'collections'
    ));
}


    public function show(ProductCollection $collection)
{
    $collection->load([

        'translations',

        'cover',

        'products' => function ($q) {

            $q->with([
                'translations',
                'images',
                'supplier',
            ])
            ->orderBy('collectionables.sort_order');

        },

    ]);


    $suppliers = $collection->products
        ->pluck('supplier')
        ->filter()
        ->unique('id');


    return view('collection.show', compact(
        'collection',
        'suppliers'
    ));
}


}