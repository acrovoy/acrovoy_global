@php
    use App\Models\User;
    use App\Models\Buyer;

    $buyer ??= null;

    $buyerName = 'Buyer';
    $country = null;
    $memberSince = '-';
    $logo = asset('images/no-logo.png');

    if ($buyer instanceof User) {
        $buyerName = trim($buyer->name . ' ' . $buyer->last_name);
        $country = $buyer->purchaseCountry?->name ?? $buyer->purchase_country;
        $memberSince = optional($buyer->created_at)->format('Y');
        $logo = $buyer->avatar?->cdn_url ?? $logo;

        $level = $buyer->level ?? 'Basic';
        $verified = false;
        $premium = false;
        $status = 'Active';
        $reputation = 0;
    }

    if ($buyer instanceof Buyer) {

        $buyerName = $buyer->name ?: 'Buyer Company';
        $country = $buyer->country?->name;
        $memberSince = optional($buyer->created_at)->format('Y');
        $logo = $buyer->logo()?->cdn_url ?? $logo;

        $level = $buyer->level ?? 'Basic';
        $verified = (bool) $buyer->is_verified;
        $premium = (bool) $buyer->is_premium;
        $status = ucfirst($buyer->status ?? 'Active');
        $reputation = $buyer->reputation ?? 0;
    }

    $glow = match($level) {
        'Silver'   => 'linear-gradient(135deg,#d1d5db,#f3f4f6)',
        'Gold'     => 'linear-gradient(135deg,#f59e0b,#fde68a)',
        'Platinum' => 'linear-gradient(135deg,#1f2937,#6b7280)',
        default    => 'linear-gradient(135deg,#e5e7eb,#f9fafb)',
    };
@endphp

<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

    {{-- HEADER --}}
    <div class="relative overflow-hidden px-6 py-7 border-b border-gray-200 bg-white">

        <div class="absolute inset-0 pointer-events-none overflow-hidden">

            <div
                class="absolute -top-20 -right-16 w-52 h-52 rounded-full blur-3xl opacity-20"
                style="background: {{ $glow }}">
            </div>

            <div
                class="absolute -bottom-20 -left-16 w-56 h-56 rounded-full blur-3xl opacity-10"
                style="background: {{ $glow }}">
            </div>

        </div>

        <div class="relative flex flex-col items-center text-center">

            <div class="w-20 h-20 rounded-2xl overflow-hidden border border-gray-200 bg-white shadow-sm">

                <img
                    src="{{ $logo }}"
                    class="w-full h-full object-cover">

            </div>

            <h3 class="mt-4 text-lg font-semibold text-gray-900">
                {{ $buyerName }}
            </h3>

            <div class="mt-2">

                <span
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold

                    {{ $level === 'Basic' ? 'bg-gray-100 text-gray-600 border border-gray-200' : '' }}
                    {{ $level === 'Silver' ? 'bg-gray-200 text-gray-700 border border-gray-300' : '' }}
                    {{ $level === 'Gold' ? 'bg-amber-100 text-amber-700 border border-amber-200' : '' }}
                    {{ $level === 'Platinum' ? 'bg-slate-900 text-white border border-slate-700' : '' }}">

                    {{ strtoupper($level) }} BUYER

                </span>

            </div>

            @if($country)
                <div class="mt-3 text-sm text-gray-500">
                    {{ $country }}
                </div>
            @endif

           

        </div>

    </div>

   


     {{-- QUICK LINKS --}}
                    <div class="border-t border-gray-200 p-1">

                        <a href="{{ route('buyer.show', $buyer) }}"
                            class="flex items-center justify-between rounded-xl
                      px-4 py-3
                      text-sm
                      text-gray-600
                      hover:bg-gray-50
                      transition">

                            <span>View Public Profile</span>

                            <svg class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M7 17L17 7M17 7H9M17 7v8" />
                            </svg>

                        </a>

                    </div>

</div>