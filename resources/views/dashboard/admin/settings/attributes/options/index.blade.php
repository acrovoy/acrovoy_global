@extends('dashboard.admin.settings.layout')

@section('settings-content')
<div class="flex flex-col gap-6 max-w-3xl">

    <x-alerts />

    {{-- Header --}}
    <div>
        <h2 class="text-2xl font-semibold text-gray-900">
            Options for: {{ $attribute->name }}
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            Code: {{ $attribute->code }}
        </p>
    </div>

    @php
        use App\Models\Language;

        $languages = Language::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    @endphp

    {{-- CREATE OPTION FORM --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
        <form method="POST"
              action="{{ route('admin.settings.attributes.options.store', $attribute->id) }}"
              class="p-6 flex flex-col gap-6">

            @csrf

            {{-- Translations --}}
            <div class="border rounded-xl p-4 space-y-4">
                <h3 class="font-medium text-gray-700 text-sm">
                    Translations
                </h3>

                @foreach($languages as $lang)
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">
                            Value ({{ strtoupper($lang->code) }})
                        </label>

                        <input type="text"
                               name="translations[{{ $lang->code }}]"
                               value="{{ old('translations.' . $lang->code) }}"
                               class="w-full border border-gray-300 rounded px-3 py-2"
                               required>
                    </div>
                @endforeach
            </div>

            {{-- Sort Order --}}
            <div>
                <label class="block font-medium mb-1">
                    Sort order
                </label>
                <input type="number"
                       name="sort_order"
                       value="{{ old('sort_order', 0) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.settings.attributes.index') }}"
                   class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                    Cancel
                </a>

                <button type="submit"
                        class="px-5 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                    Add Option
                </button>
            </div>
        </form>
    </div>

    {{-- OPTIONS TABLE --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Name</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-600">Sort order</th>
                    <th class="px-5 py-3 text-right font-medium text-gray-600">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($options as $option)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <form method="POST"
                                  action="{{ route('admin.settings.attributes.options.update', [$attribute->id, $option->id]) }}"
                                  class="flex flex-col gap-2">
                                @csrf
                                @method('PUT')

                                <div class="grid grid-cols-2 gap-4">
                                    @foreach($languages as $lang)
                                        <input type="text"
                                               name="translations[{{ $lang->code }}]"
                                               value="{{ $option->translations->where('locale', $lang->code)->first()?->value ?? '' }}"
                                               class="w-full border border-gray-300 rounded px-3 py-2">
                                    @endforeach
                                </div>

                                <input type="number"
                                       name="sort_order"
                                       value="{{ $option->sort_order }}"
                                       class="w-full border border-gray-300 rounded px-3 py-2 mt-2">

                                <div class="flex justify-end gap-2 mt-2">
                                    <button class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                                        Save
                                    </button>
                            </form>

                            <form method="POST"
                                  action="{{ route('admin.settings.attributes.options.destroy', [$attribute->id, $option->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-500 transition">
                                    Delete
                                </button>
                            </form>
                                </div>
                        </td>
                        <td class="px-5 py-3">{{ $option->sort_order }}</td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection