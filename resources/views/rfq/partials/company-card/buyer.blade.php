@php
    use App\Models\User;
    use App\Models\Buyer;

    $buyer = null;

    if ($rfq->buyer_type === User::class) {
        $buyer = User::find($rfq->buyer_id);
    } elseif ($rfq->buyer_type === Buyer::class) {
        $buyer = Buyer::find($rfq->buyer_id);
    }

    $buyerName = '-';
    $buyerCountry = 'Unknown';
    $memberSince = '-';

    if ($buyer instanceof User) {
        $buyerName = trim($buyer->name . ' ' . $buyer->last_name);
        $buyerCountry = strtoupper($buyer->purchase_country ?? 'Unknown');
        $memberSince = optional($buyer->created_at)->format('Y');
    }

    if ($buyer instanceof Buyer) {
        $buyerName = $buyer->name ?? 'Buyer Company';
        $buyerCountry = strtoupper($buyer->country_code ?? 'Unknown');
        $memberSince = optional($buyer->created_at)->format('Y');
    }
@endphp

<div class="rounded-md overflow-hidden bg-stone-50 border border-stone-300 shadow-sm">

    {{-- HEADER --}}
    <div class="relative border-b border-stone-300 bg-gradient-to-b from-stone-100 to-stone-50">

        <div class="px-5 py-2">

            <div class="mt-2 text-center">

                <h3 class="text-lg font-serif text-stone-900 tracking-wide">
                    {{ $buyerName }}
                </h3>

                <div class="mt-1 text-[11px] tracking-[0.25em] uppercase text-stone-500">
                    {{ $buyerCountry }}
                </div>

            </div>

        </div>

    </div>

    {{-- CONTENT --}}
    <div class="px-5 py-5">

        <div class="space-y-3 text-xs">

            <div class="flex justify-between border-b border-stone-200 pb-2">
                <span class="text-stone-500 tracking-widest uppercase">
                    RFQs
                </span>

                <span class="font-semibold text-stone-900">
                    24
                </span>
            </div>

            <div class="flex justify-between border-b border-stone-200 pb-2">
                <span class="text-stone-500 tracking-widest uppercase">
                    Orders
                </span>

                <span class="font-semibold text-stone-900">
                    12
                </span>
            </div>

            <div class="flex justify-between border-b border-stone-200 pb-2">
                <span class="text-stone-500 tracking-widest uppercase">
                    Member Since
                </span>

                <span class="font-semibold text-stone-900">
                    {{ $memberSince }}
                </span>
            </div>

            <div class="flex justify-between border-b border-stone-200 pb-2">
                <span class="text-stone-500 tracking-widest uppercase">
                    Response Rate
                </span>

                <span class="font-semibold text-stone-900">
                    92%
                </span>
            </div>

            <div class="flex justify-between">
                <span class="text-stone-500 tracking-widest uppercase">
                    Rating
                </span>

                <span class="font-semibold text-stone-900">
                    ★ 4.8 / 5
                </span>
            </div>

        </div>

    </div>

    {{-- FOOTER --}}
    <div class="border-t border-stone-300 bg-stone-100 py-3 text-center">

        <div class="text-[10px] uppercase tracking-[0.45em] text-stone-500">
            BUYER STATUS
        </div>

        <div class="mt-1 text-sm font-serif text-stone-800">
            PREMIUM BUYER
        </div>

    </div>

</div>