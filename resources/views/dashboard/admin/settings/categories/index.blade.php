@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-xl font-semibold text-gray-900">Categories</h1>
        <p class="text-sm text-gray-500 mt-1">
            Manage product categories and hierarchy
        </p>
    </div>

    <a href="{{ route('admin.settings.categories.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-md
              hover:bg-gray-800 text-sm shadow-sm">
        <span class="text-lg leading-none">+</span>
        Add Category
    </a>
</div>

<x-alerts />


{{-- ============================================================
    FILTERS
============================================================= --}}
<form method="GET" action="{{ route('admin.settings.categories.index') }}" class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="font-semibold text-gray-800 text-sm">
                Filters
            </h3>
            <p class="text-xs text-gray-500 mt-1">
                Find and sort categories
            </p>
        </div>

        @if(request()->hasAny(['search','parent_id','level','is_selectable','is_leaf','is_visible','sort','direction']))
            <a href="{{ route('admin.settings.categories.index') }}" class="text-sm text-gray-500 hover:text-gray-900 hover:underline">
                Reset
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- SEARCH --}}
        <div class="lg:col-span-2">
            <label for="category-search" class="block text-xs font-medium text-gray-600 mb-1">
                Search
            </label>
            <input id="category-search" type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or slug..." class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 placeholder:text-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
        </div>

        {{-- PARENT --}}
        <div>
            <label for="category-parent" class="block text-xs font-medium text-gray-600 mb-1">
                Parent Category
            </label>
            <select id="category-parent" name="parent_id" class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                <option value="">All parents</option>
                @foreach($categories_map->where('level', 0) as $parent)
                    <option value="{{ $parent['id'] }}" @selected((string) request('parent_id') === (string) $parent['id'])>
                        {{ $parent['name'] }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- LEVEL --}}
        <div>
            <label for="category-level" class="block text-xs font-medium text-gray-600 mb-1">
                Level
            </label>
            <select id="category-level" name="level" class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                <option value="">All levels</option>
                <option value="0" @selected(request('level') === '0')>Level 0</option>
                <option value="1" @selected(request('level') === '1')>Level 1</option>
                <option value="2" @selected(request('level') === '2')>Level 2</option>
                <option value="3" @selected(request('level') === '3')>Level 3</option>
            </select>
        </div>

        {{-- SELECTABLE --}}
        <div>
            <label for="category-selectable" class="block text-xs font-medium text-gray-600 mb-1">
                Selectable
            </label>
            <select id="category-selectable" name="is_selectable" class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                <option value="">All</option>
                <option value="1" @selected(request('is_selectable') === '1')>Yes</option>
                <option value="0" @selected(request('is_selectable') === '0')>No</option>
            </select>
        </div>

        {{-- LEAF --}}
        <div>
            <label for="category-leaf" class="block text-xs font-medium text-gray-600 mb-1">
                Leaf
            </label>
            <select id="category-leaf" name="is_leaf" class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                <option value="">All</option>
                <option value="1" @selected(request('is_leaf') === '1')>Yes</option>
                <option value="0" @selected(request('is_leaf') === '0')>No</option>
            </select>
        </div>

        {{-- VISIBLE --}}
        <div>
            <label for="category-visible" class="block text-xs font-medium text-gray-600 mb-1">
                Visible
            </label>
            <select id="category-visible" name="is_visible" class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                <option value="">All</option>
                <option value="1" @selected(request('is_visible') === '1')>Yes</option>
                <option value="0" @selected(request('is_visible') === '0')>No</option>
            </select>
        </div>
    </div>

    {{-- SORTING --}}
    <div class="mt-4 pt-4 border-t border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- SORT BY --}}
            <div>
                <label for="category-sort" class="block text-xs font-medium text-gray-600 mb-1">
                    Sort by
                </label>
                <select id="category-sort" name="sort" class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                    <option value="sort_order" @selected(request('sort', 'sort_order') === 'sort_order')>Sort Order</option>
                    <option value="name" @selected(request('sort') === 'name')>Name</option>
                    <option value="slug" @selected(request('sort') === 'slug')>Slug</option>
                    <option value="level" @selected(request('sort') === 'level')>Level</option>
                    <option value="created_at" @selected(request('sort') === 'created_at')>Created</option>
                </select>
            </div>

            {{-- DIRECTION --}}
            <div>
                <label for="category-direction" class="block text-xs font-medium text-gray-600 mb-1">
                    Direction
                </label>
                <select id="category-direction" name="direction" class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                    <option value="asc" @selected(request('direction', 'asc') === 'asc')>Ascending</option>
                    <option value="desc" @selected(request('direction') === 'desc')>Descending</option>
                </select>
            </div>

            {{-- APPLY --}}
            <div class="flex items-end">
                <button type="submit" class="w-full h-10 px-4 rounded-lg bg-gray-900 text-white text-sm font-medium shadow-sm hover:bg-gray-800 transition">
                    Apply filters
                </button>
            </div>
        </div>
    </div>
