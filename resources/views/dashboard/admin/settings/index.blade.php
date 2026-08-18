@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="space-y-8">

    {{-- HEADER --}}
    <div class="flex items-start justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">
                <span>Administration</span>
                <span class="text-gray-300">/</span>
                <span>Settings</span>
            </div>

            <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
                Settings
            </h1>

            <p class="text-sm text-gray-500 mt-1 max-w-2xl">
                Configure the core structure, classifications, attributes and system data used across Acrovoy.
            </p>
        </div>
    </div>


    {{-- MAIN SETTINGS --}}
    <div>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-1.5 h-6 bg-gray-900 rounded-full"></div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900">
                    Core Configuration
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Main structures that define how the marketplace works.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

            {{-- CATEGORIES --}}
            <a href="{{ route('admin.settings.categories.index') }}"
               class="group relative bg-white border border-gray-200 rounded-2xl p-5 hover:border-gray-300 hover:shadow-md transition-all duration-200">

                <div class="flex items-start justify-between">
                    <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-gray-900 text-white shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5h6v6H4zM14 5h6v6h-6zM4 15h6v6H4zM14 15h6v6h-6z"/>
                        </svg>
                    </div>

                    <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-600 group-hover:translate-x-0.5 transition"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>

                <h3 class="mt-5 text-sm font-semibold text-gray-900">
                    Categories
                </h3>

                <p class="text-xs text-gray-500 mt-1.5 leading-5">
                    Manage product categories, hierarchy, contexts and commissions.
                </p>
            </a>


            {{-- ATTRIBUTES --}}
            <a href="{{ route('admin.settings.attributes.index') }}"
               class="group relative bg-white border border-gray-200 rounded-2xl p-5 hover:border-gray-300 hover:shadow-md transition-all duration-200">

                <div class="flex items-start justify-between">
                    <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-gray-100 text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h10"/>
                            <circle cx="8" cy="6" r="1.5"/>
                            <circle cx="14" cy="12" r="1.5"/>
                            <circle cx="6" cy="18" r="1.5"/>
                        </svg>
                    </div>

                    <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-600 group-hover:translate-x-0.5 transition"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>

                <h3 class="mt-5 text-sm font-semibold text-gray-900">
                    Attributes
                </h3>

                <p class="text-xs text-gray-500 mt-1.5 leading-5">
                    Define product attributes, types, options, groups and custom fields.
                </p>
            </a>


            {{-- MATERIALS --}}
            <a href="{{ route('admin.settings.materials.index') }}"
               class="group relative bg-white border border-gray-200 rounded-2xl p-5 hover:border-gray-300 hover:shadow-md transition-all duration-200">

                <div class="flex items-start justify-between">
                    <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-gray-100 text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16H6zM9 8h6M9 12h6M9 16h4"/>
                        </svg>
                    </div>

                    <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-600 group-hover:translate-x-0.5 transition"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>

                <h3 class="mt-5 text-sm font-semibold text-gray-900">
                    Materials
                </h3>

                <p class="text-xs text-gray-500 mt-1.5 leading-5">
                    Maintain the material library available for products and suppliers.
                </p>
            </a>

        </div>
    </div>


    {{-- MARKETPLACE DATA --}}
    <div>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-1.5 h-6 bg-gray-300 rounded-full"></div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900">
                    Marketplace Data
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Reference data used throughout the platform.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">

            {{-- COUNTRIES --}}
            <a href="{{ route('admin.settings.countries.index') }}"
               class="group bg-white border border-gray-200 rounded-xl p-4 hover:border-gray-300 hover:shadow-sm transition">
                <div class="flex items-center justify-between">
                    <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-6.2 7-11a7 7 0 10-14 0c0 4.8 7 11 7 11zM12 13a3 3 0 100-6 3 3 0 000 6z"/>
                        </svg>
                    </span>
                    <span class="text-gray-300 group-hover:text-gray-500">→</span>
                </div>
                <div class="mt-3 text-sm font-medium text-gray-800">Countries</div>
                <div class="text-xs text-gray-400 mt-0.5">Country directory</div>
            </a>


            {{-- LOCATIONS --}}
            <a href="{{ route('admin.settings.locations.index') }}"
               class="group bg-white border border-gray-200 rounded-xl p-4 hover:border-gray-300 hover:shadow-sm transition">
                <div class="flex items-center justify-between">
                    <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-4.5 7-10a7 7 0 10-14 0c0 5.5 7 10 7 10zM12 11a2 2 0 100-4 2 2 0 000 4z"/>
                        </svg>
                    </span>
                    <span class="text-gray-300 group-hover:text-gray-500">→</span>
                </div>
                <div class="mt-3 text-sm font-medium text-gray-800">Locations</div>
                <div class="text-xs text-gray-400 mt-0.5">Geographic data</div>
            </a>


            {{-- LANGUAGES --}}
            <a href="{{ route('admin.settings.languages.index') }}"
               class="group bg-white border border-gray-200 rounded-xl p-4 hover:border-gray-300 hover:shadow-sm transition">
                <div class="flex items-center justify-between">
                    <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5h16M8 5v3a6 6 0 0012 0M4 19l4-6 4 6M6 17h5"/>
                        </svg>
                    </span>
                    <span class="text-gray-300 group-hover:text-gray-500">→</span>
                </div>
                <div class="mt-3 text-sm font-medium text-gray-800">Languages</div>
                <div class="text-xs text-gray-400 mt-0.5">Translations</div>
            </a>


            {{-- BUSINESS TYPES --}}
            <a href="{{ route('admin.settings.business-types.index') }}"
               class="group bg-white border border-gray-200 rounded-xl p-4 hover:border-gray-300 hover:shadow-sm transition">
                <div class="flex items-center justify-between">
                    <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 20V9l8-5 8 5v11M8 20v-6h8v6M12 8v2"/>
                        </svg>
                    </span>
                    <span class="text-gray-300 group-hover:text-gray-500">→</span>
                </div>
                <div class="mt-3 text-sm font-medium text-gray-800">Business Types</div>
                <div class="text-xs text-gray-400 mt-0.5">Business classification</div>
            </a>


            {{-- MANUFACTURING --}}
            <a href="{{ route('admin.settings.manufacturing-capabilities.index') }}"
               class="group bg-white border border-gray-200 rounded-xl p-4 hover:border-gray-300 hover:shadow-sm transition">
                <div class="flex items-center justify-between">
                    <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 20V10l6-4v5l6-4v5l4-3v11H4zM8 16h2M13 16h2M18 16h2"/>
                        </svg>
                    </span>
                    <span class="text-gray-300 group-hover:text-gray-500">→</span>
                </div>
                <div class="mt-3 text-sm font-medium text-gray-800">Manufacturing</div>
                <div class="text-xs text-gray-400 mt-0.5">Production capabilities</div>
            </a>

        </div>
    </div>


    {{-- SYSTEM --}}
    <div>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-1.5 h-6 bg-gray-300 rounded-full"></div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900">
                    System
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Global platform-level configuration.
                </p>
            </div>
        </div>

        <a href="{{ route('admin.settings.constants') }}"
           class="group flex items-center justify-between bg-gray-50 border border-gray-200 rounded-xl px-5 py-4 hover:bg-white hover:border-gray-300 hover:shadow-sm transition">

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-200 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16H6zM9 8h6M9 12h4M9 16h6"/>
                    </svg>
                </div>

                <div>
                    <div class="text-sm font-semibold text-gray-800">
                        Constants
                    </div>
                    <div class="text-xs text-gray-500 mt-0.5">
                        Manage global values, limits and platform constants.
                    </div>
                </div>
            </div>

            <svg class="w-5 h-5 text-gray-300 group-hover:text-gray-600 group-hover:translate-x-1 transition"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>

        </a>
    </div>

</div>

@endsection