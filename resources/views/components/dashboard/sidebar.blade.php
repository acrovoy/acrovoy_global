<aside class="w-full lg:w-1/4 bg-white border border-gray-200 rounded-2xl shadow-sm p-5 self-start">

    
@php
    $role = $role ?? \App\Facades\ActiveContext::role();

    $menuIcons = [
        'dashboard.home' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',

        'logistics.home' => '<path d="M3 7h18M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><path d="m8 13 2 2 5-5"/>',

        'buyer.orders.index' => '<path d="M8 4H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-3"/><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M8 12h8M8 16h5"/>',

        'supplier.orders' => '<path d="M8 4H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-3"/><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M8 12h8M8 16h5"/>',

        'buyer.rfqs.index' => '<path d="M12 3a9 9 0 1 0 9 9"/><path d="M12 7v5l3 2"/><path d="M16 3h5v5"/>',

        'supplier.rfqs.index' => '<path d="M12 3a9 9 0 1 0 9 9"/><path d="M12 7v5l3 2"/><path d="M16 3h5v5"/>',

        'buyer.projects.index' => '<path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/><path d="M3 11h18"/>',

        'supplier.projects.index' => '<path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/><path d="M3 11h18"/>',

        'buyer.cart.index' => '<circle cx="9" cy="20" r="1"/><circle cx="19" cy="20" r="1"/><path d="M2 3h2l3 12a2 2 0 0 0 2 1h9a2 2 0 0 0 2-1l2-8H5"/>',

        'buyer.wishlist.index' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/>',

        'buyer.saved-suppliers.index' => '<path d="M6 3h12a2 2 0 0 1 2 2v16l-8-4-8 4V5a2 2 0 0 1 2-2Z"/><path d="M9 8h6M9 11h6"/>',

        'buyer.addresses.index' => '<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',

        'buyer.messenger.index' => '<path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8Z"/>',

        'supplier.messenger.index' => '<path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8Z"/>',

        'buyer.profile.show' => '<circle cx="12" cy="8" r="4"/><path d="M5 21v-2a7 7 0 0 1 14 0v2"/>',

        'supplier.profile.show' => '<circle cx="12" cy="8" r="4"/><path d="M5 21v-2a7 7 0 0 1 14 0v2"/>',

        'supplier.products.create' => '<path d="M12 5v14M5 12h14"/>',

        'supplier.products.index' => '<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5"/>',

        'supplier.materials.index' => '<circle cx="12" cy="12" r="9"/><path d="M12 3v18M3 12h18M5.6 5.6l12.8 12.8M18.4 5.6 5.6 18.4"/>',

        'supplier.shipping-templates.index' => '<path d="M3 6h11v12H3z"/><path d="M14 10h4l3 3v5h-7z"/><circle cx="7.5" cy="18" r="2"/><circle cx="17.5" cy="18" r="2"/>',

        'supplier.return-policies.index' => '<path d="M3 11a9 9 0 1 0 3-6.7L3 7"/><path d="M3 3v4h4"/>',

        'supplier.team.members' => '<circle cx="9" cy="8" r="3"/><path d="M3 20v-2a6 6 0 0 1 12 0v2"/><path d="M16 5a3 3 0 0 1 0 6M18 14a5 5 0 0 1 3 4v2"/>',

        'supplier.team.invite' => '<circle cx="9" cy="8" r="3"/><path d="M3 20v-2a6 6 0 0 1 12 0v2M19 8v6M16 11h6"/>',

        'supplier.team.roles' => '<path d="M12 3 4 6v5c0 5 3.4 8.5 8 10 4.6-1.5 8-5 8-10V6l-8-3Z"/><path d="m9 12 2 2 4-4"/>',

        'supplier.premium-plans' => '<path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z"/>',

        'logistics.templates.index' => '<path d="M3 6h11v12H3z"/><path d="M14 10h4l3 3v5h-7z"/><circle cx="7.5" cy="18" r="2"/><circle cx="17.5" cy="18" r="2"/>',

        'logistics.offers.index' => '<path d="M12 3v18M17 7H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/>',

        'logistics.company.show' => '<path d="M4 21V5l8-3 8 3v16M2 21h20"/><path d="M9 8h1M14 8h1M9 12h1M14 12h1M10 21v-4h4v4"/>',

        'logistics.settings' => '<circle cx="12" cy="12" r="3"/><path d="m19.4 15 .1.1 1.4 1.1-1.4 2.4-1.7-.7a8 8 0 0 1-1.8 1l-.3 1.8h-2.8l-.3-1.8a8 8 0 0 1-1.8-1l-1.7.7-1.4-2.4L7.1 15a8 8 0 0 1 0-2l-1.4-1.1 1.4-2.4 1.7.7a8 8 0 0 1 1.8-1l.3-1.8h2.8l.3 1.8a8 8 0 0 1 1.8 1l1.7-.7 1.4 2.4-1.4 1.1a8 8 0 0 1-.1 2Z"/>',

        'dashboard.companies.index' => '<path d="M4 21V5l8-3 8 3v16M2 21h20"/><path d="M9 8h1M14 8h1M9 12h1M14 12h1M10 21v-4h4v4"/>',

        'dashboard.companies.create' => '<path d="M12 5v14M5 12h14"/>',

        'default' => '<circle cx="12" cy="12" r="9"/>',
    ];
