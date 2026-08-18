@extends('dashboard.admin.settings.layout')

@section('settings-content')
<div class="flex flex-col gap-6 max-w-5xl">
    <x-alerts />
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Edit Unit</h2>
            <p class="text-sm text-gray-500 mt-1">
                Edit unit definition, conversion settings and translations.
            </p>
        </div>
        <a href="{{ route('admin.settings.units.index') }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
            ← Units
        </a>
    </div>

    <form method="POST"
          action="{{ route('admin.settings.units.update', $unit) }}"
          class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @csrf
        @method('PUT')

        <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800 text-sm">Unit Definition</h3>
            <p class="text-xs text-gray-500 mt-1">
                Configure the unit and how it behaves in conversions.
            </p>
        </div>

        <div class="p-5 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Code</label>
                    <input type="text"
                           name="code"
                           value="{{ old('code', $unit->code) }}"
                           class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                           required>
                    @error('code')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Symbol</label>
                    <input type="text"
                           name="symbol"
                           value="{{ old('symbol', $unit->symbol) }}"
                           class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                           required>
                    @error('symbol')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Unit Group</label>
                    <input type="text"
                           name="unit_group"
                           value="{{ old('unit_group', $unit->unit_group) }}"
                           placeholder="length, weight, area..."
                           class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                           required>
                    @error('unit_group')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="border border-gray-200 rounded-xl p-4">
                <div class="mb-4">
                    <h3 class="font-medium text-gray-700 text-sm">Conversion</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Define how this unit converts relative to the base unit of its group.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Conversion Factor</label>
                        <input type="number"
                               step="0.000000000001"
                               name="conversion_factor"
                               value="{{ old('conversion_factor', $unit->conversion_factor) }}"
                               class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Conversion Offset</label>
                        <input type="number"
                               step="0.000000000001"
                               name="conversion_offset"
                               value="{{ old('conversion_offset', $unit->conversion_offset) }}"
                               class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Sort Order</label>
                        <input type="number"
                               min="0"
                               name="sort_order"
                               value="{{ old('sort_order', $unit->sort_order) }}"
                               class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100"
                               required>
                    </div>
                </div>

                <div class="flex flex-wrap gap-6 mt-5 pt-4 border-t border-gray-100">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox"
                               name="is_base"
                               value="1"
                               @checked(old('is_base', $unit->is_base))
                               class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                        <span class="text-sm font-medium text-gray-700">Base unit</span>
                    </label>

                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               @checked(old('is_active', $unit->is_active))
                               class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <div class="border border-gray-200 rounded-xl p-4 space-y-4">
                <div>
                    <h3 class="font-medium text-gray-700 text-sm">Translations</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Set the display name for this unit in each active language.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($languages as $language)
                        @php
                            $translation = $unit->translations
                                ->firstWhere('locale', $language->code);
                        @endphp

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">
                                Name
                                <span class="text-gray-400">
                                    ({{ strtoupper($language->code) }})
                                </span>
                            </label>

                            <input type="text"
                                   name="translations[{{ $language->code }}]"
                                   value="{{ old('translations.' . $language->code, $translation?->name) }}"
                                   class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.settings.units.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>

                <button type="submit"
                        class="px-5 py-2 text-sm font-medium bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition shadow-sm">
                    Save Changes
                </button>
            </div>
        </div>
    </form>
</div>
@endsection