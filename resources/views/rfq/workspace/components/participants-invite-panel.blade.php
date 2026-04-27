<div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-4">

    <div class="text-sm font-semibold">
        Invite Suppliers
    </div>

    {{-- ========================= --}}
    {{-- PRIVATE MODE --}}
    {{-- ========================= --}}
    @if($visibility === 'private')

        <div class="text-xs text-gray-500">
            Private RFQ — invite suppliers manually only
        </div>

        {{-- INVITE FROM PLATFORM --}}
        <form method="POST"
      action="{{ route('buyer.rfq.participants.store', $rfq) }}"
      class="flex gap-2">

    @csrf

    <input type="hidden"
           name="participant_type"
           value="{{ \App\Models\Supplier::class }}">

    <select name="participant_id"
            class="border border-gray-300 rounded px-3 py-2 text-sm text-gray-600 w-full"
            required>

        <option value="">
            Select supplier
        </option>

        @foreach($suppliers ?? [] as $supplier)
            <option value="{{ $supplier->id }}">
                {{ $supplier->name }}
            </option>
        @endforeach

    </select>

    <button class="px-4 py-2 bg-gray-800 text-white text-sm rounded">
        Invite
    </button>

</form>

{{-- ========================= --}}
{{-- CATEGORY MODE (MULTI) --}}
{{-- ========================= --}}
@elseif($visibility === 'category')

    <div class="text-xs text-gray-500 mb-2">
        Select categories that can see this RFQ
        
    </div>

    <form method="POST"
          action="{{ route('buyer.rfq.visibility.category.update', $rfq) }}"
          class="space-y-3">

        @csrf
        @method('PATCH')

        <div class="border border-gray-200 rounded p-3 max-h-64 overflow-y-auto space-y-2">

            @foreach($allCategories ?? [] as $category)

                <label class="flex items-center gap-2 text-sm text-gray-700">

                    <input type="checkbox"
       name="category_ids[]"
       value="{{ $category->id }}"
       class="rounded border-gray-300"

       @if(in_array($category->id, $selectedCategoryIds ?? []))
           checked
       @endif
>

                    <span>{{ $category->name }}</span>

                </label>

            @endforeach

        </div>

        <button class="px-4 py-2 bg-gray-800 text-white text-sm rounded">
            Save Categories
        </button>

    </form>

        {{-- optional manual invite --}}
        <div class="pt-2 border-t border-gray-200">

            <form method="POST"
                  action="{{ route('buyer.rfq.participants.store', $rfq) }}"
                  class="flex gap-2">

                @csrf

                <select name="supplier_id"
                        class="border border-gray-300 rounded px-3 py-2 text-sm text-gray-600 w-full">

                    <option value="">
                        Select supplier
                    </option>

                    @foreach($suppliers ?? [] as $supplier)
                        <option value="{{ $supplier->id }}">
                            {{ $supplier->name }}
                        </option>
                    @endforeach

                </select>

                <button class="px-4 py-2 bg-gray-800 text-white text-sm rounded">
                    Invite
                </button>

            </form>

        </div>

     {{-- ========================= --}}
    {{-- PLATFORM MODE (ONLY EMAIL) --}}
    {{-- ========================= --}}
    @elseif($visibility === 'platform')

        <div class="text-xs text-gray-500">
            Platform mode — invite suppliers only via email
        </div>


    {{-- ========================= --}}
    {{-- OPEN MODE (future) --}}
    {{-- ========================= --}}
    @elseif($visibility === 'open')

        <div class="text-xs text-gray-400">
            Open RFQ — public discovery mode (not active yet)
        </div>

    @endif

    {{-- ========================= --}}
    {{-- EMAIL INVITE (GLOBAL) --}}
    {{-- ========================= --}}
    <form method="POST"
          action="{{ route('buyer.rfq.participants.store', $rfq) }}"
          class="flex gap-2 pt-3 border-t border-gray-200">

        @csrf

        <input type="email"
               name="email"
               placeholder="supplier@email.com"
               class="border border-gray-300 rounded px-3 py-2 text-sm w-full">

        <button class="px-4 py-2 bg-gray-800 text-white text-sm rounded">
            Invite by Email
        </button>

    </form>

</div>