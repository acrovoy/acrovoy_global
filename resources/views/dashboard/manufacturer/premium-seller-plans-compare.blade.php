@extends('dashboard.layout')

@section('dashboard-content')
<a href="{{ route('manufacturer.premium-plans') }}"
           class="text-sm text-gray-500 mb-2 hover:text-gray-700 flex items-center gap-1">
            ← Back to plans
        </a>

<h2 class="text-2xl font-bold">Compare Seller Plans</h2>
<p class="text-gray-600 mb-6">
    Choose the plan that fits your business growth.
</p>

<div class="overflow-x-auto">
    <table class="min-w-full border rounded-lg overflow-hidden">
        <thead>
            <tr class="bg-gray-100">
                <th class="text-left p-4">Features</th>

                @foreach($plans as $plan)
                    <th class="text-center p-4
                        {{ $plan->popular ? 'bg-blue-50 border-blue-500 font-semibold relative' : '' }}">
                        <div class="font-semibold">
                             @if($plan->popular)
                                <div class=" bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                    Most Popular
                                </div>
                            @endif
                            {{ $plan->name }}
                           
                        </div>
                        <div class="text-sm text-gray-600">{{ $plan->price }}</div>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                // Подготавливаем фичи каждого плана в удобном виде
                foreach($plans as $plan) {
                    $f = [];
                    foreach($plan->planFeatures as $pf) {
                        if(isset($pf->feature)) {
                            $f[$pf->feature->slug] = $pf->value;
                        }
                    }
                    $plan->prepared_features = $f;
                }
            @endphp

            {{-- Products limit --}}
            <tr class="border-t">
                <td class="p-4 font-medium">Products limit</td>
                @foreach($plans as $plan)
                    @php
                        $v = $plan->prepared_features['products-limit'] ?? null;
                    @endphp
                    <td class="p-4 text-center {{ $plan->popular ? 'bg-blue-50' : '' }}">
                        {{ $v === null || $v === '' ? 'Unlimited' : $v }}
                    </td>
                @endforeach
            </tr>

            {{-- Visibility --}}
            <tr class="border-t">
                <td class="p-4 font-medium">Visibility</td>
                @foreach($plans as $plan)
                    @php
                        $visibility = collect($plan->prepared_features)
                            ->filter(fn($val, $slug) => str_starts_with($slug, 'visibility'))
                            ->keys()
                            ->map(fn($slug) => ucfirst(str_replace('visibility:-','',$slug)))
                            ->first() ?? '-';
                    @endphp
                    <td class="p-4 text-center {{ $plan->popular ? 'bg-blue-50' : '' }}">{{ $visibility }}</td>
                @endforeach
            </tr>

            {{-- Analytics --}}
            <tr class="border-t">
                <td class="p-4 font-medium">Analytics</td>
                @foreach($plans as $plan)
                    @php
                        $analytics = collect($plan->prepared_features)
                            ->filter(fn($val, $slug) => str_starts_with($slug, 'analytics'))
                            ->keys()
                            ->map(fn($slug) => ucfirst(str_replace('analytics:-','',$slug)))
                            ->first() ?? '-';
                    @endphp
                    <td class="p-4 text-center {{ $plan->popular ? 'bg-blue-50' : '' }}">{{ $analytics }}</td>
                @endforeach
            </tr>

            {{-- Priority placement --}}
            <tr class="border-t">
                <td class="p-4 font-medium">Priority placement</td>
                @foreach($plans as $plan)
                    @php
                        $priority = collect($plan->prepared_features)
                            ->filter(fn($val, $slug) => $slug === 'priority-placement:-yes')
                            ->count() ? '✔' : '-';
                    @endphp
                    <td class="p-4 text-center {{ $plan->popular ? 'bg-blue-50' : '' }}">{{ $priority }}</td>
                @endforeach
            </tr>

            {{-- Support --}}
            <tr class="border-t">
                <td class="p-4 font-medium">Support</td>
                @foreach($plans as $plan)
                    @php
                        $support = collect($plan->prepared_features)
                            ->filter(fn($val, $slug) => str_starts_with($slug, 'support'))
                            ->keys()
                            ->map(fn($slug) => ucwords(trim(str_replace(['support:-','-'],' ',$slug))))
                            ->implode(', ') ?: '-';
                    @endphp
                    <td class="p-4 text-center {{ $plan->popular ? 'bg-blue-50' : '' }}">{{ $support }}</td>
                @endforeach
            </tr>

        </tbody>
    </table>
</div>

<div class="mt-6 flex flex-wrap gap-4">
    
</div>
@endsection
