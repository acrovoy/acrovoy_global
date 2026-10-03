<x-alerts />

<form method="POST"
      action="{{ $company
            ? route('dashboard.companies.update', $company->id)
            : route('dashboard.companies.store') }}"
      class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

    @csrf

    @if($company)
        @method('PUT')
    @endif

    {{-- Form header --}}
    <div class="px-6 py-5 border-b border-gray-200">
        <h2 class="text-base font-semibold text-gray-900">
            Company information
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Provide the basic information for this company.
        </p>
    </div>

    <div class="p-6 space-y-8">

        {{-- COMPANY TYPE --}}
        <section>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-900">
                    Company type
                </label>

                <p class="mt-1 text-sm text-gray-500">
                    Select how this company will operate on ACROVOY.
                </p>
            </div>

            @if($company)

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <div class="flex items-center gap-3">

                        <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-500"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ ucfirst($company->type) }}
                            </p>

                            <p class="text-xs text-gray-500 mt-0.5">
                                Company type cannot be changed after creation.
                            </p>
                        </div>

                    </div>

                    <input type="hidden"
                           name="type"
                           value="{{ $company->type }}">
                </div>

            @else

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

                    {{-- BUYER --}}
                    <label class="relative cursor-pointer">

                        <input type="radio"
                               name="type"
                               value="buyer"
                               class="peer sr-only"
                               {{ old('type', $type ?? '') === 'buyer' ? 'checked' : '' }}>

                        <div class="h-full rounded-xl border border-gray-200 bg-white p-4 transition
                                    hover:border-gray-300 hover:shadow-sm
                                    peer-checked:border-gray-900
                                    peer-checked:ring-1
                                    peer-checked:ring-gray-900">

                            <div class="flex items-start justify-between gap-3">

                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-600"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="1.8"
                                              d="M3 3h2l2.4 11.5a2 2 0 002 1.5h8.8a2 2 0 002-1.5L22 7H6M10 20a1 1 0 11-2 0 1 1 0 012 0zm9 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                                    </svg>
                                </div>

                                <span class="hidden peer-checked:block text-xs font-medium text-gray-900">
                                    Selected
                                </span>

                            </div>

                            <h3 class="mt-4 text-sm font-semibold text-gray-900">
                                Buyer
                            </h3>

                            <p class="mt-1 text-xs leading-5 text-gray-500">
                                Purchase furniture, décor and hospitality products.
                            </p>

                        </div>
                    </label>

                    {{-- SUPPLIER --}}
                    <label class="relative cursor-pointer">

                        <input type="radio"
                               name="type"
                               value="supplier"
                               class="peer sr-only"
                               {{ old('type', $type ?? '') === 'supplier' ? 'checked' : '' }}>

                        <div class="h-full rounded-xl border border-gray-200 bg-white p-4 transition
                                    hover:border-gray-300 hover:shadow-sm
                                    peer-checked:border-gray-900
                                    peer-checked:ring-1
                                    peer-checked:ring-gray-900">

                            <div class="flex items-start justify-between gap-3">

                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-600"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="1.8"
                                              d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>
                                    </svg>
                                </div>

                                <span class="hidden peer-checked:block text-xs font-medium text-gray-900">
                                    Selected
                                </span>

                            </div>

                            <h3 class="mt-4 text-sm font-semibold text-gray-900">
                                Supplier
                            </h3>

                            <p class="mt-1 text-xs leading-5 text-gray-500">
                                Sell furniture, décor and other hospitality products.
                            </p>

                        </div>
                    </label>



                    {{-- LOGISTICS --}}
<div class="relative">

    <div class="h-full rounded-xl border border-gray-200 bg-gray-50 p-4 opacity-60 cursor-not-allowed">

        <div class="flex items-start justify-between gap-3">

            <div class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-400"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="1.8"
                          d="M3 7h11v10H3V7zm11 3h4l3 3v4h-7v-7zM7 19a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z"/>
                </svg>
            </div>

            <span class="inline-flex items-center rounded-full bg-gray-200 px-2 py-1 text-[11px] font-medium text-gray-500">
                Coming soon
            </span>

        </div>

        <h3 class="mt-4 text-sm font-semibold text-gray-500">
            Logistics
        </h3>

        <p class="mt-1 text-xs leading-5 text-gray-400">
            Manage transportation, delivery and logistics services.
        </p>

    </div>

