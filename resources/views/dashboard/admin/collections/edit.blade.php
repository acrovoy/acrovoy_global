@extends('dashboard.admin.layout')

@section('dashboard-content')


<a href="{{ route('admin.collections.index') }}"
   class="inline-flex items-center gap-1 text-sm text-gray-500 transition hover:text-gray-700">

    <svg class="w-4 h-4"
         fill="none"
         stroke="currentColor"
         viewBox="0 0 24 24">
        <path stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 19l-7-7 7-7"/>
    </svg>

    Back to Collections

</a>
<div class="max-w-5xl mx-auto">

    {{-- ================= HEADER ================= --}}
    <div class="mb-8">

        <div class="flex items-start justify-between gap-6">

            <div>

                <div class="flex items-center gap-3">

                    <h1 class="text-3xl font-semibold tracking-tight text-gray-900">
                        Edit Collection
                    </h1>

                    @if($collection->visibility === 'public')
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 border border-emerald-200">
                            Public
                        </span>
                    @elseif($collection->visibility === 'draft')
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 border border-amber-200">
                            Draft
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 border border-gray-200">
                            Private
                        </span>
                    @endif

                </div>

                <p class="mt-3 max-w-3xl text-sm leading-6 text-gray-500">
                    Update collection information, manage visibility and maintain
                    a curated group of products presented across the marketplace.
                </p>

            </div>

           

        </div>

    </div>

    <x-alerts />

    <form method="POST"
          action="{{ route('admin.collections.update', $collection) }}"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

            {{-- Top Summary --}}
            <div class="border-b border-gray-200 bg-gradient-to-r from-gray-900 via-gray-800 to-black px-8 py-8">

                <div class="flex items-center gap-6">

                    <div class="h-28 w-40 overflow-hidden rounded-xl border border-white/10 bg-white/10">

                        <img
                            src="{{ $collection->cover?->cdn_url ?? asset('images/no-image.png') }}"
                            class="h-full w-full object-cover">

                    </div>

                    <div class="flex-1">

                        <div class="text-xs uppercase tracking-[0.25em] text-gray-400">
                            Collection
                        </div>

                        <div class="mt-2 text-2xl font-semibold text-white">

                            {{ $collection->currentTranslation?->title ?? 'Untitled Collection' }}

                        </div>

                        <div class="mt-4 flex flex-wrap gap-5 text-sm text-gray-300">

                            <div>
                                <span class="text-gray-500">ID</span><br>
                                {{ $collection->public_id }}
                            </div>

                            <div>
                                <span class="text-gray-500">Products</span><br>
                                {{ $collection->products_count ?? $collection->products()->count() }}
                            </div>

                            <div>
                                <span class="text-gray-500">Type</span><br>
                                {{ ucfirst($collection->type) }}
                            </div>

                            <div>
                                <span class="text-gray-500">Created</span><br>
                                {{ $collection->created_at->format('d M Y') }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="p-8 space-y-10">


                            {{-- ================= GENERAL ================= --}}
                <div>

                    <div class="mb-6">

                        <h2 class="text-lg font-semibold text-gray-900">
                            General Information
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Define how this collection is presented throughout the marketplace.
                        </p>

                    </div>

                    <div class="space-y-7">

                        {{-- TITLE --}}
                        <div>

                            <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 mb-2">
                                Collection Title
                            </label>

                            <input
                                type="text"
                                name="translations[en][title]"
                                value="{{ old('translations.en.title', $collection->currentTranslation?->title) }}"
                                placeholder="Luxury Hotel Collection"
                                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm transition focus:border-gray-900 focus:ring-4 focus:ring-gray-900/5 focus:outline-none">

                            <p class="mt-2 text-xs text-gray-400">
                                Public title displayed across the marketplace.
                            </p>

                        </div>

                        {{-- SLUG --}}
                        <div>

                            <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 mb-2">
                                URL Slug
                            </label>

                            <input
                                type="text"
                                name="slug"
                                value="{{ old('slug', $collection->slug) }}"
                                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm transition focus:border-gray-900 focus:ring-4 focus:ring-gray-900/5 focus:outline-none">

                            <p class="mt-2 text-xs text-gray-400">
                                SEO friendly URL identifier.
                            </p>

                        </div>

                        {{-- DESCRIPTION --}}
                        <div>

                            <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 mb-2">
                                Description
                            </label>

                            <textarea
                                rows="8"
                                name="translations[en][description]"
                                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm transition focus:border-gray-900 focus:ring-4 focus:ring-gray-900/5 focus:outline-none">{{ old('translations.en.description', $collection->currentTranslation?->description) }}</textarea>

                            <p class="mt-2 text-xs text-gray-400">
                                Describe the purpose, audience and overall concept of this collection.
                            </p>

                        </div>

                    </div>

                </div>

               {{-- ================= COVER IMAGE ================= --}}
<div class="border-t border-gray-100 pt-10">

    <div class="mb-6">

        <h2 class="text-lg font-semibold text-gray-900">
            Cover Image
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Upload a new cover image to replace the current one.
        </p>

    </div>

    <label
        for="cover_file"
        class="group relative block overflow-hidden rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 cursor-pointer transition hover:border-gray-900">

        <img
            id="cover-preview"
            src="{{ $collection->cover?->cdn_url ?? asset('images/no-image.png') }}"
            class="aspect-[16/9] w-full object-cover">

        <div class="absolute inset-0 flex items-center justify-center bg-black/0 transition group-hover:bg-black/40">

            <div
                class="rounded-xl bg-white/95 px-5 py-3 text-sm font-medium text-gray-900 opacity-0 shadow-lg transition group-hover:opacity-100">

                Replace Cover Image

            </div>

        </div>

        <input
            id="cover_file"
            type="file"
            name="cover"
            class="hidden"
            accept="image/png,image/jpeg,image/webp">

    </label>

    <p class="mt-3 text-xs text-gray-400">
        PNG, JPG or WebP · Recommended size 1600 × 900 px.
    </p>

</div>


                                {{-- ================= SETTINGS ================= --}}
                <div class="border-t border-gray-100 pt-10">

                    <div class="mb-6">

                        <h2 class="text-lg font-semibold text-gray-900">
                            Collection Settings
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Control how this collection is published and displayed.
                        </p>

                    </div>

                    <div class="grid lg:grid-cols-2 gap-8">

                        {{-- Visibility --}}
                        <div>

                            <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 mb-2">
                                Visibility
                            </label>

                            <select
                                name="visibility"
                                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm transition focus:border-gray-900 focus:ring-4 focus:ring-gray-900/5">

                                <option value="public" @selected(old('visibility', $collection->visibility) === 'public')>
                                    Public
                                </option>

                                <option value="draft" @selected(old('visibility', $collection->visibility) === 'draft')>
                                    Draft
                                </option>

                                <option value="private" @selected(old('visibility', $collection->visibility) === 'private')>
                                    Private
                                </option>

                            </select>

                        </div>

                        {{-- Type --}}
                        <div>

                            <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 mb-2">
                                Collection Type
                            </label>

                            <select
                                name="type"
                                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm transition focus:border-gray-900 focus:ring-4 focus:ring-gray-900/5">

                                <option value="platform" @selected(old('type', $collection->type) === 'platform')>
                                    Platform
                                </option>

                                <option value="supplier" @selected(old('type', $collection->type) === 'supplier')>
                                    Supplier
                                </option>

                                <option value="buyer" @selected(old('type', $collection->type) === 'buyer')>
                                    Buyer
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="mt-8 rounded-2xl border border-gray-200 bg-gray-50 p-6">

                        <label class="flex items-start gap-4 cursor-pointer">

                            <input
                                type="checkbox"
                                name="is_featured"
                                value="1"
                                @checked(old('is_featured', $collection->is_featured))
                                class="mt-1 h-5 w-5 rounded border-gray-300 text-gray-900 focus:ring-gray-900">

                            <div>

                                <div class="font-semibold text-gray-900">
                                    Featured Collection
                                </div>

                                <div class="mt-1 text-sm text-gray-500">
                                    Featured collections receive priority placement
                                    across homepage sections and curated showcases.
                                </div>

                            </div>

                        </label>

                    </div>

                </div>


<input type="hidden" id="collection-id" value="{{ $collection->id }}">
                
{{-- ================= PRODUCTS ================= --}}
<div class="border-t border-gray-100 pt-10">

    <div class="mb-6">

        <h2 class="text-lg font-semibold text-gray-900">
            Collection Products
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Search products and add them to this collection. Products can be
            reordered after being added.
        </p>

    </div>

    <div class="grid lg:grid-cols-[430px_1fr] gap-8">

        {{-- ================= SEARCH ================= --}}
        <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">

            <div class="border-b border-gray-200 p-5">

                <label class="block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 mb-3">
                    Search Products
                </label>

                <div class="relative">

                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M21 21l-4.3-4.3m1.8-5.2a7 7 0 11-14 0 7 7 0 0114 0z"/>

                    </svg>

                    <input
                        id="collection-product-search"
                        type="text"
                        placeholder="Search by product name or SKU..."
                        class="w-full rounded-xl border border-gray-200 pl-12 pr-4 py-3 text-sm shadow-sm transition
                               focus:border-gray-900 focus:ring-4 focus:ring-gray-900/5 focus:outline-none">

                </div>

            </div>

            <div
                id="collection-search-results"
                class="h-[520px] overflow-y-auto bg-gray-50 items-center justify-center">

                <div class="flex h-full items-center justify-center">

                
                <div class="text-center max-w-xs">

                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white border border-gray-200">

                        <svg class="w-7 h-7 text-gray-400"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M21 21l-4.3-4.3m1.8-5.2a7 7 0 11-14 0 7 7 0 0114 0z"/>

                        </svg>

                    </div>

                    <h3 class="mt-5 text-base font-semibold text-gray-900">
                        Search Products
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-gray-500">
                        Start typing a product name, SKU or keyword to search
                        your catalog.
                    </p>

                </div>

                </div>
            </div>

        </div>

        {{-- ================= SELECTED ================= --}}
        <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">

            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5">

    <div>

        <h3 class="font-semibold text-gray-900">
            Products in Collection
        </h3>

        <p id="collection-products-count"
           class="mt-1 text-sm text-gray-500">
            {{ $collection->products()->count() }} products
        </p>

    </div>

</div>

            <div id="collection-products-list"
     class="h-[520px] bg-gray-50 overflow-y-auto">

</div>

        </div>

    </div>

</div>





                {{-- ================= INFO ================= --}}
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">

                    <div class="flex gap-4">

                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-white">

                            <svg class="w-5 h-5"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M13 16h-1v-4h-1m1-4h.01M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                            </svg>

                        </div>

                        <div>

                            <h3 class="font-semibold text-slate-900">
                                Collection Management
                            </h3>

                            <ul class="mt-3 space-y-2 text-sm text-slate-700">

                                <li>• Products can be added or removed at any time.</li>
                                <li>• Display order can be customized independently.</li>
                                <li>• Hidden collections remain accessible to administrators.</li>
                                <li>• Changes become visible immediately after saving.</li>

                            </ul>

                        </div>

                    </div>

                </div>

            </div>

            {{-- ================= FOOTER ================= --}}
            <div class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-8 py-5">

                <button
    id="deleteCollectionButton"
    type="button"
    class="rounded-lg border border-red-200 bg-white px-5 py-2.5 text-sm font-medium text-red-600 transition hover:bg-red-50">

    Delete Collection

</button>

                <div class="flex items-center gap-3">

                    <a href="{{ route('admin.collections.index') }}"
                       class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-100">

                        Cancel

                    </a>

                    <button
                        type="submit"
                        class="rounded-lg bg-gray-900 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-black">

                        Save Changes

                    </button>

                </div>

            </div>

        </div>

    </form>


    <form
    id="deleteCollectionForm"
    method="POST"
    action="{{ route('admin.collections.destroy', $collection) }}"
    >

    @csrf
    @method('DELETE')

</form>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const button = document.getElementById('deleteCollectionButton');

    if (!button) {
        return;
    }

    button.addEventListener('click', () => {

        window.confirmModal.open({

            type: 'danger',

            title: 'Delete Collection',

            message: 'Are you sure you want to delete this collection?',

            description:
                'This action cannot be undone. The collection and all related data will be permanently deleted.',

            confirmText: 'Delete Collection',

            cancelText: 'Cancel',

            onConfirm: () => {
                document
                    .getElementById('deleteCollectionForm')
                    .submit();
            }

        });

    });

});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const input = document.getElementById('cover_file');
    const preview = document.getElementById('cover-preview');

    input.addEventListener('change', function () {

        const file = this.files[0];

        if (!file) {
            return;
        }

        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
        };

        reader.readAsDataURL(file);

    });

});
</script>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('collection-product-search');
    const results = document.getElementById('collection-search-results');

    if (!searchInput) {
        return;
    }


    let timer = null;


    searchInput.addEventListener('input', function () {

        clearTimeout(timer);

        const query = this.value.trim();


        if (query.length < 2) {

            results.innerHTML = `
    <div class="flex h-full items-center justify-center">

        <div class="text-center max-w-xs">

            <h3 class="text-base font-semibold text-gray-900">
                Search Products
            </h3>

            <p class="mt-2 text-sm text-gray-500">
                Start typing a product name, SKU or keyword to search.
            </p>

        </div>

    </div>
`;

            return;
        }


        timer = setTimeout(() => {

            searchProducts(query);

        }, 400);


    });


    function loadCollectionProducts()
{

    const collectionId =
        document.getElementById('collection-id').value;


    fetch(
        `/dashboard/admin/collections/${collectionId}/products`
    )

    .then(response => response.json())

    .then(products => {


        const list =
            document.getElementById('collection-products-list');


        const count =
            document.getElementById('collection-products-count');


        count.innerHTML =
            `${products.length} products`;


        if(!products.length){

            list.innerHTML = `
                <div class="flex items-center justify-center h-full">

                    <div class="text-center">

                        <h3 class="font-semibold text-gray-900">
                            No Products Yet
                        </h3>

                        <p class="mt-2 text-sm text-gray-500">
                            Search products and add them.
                        </p>

                    </div>

                </div>
            `;

            return;
        }



      list.innerHTML = products.map(product => `

<div
    class="collection-product-item flex items-center gap-4 p-4 border-b border-gray-100 bg-white"
    data-id="${product.id}">

    <div class="drag-handle cursor-grab text-gray-400 text-lg select-none">
        ☰
    </div>

    <img
        src="${product.image ?? '/images/no-image.png'}"
        class="h-14 w-14 rounded-lg object-cover">

    <div class="flex-1">

        <div class="font-medium text-gray-900">
            ${product.name}
        </div>

        <div class="text-xs text-gray-500">
            SKU: ${product.sku}
        </div>

    </div>

    <button
        type="button"
        class="remove-product-btn rounded-lg border border-red-200 px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50"
        data-product-id="${product.id}">

        Remove

    </button>

</div>

`).join('');

    });

}



    function searchProducts(query)
    {

        const collectionId =
            document.getElementById('collection-id').value;


        fetch(
            `/dashboard/admin/collections/${collectionId}/search-products?q=${encodeURIComponent(query)}`
        )

        .then(response => response.json())

        .then(products => {


            if (!products.length) {

                results.innerHTML = `
                    <div class="text-center">

                        <h3 class="font-semibold text-gray-900">
                            No products found
                        </h3>

                        <p class="mt-2 text-sm text-gray-500">
                            Try another search query.
                        </p>

                    </div>
                `;

                return;

            }



            results.innerHTML = products.map(product => `

                <div class="flex items-center gap-4 border-b border-gray-100 p-4 bg-white">


                    <img
                        src="${product.image ?? '/images/no-image.png'}"
                        class="h-16 w-16 rounded-lg object-cover border border-gray-200">


                    <div class="flex-1">

                        <div class="font-medium text-gray-900">
                            ${product.name}
                        </div>


                        <div class="mt-1 text-xs text-gray-500">
                            SKU: ${product.sku}
                        </div>

                    </div>


                    <button
    type="button"
    class="add-product-btn rounded-lg bg-gray-900 px-4 py-2 text-xs font-medium text-white hover:bg-black transition"
    data-product-id="${product.id}">

    Add 

</button>


                </div>


            `).join('');


        })

        .catch(error => {

            console.error(error);

        });



    }


    document.addEventListener('click', function(e){

    if (!e.target.classList.contains('remove-product-btn')) {
        return;
    }


    const button = e.target;

    const productId = button.dataset.productId;


    const collectionId =
        document.getElementById('collection-id').value;



    fetch(
        `/dashboard/admin/collections/${collectionId}/products/${productId}`,
        {
            method: 'DELETE',

            headers: {

                'Accept': 'application/json',

                'X-CSRF-TOKEN':
                    document.querySelector('meta[name="csrf-token"]').content

            }
        }
    )


    .then(response => response.json())


    .then(data => {

        if(data.success){

            loadCollectionProducts();

        }

    });


});

document.addEventListener('click', function(e){

    if (!e.target.classList.contains('add-product-btn')) {
        return;
    }


    const button = e.target;

    const productId = button.dataset.productId;


    const collectionId =
        document.getElementById('collection-id').value;


    fetch(
        `/dashboard/admin/collections/${collectionId}/products`,
        {
            method: 'POST',

            headers: {

                'Content-Type': 'application/json',

                'Accept': 'application/json',

                'X-CSRF-TOKEN':
                    document.querySelector('meta[name="csrf-token"]').content

            },

            body: JSON.stringify({

                product_id: productId

            })

        }
    )

    .then(response => response.json())

    .then(data => {

        if(data.success){

            button.innerHTML = 'Added';

    button.disabled = true;

    button.className = `
        rounded-lg
        bg-gray-100
        px-4
        py-2
        text-xs
        font-medium
        text-gray-400
        border
        border-gray-200
        cursor-not-allowed
    `;

            loadCollectionProducts();

        }

    });

});

    




loadCollectionProducts();


});

</script>

@endsection