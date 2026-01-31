@extends('layouts.app')

@section('content')
<div class="bg-[#F7F3EA] py-12">
    <div class="max-w-6xl mx-auto px-4">

        {{-- Заголовок --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-3">Help Center</h1>
            <p class="text-gray-700 text-lg">
                Find answers, guides, and support articles to help you get the most out of Acrovoy B2B platform.
            </p>
        </div>

        {{-- Быстрые ссылки --}}
        <div class="flex flex-wrap justify-center gap-4 mb-12">
            <a href="{{ route('faq') }}" 
               class="px-6 py-3 bg-white rounded-lg shadow hover:shadow-md font-semibold transition">
                FAQ
            </a>
            <a href="{{ route('help.category', 'buyers') }}" 
               class="px-6 py-3 bg-white rounded-lg shadow hover:shadow-md font-semibold transition">
                {{ optional($categories->firstWhere('slug', 'buyers'))->translated_name ?? 'Buyers Help' }}
            </a>
            <a href="{{ route('help.category', 'sellers') }}" 
               class="px-6 py-3 bg-white rounded-lg shadow hover:shadow-md font-semibold transition">
                {{ optional($categories->firstWhere('slug', 'sellers'))->translated_name ?? 'Sellers Help' }}
            </a>
            <a href="{{ route('help.category', 'payments') }}" 
               class="px-6 py-3 bg-white rounded-lg shadow hover:shadow-md font-semibold transition">
                {{ optional($categories->firstWhere('slug', 'payments'))->translated_name ?? 'Payments' }}
            </a>
        </div>

        {{-- Сетка категорий --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($categories as $category)
                <a href="{{ route('help.category', $category->slug) }}" 
                   class="block p-6 bg-white rounded-lg shadow hover:shadow-lg transition group">

                    <h2 class="text-2xl font-semibold mb-2 group-hover:text-blue-600 transition">
                        {{ $category->translated_name ?? $category->name }}
                    </h2>
                    <p class="text-gray-600 text-sm">
                        {{ $category->translated_description ?? ($category->description ?? 'Explore helpful articles about '.strtolower($category->name).'.') }}
                    </p>
                </a>
            @endforeach
        </div>

        {{-- Дополнительные ресурсы --}}
        <div class="mt-12 text-center text-gray-600">
            <p>Can't find what you're looking for? Contact our <a href="" class="text-blue-600 underline">support team</a>.</p>
        </div>

    </div>
</div>
@endsection
