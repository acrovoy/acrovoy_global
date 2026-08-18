@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-semibold text-gray-900">Edit Category <span class="text-amber-600">{{ $category->name }}</span> <span class="p-1 text-sm text-gray-600 rounded-md border border-gray-900 bg-gray-100">{{ $category->id }}</span>
    </h1>
</div>

<x-alerts />

<form action="{{ route('admin.settings.categories.update', $category) }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')

    @php
        use App\Models\Language;

        $languages = Language::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    @endphp

    <div>
        <label class="block text-gray-700">Slug</label>
        <input type="text" name="slug" class="mt-1 block w-full border-gray-300 rounded" value="{{ old('slug', $category->slug) }}">
        @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-gray-700">Parent Category</label>
        <select name="parent_id" class="mt-1 block w-full border-gray-300 rounded">
            <option value="">None</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('parent_id', $category->parent_id) == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        @error('parent_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-gray-700 mb-1">Names by locale</label>
        @foreach($languages as $lang)
            <input type="text"
                   name="name[{{ $lang->code }}]"
                   placeholder="{{ strtoupper($lang->code) }}"
                   class="mt-1 block w-full border-gray-300 rounded mb-2"
                   value="{{ old("name.$lang->code", $category->translations->firstWhere('locale', $lang->code)?->name) }}">
            @error("name.$lang->code")
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        @endforeach
    </div>

    <div>
        <label class="block text-gray-700">Commission Percent (for Level 3)</label>
        <input type="number" name="commission_percent" step="0.01" min="0" max="100"
               class="mt-1 block w-full border-gray-300 rounded"
               value="{{ old('commission_percent', $category->commission_percent) }}">
        @error('commission_percent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="border border-gray-200 rounded-xl overflow-hidden">

    <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">

        <h3 class="font-semibold text-gray-800 text-sm">
            Category Attributes
        </h3>

        <p class="text-xs text-gray-500 mt-1">
            Select attributes and configure whether they are required and their display order.
        </p>

    </div>


    <div class="divide-y divide-gray-100">

        @foreach($attributes as $attribute)

            @php

                /*
                |--------------------------------------------------------------------------
                | Current category attribute
                |--------------------------------------------------------------------------
                */

                $categoryAttribute = $category->attributes
                    ->firstWhere('id', $attribute->id);


                /*
                |--------------------------------------------------------------------------
                | Enabled
                |--------------------------------------------------------------------------
                */

                $isEnabled = $categoryAttribute !== null;


                /*
                |--------------------------------------------------------------------------
                | Required
                |--------------------------------------------------------------------------
                */

                $isRequired = $categoryAttribute
                    ? (bool) $categoryAttribute->pivot->is_required
                    : false;


                /*
                |--------------------------------------------------------------------------
                | Sort order
                |--------------------------------------------------------------------------
                */

                $sortOrder = $categoryAttribute
                    ? $categoryAttribute->pivot->sort_order
                    : 0;

            @endphp


            <div class="px-5 py-4">

                <div class="grid grid-cols-12 gap-4 items-center">


                    {{-- =====================================================
                        ATTRIBUTE
                    ====================================================== --}}

                    <div class="col-span-6 flex items-center gap-3">

                        <input
                            type="checkbox"
                            name="attributes[{{ $attribute->id }}][enabled]"
                            value="1"
                            id="attr-{{ $attribute->id }}"
                            class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400"

                            @checked(
                                old(
                                    "attributes.{$attribute->id}.enabled",
                                    $isEnabled
                                )
                            )
                        >

                        <label
                            for="attr-{{ $attribute->id }}"
                            class="text-sm font-medium text-gray-800 cursor-pointer"
                        >
                            {{ $attribute->name }}
                        </label>

                    </div>


                    {{-- =====================================================
                        REQUIRED
                    ====================================================== --}}

                    <div class="col-span-3">

                        <label class="flex items-center gap-2 cursor-pointer">

                            <input
                                type="checkbox"
                                name="attributes[{{ $attribute->id }}][is_required]"
                                value="1"
                                class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400"

                                @checked(
                                    old(
                                        "attributes.{$attribute->id}.is_required",
                                        $isRequired
                                    )
                                )
                            >

                            <span class="text-sm text-gray-600">
                                Required
                            </span>

                        </label>

                    </div>


                    {{-- =====================================================
                        SORT ORDER
                    ====================================================== --}}

                    <div class="col-span-3">

                        <div class="flex items-center gap-2">

                            <label
                                for="sort-{{ $attribute->id }}"
                                class="text-sm text-gray-500 whitespace-nowrap"
                            >
                                Order
                            </label>

                            <input
                                type="number"
                                name="attributes[{{ $attribute->id }}][sort_order]"
                                id="sort-{{ $attribute->id }}"
                                min="0"

                                value="{{ old(
                                    "attributes.{$attribute->id}.sort_order",
                                    $sortOrder
                                ) }}"

                                class="w-20 h-9 px-2 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                            >

                        </div>

                    </div>


                </div>

            </div>

        @endforeach

    </div>

</div>


    @php
    $types = [
        'product' => 'Product',
        'rfq' => 'RFQ',
        'project' => 'Project',
    ];

    $selectedTypes = $category->types->pluck('type')->toArray();
@endphp

<div class="border rounded-xl p-4 space-y-4">
    <h3 class="font-medium text-gray-700 text-sm">Category Contexts</h3>

    @foreach($types as $key => $label)
        <div class="flex items-center gap-2">
            <input type="checkbox"
                   name="types[]"
                   value="{{ $key }}"
                   id="type-{{ $key }}"
                   {{ in_array($key, old('types', $selectedTypes)) ? 'checked' : '' }}>
            <label for="type-{{ $key }}" class="text-gray-700">
                {{ $label }}
            </label>
        </div>
    @endforeach
</div>



   <div>
    <label class="block text-gray-700">OLD Category Type</label>

    <div class="mt-1 w-full border border-gray-200 bg-gray-50 rounded px-3 py-2 text-gray-700">
        {{ $category->type ?? '—' }}
    </div>
</div>

    <div class="flex gap-4">
        <div>
            <label class="block text-gray-700">Level</label>
            <input type="number" name="level" min="0" class="mt-1 block border-gray-300 rounded"
                   value="{{ old('level', $category->level) }}">
            @error('level') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="flex items-center mt-6">
            <input type="checkbox" name="is_selectable" id="is_selectable" value="1" @checked(old('is_selectable', $category->is_selectable))>
            <label for="is_selectable" class="ml-2 text-gray-700">Is Selectable</label>
        </div>
        <div class="flex items-center mt-6">
            <input type="checkbox" name="is_leaf" id="is_leaf" value="1" @checked(old('is_leaf', $category->is_leaf))>
            <label for="is_leaf" class="ml-2 text-gray-700">Is Leaf</label>
        </div>
        <div class="flex items-center mt-6">
    <input type="checkbox"
           name="is_visible"
           id="is_visible"
           value="1"
           @checked(old('is_visible', $category->is_visible))>

    <label for="is_visible" class="ml-2 text-gray-700">
        Visible
    </label>
</div>

        <div>
            <label class="block text-gray-700">Sort Order</label>
            <input type="number" name="sort_order" class="mt-1 block border-gray-300 rounded"
                   value="{{ old('sort_order', $category->sort_order) }}">
            @error('sort_order') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>

    <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded hover:bg-gray-800 shadow">Update Category</button>
</form>

@endsection