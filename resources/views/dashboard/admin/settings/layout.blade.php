@extends('dashboard.admin.layout')

@section('dashboard-content')
    {{-- HEADER TABS --}}
    <div class="flex gap-2 mb-6 border-b border-gray-300 pb-2 overflow-x-auto">
        <a href="{{ route('admin.settings.index') }}"
           class="px-5 py-2 rounded-t-lg text-sm font-medium transition-colors duration-200
           {{ request()->routeIs('admin.settings.index') ? 'bg-white text-gray-900 shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Home
        </a>

        <a href="{{ route('admin.settings.categories.index') }}"
        class="px-4 py-2 rounded-t-lg
        {{ request()->routeIs('admin.settings.categories.*')
                ? 'bg-white font-semibold shadow'
                : 'bg-gray-100' }}">
            Categories
        </a>

        <a href="{{ route('admin.settings.materials.index') }}"
            class="px-4 py-2 rounded-t-lg {{ request()->routeIs('admin.settings.materials*') ? 'bg-white font-semibold shadow' : 'bg-gray-100' }}">
            Materials
        </a>

        <a href="{{ route('admin.settings.languages.index') }}"
        class="px-4 py-2 rounded-t-lg transition
        {{ request()->routeIs('admin.settings.languages.*')
                ? 'bg-white font-semibold shadow'
                : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Languages
        </a>

        <a href="{{ route('admin.settings.countries.index') }}"
        class="px-4 py-2 rounded-t-lg {{ request()->routeIs('admin.settings.countries*') ? 'bg-white font-semibold shadow' : 'bg-gray-100' }}">
            Countries
        </a>

        <a href="{{ route('admin.settings.locations.index') }}"
        class="px-4 py-2 rounded-t-lg transition
        {{ request()->routeIs('admin.settings.locations.*') ? 'bg-white font-semibold shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Locations
        </a>

        <a href="{{ route('admin.settings.supplier-types.index') }}"
        class="px-4 py-2 rounded-t-lg transition
        {{ request()->routeIs('admin.settings.supplier-types.*')
                ? 'bg-white font-semibold shadow'
                : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Supplier Types
        </a>

        <a href="{{ route('admin.settings.constants') }}"
           class="px-5 py-2 rounded-t-lg text-sm font-medium transition-colors duration-200
           {{ request()->routeIs('admin.settings.constants*') ? 'bg-white text-gray-900 shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Constants
        </a>

       

        {{-- Добавляй другие вкладки --}}
    </div>

    {{-- CONTENT AREA --}}
    <div class="bg-white p-6 rounded-b-xl shadow-lg min-h-[400px] border border-gray-200">
        @yield('settings-content')
    </div>
@endsection
