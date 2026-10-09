@extends('dashboard.admin.layout')

@section('dashboard-content')

<div class="mx-auto max-w-6xl space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-stone-900">
                Edit Courier
            </h1>
            <p class="mt-1 text-sm text-stone-500">
                Update courier details, logo and translations.
            </p>
        </div>

        <a href="{{ route('admin.couriers.index') }}"
           class="inline-flex items-center justify-center rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">
            <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
            </svg>
            Back to Couriers
        </a>
    </div>

    {{-- ALERTS --}}
    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">Please correct the following errors:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('admin.couriers.update', $courier) }}"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @method('PUT')

        {{-- GENERAL INFORMATION --}}
        <section class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
            <div class="border-b border-stone-100 px-6 py-5">
                <h2 class="text-base font-semibold text-stone-900">General Information</h2>
                <p class="mt-1 text-sm text-stone-500">
                    Basic details used to identify and display the courier.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                {{-- CODE --}}
                <div>
                    <label for="code" class="mb-2 block text-sm font-medium text-stone-700">
                        Courier Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="code"
                           name="code"
                           value="{{ old('code', $courier->code) }}"
                           required
                           maxlength="100"
                           autocomplete="off"
                           placeholder="e.g. nova_poshta"
                           class="w-full rounded-xl border border-stone-200 px-4 py-3 text-sm text-stone-900 outline-none transition placeholder:text-stone-400 focus:border-stone-400 focus:ring-2 focus:ring-stone-100 @error('code') border-red-300 @enderror">
                    @error('code')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-stone-400">
                        Unique internal identifier. Keep it stable after creation.
                    </p>
                </div>

                {{-- SORT ORDER --}}
                <div>
                    <label for="sort_order" class="mb-2 block text-sm font-medium text-stone-700">
                        Sort Order
                    </label>
                    <input type="number"
                           id="sort_order"
                           name="sort_order"
                           value="{{ old('sort_order', $courier->sort_order) }}"
                           min="0"
                           step="1"
                           class="w-full rounded-xl border border-stone-200 px-4 py-3 text-sm text-stone-900 outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-100 @error('sort_order') border-red-300 @enderror">
                    @error('sort_order')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-stone-400">
                        Lower values appear first.
                    </p>
                </div>

                {{-- WEBSITE --}}
                <div>
                    <label for="website" class="mb-2 block text-sm font-medium text-stone-700">
                        Website
                    </label>
                    <input type="url"
                           id="website"
                           name="website"
                           value="{{ old('website', $courier->website) }}"
                           maxlength="2048"
                           placeholder="https://example.com"
                           class="w-full rounded-xl border border-stone-200 px-4 py-3 text-sm text-stone-900 outline-none transition placeholder:text-stone-400 focus:border-stone-400 focus:ring-2 focus:ring-stone-100 @error('website') border-red-300 @enderror">
                    @error('website')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- TRACKING URL --}}
                <div>
                    <label for="tracking_url" class="mb-2 block text-sm font-medium text-stone-700">
                        Tracking URL
                    </label>
                    <input type="url"
                           id="tracking_url"
                           name="tracking_url"
                           value="{{ old('tracking_url', $courier->tracking_url) }}"
                           maxlength="2048"
                           placeholder="https://example.com/track"
                           class="w-full rounded-xl border border-stone-200 px-4 py-3 text-sm text-stone-900 outline-none transition placeholder:text-stone-400 focus:border-stone-400 focus:ring-2 focus:ring-stone-100 @error('tracking_url') border-red-300 @enderror">
                    @error('tracking_url')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- LOGO --}}
                <div class="md:col-span-2"
                     x-data="{
                        preview: @js($courier->logo ? (str_starts_with($courier->logo, 'http://') || str_starts_with($courier->logo, 'https://') ? $courier->logo : \Illuminate\Support\Facades\Storage::disk('public')->url($courier->logo)) : null),
                        existingLogo: @js((bool) $courier->logo),
                        selectedFile: false,
                        updatePreview(event) {
                            const file = event.target.files[0];
                            if (!file) {
                                this.selectedFile = false;
                                return;
                            }
                            this.selectedFile = true;
                            this.preview = URL.createObjectURL(file);
                        }
                     }">
                    <label for="logo" class="mb-2 block text-sm font-medium text-stone-700">
                        Courier Logo
                    </label>

                    <div class="flex flex-col gap-5 rounded-xl border border-dashed border-stone-300 bg-stone-50/70 p-5 sm:flex-row sm:items-center">
                        <div class="flex h-24 w-32 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-stone-200 bg-white p-3">
                            <template x-if="preview">
                                <img :src="preview"
                                     alt="Courier logo preview"
                                     class="max-h-full max-w-full object-contain">
                            </template>
                            <template x-if="!preview">
                                <div class="text-center text-xs text-stone-400">
                                    <svg class="mx-auto mb-2 h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 15l-5-5L5 21"/>
                                    </svg>
                                    No logo
                                </div>
                            </template>
                        </div>

                        <div class="min-w-0 flex-1">
                            <input type="file"
                                   id="logo"
                                   name="logo"
                                   accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                   @change="updatePreview($event)"
                                   class="block w-full text-sm text-stone-600 file:mr-4 file:rounded-lg file:border-0 file:bg-stone-900 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-white hover:file:bg-stone-700">

                            <p class="mt-2 text-xs leading-relaxed text-stone-500">
                                Upload a new logo to replace the current one. Leave empty to keep the existing logo.
                                JPG, PNG or WebP, maximum 2 MB.
                            </p>

                            @error('logo')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ACTIVE STATUS --}}
                <div class="md:col-span-2">
                    <input type="hidden" name="is_active" value="0">

                    <label for="is_active"
                           class="flex cursor-pointer items-start gap-3 rounded-xl border border-stone-200 p-4 transition hover:bg-stone-50">
                        <input type="checkbox"
                               id="is_active"
                               name="is_active"
                               value="1"
                               @checked((bool) old('is_active', $courier->is_active))
                               class="mt-0.5 h-4 w-4 rounded border-stone-300 text-stone-900 focus:ring-stone-500">

                        <span>
                            <span class="block text-sm font-medium text-stone-900">
                                Active courier
                            </span>
                            <span class="mt-1 block text-sm text-stone-500">
                                Active couriers can be offered for selection in delivery settings.
                            </span>
                        </span>
                    </label>
                    @error('is_active')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- TRANSLATIONS --}}
        @php
            $locales = [
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

        <section class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
            <div class="border-b border-stone-100 px-6 py-5">
                <h2 class="text-base font-semibold text-stone-900">Translations</h2>
                <p class="mt-1 text-sm text-stone-500">
                    Provide the courier name and optional description for each language.
                </p>
            </div>

            <div class="divide-y divide-stone-100">
                @foreach($locales as $locale => $language)
                    @php
                        $translation = $courier->translations->firstWhere('locale', $locale);
                    @endphp

                    <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-12">
                        <div class="md:col-span-2">
                            <span class="inline-flex items-center rounded-lg bg-stone-100 px-2.5 py-1.5 text-xs font-semibold uppercase tracking-wide text-stone-700">
                                {{ $locale }}
                            </span>
                            <p class="mt-2 text-sm font-medium text-stone-800">
                                {{ $language }}
                                @if($locale === 'en')
                                    <span class="text-red-500">*</span>
                                @endif
                            </p>
                        </div>

                        <div class="space-y-4 md:col-span-10">
                            <div>
                                <label for="translation_{{ $locale }}_name"
                                       class="mb-1.5 block text-sm font-medium text-stone-700">
                                    Name
                                </label>
                                <input type="text"
                                       id="translation_{{ $locale }}_name"
                                       name="translations[{{ $locale }}][name]"
                                       value="{{ old("translations.{$locale}.name", $translation?->name) }}"
                                       maxlength="255"
                                       @if($locale === 'en') required @endif
                                       placeholder="Courier name in {{ $language }}"
                                       class="w-full rounded-xl border border-stone-200 px-4 py-3 text-sm text-stone-900 outline-none transition placeholder:text-stone-400 focus:border-stone-400 focus:ring-2 focus:ring-stone-100 @error("translations.{$locale}.name") border-red-300 @enderror">
                                @error("translations.{$locale}.name")
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="translation_{{ $locale }}_description"
                                       class="mb-1.5 block text-sm font-medium text-stone-700">
                                    Description <span class="font-normal text-stone-400">(optional)</span>
                                </label>
                                <textarea id="translation_{{ $locale }}_description"
                                          name="translations[{{ $locale }}][description]"
                                          rows="2"
                                          placeholder="Short description in {{ $language }}"
                                          class="w-full rounded-xl border border-stone-200 px-4 py-3 text-sm text-stone-900 outline-none transition placeholder:text-stone-400 focus:border-stone-400 focus:ring-2 focus:ring-stone-100 @error("translations.{$locale}.description") border-red-300 @enderror">{{ old("translations.{$locale}.description", $translation?->description) }}</textarea>
                                @error("translations.{$locale}.description")
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ACTIONS --}}
        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-stone-400">
                Fields marked with <span class="text-red-500">*</span> are required.
            </p>

            <div class="flex flex-col-reverse gap-3 sm:flex-row">
                <a href="{{ route('admin.couriers.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-stone-200 bg-white px-5 py-3 text-sm font-medium text-stone-700 transition hover:bg-stone-50">
                    Cancel
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-stone-700 focus:outline-none focus:ring-2 focus:ring-stone-400 focus:ring-offset-2">
                    Save Changes
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

