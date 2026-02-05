@extends('dashboard.layout')

@section('dashboard-content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-2">
    <div>
        <h2 class="text-2xl font-bold">Premium Buyer Plans</h2>
        <p class="text-gray-600 text-sm">
            Increase your sales, visibility and trust with premium features.
        </p>
    </div>

    <a href="{{ route('buyer.premium-plans.compare') }}"
       class="inline-flex items-center gap-1 text-sm font-semibold
              text-blue-600 hover:text-blue-800 transition">
        Compare all plans
        <span class="text-base">→</span>
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    @foreach($plans as $plan)

        {{-- Пропускаем бесплатный план --}}
        @if(strtolower($plan->name) === 'free')
            @continue
        @endif

        @php
            $f = [];

            foreach($plan->planFeatures as $pf) {
                if($pf->feature) {
                    $f[$pf->feature->slug] = $pf->value;
                }
            }

            // Вытаскиваем видимость, аналитику и поддержку отдельно
            $visibility = null;
            $analytics = null;
            $support = null;

            foreach($f as $slug => $value) {
                if(str_starts_with($slug, 'visibility')) $visibility = $slug;
                if(str_starts_with($slug, 'analytics')) $analytics = $slug;
                if(str_starts_with($slug, 'support')) $support = $slug;
            }
        @endphp

        <div class="relative border rounded-xl p-6 shadow-sm
            {{ $plan->popular ? 'border-blue-500 scale-[1.02]' : 'border-gray-200' }}
            {{ $currentPlanId === $plan->id ? 'ring-2 ring-green-500' : '' }}
            transition">

            {{-- Popular badge --}}
            @if($plan->popular)
                <span class="absolute -top-3 left-1/2 -translate-x-1/2
                    bg-blue-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                    Most Popular
                </span>
            @endif

            {{-- Current plan badge --}}
            @if($currentPlanId === $plan->id)
                <span class="absolute top-3 right-3
                    bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded">
                    Current Plan
                </span>
            @endif

            <h3 class="text-xl font-semibold mb-2">{{ $plan->name }}</h3>
            <p class="text-3xl font-bold mb-4">{{ $plan->price }}</p>

            {{-- FEATURES --}}
            <ul class="space-y-2 mb-6 text-gray-700 text-sm">

                {{-- Products limit --}}
                @if(array_key_exists('products-limit', $f))
                    <li class="flex items-center gap-2">
                        <span class="text-green-500">✔</span>
                        @if(is_null($f['products-limit']) || $f['products-limit'] === '')
                            Unlimited products
                        @else
                            Up to {{ $f['products-limit'] }} products
                        @endif
                    </li>
                @endif

                {{-- Visibility --}}
                @if($visibility)
                    <li class="flex items-center gap-2">
                        <span class="text-green-500">✔</span>
                        {{ ucfirst(str_replace(['visibility:-'], '', $visibility)) }} visibility
                    </li>
                @endif

                {{-- Priority placement --}}
                @if(!empty($f['priority-placement:-yes']))
                    <li class="flex items-center gap-2">
                        <span class="text-green-500">✔</span>
                        Top Placement
                    </li>
                @endif

                {{-- Analytics --}}
                @if($analytics)
                    <li class="flex items-center gap-2">
                        <span class="text-green-500">✔</span>
                        {{ ucfirst(str_replace(['analytics:-'], '', $analytics)) }} analytics
                    </li>
                @endif

                {{-- Support --}}
                @if($support)
                    <li class="flex items-center gap-2">
                        <span class="text-green-500">✔</span>
                        {{ ucwords(trim(str_replace(['support:-', '-'], ['',' '], $support))) }} support
                    </li>
                @endif

            </ul>

            {{-- BUTTONS --}}
            @if(strtolower($plan->name) !== 'free')
                @if($currentPlanId === $plan->id)
                    <button disabled
                            class="w-full py-2 rounded-md bg-gray-200 text-gray-500 font-semibold cursor-not-allowed">
                        Active
                    </button>
                @else
                    <button
                        class="w-full py-2 rounded-md font-semibold text-white
                        {{ $plan->popular ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-800 hover:bg-black' }}
                        transition">
                        Upgrade
                    </button>
                @endif
            @endif

        </div>
    @endforeach

</div>
@endsection
