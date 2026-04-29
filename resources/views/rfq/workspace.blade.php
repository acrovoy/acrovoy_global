@extends('dashboard.layout')

@section('dashboard-content')

@php
    $context = app(\App\Services\Company\ActiveContextService::class);
    $mode = $context->role(); // buyer / supplier
@endphp

@include('rfq.partials.header', ['rfq' => $rfq])

{{-- 🔥 ROLE-AWARE TABS --}}
@if($mode === 'buyer')
    @include('rfq.partials.tabs.buyer', [
        'rfq' => $rfq,
        'activeTab' => $activeTab
    ])
@else
    @include('rfq.partials.tabs.supplier', [
        'rfq' => $rfq,
        'activeTab' => $activeTab
    ])
@endif

<div class="space-y-6">

    {{-- 🔥 ROLE-AWARE CONTENT --}}
    @switch($activeTab)

        @case('overview')
            @include('rfq.workspace.overview')
        @break

        @case('requirements')
            @include('rfq.workspace.requirements')
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