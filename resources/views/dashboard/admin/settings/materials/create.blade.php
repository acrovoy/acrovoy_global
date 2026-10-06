@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">
        Добавить материал
    </h1>
</div>

<x-alerts />

<form
    action="{{ route('admin.settings.materials.store') }}"
    method="POST"
    enctype="multipart/form-data"
    class="space-y-4 bg-white p-6 rounded shadow"
>
    @csrf

    {{-- Group --}}
    <div>
        <label class="block mb-1 font-medium text-gray-700">
            Группа материала
        </label>

        <select
            name="material_group_id"
            class="w-full border rounded px-3 py-2
                   focus:ring focus:ring-blue-300
                   bg-white"
            required
        >
            <option value="">
                Выберите группу
            </option>

            @foreach($groups as $group)
                <option
                    value="{{ $group->id }}"
                    {{ old('material_group_id') == $group->id ? 'selected' : '' }}
                >
                    {{ $group->translatedName() ?? $group->slug }}
                </option>
            @endforeach
        </select>

        @error('material_group_id')
            <p class="text-red-500 text-sm mt-1">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Slug --}}
    <div>
        <label class="block mb-1 font-medium text-gray-700">
            Slug
        </label>

        <input
            type="text"
            name="slug"
            value="{{ old('slug') }}"
            class="w-full border rounded px-3 py-2
                   focus:ring focus:ring-blue-300"
            required
        >

        @error('slug')
            <p class="text-red-500 text-sm mt-1">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Названия / коды материала на активных языках --}}
    @foreach($languages as $language)

        <div>
            <label class="block mb-1 font-medium text-gray-700">
                Название или код материала ({{ $language->code }})
            </label>

            <input
                type="text"
                name="name[{{ $language->code }}]"
                value="{{ old("name.{$language->code}") }}"
                class="w-full border rounded px-3 py-2
                       focus:ring focus:ring-blue-300"
                required
            >

            @error("name.{$language->code}")
                <p class="text-red-500 text-sm mt-1">
                    {{ $message }}
                </p>
            @enderror
        </div>

    @endforeach

    {{-- Photo --}}
    <div>
        <label class="block mb-1 font-medium text-gray-700">
            Фото материала
        </label>

        <input
            type="file"
            name="photo"
            accept="image/jpeg,image/png,image/webp"
            class="w-full border border-gray-200 rounded-lg px-3 py-2
                   text-sm text-gray-600
                   focus:outline-none
                   focus:ring-1 focus:ring-blue-300"
        >

        <p class="mt-1 text-xs text-gray-400">
            JPG, JPEG, PNG, WEBP. Максимальный размер — 4 MB.
        </p>

        @error('photo')
            <p class="text-red-500 text-sm mt-1">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Submit --}}
    <button
        type="submit"
        class="px-4 py-2
               bg-blue-600
               hover:bg-blue-700
               text-white
               font-semibold
               rounded
               shadow
               transition"
    >
        Сохранить
    </button>

</form>

@endsection