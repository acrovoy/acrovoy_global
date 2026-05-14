@extends('dashboard.layout')

@section('dashboard-sidebar')

@include('rfq.partials.aside-panel', ['rfq' => $rfq,
'activeTab' => $activeTab])

@endsection




@section('dashboard-content')




@php
$context = app(\App\Services\Company\ActiveContextService::class);
$mode = $context->role(); // buyer / supplier

$itemsByRequirement = $offerVersion?->items
    ?->whereNotNull('requirement_id')
    ?->keyBy('requirement_id') ?? collect();

$itemsByAttribute = $offerVersion?->items
    ?->whereNotNull('attribute_id')
    ?->keyBy('attribute_id') ?? collect();
    
@endphp




<div class="">

    {{-- 🔥 ROLE-AWARE CONTENT --}}
    @switch($activeTab)

    @case('overview')
    @include('rfq.workspace.overview')
    @break

    @case('requirements')
    @include('rfq.workspace.requirements')
    @break

    @case('s-requirements')
@include('rfq.workspace.s-requirements', [
    'itemsByRequirement' => $itemsByRequirement,
    'itemsByAttribute' => $itemsByAttribute,
    'offerVersion' => $offerVersion
])
@break

    @case('participants')
    @include('rfq.workspace.participants')
    @break

   @case('offers')
@include('rfq.workspace.offers', [
    'offer' => $offer ?? null,
    'offerVersion' => $offerVersion ?? null,
    'itemsByRequirement' => $itemsByRequirement ?? collect(),
    'itemsByAttribute' => $itemsByAttribute ?? collect(),
])
@break



    @case('audit')
    @include('rfq.workspace.audit')
    @break

    @default
    @include('rfq.workspace.overview')

    @endswitch

</div>

<script>
document.addEventListener('click', function (e) {

    const btn = e.target.closest('.remove-attr');
    if (!btn) return;

    const url = btn.dataset.url;

    console.log('CLICK DELETE', url);

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(r => r.json())
    .then(() => location.reload());
});
</script>

@endsection