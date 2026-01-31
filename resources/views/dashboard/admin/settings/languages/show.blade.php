@extends('dashboard.admin.settings.layout')

@section('settings-content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-xl font-semibold text-gray-900">Language Details: {{ $language->code }}</h1>
        <p class="text-sm text-gray-500 mt-1">
            All parameters for this language
        </p>
    </div>

    <a href="{{ route('admin.settings.languages.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-md
              hover:bg-gray-800 text-sm shadow-sm">
        Back to list
    </a>
</div>

<div class="overflow-hidden border border-gray-200 rounded-lg bg-white shadow-sm">
    <table class="w-full text-sm">
        <tbody class="divide-y divide-gray-100">
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-medium text-gray-700 w-1/3">Code</td>
                <td class="px-4 py-3 text-gray-800">{{ $language->code }}</td>
            </tr>
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-medium text-gray-700">Name</td>
                <td class="px-4 py-3 text-gray-800">{{ $language->name }}</td>
            </tr>
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-medium text-gray-700">Native Name</td>
                <td class="px-4 py-3 text-gray-800">{{ $language->native_name }}</td>
            </tr>
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-medium text-gray-700">Locale</td>
                <td class="px-4 py-3 text-gray-800">{{ $language->locale }}</td>
            </tr>
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-medium text-gray-700">Direction</td>
                <td class="px-4 py-3 text-gray-800">{{ strtoupper($language->direction) }}</td>
            </tr>
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-medium text-gray-700">Priority</td>
                <td class="px-4 py-3 text-gray-800">{{ ucfirst($language->priority) }}</td>
            </tr>
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-medium text-gray-700">Sort Order</td>
                <td class="px-4 py-3 text-gray-800">{{ $language->sort_order }}</td>
            </tr>
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-medium text-gray-700">Default</td>
                <td class="px-4 py-3">
                    @if($language->is_default)
                        <span class="inline-flex items-center px-2 py-0.5
                                     text-xs font-medium text-blue-700
                                     bg-blue-100 rounded-full">Yes</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5
                                     text-xs font-medium text-gray-500
                                     bg-gray-100 rounded-full">No</span>
                    @endif
                </td>
            </tr>
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-medium text-gray-700">Active</td>
                <td class="px-4 py-3">
                    @if($language->is_active)
                        <span class="inline-flex items-center px-2 py-0.5
                                     text-xs font-medium text-green-700
                                     bg-green-100 rounded-full">Active</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5
                                     text-xs font-medium text-gray-500
                                     bg-gray-100 rounded-full">Inactive</span>
                    @endif
                </td>
            </tr>
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-medium text-gray-700">Notes</td>
                <td class="px-4 py-3 text-gray-800">{{ $language->notes ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="px-4 py-4 flex justify-end gap-3 border-t border-gray-200">
        <a href="{{ route('admin.settings.languages.edit', $language) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
            Edit
        </a>

        <form action="{{ route('admin.settings.languages.destroy', $language) }}" method="POST">
            @csrf
            @method('DELETE')
            <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm"
                    onclick="return confirm('Delete language?')">
                Delete
            </button>
        </form>
    </div>
</div>
@endsection
