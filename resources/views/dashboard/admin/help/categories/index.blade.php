@extends('dashboard.admin.help.layout')

@section('settings-content')
    <div class="flex flex-col gap-4">
        <h1 class="text-2xl font-bold mb-4">Help Categories</h1>

        {{-- Кнопка добавления новой категории --}}
        <div class="mb-4">
            <a href="{{ route('admin.help.categories.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Add New Category
            </a>
        </div>

        {{-- Таблица категорий --}}
        <table class="w-full text-left border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Name</th>
                    <th class="border px-4 py-2">Description</th>
                    <th class="border px-4 py-2">Slug</th>
                    <th class="border px-4 py-2">Created At</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td class="border px-4 py-2">{{ $category->id }}</td>
                        <td>
            @foreach($category->translations as $translation)
                <div class="mb-1">
                    <strong>{{ $translation->locale }}:</strong> {{ $translation->name }}
                </div>
            @endforeach
        </td>
        <td>
            @foreach($category->translations as $translation)
                <div class="mb-1">
                    <strong>{{ $translation->locale }}:</strong> {{ $translation->description }}
                </div>
            @endforeach
        </td>
                        <td class="border px-4 py-2">{{ $category->slug }}</td>
                        <td class="border px-4 py-2">{{ $category->created_at }}</td>
                        <td class="border px-4 py-2 space-x-2">

    {{-- Edit --}}
    <a href="{{ route('admin.help.categories.edit', $category->id) }}"
       class="px-2 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 transition">
        Edit
    </a>

    {{-- Delete --}}
    <form action="{{ route('admin.help.categories.destroy', $category->id) }}" method="POST" class="inline-block"
          onsubmit="return confirm('Delete this category?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition">
            Delete
        </button>
    </form>

</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
