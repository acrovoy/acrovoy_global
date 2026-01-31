{{-- resources/views/help/category.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="bg-[#F7F3EA] py-12 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 flex flex-col lg:flex-row gap-6">

        {{-- LEFT: Список статей --}}
        <aside class="w-full lg:w-1/4 bg-white rounded-lg shadow p-6 sticky top-6 h-fit">
            <h2 class="text-xl font-semibold mb-4 border-b pb-2">
                {{ $category->translations->firstWhere('locale', app()->getLocale())->name ?? $category->name }}
            </h2>
            <p class="text-gray-600 mb-6">
                {{ $category->translations->firstWhere('locale', app()->getLocale())->description ?? $category->description }}
            </p>

            <ul class="space-y-2">
    @foreach($articles as $article)
        <li>
            <a href="{{ route('help.category', $category->slug) }}?article={{ $article->slug }}"
               class="block px-3 py-2 rounded hover:bg-blue-50 transition
               {{ isset($selectedArticle) && $selectedArticle->slug === $article->slug ? 'bg-blue-100 font-semibold' : '' }}">
                {{ $article->translated_title }}
            </a>
        </li>
    @endforeach
</ul>
        </aside>

        {{-- RIGHT: Контент выбранной статьи --}}
        <main class="w-full lg:w-3/4 bg-white rounded-lg shadow p-8 prose max-w-none">
            @if($selectedArticle)
                <h1 class="text-3xl font-bold mb-4">{{ $selectedArticle->title }}</h1>
                <div class="text-gray-800 leading-relaxed">
                    {!! $selectedArticle->content !!}
                </div>
            @else
                <p class="text-gray-600">Выберите статью слева, чтобы увидеть её содержание.</p>
            @endif
        </main>

    </div>
</div>
@endsection
