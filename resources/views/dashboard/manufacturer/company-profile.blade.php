@extends('dashboard.layout')

@section('dashboard-content')

<div class="max-w-7xl mx-auto space-y-4">


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



{{-- ================= CERTIFICATES BLOCK ================= --}}

<div class="bg-white border rounded-2xl shadow-sm p-6 space-y-5">

<div class="flex justify-between">
    <h3 class="text-sm text-gray-400 uppercase tracking-wider">
        Certificates
    </h3>
    <button
        type="button"
        onclick="openModal('certificatesModal')"
        class="px-4 py-2 text-sm rounded-lg border hover:bg-gray-50 transition">

        + Certificates
    </button>
</div>
    @if($company->certificates->count())

    <div class="flex flex-wrap gap-4">

        @foreach($company->certificates as $certificate)

        @php
            $ext = strtolower(pathinfo($certificate->file_path, PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg','jpeg','png','webp']);
        @endphp

        <div class="border rounded-xl p-3 w-32 flex flex-col items-center gap-2">

            <a href="{{ asset('storage/'.$certificate->file_path) }}"
               target="_blank"
               class="block text-center w-full">

                @if($isImage)

                    <img src="{{ asset('storage/'.$certificate->file_path) }}"
                         class="w-full h-20 object-contain rounded">

                @elseif($ext === 'pdf')

                    <div class="w-full h-20 flex items-center justify-center bg-gray-100 rounded">
                        <img src="{{ asset('images/pdf-icon.png') }}"
                             class="w-10 h-10 object-contain">
                    </div>

                @else

                    <div class="w-full h-20 flex items-center justify-center bg-gray-100 rounded text-xs text-gray-500">
                        {{ strtoupper($ext) }}
                    </div>

                @endif

                <div class="text-xs truncate mt-1">
                    {{ $certificate->name }}
                </div>

            </a>

        </div>

        @endforeach

    </div>

    @else

    <p class="text-sm text-gray-400">
        No certificates uploaded yet
    </p>

    @endif

</div>



{{-- ================= MAIN PROFILE FORM ================= --}}

@include('dashboard.manufacturer.partials.company-profile-form', [
    'supplierTypes' => $supplierTypes
])

</div>



{{-- ================= CERTIFICATE MODAL ================= --}}

<div id="certificatesModal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">

    <div class="bg-white w-full max-w-3xl rounded-2xl shadow-xl overflow-hidden">

        <div class="flex justify-between items-center px-6 py-4 border-b">

            <h3 class="text-lg font-semibold">
                Manage certificates
            </h3>

            <button type="button"
                    onclick="closeModal('certificatesModal')"
                    class="text-gray-400 hover:text-gray-600 text-2xl leading-none">

                &times;

            </button>

        </div>


        <div class="p-6 max-h-[70vh] overflow-y-auto">
            @include('dashboard.manufacturer.partials.manage-certificates')
        </div>

    </div>
</div>



<script>
function openModal(id){
    document.getElementById(id)?.classList.remove('hidden');
}

function closeModal(id){
    document.getElementById(id)?.classList.add('hidden');
}
</script>

@endsection