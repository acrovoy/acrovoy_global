@extends('dashboard.layout')

@section('dashboard-content')

<!-- Ссылка назад -->
<a href="{{ route('buyer.projects.index') }}"
   class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
    ← Back to projects
</a>

<div class="max-w-3xl mx-auto bg-white border rounded-xl shadow-sm p-6">

    <div class="mb-6">
        <h2 class="text-2xl font-semibold">Edit Project</h2>
        <p class="text-sm text-gray-500">
            Update project details. You can later edit items, materials, descriptions, and colors.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded border border-green-200 bg-green-50 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded border border-red-200 bg-red-50 text-red-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('buyer.projects.update', $project) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Title --}}
        <div>
            <label class="block mb-2 font-medium text-gray-700">Title</label>
            <input type="text" name="title" value="{{ old('title', $project->title) }}"
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Enter project title">
            @error('title')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- General Note / Description --}}
        <div>
            <label class="block mb-2 font-medium text-gray-700">Project Note</label>
            <textarea name="description" rows="5"
                      class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Optional: add a general note or description for this project">{{ old('description', $project->description) }}</textarea>
            @error('description')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Category --}}
        <div>
            <label class="block mb-2 font-medium text-gray-700">Category</label>
            <select 
                name="category_id"
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">— Select category —</option>
                @foreach($categories ?? [] as $category)
                    <option
                        value="{{ $category->id }}"
                        @selected(old('category_id', $project->category_id) == $category->id)
                    >
                        {{ $category->translations->first()->name ?? $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <div class="pt-2">
            <button type="submit"
                    class="px-5 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                Update Project
            </button>
        </div>
    </form>
</div>
@endsection