@endphp



    {{-- =========================================================
        COMPANY SWITCHER
    ========================================================== --}}

    <div class="mb-6 relative">

        {{-- BUTTON --}}
        <button
            type="button"
            onclick="document.getElementById('companyDropdown').classList.toggle('hidden')"
            class="group w-full flex items-center justify-between
                   bg-gray-50 border border-gray-200 rounded-xl
                   px-4 py-3
                   transition-all duration-200
                   hover:bg-white hover:border-gray-300 hover:shadow-sm">

            <div class="min-w-0 text-left">

                <div class="text-sm font-semibold text-gray-900 truncate">
                    @if($isPersonal)

                        @if(($role ?? 'buyer') === 'supplier')
                            Supplier Individual
                        @else
                            Buyer Individual
                        @endif

                    @else
                        {{ $active?->company?->name ?? 'Select company' }}
                    @endif
                </div>

                <div class="mt-0.5 text-xs text-gray-500">
                    @if($isPersonal)
                        Personal {{ ucfirst($role ?? 'buyer') }} account
                    @else
                        {{ ucfirst($active?->role ?? '') }}
                    @endif
                </div>

            </div>

            {{-- ICON --}}
            <svg
                class="w-4 h-4 flex-shrink-0 ml-3 text-gray-400
                       transition-transform duration-200
                       group-hover:text-gray-600"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path
                    fill-rule="evenodd"
                    d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                    clip-rule="evenodd"
                />
            </svg>

        </button>


        {{-- DROPDOWN --}}
        <div
            id="companyDropdown"
            class="hidden absolute z-50 mt-2 w-full
                   bg-white border border-gray-200
                   rounded-xl shadow-lg overflow-hidden"
        >

            {{-- PERSONAL BUYER --}}
            <form method="POST" action="{{ route('company.switch') }}">
                @csrf

                <input
                    type="hidden"
                    name="company_key"
                    value="personal_buyer|0"
                >

                <button
                    type="submit"
                    class="w-full text-left px-4 py-3
                           hover:bg-gray-50
                           transition-colors duration-150
                           flex items-center justify-between"
                >

                    <div class="min-w-0">

                        <div class="text-sm font-medium text-gray-900">
                            Buyer Individual
                        </div>

                        <div class="mt-0.5 text-xs text-gray-500">
                            Personal buyer account
                        </div>

                    </div>

                    @if($isPersonal && ($role ?? 'buyer') === 'buyer')
                        <span class="ml-3 text-[11px] font-semibold text-emerald-600">
                            Active
                        </span>
                    @endif

                </button>
            </form>


            {{-- PERSONAL SUPPLIER --}}
            <form method="POST" action="{{ route('company.switch') }}">
                @csrf

                <input
                    type="hidden"
                    name="company_key"
                    value="personal_supplier|0"
                >

                <button
                    type="submit"
                    class="w-full text-left px-4 py-3
                           hover:bg-gray-50
                           transition-colors duration-150
                           flex items-center justify-between"
                >

                    <div class="min-w-0">

                        <div class="text-sm font-medium text-gray-900">
                            Supplier Individual
                        </div>

                        <div class="mt-0.5 text-xs text-gray-500">
                            Personal supplier account
                        </div>

                    </div>

                    @if($isPersonal && ($role ?? 'buyer') === 'supplier')
                        <span class="ml-3 text-[11px] font-semibold text-emerald-600">
                            Active
                        </span>
                    @endif

                </button>
            </form>


            <div class="border-t border-gray-100"></div>


            {{-- COMPANY LIST --}}
            @foreach($companies as $company)

                <form method="POST" action="{{ route('company.switch') }}">
                    @csrf

                    <input
                        type="hidden"
                        name="company_key"
                        value="{{ $company->company_type.'|'.$company->company_id }}"
                    >

                    <button
                        type="submit"
                        class="w-full text-left px-4 py-3
                               hover:bg-gray-50
                               transition-colors duration-150
                               flex items-center justify-between"
                    >

                        <div class="min-w-0">

                            <div class="text-sm font-medium text-gray-900 truncate">
                                {{ $company->company?->name }}
                            </div>

                            <div class="mt-0.5 text-xs text-gray-500">
                                {{ ucfirst($company->role) }} •

                                @if(str_contains($company->company_type, 'Supplier'))
                                    Supplier Company
                                @elseif(str_contains($company->company_type, 'Buyer'))
                                    Buyer Company
                                @elseif(str_contains($company->company_type, 'Logistic'))
                                    Logistics Company
                                @else
                                    Company
                                @endif
                            </div>

                        </div>

                        @if(
                            !$isPersonal
                            && $company->company_id === $active?->company_id
                            && $company->company_type === $active?->company_type
                        )
                            <span class="ml-3 text-[11px] font-semibold text-emerald-600">
                                Active
                            </span>
                        @endif

                    </button>

                </form>

            @endforeach

        </div>

    </div>


    {{-- =========================================================
        MENU
    ========================================================== --}}

    <ul class="space-y-1">

        @foreach($menu as $item)

            @if(($item['type'] ?? null) === 'header')

                {{-- MENU HEADER --}}
                <li class="pt-4 pb-2">

                    <div class="px-3">

                        <div class="flex items-center gap-2">

                            <span class="text-[10px] font-semibold
                                         tracking-[0.14em]
                                         text-gray-400 uppercase">
                                {{ $item['label'] }}
                            </span>

                            <span class="flex-1 h-px bg-gray-100"></span>

                        </div>

                    </div>

                </li>

            @else

                
{{-- MENU ITEM --}}
<li>
    @php
        $routeName = $item['route'] ?? '';
        $isActive = $routeName !== '' && request()->routeIs($routeName);
        $iconPath = $menuIcons[$routeName] ?? $menuIcons['default'];
    @endphp

    <a
        href="{{ $routeName !== '' ? route($routeName) : '#' }}"
        class="group flex items-center justify-between
               px-3.5 py-2.5 rounded-xl
               text-sm transition-all duration-200
               {{ $isActive
                    ? 'bg-gray-100 text-gray-900 font-semibold shadow-sm'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
               }}"
    >
        <span class="flex items-center min-w-0">

            {{-- SVG ICON --}}
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.7"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="w-5 h-5 mr-3 flex-shrink-0
                       transition-colors duration-200
                       {{ $isActive
                            ? 'text-gray-900'
                            : 'text-gray-400 group-hover:text-gray-700'
                       }}"
                aria-hidden="true"
            >
                {!! $iconPath !!}
            </svg>

            {{-- LABEL --}}
            <span class="truncate">
                {{ $item['label'] }}
            </span>

        </span>

        {{-- BADGE --}}
        @if(!empty($item['badge']))
            <span
                class="ml-2 px-2 py-0.5
                       text-[10px] font-semibold rounded-full
                       {{ $isActive
                            ? 'bg-white text-gray-700 border border-gray-200'
                            : 'bg-gray-100 text-gray-600 border border-gray-200'
                       }}"
            >
                {{ $item['badge'] }}
            </span>
        @endif
    </a>
</li>


            @endif

        @endforeach

    </ul>

</aside>