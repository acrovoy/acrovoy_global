@extends('dashboard.admin.layout')

@section('dashboard-content')

<div class="max-w-5xl mx-auto">

    {{-- ================= HEADER ================= --}}
    <div class="mb-10">

        <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-white via-gray-50 to-white p-8">

            <div class="inline-flex items-center rounded-full border border-gray-300 bg-white px-3 py-1 text-xs font-medium uppercase tracking-wider text-gray-600">
                Content Management
            </div>

            <h1 class="mt-5 text-3xl font-semibold tracking-tight text-gray-900">
                Create Website Page
            </h1>

            <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
                Create and manage static platform pages such as About Us, Terms,
                Privacy Policy, Company Information and other content sections.
            </p>

        </div>

    </div>


    <x-alerts />


    <form method="POST"
        action="{{ route('admin.pages.store') }}"
        enctype="multipart/form-data">

        @csrf

        <div class="space-y-8">

            {{-- ================= GENERAL INFORMATION ================= --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 bg-gray-50 px-8 py-5">
                    <h2 class="text-lg font-semibold text-gray-900">
                        General Information
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Basic page information and URL configuration.
                    </p>
                </div>
                <div class="space-y-7 p-8">
                    {{-- SLUG --}}
                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                            URL Slug
                        </label>
                        <input
                            type="text"
                            name="slug"
                            value="{{ old('slug') }}"
                            placeholder="about-acrovoy"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">
                        <p class="mt-2 text-sm text-gray-500">
                            Leave empty to generate automatically from title.
                        </p>
                    </div>
                </div>

            </div>


            {{-- ================= PAGE CONTENT ================= --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                <div class="border-b border-gray-100 bg-gray-50 px-8 py-5">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Page Content
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Main content displayed on the website page.
                    </p>
                </div>

                <div class="space-y-7 p-8">

                    @foreach($locales as $locale)

                    <div class="border-b pb-8 last:border-0">

                        <h3 class="mb-4 font-semibold text-gray-900">
                            {{ strtoupper($locale) }} Translation
                        </h3>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Title
                        </label>
                        <input
                            type="text"
                            name="translations[{{ $locale }}][title]"
                            value="{{ old("translations.$locale.title") }}"
                            class="mb-5 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">

                        {{-- EXCERPT --}}
                        <div class="mb-6">
                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Short Description
                            </label>
                            <textarea
                                rows="3"
                                name="translations[{{ $locale }}][excerpt]"
                                placeholder="Short description..."
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm leading-7 focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">{{ old("translations.$locale.excerpt") }}</textarea>
                        </div>

                        {{-- CONTENT --}}
                        <div class="mb-6">
                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Content
                            </label>

                            <textarea
                                name="translations[{{ $locale }}][content]"
                                rows="12"
                                class="ckeditor w-full rounded-xl border border-gray-300 px-4 py-3">{!! old("translations.$locale.content") !!}</textarea>
                        </div>

                        {{-- SEO TITLE --}}
                        <div class="mb-6">

                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                                SEO Title
                            </label>

                            <input
                                type="text"
                                name="translations[{{ $locale }}][seo_title]"
                                value="{{ old("translations.$locale.seo_title") }}"
                                placeholder="Optimized title for search engines"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm transition focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">

                            <p class="mt-2 text-sm text-gray-500">
                                Title displayed by search engines and browser tabs.
                            </p>

                        </div>


                        {{-- SEO DESCRIPTION --}}
                        <div class="mb-6">

                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                                SEO Description
                            </label>

                            <textarea
                                rows="3"
                                name="translations[{{ $locale }}][seo_description]"
                                placeholder="Short search engine description..."
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm leading-7 transition focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">{{ old("translations.$locale.seo_description") }}</textarea>

                            <p class="mt-2 text-sm text-gray-500">
                                Recommended length: 140–160 characters.
                            </p>

                        </div>


                        {{-- SEO KEYWORDS --}}
                        <div>

                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                                SEO Keywords
                            </label>

                            <input
                                type="text"
                                name="translations[{{ $locale }}][seo_keywords]"
                                value="{{ old("translations.$locale.seo_keywords") }}"
                                placeholder="marketplace, furniture, hospitality, hotel..."
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm transition focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">

                            <p class="mt-2 text-sm text-gray-500">
                                Separate keywords with commas.
                            </p>

                        </div>


                    </div>


                   
                        
                    


                   

                    @endforeach



                     {{-- ================= SETTINGS ================= --}}
                    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 bg-gray-50 px-8 py-5">
                            <h2 class="text-lg font-semibold text-gray-900">
                                Page Settings
                            </h2>
                            <p class="mt-1 text-sm text-gray-500">
                                Control template and publication status.
                            </p>
                        </div>
                        <div class="grid gap-8 p-8 lg:grid-cols-2">
                            {{-- TEMPLATE --}}
                            <div>
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Template
                                </label>

                               <select
    name="template"
    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">

    <option value="legal" {{ old('template') == 'legal' ? 'selected' : '' }}>
        Legal Document
    </option>

    <option value="corporate" {{ old('template') == 'corporate' ? 'selected' : '' }}>
        Corporate Page
    </option>

    <option value="landing" {{ old('template') == 'landing' ? 'selected' : '' }}>
        Landing Page
    </option>

</select>
                            </div>

                            {{-- STATUS --}}
                            <div>

                                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Status
                                </label>
                                <select
                                    name="status"
                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">
                                    <option value="draft">
                                        Draft
                                    </option>
                                    <option value="published">
                                        Published
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>



                    {{-- ================= FOOTER ================= --}}
                    <div class="flex items-center justify-between border-t border-gray-200 pt-8 pb-4">
                        <div>
                            <div class="text-sm font-medium text-gray-900">
                                Ready to create this page?
                            </div>
                            <div class="mt-1 text-sm text-gray-500">
                                You can edit content and settings later.
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.pages.index') }}"
                                class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="rounded-xl bg-gray-900 px-7 py-3 text-sm font-medium text-white hover:bg-black">
                                Create Page
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </form>


</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<script>

document.addEventListener('DOMContentLoaded', () => {


    document.querySelectorAll('.ckeditor').forEach(el => {


        ClassicEditor
            .create(el, {

                ckfinder: {
                    uploadUrl: "{{ route('admin.pages.upload') }}?_token={{ csrf_token() }}"
                }

            })

            .catch(error => {
                console.error(error);
            });


    });


});

</script>



@endsection