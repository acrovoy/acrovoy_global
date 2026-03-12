@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-xl font-semibold text-gray-900">
            Manufacturing Capabilities
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Manage manufacturing capability signals
        </p>
    </div>

    <a href="{{ route('admin.settings.manufacturing-capabilities.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-md
              hover:bg-gray-800 text-sm shadow-sm">

        <span class="text-lg leading-none">+</span>
        Add Capability
    </a>
</div>

<x-alerts />

<div class="overflow-x-auto rounded-lg shadow border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200">

        <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name (translation)</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sort</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Visible</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
        </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">

        @forelse($capabilities as $capability)

            <tr class="hover:bg-gray-50 transition">

                <td class="px-6 py-4">{{ $capability->id }}</td>

                <td class="px-6 py-4">{{ $capability->slug }}</td>

                <td class="px-6 py-4">
                    {{ $capability->name }}
                </td>

                <td class="px-6 py-4">
                    {{ $capability->sort_order }}
                </td>

                <td class="px-6 py-4">
                    {{ $capability->visibility_flag ? 'Yes' : 'No' }}
                </td>

                <td class="px-6 py-4 flex gap-2">

                    <a href="{{ route('admin.settings.manufacturing-capabilities.edit', $capability) }}"
                       class="px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm shadow">
                        Edit
                    </a>

                    <form
                        action="{{ route('admin.settings.manufacturing-capabilities.destroy', $capability) }}"
                        method="POST">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm shadow">
                            Delete
                        </button>

                    </form>

                </td>

            </tr>

        @empty

            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    No capabilities
                </td>
            </tr>

        @endforelse

        </tbody>
    </table>
</div>

@endsection