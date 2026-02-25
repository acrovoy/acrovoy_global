@extends('dashboard.layout')

@section('dashboard-content')

<div class="max-w-7xl mx-auto">
    <a href="{{ route('manufacturer.company.show') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
            ← Back to company profile
        </a>




{{-- ================= HEADER ================= --}}

<div class="flex flex-col lg:flex-row justify-between gap-2">

    <div>
        <h1 class="text-2xl font-semibold text-gray-900">
            Company Profile
        </h1>

        <p class="text-gray-500 text-sm mt-1">
            Manage manufacturer identity and marketplace listing settings
        </p>
    </div>

   

</div>



{{-- ================= ALERT ================= --}}

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800
            px-4 py-3 rounded-xl text-sm">

    {{ session('success') }}

</div>
@endif







{{-- ================= MAIN PROFILE FORM ================= --}}

@include('dashboard.manufacturer.partials.company-profile-form', [
    'supplierTypes' => $supplierTypes
])

</div>





@endsection