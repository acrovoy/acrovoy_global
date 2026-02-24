
<div class="grid lg:grid-cols-3 gap-4">

    {{-- ================= LEFT PROFILE CONTENT ================= --}}
    <div class="lg:col-span-2 space-y-12">

        {{-- 👉 Твой существующий профильный контент вставь сюда --}}
        @include('supplier.partials.profile_main')

    </div>



    {{-- ================= RIGHT SIDEBAR CONTACT ================= --}}
    <div class="space-y-6">

        <div class="sticky top-24 bg-white border rounded-2xl shadow-sm p-6 space-y-5">

            <h3 class="font-semibold text-lg text-gray-900 border-l-4 border-yellow-800 pl-3">
                Contact Supplier
            </h3>



            <button class="w-full bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold py-3 rounded-xl transition">
                Send Inquiry
            </button>



            <div class="text-xs text-gray-500 space-y-3 pt-3 border-t">

                <div>
                    <div class="text-gray-400">Email</div>
                    <div class="font-medium text-gray-800">
                        {{ $supplier->email ?? '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-400">Phone</div>
                    <div class="font-medium text-gray-800">
                        {{ $supplier->phone ?? '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-400">Country</div>
                    <div class="font-medium text-gray-800">
                        {{ $supplier->country?->name ?? '—' }}
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>









