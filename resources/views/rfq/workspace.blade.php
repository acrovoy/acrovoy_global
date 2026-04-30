@extends('dashboard.layout')

@section('dashboard-sidebar')

@include('rfq.partials.aside-panel', ['rfq' => $rfq,
'activeTab' => $activeTab])

@endsection




@section('dashboard-content')




@php
$context = app(\App\Services\Company\ActiveContextService::class);
$mode = $context->role(); // buyer / supplier
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