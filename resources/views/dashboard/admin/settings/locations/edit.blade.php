@extends('dashboard.admin.settings.layout')

@section('settings-content')
<div class="flex flex-col gap-6 max-w-3xl">

    {{-- Errors --}}
    @if ($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header --}}
    <div>
        <h2 class="text-2xl font-semibold text-gray-900">Редактировать населённый пункт</h2>
        <p class="text-sm text-gray-500 mt-1">
            Измените данные населённого пункта, его страну или регион
        </p>
    </div>

    {{-- Form Card --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
        <form action="{{ route('admin.settings.locations.update', $location->id) }}" method="POST" class="p-6 flex flex-col gap-6">
            @csrf
            @method('PUT')

           <input type="hidden" name="updated_by" value="{{ auth()->id() }}">

            {{-- Название --}}
            <div>
                <label for="name" class="block font-medium mb-1">Название</label>
                <input type="text" name="name" id="name" value="{{ old('name', $location->name) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2"
                       required>
                @error('name')
                    <p class="text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Страна --}}
            <div>
                <label for="country_id" class="block font-medium mb-1">Страна</label>
                <select name="country_id" id="country_id" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">-- Выберите страну --</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ old('country_id', $location->country_id) == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
                @error('country_id')
                    <p class="text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Регион --}}
            <div>
                <label for="parent_id" class="block font-medium mb-1">Регион (если это область)</label>
                <select name="parent_id" id="parent_id" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">— Нет —</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" {{ old('parent_id', $location->parent_id) == $region->id ? 'selected' : '' }}>
                            {{ $region->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <p class="text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.settings.locations.index') }}"
                   class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                    Отмена
                </a>
                <button type="submit"
                        class="px-5 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                    Обновить
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
