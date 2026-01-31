@extends('dashboard.admin.help.layout')

@section('settings-content')

{{-- Ошибки валидации --}}
@if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <strong>Please fix the following errors:</strong>
        <ul class="mt-2 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<h1 class="text-2xl font-bold mb-4">Edit Article</h1>

<form action="{{ route('admin.help.articles.update', $article->id) }}" method="POST">
    @csrf
    @method('PUT')


  

   <div class="mb-4">
    <label class="block mb-1 font-semibold">Category</label>
    <select name="category" class="w-full border p-2 rounded">
        <option value="">-- Select Category --</option>
        @foreach($categories as $cat)
            @php
                $name = $cat->translations->first()->name ?? 'No Name';
            @endphp
            <option value="{{ $cat->slug }}"
                {{ old('category', $article->category ?? '') == $cat->slug ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    @error('category') <p class="text-red-500">{{ $message }}</p> @enderror
</div>


    {{-- Slug --}}
    <div class="mb-4">
        <label class="block mb-1 font-semibold">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $article->slug) }}" class="w-full border p-2 rounded">
        @error('slug') <p class="text-red-500">{{ $message }}</p> @enderror
    </div>

    @foreach($locales as $locale)
        @php
            $translation = $article->translations->firstWhere('locale', $locale);
        @endphp
        <div class="mb-6 border-b pb-4">
            <h3 class="font-semibold mb-2">{{ strtoupper($locale) }} Translation</h3>

            <label class="block mb-1">Title</label>
            <input type="text" name="translations[{{ $locale }}][title]"
                   value="{{ old('translations.'.$locale.'.title', $translation->title ?? '') }}"
                   class="w-full border p-2 rounded">
            @error("translations.$locale.title") <p class="text-red-500">{{ $message }}</p> @enderror

            <label class="block mt-2 mb-1">Content</label>
            <textarea name="translations[{{ $locale }}][content]"
                      class="w-full border p-2 rounded ckeditor">{{ old('translations.'.$locale.'.content', $translation->content ?? '') }}</textarea>
            @error("translations.$locale.content") <p class="text-red-500">{{ $message }}</p> @enderror
        </div>
    @endforeach

    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
        Update
    </button>
</form>

{{-- CKEditor 5 CDN --}}
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('textarea.ckeditor').forEach(el => {
        ClassicEditor.create(el).catch(error => console.error(error));
    });
});
</script>
@endsection
