@php
    $locales = [
        'en' => 'English',
        'es' => 'Español',
        'fr' => 'Français',
        'ru' => 'Русский',
        'ar' => 'العربية',
        'zh' => '中文',
        'tr' => 'Türkçe',
        'uk' => 'Українська',
    ];

    $post = $post ?? null;
    $media = $media ?? collect();
    $isEdit = $post !== null;
@endphp

<form
    action="{{ $isEdit ? route('community.posts.update', $post->id) : route('community.posts.store') }}"
    method="POST"
    enctype="multipart/form-data"
    id="postForm"
    class="space-y-8">

    @csrf

    @if($isEdit)
        @method('PUT')
    @endif

    {{-- =========================================================
        GENERAL INFORMATION
    ========================================================== --}}

    <section class="rounded-2xl border border-gray-200 bg-white">

        <div class="border-b border-gray-100 px-6 py-5">
            <h2 class="text-base font-semibold text-gray-900">
                Publication Details
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Choose a category and provide the content of your publication.
            </p>
        </div>

        <div class="space-y-6 p-6">

            <div>
                <label for="post_category_id" class="mb-2 block text-sm font-medium text-gray-700">
                    Category <span class="text-red-500">*</span>
                </label>

                <select
                    id="post_category_id"
                    name="post_category_id"
                    required
                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">

                    <option value="">Select category</option>

                    @foreach($categories as $category)
                        <option
                            value="{{ $category->id }}"
                            @selected(old('post_category_id', $post?->post_category_id) == $category->id)>
                            {{ $category->getTranslation(app()->getLocale())?->name
                                ?? $category->getTranslation('en')?->name
                                ?? $category->name }}
                        </option>
                    @endforeach

                </select>

                @error('post_category_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </section>


    {{-- =========================================================
        TRANSLATIONS
    ========================================================== --}}

    <section class="rounded-2xl border border-gray-200 bg-white">

        <div class="border-b border-gray-100 px-6 py-5">
            <h2 class="text-base font-semibold text-gray-900">
                Publication Content
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Add content in the languages you want to make available.
                At least one complete language version is required.
            </p>
        </div>

        <div class="p-6">

            {{-- LANGUAGE TABS --}}

            <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-4" id="languageTabs">

                @foreach($locales as $locale => $language)
                    <button
                        type="button"
                        data-locale="{{ $locale }}"
                        class="language-tab rounded-lg border px-4 py-2 text-sm font-medium transition
                        {{ $loop->first
                            ? 'border-gray-900 bg-gray-900 text-white'
                            : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50' }}">

                        {{ $language }}

                        @if($locale === 'en')
                            <span class="ml-1 text-red-400">*</span>
                        @endif

                    </button>
                @endforeach

            </div>


            {{-- LANGUAGE CONTENT --}}

            @foreach($locales as $locale => $language)

                @php
                    $translation = $post?->translations?->firstWhere('locale', $locale);

                    $title = old("translations.$locale.title", $translation?->title);
                    $slug = old("translations.$locale.slug", $translation?->slug);
                    $excerpt = old("translations.$locale.excerpt", $translation?->excerpt);
                    $content = old("translations.$locale.content", $translation?->content);
                    $seoTitle = old("translations.$locale.seo_title", $translation?->seo_title);
                    $seoDescription = old("translations.$locale.seo_description", $translation?->seo_description);
                @endphp

                <div
                    data-locale-panel="{{ $locale }}"
                    class="language-panel space-y-6 pt-6 {{ !$loop->first ? 'hidden' : '' }}">

                    {{-- TITLE --}}

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            Publication Title
                        </label>

                        <input
                            type="text"
                            name="translations[{{ $locale }}][title]"
                            value="{{ $title }}"
                            maxlength="255"
                            data-translation-field
                            data-required-field
                            data-slug-source
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                            placeholder="Enter publication title">

                        @error("translations.$locale.title")
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- SLUG --}}

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            URL Slug
                        </label>

                        <input
                            type="text"
                            name="translations[{{ $locale }}][slug]"
                            value="{{ $slug }}"
                            maxlength="255"
                            data-translation-field
                            data-required-field
                            data-slug-target
                            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm focus:border-gray-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gray-900"
                            placeholder="publication-url-slug">

                        <p class="mt-2 text-xs text-gray-400">
                            Use lowercase Latin letters, numbers and hyphens.
                        </p>

                        @error("translations.$locale.slug")
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- EXCERPT --}}

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            Short Description
                        </label>

                        <textarea
                            name="translations[{{ $locale }}][excerpt]"
                            rows="3"
                            maxlength="1000"
                            data-translation-field
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                            placeholder="A short introduction to your publication...">{{ $excerpt }}</textarea>

                        @error("translations.$locale.excerpt")
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- MAIN CONTENT / RICH TEXT EDITOR --}}

                    <div
                        class="rounded-xl border border-gray-200 bg-white shadow-sm"
                        data-post-editor="{{ $locale }}">

                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 px-4 py-3">

                            <div>
                                <label class="block text-sm font-semibold text-gray-900">
                                    Main Content
                                </label>

                                <p class="mt-1 text-xs text-gray-500">
                                    Format your publication using the editing tools below.
                                </p>
                            </div>

                            <span class="text-xs text-gray-400">
                                Maximum 20,000 characters
                            </span>

                        </div>


                        {{-- TOOLBAR --}}

                        <div class="flex flex-wrap items-center gap-1 border-b border-gray-200 bg-gray-50 px-3 py-2">

                            <button
                                type="button"
                                data-editor-command="bold"
                                data-editor-locale="{{ $locale }}"
                                class="editor-toolbar-button font-bold"
                                title="Bold">
                                B
                            </button>

                            <button
                                type="button"
                                data-editor-command="italic"
                                data-editor-locale="{{ $locale }}"
                                class="editor-toolbar-button italic"
                                title="Italic">
                                I
                            </button>

                            <button
                                type="button"
                                data-editor-command="underline"
                                data-editor-locale="{{ $locale }}"
                                class="editor-toolbar-button underline"
                                title="Underline">
                                U
                            </button>

                            <span class="mx-1 h-5 border-l border-gray-300"></span>

                            <button
                                type="button"
                                data-editor-command="formatBlock"
                                data-editor-value="H2"
                                data-editor-locale="{{ $locale }}"
                                class="editor-toolbar-button text-xs font-semibold"
                                title="Heading 2">
                                H2
                            </button>

                            <button
                                type="button"
                                data-editor-command="formatBlock"
                                data-editor-value="H3"
                                data-editor-locale="{{ $locale }}"
                                class="editor-toolbar-button text-xs font-semibold"
                                title="Heading 3">
                                H3
                            </button>

                            <span class="mx-1 h-5 border-l border-gray-300"></span>

                            <button
                                type="button"
                                data-editor-command="insertUnorderedList"
                                data-editor-locale="{{ $locale }}"
                                class="editor-toolbar-button"
                                title="Bullet list">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                                </svg>
                            </button>

                            <button
                                type="button"
                                data-editor-command="insertOrderedList"
                                data-editor-locale="{{ $locale }}"
                                class="editor-toolbar-button"
                                title="Numbered list">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M10 6h11M10 12h11M10 18h11"/>
                                    <path d="M4 5h1v4M4 9h2M4 13h2l-2 3h2"/>
                                </svg>
                            </button>

                            <button
                                type="button"
                                data-editor-command="formatBlock"
                                data-editor-value="BLOCKQUOTE"
                                data-editor-locale="{{ $locale }}"
                                class="editor-toolbar-button"
                                title="Quote">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M7 7h5v6H8l-1 4H4l1-5V7zm9 0h5v6h-4l-1 4h-3l1-5V7z"/>
                                </svg>
                            </button>

                            <span class="mx-1 h-5 border-l border-gray-300"></span>

                            <button
                                type="button"
                                data-editor-command="createLink"
                                data-editor-locale="{{ $locale }}"
                                class="editor-toolbar-button"
                                title="Insert link">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M10 13a5 5 0 0 0 7.07 0l3-3A5 5 0 0 0 13 2.93l-1.72 1.71"/>
                                    <path d="M14 11a5 5 0 0 0-7.07 0l-3 3A5 5 0 0 0 11 21.07l1.71-1.71"/>
                                </svg>
                            </button>

                            <button
                                type="button"
                                data-editor-command="removeFormat"
                                data-editor-locale="{{ $locale }}"
                                class="editor-toolbar-button"
                                title="Remove formatting">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m3 6 18 0M8 6l1 14h6l1-14M10 6V4h4v2"/>
                                    <path d="m4 4 16 16"/>
                                </svg>
                            </button>

                        </div>


                        {{-- EDITOR --}}

                        <div
                            id="post-editor-{{ $locale }}"
                            contenteditable="true"
                            data-editor-locale="{{ $locale }}"
                            data-post-editor-area
                            class="post-rich-editor min-h-[320px] px-5 py-4 text-sm leading-7 text-gray-700 focus:outline-none"
                            dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}"
                            spellcheck="true"
                            data-placeholder="Write your publication here...">{!! $content !!}</div>


                        {{-- HIDDEN INPUT --}}

                        <textarea
                            name="translations[{{ $locale }}][content]"
                            id="post-content-{{ $locale }}"
                            class="hidden"
                            data-translation-field
                            data-required-field
                            maxlength="200000">{{ $content }}</textarea>


                        {{-- COUNTER --}}

                        <div class="flex items-center justify-between border-t border-gray-100 px-4 py-3">

                            <span class="text-xs text-gray-400">
                                Use headings, lists, links and quotations to structure your publication.
                            </span>

                            <div
                                id="post-editor-counter-{{ $locale }}"
                                class="shrink-0 text-xs font-medium text-gray-400">
                                <span>0</span>/20,000
                            </div>

                        </div>

                    </div>

                    @error("translations.$locale.content")
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror


                    {{-- SEO --}}

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">

                        <div class="mb-5">
                            <h3 class="text-sm font-semibold text-gray-900">
                                Search Engine Optimization
                            </h3>
                            <p class="mt-1 text-xs text-gray-500">
                                Optional metadata for search engines.
                            </p>
                        </div>

                        <div class="space-y-4">

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    SEO Title
                                </label>

                                <input
                                    type="text"
                                    name="translations[{{ $locale }}][seo_title]"
                                    value="{{ $seoTitle }}"
                                    maxlength="255"
                                    data-translation-field
                                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">

                                @error("translations.$locale.seo_title")
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    SEO Description
                                </label>

                                <textarea
                                    name="translations[{{ $locale }}][seo_description]"
                                    rows="2"
                                    maxlength="500"
                                    data-translation-field
                                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">{{ $seoDescription }}</textarea>

                                @error("translations.$locale.seo_description")
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                </div>

            @endforeach


            @if(count($locales) > 1)
                <div class="mt-5 flex items-center justify-between">
                    <p class="text-xs text-red-500 italic">
                        * At least one complete language version is required.
                    </p>

                    <p class="text-xs text-gray-400">
                        English is recommended as the primary language.
                    </p>
                </div>
            @endif

        </div>
    </section>


    {{-- =========================================================
        MEDIA
    ========================================================== --}}

    <section class="rounded-2xl border border-gray-200 bg-white">

        <div class="border-b border-gray-100 px-6 py-5">
            <h2 class="text-base font-semibold text-gray-900">
                Publication Images
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Upload high-quality images to illustrate your publication.
                JPG, PNG and WebP. Maximum 10 MB per image.
            </p>
        </div>

        <div class="space-y-6 p-6">

            {{-- EXISTING IMAGES --}}

            @if($media->isNotEmpty())
                <div>
                    <h3 class="mb-4 text-sm font-medium text-gray-700">
                        Current Images
                    </h3>

                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">

                        @foreach($media as $item)
                            <div class="relative">

                                <input
                                    type="checkbox"
                                    name="remove_media[]"
                                    value="{{ $item->id }}"
                                    id="remove-media-{{ $item->id }}"
                                    class="peer sr-only">

                                <label
                                    for="remove-media-{{ $item->id }}"
                                    class="group relative block cursor-pointer overflow-hidden rounded-xl border border-gray-200 bg-gray-100 transition peer-checked:border-red-500 peer-checked:ring-2 peer-checked:ring-red-100">

                                    <div class="aspect-[4/3]">
                                        <img
                                            src="{{ $item->cdn_url }}"
                                            alt="Publication image"
                                            class="h-full w-full object-cover transition group-hover:scale-105">
                                    </div>

                                    @if($item->is_main)
                                        <span class="absolute left-2 top-2 rounded-md bg-gray-900 px-2 py-1 text-[10px] font-semibold uppercase tracking-wider text-white">
                                            Main
                                        </span>
                                    @endif

                                </label>

                                {{-- OVERLAY: sibling of label, so peer-checked works --}}

                                <div class="pointer-events-none absolute inset-0 flex items-center justify-center rounded-xl bg-red-900/50 text-sm font-semibold text-white opacity-0 transition peer-checked:opacity-100">
                                    Marked for removal
                                </div>

                                <div class="pointer-events-none absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-white text-gray-500 shadow-sm peer-checked:bg-red-600 peer-checked:text-white">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="m6 6 12 12M18 6 6 18"/>
                                    </svg>
                                </div>

                            </div>
                        @endforeach

                    </div>
                </div>
            @endif


            {{-- NEW IMAGES --}}

            <div>
                <label
                    for="media"
                    class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center transition hover:border-gray-500 hover:bg-gray-100">

                    <svg class="h-9 w-9 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <path d="m21 15-5-5L5 21"/>
                        <path d="M12 12v6M9 15h6"/>
                    </svg>

                    <span class="mt-4 text-sm font-semibold text-gray-900">
                        Click to upload images
                    </span>

                    <span class="mt-1 text-xs text-gray-500">
                        JPEG, PNG or WebP · Maximum 10 images per upload
                    </span>

                    <input
                        type="file"
                        id="media"
                        name="media[]"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        multiple
                        class="hidden">

                </label>

                @error('media')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                @error('media.*')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div
                    id="mediaPreview"
                    class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                </div>
            </div>

        </div>
    </section>


    {{-- =========================================================
        ACTIONS
    ========================================================== --}}

    <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:items-center sm:justify-between">

        <a
            href="{{ route('community.manage.index') }}"
            class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
            Cancel
        </a>

        <div class="flex flex-col gap-3 sm:flex-row">

            @if($isEdit)
                <a
                    href="{{ route('community.posts.preview', $post->id) }}"
                    target="_blank"
                    class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Preview
                </a>
            @endif

            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-7 py-3 text-sm font-semibold text-white transition hover:bg-gray-700">
                {{ $isEdit ? 'Save Changes' : 'Create Publication' }}
            </button>

        </div>

    </div>

