@extends('dashboard.admin.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Shipping Centers</h2>
            <p class="text-sm text-gray-500">
                Manage your delivery services for the platform
            </p>
        </div>

        <div>
            <a href="{{ route('admin.shipping-center.create') }}"
               class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                + Add Shipping Center
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Origin</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Destination</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Price</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Delivery Days</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Active</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Notes</th>
                    <th class="px-5 py-3 text-right font-medium text-gray-600">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">
    @foreach($centers as $center)
        <tr class="hover:bg-gray-50 transition">
            {{-- Origin country --}}
            <td class="px-5 py-3">
                {{ $center->originCountry->name ?? '—' }}
                @if($center->originLocation)
                    <span class="inline-block ml-1 px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">
                        {{ $center->originLocation->name }}
                    </span>
                @endif
            </td>

            {{-- Destination country --}}
            <td class="px-5 py-3">
                {{ $center->destinationCountry->name ?? '—' }}
                @if($center->destinationLocation)
                    <span class="inline-block ml-1 px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">
                        {{ $center->destinationLocation->name }}
                    </span>
                @endif
            </td>

            {{-- Price --}}
            <td class="px-5 py-3">{{ $center->price }}</td>

            {{-- Delivery Days --}}
            <td class="px-5 py-3">{{ $center->delivery_days }} days</td>

            {{-- Active --}}
            <td class="px-5 py-3">
                @if($center->is_active)
                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-50 text-green-700">Active</span>
                @else
                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>
                @endif
            </td>

            {{-- Notes --}}
            <td class="px-5 py-3">{{ Str::limit($center->notes, 50) ?? '—' }}</td>

            {{-- Actions --}}
            <td class="px-5 py-3 text-right whitespace-nowrap">
                <a href="{{ route('admin.shipping-center.edit', $center) }}" class="text-sm text-gray-700 hover:underline mr-3">Edit</a>
                <form action="{{ route('admin.shipping-center.destroy', $center) }}" method="POST" class="inline" onsubmit="return confirm('Delete shipping center?')">
                    @csrf
                    @method('DELETE')
                    <button class="text-sm text-red-600 hover:underline">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
</tbody>
        </table>
    </div>

</div>
@endsection
