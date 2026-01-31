@extends('dashboard.admin.help.layout')

@section('settings-content')
    {{-- Заголовок главной страницы Help админки --}}
    <div class="flex flex-col gap-4">
        <h1 class="text-2xl font-bold mb-2">Help Admin Dashboard</h1>
        <p class="text-gray-700">
            Welcome to the Help admin panel. From here you can manage help categories and articles.
        </p>

        {{-- Быстрые ссылки на разделы --}}
        <div class="flex gap-4 mt-6 flex-wrap">
            <a href="{{ route('admin.help.categories.index') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Manage Categories
            </a>
            <a href="{{ route('admin.help.articles.index') }}"
               class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                Manage Articles
            </a>
        </div>
    </div>
@endsection
