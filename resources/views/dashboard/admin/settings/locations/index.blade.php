@extends('dashboard.admin.settings.layout')

@section('settings-content')
<div class="flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Locations</h2>
            <p class="text-sm text-gray-500">
                Manage locations, assign to countries and organize regions/areas
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.settings.locations.create') }}"
               class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                + Add location
            </a>
        </div>
    </div>

    <div class="flex flex-col gap-6 max-w-4xl">

    {{-- Фильтры --}}
<div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 flex flex-wrap gap-4">
    <form method="GET" class="flex flex-wrap gap-4 w-full items-end">
        <div class="flex flex-col">
            <label for="country-filter" class="text-sm font-medium text-gray-700">Country</label>
            <select name="country_id" id="country-filter" class="border border-gray-300 rounded px-3 py-2 text-sm">
                <option value="">All countries</option>
                @foreach($allCountries as $country)
                    <option value="{{ $country->id }}" {{ $selectedCountry == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col">
            <label for="region-filter" class="text-sm font-medium text-gray-700">Region</label>
            <select name="region_id" id="region-filter" class="border border-gray-300 rounded px-3 py-2 text-sm">
                <option value="">All regions</option>
                @foreach($allRegions as $region)
                    <option value="{{ $region->id }}" {{ $selectedRegion == $region->id ? 'selected' : '' }}>
                        {{ $region->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition text-sm">
                Apply
            </button>
        </div>
    </form>
</div>




    {{-- Success message --}}
    @if(session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif



    {{-- Блок неподтверждённых локаций --}}
@if(isset($unverifiedLocations) && $unverifiedLocations->isNotEmpty())
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-3 font-semibold text-yellow-800 bg-yellow-100 border-b">Unverified Locations</div>
        <table class="w-full text-sm">
            <thead class="bg-yellow-50 border-b">
                <tr>
                    <th class="px-5 py-3 text-left font-medium text-yellow-700">Name</th>
                    <th class="px-5 py-3 text-left font-medium text-yellow-700">Type</th>
                    <th class="px-5 py-3 text-left font-medium text-yellow-700">Country</th>
                    <th class="px-5 py-3 text-right font-medium text-yellow-700">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($unverifiedLocations as $loc)
                    <tr class="hover:bg-yellow-50 transition">
                        <td class="px-5 py-3 font-semibold text-yellow-900">{{ $loc->name }}</td>
                        <td class="px-5 py-3 text-yellow-700">
                            {{ $loc->parent_id ? 'Location' : 'Region' }}
                        </td>
                        <td class="px-5 py-3 text-yellow-700">{{ $loc->country->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.settings.locations.edit', $loc->id) }}"
                               class="text-sm text-yellow-800 hover:underline mr-3">
                                Edit
                            </a>
                            <form action="{{ route('admin.settings.locations.destroy', $loc->id) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Delete location?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-sm text-red-600 hover:underline">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif



    {{-- Locations Table Card --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Name</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Type</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Country</th>
                    <th class="px-5 py-3 text-right font-medium text-gray-600">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach($regions as $region)
                    {{-- Регионы --}}
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-semibold text-gray-900">
                            {{ $region->name }}
                        </td>
                        <td class="px-5 py-3 text-gray-700">
                            Region
                        </td>
                        <td class="px-5 py-3 text-gray-700">
                            {{ $region->country->name ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.settings.locations.edit', $region->id) }}"
                               class="text-sm text-gray-700 hover:underline mr-3">
                                Edit
                            </a>
                            <form action="{{ route('admin.settings.locations.destroy', $region->id) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Delete region?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-sm text-red-600 hover:underline">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>

                    {{-- Области --}}
                    @foreach($region->children as $area)
                        <tr class="hover:bg-gray-50 transition bg-gray-50">
                            <td class="px-5 py-3 text-gray-700 pl-8">
                                {{ $area->name }}
                            </td>
                            <td class="px-5 py-3 text-gray-700">
                                Location
                            </td>
                            <td class="px-5 py-3 text-gray-700">
                                {{ $area->country->name ?? $region->country->name ?? '—' }}
                            </td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.settings.locations.edit', $area->id) }}"
                                   class="text-sm text-gray-700 hover:underline mr-3">
                                    Edit
                                </a>
                                <form action="{{ route('admin.settings.locations.destroy', $area->id) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Удалить область?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm text-red-600 hover:underline">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                @endforeach
            </tbody>
        </table>

        
    </div>

<div class="px-5 py-4 border bg-gray-50">
    {{ $regions->links() }}
</div>

</div>

{{-- JS для динамических регионов --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('country-filter');
    const regionSelect = document.getElementById('region-filter');

    countrySelect.addEventListener('change', function() {
        const countryId = this.value;

        // Сброс всех опций региона
        regionSelect.innerHTML = '';
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'All regions';
        regionSelect.appendChild(defaultOption);

        if (!countryId) return; // если страна не выбрана, оставляем только All regions

        // Подгружаем регионы через AJAX
        fetch("{{ route('admin.settings.locations.regions') }}?country_id=" + countryId)
            .then(response => response.json())
            .then(data => {
                data.forEach(region => {
                    const option = document.createElement('option');
                    option.value = region.id;
                    option.textContent = region.name;
                    regionSelect.appendChild(option);
                });
            })
            .catch(err => console.error(err));
    });
});
</script>
@endsection