</form>


{{-- =========================================================
    FORM STYLES
========================================================= --}}

<style>
.editor-toolbar-button {
    display: inline-flex;
    width: 32px;
    height: 32px;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    color: #4b5563;
    transition: background .15s, color .15s;
}

.editor-toolbar-button:hover {
    background: #e5e7eb;
    color: #111827;
}

.post-rich-editor {
    overflow-wrap: anywhere;
    word-break: break-word;
}

.post-rich-editor:focus {
    outline: none;
}

.post-rich-editor:empty:before {
    content: attr(data-placeholder);
    color: #9ca3af;
    pointer-events: none;
}

.post-rich-editor h2 {
    font-size: 1.5rem;
    line-height: 2rem;
    font-weight: 700;
    margin: 1rem 0 .5rem;
}

.post-rich-editor h3 {
    font-size: 1.2rem;
    line-height: 1.75rem;
    font-weight: 600;
    margin: .75rem 0 .4rem;
}

.post-rich-editor p {
    margin: .5rem 0;
}

.post-rich-editor ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin: .75rem 0;
}

.post-rich-editor ol {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin: .75rem 0;
}

.post-rich-editor li {
    margin: .2rem 0;
}

.post-rich-editor blockquote {
    border-left: 3px solid #9ca3af;
    padding-left: 1rem;
    margin: 1rem 0;
    color: #6b7280;
    font-style: italic;
}

.post-rich-editor a {
    color: #2563eb;
    text-decoration: underline;
}

.post-rich-editor strong,
.post-rich-editor b {
    font-weight: 700;
}

.post-rich-editor em,
.post-rich-editor i {
    font-style: italic;
}

.post-rich-editor u {
    text-decoration: underline;
}

.post-rich-editor[dir="rtl"] {
    text-align: right;
}

.post-rich-editor[dir="rtl"] ul,
.post-rich-editor[dir="rtl"] ol {
    padding-left: 0;
    padding-right: 1.5rem;
}

.post-rich-editor[dir="rtl"] blockquote {
    border-left: 0;
    border-right: 3px solid #9ca3af;
    padding-left: 0;
    padding-right: 1rem;
}
</style>


{{-- =========================================================
    FORM SCRIPTS IN JS folder
========================================================= --}}