</div>



                    <!-- {{-- LOGISTICS --}}
                    <label class="relative cursor-pointer">

                        <input type="radio"
                               name="type"
                               value="logistics"
                               class="peer sr-only"
                               {{ old('type', $type ?? '') === 'logistics' ? 'checked' : '' }}>

                        <div class="h-full rounded-xl border border-gray-200 bg-white p-4 transition
                                    hover:border-gray-300 hover:shadow-sm
                                    peer-checked:border-gray-900
                                    peer-checked:ring-1
                                    peer-checked:ring-gray-900">

                            <div class="flex items-start justify-between gap-3">

                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-600"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="1.8"
                                              d="M3 7h11v10H3V7zm11 3h4l3 3v4h-7v-7zM7 19a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z"/>
                                    </svg>
                                </div>

                                <span class="hidden peer-checked:block text-xs font-medium text-gray-900">
                                    Selected
                                </span>

                            </div>

                            <h3 class="mt-4 text-sm font-semibold text-gray-900">
                                Logistics
                            </h3>

                            <p class="mt-1 text-xs leading-5 text-gray-500">
                                Manage transportation, delivery and logistics services.
                            </p>

                        </div>
                    </label> -->

                </div>

                @error('type')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            @endif

        </section>


        {{-- BASIC DETAILS --}}
        <section class="border-t border-gray-100 pt-8">

            <div class="mb-5">
                <h3 class="text-sm font-semibold text-gray-900">
                    Basic details
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    This information will identify your company across the platform.
                </p>
            </div>

            <div class="space-y-5">

                {{-- NAME --}}
                <div>
                    <label for="name"
                           class="block text-sm font-medium text-gray-900">
                        Company name
                        <span class="text-red-500">*</span>
                    </label>

                    <p class="mt-1 text-xs text-gray-500">
                        Use the official or commonly used name of the company.
                    </p>

                    <input type="text"
                           name="name"
                           id="name"
                           required
                           autocomplete="organization"
                           value="{{ old('name', $company->name ?? '') }}"
                           placeholder="e.g. MAVENTO FURNITURE"
                           class="mt-2 block w-full rounded-lg border border-gray-300 px-3 py-2.5
                                  text-sm text-gray-900 placeholder-gray-400
                                  focus:border-gray-900 focus:ring-1 focus:ring-gray-900
                                  outline-none transition">

                    @error('name')
                        <p class="mt-1.5 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- SLUG --}}
                <div>
                    <div class="flex items-center justify-between gap-4">

                        <label for="slug"
                               class="block text-sm font-medium text-gray-900">
                            URL identifier
                        </label>

                        <span class="text-xs text-gray-400">
                            Optional
                        </span>

                    </div>

                    <p class="mt-1 text-xs text-gray-500">
                        Used in the company's web address. It is generated automatically
                        from the company name.
                    </p>

                    <div class="relative mt-2">

                        <input type="text"
                               name="slug"
                               id="slug"
                               value="{{ old('slug', $company->slug ?? '') }}"
                               placeholder="mavento-furniture"
                               class="block w-full rounded-lg border border-gray-300 px-3 py-2.5
                                      text-sm text-gray-900 placeholder-gray-400
                                      focus:border-gray-900 focus:ring-1 focus:ring-gray-900
                                      outline-none transition">

                    </div>

                    @error('slug')
                        <p class="mt-1.5 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

        </section>

    </div>


    {{-- ACTIONS --}}
    <div class="flex items-center justify-between gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">

        <p class="hidden sm:block text-xs text-gray-500">
            You can add more company information after creation.
        </p>

        <div class="flex items-center gap-3 ml-auto">

            <a href="{{ route('dashboard.companies.index', ['type' => $type]) }}"
               class="inline-flex items-center justify-center px-4 py-2.5
                      text-sm font-medium text-gray-700
                      bg-white border border-gray-300 rounded-lg
                      hover:bg-gray-50 transition">
                Cancel
            </a>

            <button type="submit"
                    class="inline-flex items-center justify-center gap-2
                           px-4 py-2.5 text-sm font-medium
                           text-white bg-gray-900 rounded-lg
                           hover:bg-gray-800
                           focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2
                           transition">

                <svg class="w-4 h-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="1.8"
                          d="M5 12h14M13 6l6 6-6 6"/>
                </svg>

                {{ $company ? 'Save changes' : 'Create company' }}

            </button>

        </div>

    </div>

</form>


{{-- SLUG GENERATOR --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    if (!nameInput || !slugInput) {
        return;
    }

    let slugManuallyEdited = false;

    slugInput.addEventListener('input', function () {
        slugManuallyEdited = true;
    });

    nameInput.addEventListener('input', function () {

        if (slugManuallyEdited) {
            return;
        }

        const slug = this.value
            .toLowerCase()
            .normalize('NFKD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');

        slugInput.value = slug;
    });
});
</script>