@extends('dashboard.layout')

@section('dashboard-content')

<div class="flex items-start justify-between mb-4">
    <div>
        <h2 class="text-2xl font-bold">Company Profile</h2>
        <p class="text-sm text-gray-500">
            Create a new product listing for your catalog
        </p>
    </div>

    
    
</div>


{{-- Кнопка сертификатов --}}
    <button
        onclick="openModal('certificatesModal')"
        class="px-3 py-1.5 text-sm
               border border-gray-300 text-gray-700
               rounded-md
               hover:bg-gray-50 hover:border-gray-400">
        + Certificates
    </button>



@if($company->certificates->count())
    <div class="certificate-list mt-4 flex flex-wrap gap-2">
        @foreach($company->certificates as $certificate)
            @php
    $ext = strtolower(pathinfo($certificate->file_path, PATHINFO_EXTENSION));
    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
@endphp

<div class="certificate-item border rounded p-2 w-32 flex flex-col items-center gap-2 relative">

    <a href="{{ asset('storage/' . $certificate->file_path) }}"
       target="_blank"
       class="block w-full text-center">

        @if($isImage)
            <img src="{{ asset('storage/' . $certificate->file_path) }}"
                 class="w-full h-24 object-contain rounded"
                 alt="{{ $certificate->name }}">
        @elseif($ext === 'pdf')
    {{-- Иконка для PDF --}}
    <div class="w-full h-24 flex items-center justify-center bg-gray-100 rounded">
        <img src="{{ asset('images/pdf-icon.png') }}" 
             alt="PDF" 
             class="w-12 h-12 object-contain">
    </div>
@else
    {{-- Иконка для остальных типов файлов --}}
    <div class="w-full h-24 flex items-center justify-center bg-gray-100 rounded text-gray-500 text-sm">
        {{ strtoupper($ext) }}
    </div>
@endif

        <div class="mt-1 text-xs truncate">
            {{ $certificate->name }}
        </div>
    </a>

    
</div>
        @endforeach
    </div>
@else
    <p class="text-sm text-gray-400 mt-2">
        No certificates uploaded yet
    </p>
@endif


@include('dashboard.manufacturer.partials.company-profile-form', ['supplierTypes' => $supplierTypes])

{{-- Certificates modal --}}
<div id="certificatesModal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">

    <div class="bg-white w-full max-w-3xl rounded-lg shadow-lg relative">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <h3 class="text-lg font-semibold">
                Manage certificates
            </h3>
            <button onclick="closeModal('certificatesModal')"
                    class="text-gray-400 hover:text-gray-600 text-xl leading-none">
                &times;
            </button>
        </div>

        {{-- Content --}}
        <div class="p-4 max-h-[70vh] overflow-y-auto">
            @include('dashboard.manufacturer.partials.manage-certificates')
        </div>

    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id)?.classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id)?.classList.add('hidden');
}
</script>

@endsection