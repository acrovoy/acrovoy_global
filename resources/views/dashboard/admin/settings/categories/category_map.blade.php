@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-4">

    {{-- =========================================================
        HEADER
    ========================================================= --}}

    <div class="mb-4">

        <div class="flex items-center justify-between">

            <div>

                <h1 class="text-2xl font-semibold text-gray-900">
                    Category Map
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Categories, attributes and options in configured order.
                </p>

            </div>

            <div
                class="
                    text-xs
                    font-medium
                    uppercase
                    tracking-wider
                    text-gray-400
                "
            >
                {{ $categoryTree->count() }} root categories
            </div>

        </div>

    </div>


    {{-- =========================================================
        CATEGORY TREE
    ========================================================= --}}

    <div class="space-y-4">

        @foreach($categoryTree as $category)

            @include(
                'dashboard.admin.settings.categories.partials.category-map-node',
                [
                    'category' => $category,
                    'level' => 0,
                ]
            )

        @endforeach

    </div>

</div>

@endsection