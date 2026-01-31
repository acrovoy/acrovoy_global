@extends('dashboard.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Shipping Templates</h2>
            <p class="text-sm text-gray-500">
                Manage all your shipping templates and assign countries, prices and delivery times
            </p>
        </div>

        

        <a href="{{ route('manufacturer.shipping-templates.create') }}"
               class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                + Add New Template
            </a>

    </div>

    {{-- Success message --}}
    @if(session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table Card --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 uppercase text-gray-600">
                <tr>
                    <th class="px-5 py-3 text-left font-medium">Title</th>
                    <th class="px-5 py-3 text-left font-medium">Price</th>
                    <th class="px-5 py-3 text-left font-medium">Delivery Time</th>
                    <th class="px-5 py-3 text-left font-medium">Countries</th>
                    <th class="px-5 py-3 text-left font-medium">Actions</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($templates as $template)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $template->title }}</td>
                        <td class="px-5 py-3 text-gray-700">${{ number_format($template->price, 2) }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $template->delivery_time }}</td>
                        <td class="px-5 py-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach($template->countries as $country)
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-50 text-blue-700">
                                        {{ $country->name }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-5 py-3 text-right whitespace-nowrap space-x-2">
                            
                            <a href="{{ route('manufacturer.shipping-templates.edit', $template) }}"
                               class="text-sm text-gray-700 hover:underline mr-3">
                                Edit
                            </a>

                            

                            <form action="{{ route('manufacturer.shipping-templates.destroy', $template) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Are you sure you want to delete this template?')">
                                @csrf
                                @method('DELETE')

                                <button class="text-sm text-red-600 hover:underline">
                                    Delete
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-gray-500">
                            No shipping templates found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
