@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-semibold text-gray-900">Add Category</h1>
</div>

@if(session('success'))
    <div class="mb-4 p-2 bg-green-100 text-green-800 rounded shadow-sm">
        {{ session('success') }}
    </div>
@endif

<form action="{{ route('admin.settings.categories.store') }}" method="POST" class="space-y-4">
    @csrf

    <div>
        <label class="block text-gray-700">Slug</label>
        <input type="text" name="slug" class="mt-1 block w-full border-gray-300 rounded" value="{{ old('slug') }}">
        @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-gray-700">Parent Category</label>
        <select name="parent_id" class="mt-1 block w-full border-gray-300 rounded">
            <option value="">None</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('parent_id') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        @error('parent_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-gray-700 mb-1">Names by locale</label>
        @foreach(['en','uk','es'] as $locale)
            <input type="text" name="name[{{ $locale }}]" placeholder="{{ strtoupper($locale) }}"
                   class="mt-1 block w-full border-gray-300 rounded mb-2" value="{{ old("name.$locale") }}">
            @error("name.$locale") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        @endforeach
    </div>

    <div>
        <label class="block text-gray-700">Commission Percent (for Level 3)</label>
        <input type="number" name="commission_percent" step="0.01" min="0" max="100"
               class="mt-1 block w-full border-gray-300 rounded"
               value="{{ old('commission_percent', 0) }}">
        @error('commission_percent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-gray-700">Category Type</label>
        <select name="type" class="mt-1 block w-full border-gray-300 rounded">
            <option value="product" @selected(old('type') == 'product')>Product</option>
            <option value="rfq" @selected(old('type') == 'rfq')>RFQ/Project</option>
        </select>
        @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded hover:bg-gray-800 shadow">Add Category</button>
</form>

@endsection
