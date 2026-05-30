@extends('dashboard.layout')

@section('dashboard-content')

<a href="{{ route('dashboard.companies.index', ['type' => $type]) }}"
   class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
    ← Back to companies
</a>

<h2 class="text-2xl font-bold">
    Add {{ ucfirst($type) }} Company
</h2>

<p class="text-sm text-gray-500 mb-6">
    Create a new company profile
</p>

@include('dashboard.companies.partials.form', [
    'company' => null,
    'type' => $type
])

@endsection