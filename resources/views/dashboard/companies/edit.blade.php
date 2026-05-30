@extends('dashboard.layout')

@section('dashboard-content')

<a href="{{ route('dashboard.companies.index') }}"
   class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
    ← Back to companies
</a>

<h2 class="text-2xl font-bold">
    Edit Company
</h2>

<p class="text-sm text-gray-500 mb-6">
    Update company information
</p>

@include('dashboard.companies.partials.form', [
    'company' => $company,
    'type' => $type ?? $company->type ?? null
])

@endsection