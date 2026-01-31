@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-xl font-semibold text-gray-900">Materials</h1>
        <p class="text-sm text-gray-500 mt-1">
            Manage available interface languages
        </p>
    </div>

    <a href="{{ route('admin.settings.materials.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-md
              hover:bg-gray-800 text-sm shadow-sm">
        <span class="text-lg leading-none">+</span>
        + Add Material
    </a>
</div>




@if(session('success'))
    <div class="mb-4 p-2 bg-green-100 text-green-800 rounded shadow-sm">
        {{ session('success') }}
    </div>
@endif

<div class="overflow-x-auto rounded-lg shadow border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name (by language)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($materials as $material)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">{{ $material->id }}</td>
                <td class="px-6 py-4">{{ $material->slug }}</td>
                <td class="px-6 py-4">
                    @foreach($material->translations as $t)
                        <span class="inline-block bg-gray-100 text-gray-700 px-2 py-1 rounded mr-1 mb-1">
                            {{ $t->locale }}: {{ $t->name }}
                        </span>
                    @endforeach
                </td>
                <td class="px-6 py-4 flex gap-2">
                    <a href="{{ route('admin.settings.materials.edit', $material) }}" class="px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm shadow">Edit</a>
                    <form action="{{ route('admin.settings.materials.destroy', $material) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm shadow">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Нет материалов</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
