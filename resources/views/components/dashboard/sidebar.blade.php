<aside class="w-full lg:w-1/4 bg-white border border-gray-200 rounded-2xl shadow-sm p-5 self-start">

    @php
        $role = $role ?? \App\Facades\ActiveContext::role();
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
        $isActive = isset($item['route']) && request()->routeIs($item['route']);
    @endphp

    <a
        href="{{ isset($item['route']) ? route($item['route']) : '#' }}"
        class="group flex items-center justify-between
               px-3.5 py-2.5 rounded-xl
               text-sm
               transition-all duration-200

               {{ $isActive
                    ? 'bg-gray-100 text-gray-900 font-semibold shadow-sm'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
               }}"
    >

        <span class="flex items-center min-w-0">

            {{-- ACTIVE / HOVER INDICATOR --}}
            <span
                class="mr-3 w-1.5 h-1.5 rounded-full
                       flex-shrink-0
                       transition-all duration-200

                       {{ $isActive
                            ? 'bg-gray-900 scale-110'
                            : 'bg-gray-200 group-hover:bg-gray-400'
                       }}"
            ></span>

            <span class="truncate">
                {{ $item['label'] }}
            </span>

        </span>


        @if(!empty($item['badge']))

            <span
                class="ml-2 px-2 py-0.5
                       text-[10px] font-semibold
                       rounded-full
                       transition-colors duration-200

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