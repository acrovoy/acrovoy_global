@extends('dashboard.admin.layout')

@section('dashboard-content')

    {{-- SETTINGS NAVIGATION --}}
    <div class="mb-6">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">

            {{-- HOME --}}
            <a
                href="{{ route('admin.settings.index') }}"
                class="group flex items-center gap-3 px-4 py-3 rounded-xl border transition
                {{ request()->routeIs('admin.settings.index')
                    ? 'bg-gray-900 border-gray-900 text-white shadow-sm'
                    : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}"
            >
                <span class="flex items-center justify-center w-8 h-8 rounded-lg
                    {{ request()->routeIs('admin.settings.index') ? 'bg-white/10' : 'bg-gray-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.settings.index') ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l9-9 9 9M5 10v10h14V10M9 20v-6h6v6"/>
                    </svg>
                </span>
                <span class="text-sm font-medium">Home</span>
            </a>

            {{-- CATEGORIES --}}
            <a
                href="{{ route('admin.settings.categories.index') }}"
                class="group flex items-center gap-3 px-4 py-3 rounded-xl border transition
                {{ request()->routeIs('admin.settings.categories.*')
                    ? 'bg-gray-900 border-gray-900 text-white shadow-sm'
                    : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}"
            >
                <span class="flex items-center justify-center w-8 h-8 rounded-lg
                    {{ request()->routeIs('admin.settings.categories.*') ? 'bg-white/10' : 'bg-gray-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.settings.categories.*') ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5h6v6H4zM14 5h6v6h-6zM4 15h6v6H4zM14 15h6v6h-6z"/>
                    </svg>
                </span>
                <span class="text-sm font-medium">Categories</span>
            </a>

            {{-- MATERIALS --}}
            <a
                href="{{ route('admin.settings.materials.index') }}"
                class="group flex items-center gap-3 px-4 py-3 rounded-xl border transition
                {{ request()->routeIs('admin.settings.materials*')
                    ? 'bg-gray-900 border-gray-900 text-white shadow-sm'
                    : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}"
            >
                <span class="flex items-center justify-center w-8 h-8 rounded-lg
                    {{ request()->routeIs('admin.settings.materials*') ? 'bg-white/10' : 'bg-gray-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.settings.materials*') ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16H6zM9 8h6M9 12h6M9 16h4"/>
                    </svg>
                </span>
                <span class="text-sm font-medium">Materials</span>
            </a>

            {{-- LANGUAGES --}}
            <a
                href="{{ route('admin.settings.languages.index') }}"
                class="group flex items-center gap-3 px-4 py-3 rounded-xl border transition
                {{ request()->routeIs('admin.settings.languages.*')
                    ? 'bg-gray-900 border-gray-900 text-white shadow-sm'
                    : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}"
            >
                <span class="flex items-center justify-center w-8 h-8 rounded-lg
                    {{ request()->routeIs('admin.settings.languages.*') ? 'bg-white/10' : 'bg-gray-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.settings.languages.*') ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5h16M8 5v3a6 6 0 0012 0M4 19l4-6 4 6M6 17h5"/>
                    </svg>
                </span>
                <span class="text-sm font-medium">Languages</span>
            </a>

            {{-- COUNTRIES --}}
            <a
                href="{{ route('admin.settings.countries.index') }}"
                class="group flex items-center gap-3 px-4 py-3 rounded-xl border transition
                {{ request()->routeIs('admin.settings.countries*')
                    ? 'bg-gray-900 border-gray-900 text-white shadow-sm'
                    : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}"
            >
                <span class="flex items-center justify-center w-8 h-8 rounded-lg
                    {{ request()->routeIs('admin.settings.countries*') ? 'bg-white/10' : 'bg-gray-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.settings.countries*') ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-6.2 7-11a7 7 0 10-14 0c0 4.8 7 11 7 11zM12 13a3 3 0 100-6 3 3 0 000 6z"/>
                    </svg>
                </span>
                <span class="text-sm font-medium">Countries</span>
            </a>

            {{-- LOCATIONS --}}
            <a
                href="{{ route('admin.settings.locations.index') }}"
                class="group flex items-center gap-3 px-4 py-3 rounded-xl border transition
                {{ request()->routeIs('admin.settings.locations.*')
                    ? 'bg-gray-900 border-gray-900 text-white shadow-sm'
                    : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}"
            >
                <span class="flex items-center justify-center w-8 h-8 rounded-lg
                    {{ request()->routeIs('admin.settings.locations.*') ? 'bg-white/10' : 'bg-gray-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.settings.locations.*') ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-4.5 7-10a7 7 0 10-14 0c0 5.5 7 10 7 10zM12 11a2 2 0 100-4 2 2 0 000 4z"/>
                    </svg>
                </span>
                <span class="text-sm font-medium">Locations</span>
            </a>

            {{-- ATTRIBUTES --}}
            <a
                href="{{ route('admin.settings.attributes.index') }}"
                class="group flex items-center gap-3 px-4 py-3 rounded-xl border transition
                {{ request()->routeIs('admin.settings.attributes.*')
                    ? 'bg-gray-900 border-gray-900 text-white shadow-sm'
                    : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}"
            >
                <span class="flex items-center justify-center w-8 h-8 rounded-lg
                    {{ request()->routeIs('admin.settings.attributes.*') ? 'bg-white/10' : 'bg-gray-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.settings.attributes.*') ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h10"/>
                        <circle cx="8" cy="6" r="1.5"/>
                        <circle cx="14" cy="12" r="1.5"/>
                        <circle cx="6" cy="18" r="1.5"/>
                    </svg>
                </span>
                <span class="text-sm font-medium">Attributes</span>
            </a>

            {{-- BUSINESS TYPES --}}
            <a
                href="{{ route('admin.settings.business-types.index') }}"
                class="group flex items-center gap-3 px-4 py-3 rounded-xl border transition
                {{ request()->routeIs('admin.settings.business-types.*')
                    ? 'bg-gray-900 border-gray-900 text-white shadow-sm'
                    : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}"
            >
                <span class="flex items-center justify-center w-8 h-8 rounded-lg
                    {{ request()->routeIs('admin.settings.business-types.*') ? 'bg-white/10' : 'bg-gray-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.settings.business-types.*') ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 20V9l8-5 8 5v11M8 20v-6h8v6M12 8v2"/>
                    </svg>
                </span>
                <span class="text-sm font-medium">Business Types</span>
            </a>

            {{-- MANUFACTURING --}}
            <a
                href="{{ route('admin.settings.manufacturing-capabilities.index') }}"
                class="group flex items-center gap-3 px-4 py-3 rounded-xl border transition
                {{ request()->routeIs('admin.settings.manufacturing-capabilities.*')
                    ? 'bg-gray-900 border-gray-900 text-white shadow-sm'
                    : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}"
            >
                <span class="flex items-center justify-center w-8 h-8 rounded-lg
                    {{ request()->routeIs('admin.settings.manufacturing-capabilities.*') ? 'bg-white/10' : 'bg-gray-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.settings.manufacturing-capabilities.*') ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 20V10l6-4v5l6-4v5l4-3v11H4zM8 16h2M13 16h2M18 16h2"/>
                    </svg>
                </span>
                <span class="text-sm font-medium">Manufacturing</span>
            </a>

            {{-- CONSTANTS --}}
            <a
                href="{{ route('admin.settings.constants') }}"
                class="group flex items-center gap-3 px-4 py-3 rounded-xl border transition
                {{ request()->routeIs('admin.settings.constants*')
                    ? 'bg-gray-900 border-gray-900 text-white shadow-sm'
                    : 'bg-white border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}"
            >
                <span class="flex items-center justify-center w-8 h-8 rounded-lg
                    {{ request()->routeIs('admin.settings.constants*') ? 'bg-white/10' : 'bg-gray-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.settings.constants*') ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16H6zM9 8h6M9 12h4M9 16h6"/>
                    </svg>
                </span>
                <span class="text-sm font-medium">Constants</span>
            </a>

        </div>
    </div>

    {{-- CONTENT AREA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 min-h-[400px] p-6">
        @yield('settings-content')
    </div>

@endsection