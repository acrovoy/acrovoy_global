@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-4">

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

    <div class="space-y-2">

        @foreach($categoryTree as $category)

            @include(
                'dashboard.admin.settings.categories.partials.category-map-node',
                [
                    'category' => $category,
                    
                ]
            )

        @endforeach

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    document.addEventListener('click', async function (event) {

        const toggle = event.target.closest('[data-category-toggle]');

        if (!toggle) {
            return;
        }

        const categoryId = toggle.dataset.categoryId;

        const node = document.querySelector(
            `[data-category-node][data-category-id="${categoryId}"]`
        );

        if (!node) {
            return;
        }

        const content = node.querySelector(
            '[data-category-content]'
        );

        const arrow = node.querySelector(
            '[data-category-arrow]'
        );

        const loading = node.querySelector(
            '[data-category-loading]'
        );

        if (!content) {
            return;
        }

        const isOpen = !content.classList.contains('hidden');

        if (isOpen) {

            content.classList.add('hidden');

            if (arrow) {
                arrow.classList.remove('rotate-90');
            }

            return;
        }

        if (node.dataset.categoryLoaded === 'true') {

            content.classList.remove('hidden');

            if (arrow) {
                arrow.classList.add('rotate-90');
            }

            return;
        }

        if (loading) {
            loading.classList.remove('hidden');
        }

        try {

            const url =
                "{{ route('admin.settings.categories.category-map.node', ':category') }}"
                    .replace(':category', categoryId);

            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
            });

            if (!response.ok) {
                throw new Error(
                    `HTTP ${response.status}`
                );
            }

            const html = await response.text();

            content.innerHTML = html;

            node.dataset.categoryLoaded = 'true';

            content.classList.remove('hidden');

            if (arrow) {
                arrow.classList.add('rotate-90');
            }

        } catch (error) {

    console.error(
        'Category Map loading error:',
        error
    );

    content.innerHTML = `
        <div class="px-5 py-4 text-sm text-red-500">
            <div class="font-semibold">
                Failed to load category.
            </div>

            <div class="mt-1 text-xs text-gray-500">
                ${error.message}
            </div>
        </div>
    `;

    content.classList.remove('hidden');

} finally {

            if (loading) {
                loading.classList.add('hidden');
            }

        }

    });

});
</script>


@endsection