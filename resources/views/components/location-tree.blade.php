{{-- Blade Component: location-tree --}}
@props(['locations', 'selectedLocations'])

@php
    $selected = old('locations', $selectedLocations ?? []);
@endphp

<div class="space-y-2">
    @if($locations && count($locations))
        @foreach($locations as $country)
            @php
                $isCheckedCountry = false;

                $hasCheckedRegions = isset($country->locations)
                    ? collect($country->locations)->filter(function($region) use ($selected) {
                        if (in_array($region->id, $selected)) return true;
                        if (isset($region->children_recursive)) {
                            return collect($region->children_recursive)
                                ->pluck('id')
                                ->intersect($selected)
                                ->count() > 0;
                        }
                        return false;
                    })->count() > 0
                    : false;

                $openCountry = $hasCheckedRegions;
            @endphp

            <div x-data="{ openCountry: {{ $openCountry ? 'true' : 'false' }} }" class="border rounded p-2 bg-gray-50">
                {{-- Страна --}}
                <div class="flex items-center justify-between cursor-pointer" @click="openCountry = !openCountry">
                    <span class="font-semibold text-gray-800">{{ $country->name }}</span>
                    <svg :class="{ 'rotate-90': openCountry }"
                        class="w-4 h-4 transition-transform"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5l7 7-7 7" />
                    </svg>
                </div>

                @if($country->locations && count($country->locations))
                    <div x-show="openCountry" x-transition class="ml-5 mt-2 space-y-2">
                        @foreach($country->locations as $region)
                            @php
                                $isCheckedRegion = in_array($region->id, $selected);
                                $hasCheckedCities = isset($region->children_recursive)
                                    ? collect($region->children_recursive)->pluck('id')->intersect($selected)->count() > 0
                                    : false;
                                $openRegion = $isCheckedRegion || $hasCheckedCities;
                            @endphp

                            <div x-data="{ openRegion: {{ $openRegion ? 'true' : 'false' }} }" class="flex flex-col">
                                <div class="flex items-center justify-between cursor-pointer">
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" name="locations[]" value="{{ $region->id }}" @click.stop {{ $isCheckedRegion ? 'checked' : '' }}>
                                        <span class="text-gray-700 font-medium">{{ $region->name }}</span>
                                    </div>
                                    @if($region->children_recursive && count($region->children_recursive))
                                        <button type="button" @click="openRegion = !openRegion">
                                            <svg :class="{ 'rotate-90': openRegion }" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>

                                @if($region->children_recursive && count($region->children_recursive))
                                    <div x-show="openRegion" x-transition class="ml-5 mt-1 space-y-1">
                                        @foreach($region->children_recursive as $city)
                                            @php
                                                $isCheckedCity = in_array($city->id, $selected);
                                            @endphp
                                            <div class="flex items-center space-x-2 ml-2">
                                                <input type="checkbox" name="locations[]" value="{{ $city->id }}" {{ $isCheckedCity ? 'checked' : '' }}>
                                                <span class="text-gray-600">{{ $city->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>
