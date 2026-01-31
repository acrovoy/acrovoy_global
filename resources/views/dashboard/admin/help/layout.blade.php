@extends('dashboard.admin.layout')

@section('dashboard-content')
    {{-- HEADER TABS --}}
    <div class="flex gap-2 mb-6 border-b border-gray-300 pb-2 overflow-x-auto">

        <a href="{{ route('admin.help.index') }}"
           class="px-5 py-2 rounded-t-lg text-sm font-medium transition-colors duration-200
           {{ request()->routeIs('admin.help.index') ? 'bg-white text-gray-900 shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Home
        </a>

        <a href="{{ route('admin.help.categories.index') }}"
        class="px-4 py-2 rounded-t-lg
        {{ request()->routeIs('admin.help.categories.index')
                ? 'bg-white font-semibold shadow'
                : 'bg-gray-100' }}">
            Help Categories
        </a>

        <a href="{{ route('admin.help.articles.index') }}"
            class="px-4 py-2 rounded-t-lg {{ request()->routeIs('admin.help.articles.index') ? 'bg-white font-semibold shadow' : 'bg-gray-100' }}">
            Articles
        </a>

       

       

        {{-- Добавляй другие вкладки --}}
    </div>

    {{-- CONTENT AREA --}}
    <div class="bg-white p-6 rounded-b-xl shadow-lg min-h-[400px] border border-gray-200">
        @yield('settings-content')
    </div>
@endsection
