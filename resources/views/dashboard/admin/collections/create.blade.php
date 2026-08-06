@extends('dashboard.admin.layout')

@section('dashboard-content')

<div class="max-w-5xl mx-auto">

    {{-- ================= HEADER ================= --}}
    <div class="mb-10">

        <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-white via-gray-50 to-white p-8">

            <div class="flex items-start justify-between gap-8">

                <div>

                    <div class="inline-flex items-center rounded-full border border-gray-300 bg-white px-3 py-1 text-xs font-medium uppercase tracking-wider text-gray-600">
                        Collection Management
                    </div>

                    <h1 class="mt-5 text-3xl font-semibold tracking-tight text-gray-900">
                        Create Product Collection
                    </h1>

                    <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
                        Collections help organize products into curated showcases across the marketplace.
                        They improve product discovery, support promotional campaigns and allow buyers to
                        browse products by theme, season, industry or marketing category.
                    </p>

                </div>

            </div>

        </div>

    </div>

    <x-alerts />

    <form method="POST"
          action="{{ route('admin.collections.store') }}"
          enctype="multipart/form-data">

        @csrf

        <div class="space-y-8">

            {{-- ================= GENERAL INFORMATION ================= --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                <div class="border-b border-gray-100 bg-gray-50 px-8 py-5">

                    <h2 class="text-lg font-semibold text-gray-900">
                        General Information
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Basic information displayed throughout the marketplace.
                    </p>

                </div>

                <div class="space-y-7 p-8">

                    {{-- TITLE --}}
                    <div>

                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Collection Title
                        </label>

                        <input
                            type="text"
                            name="translations[en][title]"
                            value="{{ old('translations.en.title') }}"
                            placeholder="Luxury Hotel Furniture Collection"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm transition focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">

                        <p class="mt-2 text-sm text-gray-500">
                            This title will be visible to buyers across the platform.
                        </p>

                    </div>

                    {{-- SLUG --}}
                    <div>

                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                            URL Slug
                        </label>

                        <input
                            type="text"
                            name="slug"
                            value="{{ old('slug') }}"
                            placeholder="luxury-hotel-furniture"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm transition focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">

                        <p class="mt-2 text-sm text-gray-500">
                            Leave empty to generate the slug automatically.
                        </p>

                    </div>

                    {{-- DESCRIPTION --}}
                    <div>

                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Description
                        </label>

                        <textarea
                            name="translations[en][description]"
                            rows="8"
                            placeholder="Describe the purpose of this collection, target audience, featured products and other useful information..."
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm leading-7 transition focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">{{ old('translations.en.description') }}</textarea>

                        <p class="mt-2 text-sm text-gray-500">
                            A detailed description helps visitors better understand the purpose of the collection.
                        </p>

                    </div>

                </div>

            </div>

                        {{-- ================= PRESENTATION ================= --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                <div class="border-b border-gray-100 bg-gray-50 px-8 py-5">

                    <h2 class="text-lg font-semibold text-gray-900">
                        Presentation & Publishing
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Configure how this collection is presented throughout the marketplace.
                    </p>

                </div>

                <div class="grid gap-10 p-8 lg:grid-cols-2">

                    {{-- COVER IMAGE --}}
                    
<div>

    <label class="block text-xs font-medium uppercase tracking-wide text-gray-500 mb-3">
        Cover Image
    </label>

    <label
    for="cover_file"
    class="group relative flex flex-col items-center justify-center w-full h-64
           overflow-hidden rounded-xl border border-dashed border-gray-300
           bg-gray-50 hover:bg-white cursor-pointer transition">

    <img
        id="cover-preview"
        src=""
        class="hidden absolute inset-0 h-full w-full object-cover">

    <div id="cover-placeholder" class="flex flex-col items-center">

        <svg class="w-8 h-8 text-gray-400 group-hover:text-gray-700 transition"
             fill="none"
             stroke="currentColor"
             stroke-width="1.8"
             viewBox="0 0 24 24">

            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M12 16V4m0 0L8 8m4-4 4 4M4 16v2a2 2 0 0 0 2-2h12a2 2 0 0 0 2-2v-2"/>

        </svg>

        <div class="mt-3 text-sm font-medium text-gray-700">
            Upload cover image
        </div>

        <div class="mt-1 text-xs text-gray-400">
            PNG, JPG or WEBP · Recommended 1600×900 px
        </div>

    </div>

    <input
        id="cover_file"
        type="file"
        name="cover"
        class="hidden"
        accept="image/png,image/jpeg,image/webp">

</label>


    <p class="mt-2 text-xs text-gray-400">
        The cover image represents this collection across the marketplace.
    </p>

</div>

                    {{-- SETTINGS --}}
                    <div class="space-y-7">

                        {{-- VISIBILITY --}}
                        <div>

                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Visibility
                            </label>

                            <select
                                name="visibility"
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">

                                <option value="public">Public</option>
                                <option value="draft">Draft</option>
                                <option value="private">Private</option>

                            </select>

                            <p class="mt-2 text-sm text-gray-500">
                                Control who can view this collection.
                            </p>

                        </div>

                        {{-- TYPE --}}
                        <div>

                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Collection Type
                            </label>

                            <select
                                name="type"
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">

                                <option value="platform">Platform Collection</option>
                                <option value="supplier">Supplier Collection</option>
                                <option value="buyer">Buyer Collection</option>
                                <option value="featured">Featured Selection</option>
                                <option value="industry">Industry Collection</option>
                                <option value="seasonal">Seasonal Collection</option>
                                <option value="project">Project Collection</option>
                                <option value="showcase">Supplier Showcase</option>


                            </select>

                            <p class="mt-2 text-sm text-gray-500">
                                Defines who owns and manages this collection.
                            </p>

                        </div>

                        {{-- FEATURED --}}
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">

                            <label class="flex items-start gap-4 cursor-pointer">

                                <input
                                    type="checkbox"
                                    name="is_featured"
                                    value="1"
                                    class="mt-1 h-5 w-5 rounded border-gray-300 text-gray-900 focus:ring-gray-900">

                                <div>

                                    <div class="font-medium text-gray-900">
                                        Featured Collection
                                    </div>

                                    <div class="mt-1 text-sm leading-6 text-gray-500">
                                        Featured collections may appear on the homepage,
                                        landing pages and promotional sections throughout
                                        the marketplace.
                                    </div>

                                </div>

                            </label>

                        </div>

                    </div>

                </div>

            </div>

                       
            
{{-- COLLECTION CONTENT --}}

<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

    <div class="border-b border-gray-100 bg-gray-50 px-8 py-5">

        <h2 class="text-lg font-semibold text-gray-900">
            Collection Content
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Additional information that helps buyers better understand this curated collection.
        </p>

    </div>


    <div class="space-y-8 p-8">

        {{-- SUBTITLE --}}
        <div>

            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                Subtitle
            </label>

            <input
                type="text"
                name="subtitle"
                value="{{ old('subtitle') }}"
                placeholder="Premium furniture solutions for hotels, resorts and commercial spaces."
                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">

            <p class="mt-2 text-sm text-gray-500">
                Short introduction displayed below the collection title.
            </p>

        </div>



        {{-- OVERVIEW --}}
        <div>

            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                Collection Overview
            </label>

            <textarea
                rows="6"
                name="overview"
                placeholder="Explain what makes this collection unique, what products are included and who it is intended for."
                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm leading-7 focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">{{ old('overview') }}</textarea>

        </div>



        {{-- IDEAL FOR --}}
        <div>

            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                Ideal For
            </label>

            <textarea
                rows="3"
                name="ideal_for"
                placeholder="Hotel chains, architects, procurement companies, interior designers..."
                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm leading-7 focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">{{ old('ideal_for') }}</textarea>

        </div>



        {{-- PROCUREMENT NOTES --}}
        <div>

            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                Procurement Notes
            </label>

            <textarea
                rows="3"
                name="procurement_notes"
                placeholder="MOQ, customization, production lead times, export information..."
                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm leading-7 focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">{{ old('procurement_notes') }}</textarea>

        </div>



        {{-- HIGHLIGHTS --}}
        <div>

    <div class="flex items-center justify-between mb-4">

        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">
            Collection Highlights
        </label>

        <button
            type="button"
            id="add-highlight"
            class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium hover:bg-gray-50">

            + Add Highlight

        </button>

    </div>


    <div id="highlights-wrapper" class="space-y-3">

        <div class="flex items-center gap-3">

            <input
                type="text"
                name="highlights[]"
                placeholder="OEM Available"
                class="flex-1 rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-gray-900 focus:ring-4 focus:ring-gray-900/5">

            <button
                type="button"
                class="remove-highlight rounded-lg border border-red-200 px-3 py-2 text-red-500 hover:bg-red-50">

                ✕

            </button>

        </div>

    </div>


    <p class="mt-3 text-sm text-gray-500">
        Add the key selling points that will be displayed as highlights on the collection page.
    </p>

</div>

    </div>

</div>

 {{-- ================= GUIDELINES ================= --}}
<div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">

                <div class="flex items-start gap-5">

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gray-100">

                        <svg class="h-6 w-6 text-gray-700"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M13 16h-1v-4h-1m1-4h.01M12 22a10 10 0 100-20 10 10 0 000 20z"/>

                        </svg>

                    </div>

                    <div>

                        <h3 class="text-base font-semibold text-gray-900">
                            Collection Guidelines
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Recommendations for creating professional marketplace collections.
                        </p>


                        <ul class="mt-5 space-y-3 text-sm text-gray-600">

                            <li class="flex gap-3">

                                <span class="mt-2 h-1.5 w-1.5 rounded-full bg-gray-400"></span>

                                <span>
                                    Use a clear title that describes the products included.
                                </span>

                            </li>


                            <li class="flex gap-3">

                                <span class="mt-2 h-1.5 w-1.5 rounded-full bg-gray-400"></span>

                                <span>
                                    Upload a high-quality cover image suitable for marketplace presentation.
                                </span>

                            </li>


                            <li class="flex gap-3">

                                <span class="mt-2 h-1.5 w-1.5 rounded-full bg-gray-400"></span>

                                <span>
                                    Add relevant products and arrange them in the preferred display order.
                                </span>

                            </li>


                            <li class="flex gap-3">

                                <span class="mt-2 h-1.5 w-1.5 rounded-full bg-gray-400"></span>

                                <span>
                                    Review all information before publishing the collection publicly.
                                </span>

                            </li>

                        </ul>

                    </div>

                </div>

            </div>




            {{-- ================= FOOTER ================= --}}
            <div class="flex items-center justify-between border-t border-gray-200 pt-8 pb-4">

                <div>

                    <div class="text-sm font-medium text-gray-900">
                        Ready to create this collection?
                    </div>

                    <div class="mt-1 text-sm text-gray-500">
                        You can modify products, images and settings later.
                    </div>

                </div>


                <div class="flex items-center gap-3">


                    <a href="{{ route('admin.collections.index') }}"
                       class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">

                        Cancel

                    </a>


                    <button
                        type="submit"
                        class="rounded-xl bg-gray-900 px-7 py-3 text-sm font-medium text-white transition hover:bg-black">

                        Create Collection

                    </button>


                </div>

            </div>


        </div>

    </form>

</div>

<script>
document.getElementById('add-highlight').addEventListener('click', () => {

    document.getElementById('highlights-wrapper').insertAdjacentHTML('beforeend', `

        <div class="flex items-center gap-3">

            <input
                type="text"
                name="highlights[]"
                class="flex-1 rounded-xl border border-gray-300 px-4 py-3 text-sm"
                placeholder="New highlight">

            <button
                type="button"
                class="remove-highlight rounded-lg border border-red-200 px-3 py-2 text-red-500">

                ✕

            </button>

        </div>

    `);

});


document.addEventListener('click', function(e){

    if(e.target.classList.contains('remove-highlight')){

        e.target.closest('.flex').remove();

    }

});
    </script>
    
<script>
document.addEventListener('DOMContentLoaded', () => {

    const input = document.getElementById('cover_file');
    const preview = document.getElementById('cover-preview');
    const placeholder = document.getElementById('cover-placeholder');

    input.addEventListener('change', function () {

        const file = this.files[0];

        if (!file) {
            return;
        }

        const reader = new FileReader();

        reader.onload = function (e) {

            preview.src = e.target.result;

            preview.classList.remove('hidden');

            placeholder.classList.add('hidden');

        };

        reader.readAsDataURL(file);

    });

});
</script>

@endsection


