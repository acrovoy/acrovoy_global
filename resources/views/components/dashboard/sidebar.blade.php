<aside class="w-full lg:w-1/4 bg-white border border-gray-200 rounded-xl shadow-sm p-4 self-start">

    {{-- COMPANY SWITCHER --}}
    <div class="mb-6 relative">

        @if($companies->count())

        {{-- BUTTON --}}
        <button
            type="button"
            onclick="document.getElementById('companyDropdown').classList.toggle('hidden')"
            class="group w-full flex justify-between items-center bg-white border-2 border-brown-500 rounded-md px-3 py-2
                   transition-all duration-200 ease-out hover:shadow-md hover:-translate-y-[1px]">

            <div class="text-left">

                <div class="font-semibold truncate max-w-[200px]">

                    @if($isPersonal)
                    Buyer Mode
                    @else
                    {{ $active?->company?->name ?? 'Select company' }}
                    @endif

                </div>

                <div class="text-xs text-gray-500">

                    @if($isPersonal)
                    Personal account
                    @else
                    {{ ucfirst($active?->role ?? '') }}
                    @endif

                </div>

            </div>

            {{-- ICON --}}
            <svg
                class="w-4 h-4 text-gray-500 transition-all duration-300 ease-out group-hover:rotate-180 group-hover:text-gray-700"
                viewBox="0 0 20 20"
                fill="currentColor">
                <path
                    fill-rule="evenodd"
                    d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                    clip-rule="evenodd" />
            </svg>

        </button>


        {{-- DROPDOWN --}}
        <div
            id="companyDropdown"
            class="hidden absolute z-50 mt-2 w-full bg-white border rounded-md shadow-lg">

            {{-- PERSONAL MODE --}}
            <form method="POST" action="{{ route('company.switch') }}">
                @csrf

                <input
                    type="hidden"
                    name="company_key"
                    value="personal|0">

                <button
                    type="submit"
                    class="w-full text-left px-3 py-2 hover:bg-gray-50 transition flex justify-between items-center">

                    <div>

                        <div class="font-medium">
                            Buyer Mode
                        </div>

                        <div class="text-xs text-gray-500">
                            Personal account
                        </div>

                    </div>

                    @if($isPersonal)
                    <span class="text-xs text-green-600">Active</span>
                    @endif

                </button>

            </form>


            <hr class="my-1 border-gray-100">


            {{-- COMPANY LIST --}}
            @foreach($companies as $company)

            <form method="POST" action="{{ route('company.switch') }}">
                @csrf

                <input
                    type="hidden"
                    name="company_key"
                    value="{{ $company->company_type.'|'.$company->company_id }}">

                <button
                    type="submit"
                    class="w-full text-left px-3 py-2 hover:bg-gray-50 transition flex justify-between">

                    <div>

                        <div class="font-medium">
                            {{ $company->company?->name }}
                        </div>

                        <div class="text-xs text-gray-500">
                            {{ ucfirst($company->role) }}
                            •
                            @if(str_contains($active?->company_type, 'Supplier'))
                            Supplier Company
                            @elseif(str_contains($active?->company_type, 'LogisticCompany'))
                            Logistics Company
                            @else
                            Company
                            @endif
                        </div>

                    </div>

                    @if(
                    !$isPersonal &&
                    $company->company_id === $active?->company_id &&
                    $company->company_type === $active?->company_type
                    )
                    <span class="text-xs text-green-600">Active</span>
                    @endif

                </button>

            </form>

            @endforeach

        </div>


        @else

        {{-- NO COMPANIES --}}
        <div class="px-3 py-2 bg-gray-50 border rounded-md text-sm text-gray-600">
            Personal account
        </div>

        @endif

    </div>


    {{-- MENU --}}
    <ul class="space-y-1">

        @foreach($menu as $item)

        @if(($item['type'] ?? null) === 'header')

        <li class="mt-5 mb-2">

            <div
                class="px-3 py-2 rounded-lg bg-gray-50/60 border border-gray-100 backdrop-blur-sm">

                <div class="flex items-center justify-between">

                    <div class="text-[11px] font-semibold tracking-widest text-gray-400 uppercase">
                        {{ $item['label'] }}
                    </div>

                    <div class="w-1.5 h-1.5 rounded-full bg-gray-300"></div>

                </div>

            </div>

        </li>

        @else

        <li>

            <a
                href="{{ isset($item['route']) ? route($item['route']) : '#' }}"
                class="group flex items-center justify-between px-3 py-2 rounded-lg
                               text-sm text-gray-700
                               transition-all duration-200
                               hover:bg-gray-50 hover:text-gray-900
                               relative">

                <span
                    class="absolute left-0 top-1/2 -translate-y-1/2 h-0 w-0.5 bg-brown-500
                                   group-hover:h-5 transition-all duration-200"></span>

                <span class="ml-2 font-medium">
                    {{ $item['label'] }}
                </span>


                @if(!empty($item['badge']))

                <span
                    class="ml-2 px-2 p-0.5 text-[11px] font-semibold
                                   bg-indigo-600 text-white rounded-full">
                    {{ $item['badge'] }}
                </span>

                @endif

            </a>

        </li>

        @endif

        @endforeach

    </ul>

</aside>