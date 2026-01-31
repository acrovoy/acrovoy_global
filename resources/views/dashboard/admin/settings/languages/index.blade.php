@extends('dashboard.admin.settings.layout')

@section('settings-content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-xl font-semibold text-gray-900">Languages</h1>
        <p class="text-sm text-gray-500 mt-1">
            Manage available interface languages
        </p>
    </div>

    <a href="{{ route('admin.settings.languages.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-md
              hover:bg-gray-800 text-sm shadow-sm">
        <span class="text-lg leading-none">+</span>
        Add language
    </a>
</div>

@if(session('success'))
    <div class="mb-5 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="overflow-hidden border border-gray-200 rounded-lg">
    <table class="w-full text-sm">
    <thead class="bg-gray-50 text-gray-600">
        <tr>
            <th class="px-4 py-3 text-left font-medium">Code</th>
            <th class="px-4 py-3 text-left font-medium">Name</th>
            <th class="px-4 py-3 text-left font-medium">Native Name</th>
            <th class="px-4 py-3 text-left font-medium">Locale</th>
            <th class="px-4 py-3 text-center font-medium">Direction</th>
            <th class="px-4 py-3 text-center font-medium">Priority</th>
            <th class="px-4 py-3 text-center font-medium">Sort Order</th>
            <th class="px-4 py-3 text-center font-medium">Default</th>
            <th class="px-4 py-3 text-center font-medium">Status</th>
            <th class="px-4 py-3 text-right font-medium">Actions</th>
        </tr>
    </thead>

    <tbody class="divide-y divide-gray-100">
        @foreach($languages as $language)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-mono text-gray-800">{{ $language->code }}</td>
                <td class="px-4 py-3 text-gray-800">{{ $language->name }}</td>
                <td class="px-4 py-3 text-gray-800">{{ $language->native_name }}</td>
                <td class="px-4 py-3 text-gray-800">{{ $language->locale }}</td>
                <td class="px-4 py-3 text-center">{{ strtoupper($language->direction) }}</td>
                <td class="px-4 py-3 text-center">{{ ucfirst($language->priority) }}</td>
                <td class="px-4 py-3 text-center">{{ $language->sort_order }}</td>
                <td class="px-4 py-3 text-center">
                    @if($language->is_default)
                        <span class="inline-flex items-center px-2 py-0.5
                                     text-xs font-medium text-blue-700
                                     bg-blue-100 rounded-full">
                            Default
                        </span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    @if($language->is_active)
                        <span class="inline-flex items-center px-2 py-0.5
                                     text-xs font-medium text-green-700
                                     bg-green-100 rounded-full">
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5
                                     text-xs font-medium text-gray-500
                                     bg-gray-100 rounded-full">
                            Inactive
                        </span>
                    @endif
                </td>

                <td class="px-4 py-3 text-right space-x-4">
                    <!-- View button -->
                    <a href="{{ route('admin.settings.languages.show', $language) }}"
                       class="text-gray-800 hover:text-gray-900 font-medium">View</a>

                    <!-- Edit button -->
                    <a href="{{ route('admin.settings.languages.edit', $language) }}"
                       class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>

                    <!-- Delete button -->
                    <form action="{{ route('admin.settings.languages.destroy', $language) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600 hover:text-red-800 font-medium"
                                onclick="return confirm('Delete language?')">
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
