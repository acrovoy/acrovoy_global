@extends('dashboard.layout')

@section('dashboard-content')

@include('rfq.partials.header', ['rfq' => $rfq])

@include('rfq.partials.tabs', ['rfq' => $rfq, 'activeTab' => $activeTab])

<div class="space-y-6">



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