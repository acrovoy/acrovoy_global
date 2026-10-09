@extends('dashboard.admin.layout')

@section('dashboard-content')

<div class="bg-[#F7F3EA] py-8">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mx-auto max-w-5xl">
            {{-- Header --}}
            <div class="mb-6">
                <a href="{{ route('admin.couriers.index') }}"
                   class="inline-flex items-center gap-2 text-sm text-stone-600 transition hover:text-stone-900">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m15 18-6-6 6-6"/>
                    </svg>
                    Back to couriers
                </a>

                <div class="mt-4">
                    <h1 class="text-2xl font-semibold text-gray-900">Add courier</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Add a courier service to the Acrovoy directory.
                    </p>
                </div>
            </div>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                    <p class="text-sm font-semibold text-red-800">Please check the form for errors.</p>
                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.couriers.store') }}" 
            enctype="multipart/form-data"
            class="space-y-6">
                @csrf

                {{-- General information --}}
                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-5 border-b border-gray-100 pb-4">
                        <h2 class="text-base font-semibold text-gray-900">General information</h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Basic details used to identify the courier service.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        {{-- Code --}}
                        <div>
                            <label for="code" class="mb-1.5 block text-sm font-medium text-gray-700">
                                Courier code <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="code"
                                   name="code"
                                   value="{{ old('code') }}"
                                   required
                                   maxlength="100"
                                   placeholder="e.g. nova_poshta"
                                   class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-100 @error('code') border-red-400 @enderror">
                            @error('code')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-gray-400">
                                Unique internal identifier. Use Latin letters, numbers and underscores.
                            </p>
                        </div>

                        {{-- Sort order --}}
                        <div>
                            <label for="sort_order" class="mb-1.5 block text-sm font-medium text-gray-700">
                                Sort order
                            </label>
                            <input type="number"
                                   id="sort_order"
                                   name="sort_order"
                                   value="{{ old('sort_order', 0) }}"
                                   min="0"
                                   step="1"
                                   class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-sm text-gray-900 focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-100 @error('sort_order') border-red-400 @enderror">
                            @error('sort_order')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-gray-400">
                                Lower values appear first.
                            </p>
                        </div>

                        {{-- Website --}}
                        <div>
                            <label for="website" class="mb-1.5 block text-sm font-medium text-gray-700">
                                Website
                            </label>
                            <input type="url"
                                   id="website"
                                   name="website"
                                   value="{{ old('website') }}"
                                   maxlength="255"
                                   placeholder="https://example.com"
                                   class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-100 @error('website') border-red-400 @enderror">
                            @error('website')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tracking URL --}}
                        <div>
                            <label for="tracking_url" class="mb-1.5 block text-sm font-medium text-gray-700">
                                Tracking URL
                            </label>
                            <input type="url"
                                   id="tracking_url"
                                   name="tracking_url"
                                   value="{{ old('tracking_url') }}"
                                   maxlength="255"
                                   placeholder="https://example.com/track"
                                   class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-100 @error('tracking_url') border-red-400 @enderror">
                            @error('tracking_url')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Logo upload --}}
<div class="sm:col-span-2" x-data="{
    preview: null,
    fileName: '',
    selectFile(event) {
        const file = event.target.files[0];
        if (!file) {
            this.preview = null;
            this.fileName = '';
            return;
        }
        this.fileName = file.name;
        this.preview = URL.createObjectURL(file);
    }
}">
    <label for="logo" class="mb-1.5 block text-sm font-medium text-gray-700">
        Courier logo
    </label>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
        <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
            <template x-if="preview">
                <img :src="preview" alt="Logo preview" class="h-full w-full object-contain p-2">
            </template>

            <template x-if="!preview">
                <svg class="h-8 w-8 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="3"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <path d="m21 15-5-5L5 21"/>
                </svg>
            </template>
        </div>

        <div class="min-w-0 flex-1">
            <input type="file"
                   id="logo"
                   name="logo"
                   accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                   @change="selectFile($event)"
                   class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200">

            <p class="mt-2 text-xs text-gray-500">
                PNG, JPG or WebP. Maximum file size: 2 MB.
            </p>

            <p x-show="fileName"
               x-text="fileName"
               class="mt-1 truncate text-xs text-gray-600"></p>

            @error('logo')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

                        {{-- Active status --}}
                        <div class="sm:col-span-2">
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-4 transition hover:bg-gray-50">
                                <input type="checkbox"
                                       name="is_active"
                                       value="1"
                                       @checked(old('is_active', true))
                                       class="mt-0.5 h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                <span>
                                    <span class="block text-sm font-medium text-gray-900">Active courier</span>
                                    <span class="mt-1 block text-sm text-gray-500">
                                        Active couriers can be offered for selection in delivery settings.
                                    </span>
                                </span>
                            </label>
                        </div>
                    </div>
                </section>

                {{-- Translations --}}
                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-5 border-b border-gray-100 pb-4">
                        <h2 class="text-base font-semibold text-gray-900">Translations</h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Enter the courier name and optional description for each language.
                        </p>
                    </div>

                    @php
                        $languages = [
                            'en' => 'English',
                            'es' => 'Spanish',
                            'fr' => 'French',
                            'ru' => 'Russian',
                            'ar' => 'Arabic',
                            'zh' => 'Chinese',
                            'tr' => 'Turkish',
                            'uk' => 'Ukrainian',
                        ];
                    @endphp

                    <div class="space-y-5">
                        @foreach($languages as $locale => $language)
                            <div class="rounded-xl border border-gray-200 p-4">
                                <div class="mb-4 flex items-center justify-between">
                                    <h3 class="text-sm font-semibold text-gray-900">{{ $language }}</h3>
                                    <span class="rounded-md bg-gray-100 px-2 py-1 text-xs font-medium uppercase text-gray-600">
                                        {{ $locale }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="translation_{{ $locale }}_name"
                                               class="mb-1.5 block text-sm font-medium text-gray-700">
                                            Name
                                            @if($locale === 'en')
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        <input type="text"
                                               id="translation_{{ $locale }}_name"
                                               name="translations[{{ $locale }}][name]"
                                               value="{{ old("translations.$locale.name") }}"
                                               maxlength="255"
                                               @if($locale === 'en') required @endif
                                               placeholder="Courier name"
                                               class="w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-100">
                                        @error("translations.$locale.name")
                                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="translation_{{ $locale }}_description"
                                               class="mb-1.5 block text-sm font-medium text-gray-700">
                                            Description
                                        </label>
                                        <textarea id="translation_{{ $locale }}_description"
                                                  name="translations[{{ $locale }}][description]"
                                                  rows="2"
                                                  maxlength="5000"
                                                  placeholder="Optional description"
                                                  class="w-full resize-y rounded-xl border border-gray-200 px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-100">{{ old("translations.$locale.description") }}</textarea>
                                        @error("translations.$locale.description")
                                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- Actions --}}
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <a href="{{ route('admin.couriers.index') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                        Cancel
                    </a>

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        Create courier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

