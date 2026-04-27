@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-semibold text-gray-900">Edit Category</h1>
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

    <div class="border rounded-xl p-4 space-y-4">
        <h3 class="font-medium text-gray-700 text-sm">Attributes</h3>
        @foreach($attributes as $attribute)
            <div class="flex items-center gap-2">
                <input type="checkbox"
                       name="attributes[]"
                       value="{{ $attribute->id }}"
                       id="attr-{{ $attribute->id }}"
                       {{ $category->attributes->contains($attribute->id) ? 'checked' : '' }}>
                <label for="attr-{{ $attribute->id }}" class="text-gray-700">
                    {{ $attribute->name }}
                </label>
            </div>
        @endforeach
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