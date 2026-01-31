@extends('dashboard.admin.help.layout')

@section('settings-content')
<h1 class="text-2xl font-bold mb-4">Add New Help Category</h1>

<form action="{{ route('admin.help.categories.store') }}" method="POST">
    @csrf

    {{-- Slug --}}
    <div class="mb-4">
        <label class="block mb-1 font-semibold">Slug</label>
        <input type="text" name="slug" value="{{ old('slug') }}" class="w-full border p-2 rounded">
        @error('slug') <p class="text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Name block --}}
    <div class="mb-6 border-b pb-2">
        <h3 class="font-semibold mb-2">Name</h3>
        <div class="grid grid-cols-1 md:grid-cols-{{ count($locales) }} gap-4">
            @foreach($locales as $locale)
                <div>
                    <label class="block mb-1">{{ strtoupper($locale) }}</label>
                    <input type="text" name="translations[{{ $locale }}][name]" 
                           value="{{ old('translations.'.$locale.'.name') }}" 
                           class="w-full border p-2 rounded">
                    @error("translations.$locale.name") <p class="text-red-500">{{ $message }}</p> @enderror
                </div>
            @endforeach
        </div>
    </div>

    {{-- Description block --}}
    <div class="mb-6 border-b pb-2">
        <h3 class="font-semibold mb-2">Description</h3>
        <div class="grid grid-cols-1 md:grid-cols-{{ count($locales) }} gap-4">
            @foreach($locales as $locale)
                <div>
                    <label class="block mb-1">{{ strtoupper($locale) }}</label>
                    <textarea name="translations[{{ $locale }}][description]" 
                              class="w-full border p-2 rounded">{{ old('translations.'.$locale.'.description') }}</textarea>
                    @error("translations.$locale.description") <p class="text-red-500">{{ $message }}</p> @enderror
                </div>
            @endforeach
        </div>
    </div>

    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
        Save
    </button>
</form>
@endsection
