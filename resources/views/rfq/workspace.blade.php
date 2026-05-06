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




<div class="space-y-6">

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
    @include('rfq.workspace.offers')
    @break

    @case('audit')
    @include('rfq.workspace.audit')
    @break

    @default
    @include('rfq.workspace.overview')

    @endswitch

</div>

@endsection