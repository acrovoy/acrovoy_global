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

@if(session('success'))
    <div class="mb-4 p-2 bg-green-100 text-green-800 rounded shadow-sm">
        {{ session('success') }}
    </div>
@endif

<div class="overflow-x-auto rounded-lg shadow border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    ID
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Name (by language)
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Parent
                </th>
                
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Level
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commission %</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                    Actions
                </th>
            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($categories as $category)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        {{ $category->id }}
                    </td>

                    <td class="px-6 py-4">
                        @foreach($category->translations as $t)
                            <span class="inline-block bg-gray-100 text-gray-700 px-2 py-1 rounded mr-1 mb-1">
                                {{ $t->locale }}: {{ $t->name }}
                            </span>
                        @endforeach
                    </td>

                    <td class="px-6 py-4 text-gray-600">
                        @if($category->parent)
                            {{ $category->parent->name }}
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>

                    

                    <td class="px-6 py-4 text-gray-700">
                        {{ $category->level }}
                    </td>


                    <td class="px-6 py-4 text-gray-700">
                        @if($category->level == 2)
                            {{ $category->commission_percent }}%
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>



                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                        <a href="{{ route('admin.settings.categories.edit', $category) }}"
                           class="px-2 py-1 bg-yellow-500 hover:bg-yellow-600
                                  text-white rounded text-sm shadow">
                            Edit
                        </a>

                        <form action="{{ route('admin.settings.categories.destroy', $category) }}"
                              method="POST"
                              onsubmit="return confirm('Delete category?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-2 py-1 bg-red-600 hover:bg-red-700
                                           text-white rounded text-sm shadow">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        No categories found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>



@endsection