</form>


{{-- Category Tree --}}
<div x-data="{ openMap: false }" class="mb-6">

    {{-- Заголовок --}}
    <div @click="openMap = !openMap"
         class="flex justify-between items-center bg-gray-100 p-2 rounded cursor-pointer">
        <h2 class="text-sm font-semibold text-gray-900">Category Tree</h2>
        <svg :class="{'rotate-90': openMap}" class="w-4 h-4 text-gray-500 transition-transform duration-200"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5l7 7-7 7"></path>
        </svg>
    </div>

    {{-- Древо категорий --}}
    <div x-show="openMap" x-transition
         class="mt-2 h-[85vh] overflow-auto text-xs p-2 bg-white border border-gray-200 rounded shadow-sm">

        {{-- Цикл по корневым категориям --}}
        @foreach($categories->where('level', 0) as $rootCategory)
            @include('dashboard.admin.settings.categories.partials.category-tree', ['category' => $rootCategory])
        @endforeach

    </div>
</div>





{{-- ============================================================
    CATEGORY LIST
============================================================= --}}

<div x-data="{ openCategories: false }" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

    {{-- HEADER --}}

    <button
        type="button"
        @click="openCategories = !openCategories"
        class="w-full flex items-center justify-between px-5 py-4 text-left bg-gray-50 hover:bg-gray-100 transition"
    >

        <div>

            <div class="flex items-center gap-3">

                <h3 class="font-semibold text-gray-800 text-sm">
                    Categories
                </h3>

                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-200 text-gray-600 text-xs">
                    {{ $categories->count() }}
                </span>

            </div>

            <p class="text-xs text-gray-500 mt-1">
                Manage categories, hierarchy, contexts and commission settings.
            </p>

        </div>


        {{-- ARROW --}}

        <svg
            :class="{ 'rotate-180': openCategories }"
            class="w-5 h-5 text-gray-500 transition-transform duration-200"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>

    </button>


    {{-- CONTENT --}}

    <div x-show="openCategories" x-transition>

        @if($categories->count())

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-white border-t border-b border-gray-100">

                        <tr>

                            <th class="px-5 py-3 text-left font-medium text-gray-500">
                                ID
                            </th>

                            <th class="px-5 py-3 text-left font-medium text-gray-500">
                                Name
                            </th>

                            <th class="px-5 py-3 text-left font-medium text-gray-500">
                                Parent
                            </th>

                            <th class="px-5 py-3 text-left font-medium text-gray-500">
                                Level
                            </th>

                            <th class="px-5 py-3 text-left font-medium text-gray-500">
                                Contexts
                            </th>

                            <th class="px-5 py-3 text-left font-medium text-gray-500">
                                Commission
                            </th>

                            <th class="px-5 py-3 text-right font-medium text-gray-500">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @foreach($categories as $category)

                            <tr class="hover:bg-gray-50 transition">

                                {{-- ID --}}

                                <td class="px-5 py-3 text-gray-500">
                                    {{ $category->id }}
                                </td>


                                {{-- NAME --}}

                                <td class="px-5 py-3">

                                    <div class="font-semibold text-gray-900">
                                        {{ $category->name }}
                                    </div>

                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ $category->slug }}
                                    </div>

                                </td>


                                {{-- PARENT --}}

                                <td class="px-5 py-3 text-gray-600">

                                    @if($category->parent)
                                        {{ $category->parent->name }}
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif

                                </td>


                                {{-- LEVEL --}}

                                <td class="px-5 py-3 text-gray-600">
                                    {{ $category->level }}
                                </td>


                                {{-- CONTEXTS --}}

                                <td class="px-5 py-3">

                                    <div class="flex flex-wrap gap-1">

                                        @forelse($category->types as $type)

                                            <span class="inline-flex px-2 py-1 rounded-md bg-gray-100 text-gray-700 text-xs">
                                                {{ ucfirst($type->type) }}
                                            </span>

                                        @empty

                                            <span class="text-gray-400">
                                                —
                                            </span>

                                        @endforelse

                                    </div>

                                </td>


                                {{-- COMMISSION --}}

                                <td class="px-5 py-3 text-gray-600">

                                    @if($category->level == 2)
                                        {{ $category->commission_percent }}%
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif

                                </td>


                                {{-- ACTIONS --}}

                                <td class="px-5 py-3 text-right whitespace-nowrap">

                                    <a
                                        href="{{ route('admin.settings.categories.edit', $category) }}"
                                        class="text-sm text-gray-600 hover:text-gray-900 hover:underline mr-3"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('admin.settings.categories.destroy', $category) }}"
                                        method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Delete category?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="text-sm text-red-600 hover:underline"
                                        >
                                            Delete
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="px-5 py-8 text-center text-sm text-gray-400">
                No categories found.
            </div>

        @endif

    </div>

</div>


@endsection
