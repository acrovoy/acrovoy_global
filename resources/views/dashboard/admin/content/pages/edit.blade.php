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
                Edit Website Page
            </h1>


            <p class="mt-4 max-w-3xl text-sm leading-7 text-gray-600">
                Manage page content, translations, SEO information and publication settings.
            </p>


        </div>

    </div>


    <x-alerts />


    <form method="POST"
        action="{{ route('admin.pages.update', $page) }}"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')


        <div class="space-y-8">



            {{-- ================= GENERAL INFORMATION ================= --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">


                <div class="border-b border-gray-100 bg-gray-50 px-8 py-5">

                    <h2 class="text-lg font-semibold text-gray-900">
                        General Information
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Basic page configuration.
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
                            value="{{ old('slug',$page->slug) }}"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">


                    </div>



                </div>


            </div>







            {{-- ================= CONTENT ================= --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">


                <div class="border-b border-gray-100 bg-gray-50 px-8 py-5">


                    <h2 class="text-lg font-semibold text-gray-900">
                        Page Content
                    </h2>


                    <p class="mt-1 text-sm text-gray-500">
                        Manage translations and article content.
                    </p>


                </div>



                <div class="space-y-10 p-8">


                    @foreach($locales as $locale)


                    @php
                    $translation = $page->translations
                    ->where('locale',$locale)
                    ->first();
                    @endphp


                    <div class="border-b border-gray-200 pb-8 last:border-0">


                        <h3 class="mb-5 text-lg font-semibold text-gray-900">
                            {{ strtoupper($locale) }} Translation
                        </h3>



                        {{-- TITLE --}}
                        <div class="mb-6">


                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Title
                            </label>


                            <input
                                type="text"
                                name="translations[{{ $locale }}][title]"
                                value="{{ old(
                                    "translations.$locale.title",
                                    $translation?->title
                                ) }}"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">


                        </div>





                        {{-- CONTENT --}}
                        <div class="mb-6">


                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Content
                            </label>



                            <textarea
                                name="translations[{{ $locale }}][content]"
                                rows="12"
                                class="ckeditor w-full rounded-xl border border-gray-300 px-4 py-3 text-sm">{!! old(
        "translations.$locale.content",
        $translation?->content
    ) !!}</textarea>



                        </div>


                        {{-- SEO TITLE --}}
                    <div class="mb-6">

                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                            SEO Title
                        </label>

                        <input
                            type="text"
                            name="translations[{{ $locale }}][seo_title]"
                            value="{{ old("translations.$locale.seo_title", $translation?->seo_title) }}"
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
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm leading-7 transition focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">{{ old("translations.$locale.seo_description", $translation?->seo_description) }}</textarea>

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
                            value="{{ old("translations.$locale.seo_keywords", $translation?->seo_keywords) }}"
                            placeholder="marketplace, furniture, hospitality, hotel..."
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm transition focus:border-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-900/5">

                        <p class="mt-2 text-sm text-gray-500">
                            Separate keywords with commas.
                        </p>

                    </div>


                    </div>


                    



                    @endforeach



                </div>


            </div>








            {{-- ================= SETTINGS ================= --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">


                <div class="border-b border-gray-100 bg-gray-50 px-8 py-5">


                    <h2 class="text-lg font-semibold text-gray-900">
                        Publishing Settings
                    </h2>


                </div>




                <div class="grid gap-8 p-8 lg:grid-cols-2">



                    {{-- TEMPLATE --}}
                    <div>


                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Template
                        </label>


                        <select
                            name="template"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm">


                           <option value="legal"
    @selected($page->template === 'legal')>
    Legal Document
</option>

<option value="corporate"
    @selected($page->template === 'corporate')>
    Corporate Page
</option>

<option value="landing"
    @selected($page->template === 'landing')>
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
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm">


                            <option value="draft"
                                @selected($page->status === 'draft')>
                                Draft
                            </option>


                            <option value="published"
                                @selected($page->status === 'published')>
                                Published
                            </option>


                            <option value="archived"
                                @selected($page->status === 'archived')>
                                Archived
                            </option>



                        </select>



                    </div>



                </div>


            </div>


            {{-- ================= FOOTER ================= --}}
            <div class="flex items-center justify-between border-t border-gray-200 pt-8 pb-4">


                <div>


                    <div class="text-sm font-medium text-gray-900">
                        Update this page?
                    </div>


                    <div class="mt-1 text-sm text-gray-500">
                        Changes will be saved immediately.
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

                        Update Page

                    </button>



                </div>


            </div>



        </div>



    </form>


</div>





{{-- CKEditor --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>


<script>

document.addEventListener('DOMContentLoaded',()=>{


    document.querySelectorAll('.ckeditor').forEach(el=>{


        ClassicEditor
    .create(el,{
        ckfinder:{
            uploadUrl:"{{ route('admin.pages.upload') }}?_token={{ csrf_token() }}"
        },
        htmlSupport:{
            allow:[
                {
                    name: /.*/,
                    attributes:true,
                    classes:true,
                    styles:true
                }
            ]
        }
    })
            .catch(error=>console.error(error));


    });


});


</script>


@endsection